<?php

use Acme\Points\Api\UserResourceFields;
use Acme\Points\Api\Controller\AdjustPointsController;
use Acme\Points\Listener\PointsListener;
use Flarum\Api\Resource;
use Flarum\Extend;

return [
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/less/admin.less'),

    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/less/forum.less'),

    (new Extend\Routes('api'))
        ->post('/points/adjust', 'acme-points.adjust', AdjustPointsController::class),

    new Extend\Locales(__DIR__.'/locale'),

    (new Extend\Settings())
        ->default('acme-points.currency_name', '积分')
        ->default('acme-points.currency_icon', 'fas fa-coins')
        ->default('acme-points.currency_style', 'gold')
        ->default('acme-points.registration_reward', 0)
        ->default('acme-points.discussion_reward', 10)
        ->default('acme-points.reply_reward', 2)
        ->default('acme-points.best_answer_reward', 5)
        ->default('acme-points.like_reward', 1)
        ->default('acme-points.suspend_penalty', 0)
        ->default('acme-points.delete_reward', true)
        ->serializeToForum('pointsName', 'acme-points.currency_name')
        ->serializeToForum('pointsIcon', 'acme-points.currency_icon')
        ->serializeToForum('pointsStyle', 'acme-points.currency_style'),

    (new Extend\ApiResource(Resource\UserResource::class))
        ->fields(UserResourceFields::class),

    (new Extend\Event())
        ->subscribe(PointsListener::class),
];
