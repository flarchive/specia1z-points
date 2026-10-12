<?php

namespace Specia1z\Points\Api\Controller;

use Specia1z\Points\PointsRepository;
use Flarum\Http\Exception\InvalidParameterException;
use Flarum\Http\RequestUtil;
use Flarum\User\User;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AdjustPointsController implements RequestHandlerInterface
{
    public function __construct(private PointsRepository $points)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);
        $body = (array) ($request->getParsedBody() ?? []);
        $userId = (int) Arr::get($body, 'userId');
        $amount = (int) Arr::get($body, 'amount');
        $type = (string) Arr::get($body, 'type');

        if (! $userId || ! in_array($type, ['add', 'subtract', 'set'], true)) {
            throw new InvalidParameterException('A valid userId and adjustment type are required.');
        }

        if ($amount < 0 || ($type !== 'set' && $amount === 0)) {
            throw new InvalidParameterException('The amount must be a positive integer.');
        }

        $permission = match ($type) {
            'add' => 'points.add',
            'subtract' => 'points.subtract',
            'set' => 'points.adjust',
        };

        $actor->assertCan($permission);

        $user = User::query()->findOrFail($userId);
        $balance = $this->points->balance($user);
        $delta = match ($type) {
            'add' => $amount,
            'subtract' => -$amount,
            'set' => $amount - $balance,
        };

        $this->points->change($user, $delta, 'manual_'.$type);

        return new JsonResponse([
            'data' => [
                'type' => 'points-adjustments',
                'id' => (string) $user->id,
                'attributes' => [
                    'balance' => $this->points->balance($user),
                    'delta' => $delta,
                ],
            ],
        ]);
    }
}
