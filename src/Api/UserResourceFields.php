<?php

namespace Acme\Points\Api;

use Acme\Points\PointsRepository;
use Flarum\Api\Schema;
use Flarum\User\User;

class UserResourceFields
{
    public function __invoke(): array
    {
        return [
            Schema\Integer::make('points')
                ->get(fn (User $user) => resolve(PointsRepository::class)->balance($user)),
        ];
    }
}
