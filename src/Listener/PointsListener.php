<?php

namespace Specia1z\Points\Listener;

use Specia1z\Points\PointsRepository;
use Flarum\Discussion\Event\Deleted as DiscussionDeleted;
use Flarum\Discussion\Event\Started;
use Flarum\Likes\Event\PostWasLiked;
use Flarum\Likes\Event\PostWasUnliked;
use Flarum\Post\Event\Deleted as PostDeleted;
use Flarum\Post\Event\Posted;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Suspend\Event\Suspended;
use Flarum\User\Event\Registered;
use Illuminate\Contracts\Events\Dispatcher;

class PointsListener
{
    public function __construct(
        private PointsRepository $points,
        private SettingsRepositoryInterface $settings,
    ) {
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(Registered::class, $this->whenUserRegistered(...));
        $events->listen(Started::class, $this->whenDiscussionStarted(...));
        $events->listen(DiscussionDeleted::class, $this->whenDiscussionDeleted(...));
        $events->listen(Posted::class, $this->whenPostPosted(...));
        $events->listen(PostDeleted::class, $this->whenPostDeleted(...));

        if (class_exists('FoF\\BestAnswer\\Events\\BestAnswerSet')) {
            $events->listen('FoF\\BestAnswer\\Events\\BestAnswerSet', $this->whenBestAnswerSet(...));
            $events->listen('FoF\\BestAnswer\\Events\\BestAnswerUnset', $this->whenBestAnswerUnset(...));
        }

        if (class_exists(PostWasLiked::class)) {
            $events->listen(PostWasLiked::class, $this->whenPostLiked(...));
            $events->listen(PostWasUnliked::class, $this->whenPostUnliked(...));
        }

        if (class_exists(Suspended::class)) {
            $events->listen(Suspended::class, $this->whenUserSuspended(...));
        }
    }

    public function whenUserRegistered(Registered $event): void
    {
        $reward = $this->settingInt('registration_reward');

        $this->points->changeOnce($event->user, $reward, 'registration', 'registration:'.$event->user->id);
    }

    public function whenDiscussionStarted(Started $event): void
    {
        $this->points->change(
            $event->discussion->user,
            $this->settingInt('discussion_reward'),
            'discussion'
        );
    }

    public function whenPostPosted(Posted $event): void
    {
        $post = $event->post;
        // The first post is rewarded by Discussion\Event\Started. During the
        // Posted event the discussion's first_post_id is still empty, so we
        // can skip it without relying on the post number expression.
        if (! $post->discussion->first_post_id) {
            return;
        }

        $reward = $this->settingInt('reply_reward');

        $this->points->change($post->user, $reward, 'reply');
    }

    public function whenDiscussionDeleted(DiscussionDeleted $event): void
    {
        if (! $this->settingBool('delete_reward')) {
            return;
        }

        $this->points->changeOnce(
            $event->discussion->user,
            -$this->settingInt('discussion_reward'),
            'deleted',
            'discussion-deleted:'.$event->discussion->id
        );
    }

    public function whenPostDeleted(PostDeleted $event): void
    {
        if (! $this->settingBool('delete_reward')) {
            return;
        }

        $post = $event->post;
        // The first post is covered by Discussion\Event\Deleted. Handling it
        // here as well would deduct the discussion reward twice.
        if ($post->number === 1) {
            return;
        }

        $reward = $this->settingInt('reply_reward');

        $this->points->changeOnce($post->user, -$reward, 'deleted', 'post-deleted:'.$post->id);
    }

    public function whenBestAnswerSet(\FoF\BestAnswer\Events\BestAnswerSet $event): void
    {
        $reward = $this->settingInt('best_answer_reward');

        if ($reward === 0) {
            return;
        }

        $this->points->change(
            $event->post->user,
            $reward,
            'best_answer',
            'best-answer-set:'.$event->discussion->id.':'.$event->post->id.':'.($event->discussion->best_answer_set_at?->timestamp ?? time())
        );
    }

    public function whenBestAnswerUnset(\FoF\BestAnswer\Events\BestAnswerUnset $event): void
    {
        $reward = $this->settingInt('best_answer_reward');

        if ($reward === 0) {
            return;
        }

        $this->points->change(
            $event->post->user,
            -$reward,
            'best_answer_removed',
            'best-answer-unset:'.$event->discussion->id.':'.$event->post->id.':'.time()
        );
    }

    public function whenPostLiked(PostWasLiked $event): void
    {
        $reward = $this->settingInt('like_reward');

        if ($reward === 0 || $event->post->user_id === $event->user->id) {
            return;
        }

        $this->points->change(
            $event->post->user,
            $reward,
            'like_received',
            'like-received:'.$event->post->id.':'.$event->user->id.':'.time()
        );
    }

    public function whenPostUnliked(PostWasUnliked $event): void
    {
        $reward = $this->settingInt('like_reward');

        if ($reward === 0 || $event->post->user_id === $event->user->id) {
            return;
        }

        $this->points->change(
            $event->post->user,
            -$reward,
            'like_removed',
            'like-removed:'.$event->post->id.':'.$event->user->id.':'.time()
        );
    }

    public function whenUserSuspended(Suspended $event): void
    {
        $penalty = $this->settingInt('suspend_penalty');

        if ($penalty === 0) {
            return;
        }

        $this->points->change(
            $event->user,
            -$penalty,
            'suspended',
            'suspended:'.$event->user->id.':'.time()
        );
    }

    private function settingInt(string $key): int
    {
        return max(0, (int) $this->settings->get('acme-points.'.$key, 0));
    }

    private function settingBool(string $key): bool
    {
        return filter_var($this->settings->get('acme-points.'.$key, false), FILTER_VALIDATE_BOOLEAN);
    }
}
