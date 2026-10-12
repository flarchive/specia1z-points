<?php

namespace Acme\Points;

use Carbon\Carbon;
use Flarum\User\User;
use Illuminate\Database\ConnectionInterface;

class PointsRepository
{
    public function __construct(private ConnectionInterface $connection)
    {
    }

    public function balance(User|int $user): int
    {
        $userId = $user instanceof User ? $user->id : $user;

        return (int) ($this->connection->table('points')->where('user_id', $userId)->value('balance') ?? 0);
    }

    public function change(User|int $user, int $amount, string $reason, ?string $reference = null): bool
    {
        $userId = $user instanceof User ? $user->id : $user;

        if ($amount === 0) {
            return false;
        }

        return $this->connection->transaction(function () use ($userId, $amount, $reason, $reference): bool {
            $transaction = $this->connection->table('point_transactions')->insertGetId([
                'user_id' => $userId,
                'amount' => $amount,
                'reason' => $reason,
                'reference' => $reference,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            if (! $transaction) {
                return false;
            }

            $balance = $this->connection->table('points')->where('user_id', $userId)->lockForUpdate()->value('balance');

            if ($balance === null) {
                $this->connection->table('points')->insert([
                    'user_id' => $userId,
                    'balance' => $amount,
                ]);
            } else {
                $this->connection->table('points')
                    ->where('user_id', $userId)
                    ->update(['balance' => (int) $balance + $amount]);
            }

            return true;
        });
    }

    public function changeOnce(User|int $user, int $amount, string $reason, string $reference): bool
    {
        $userId = $user instanceof User ? $user->id : $user;

        if ($this->connection->table('point_transactions')->where('reference', $reference)->exists()) {
            return false;
        }

        return $this->change($userId, $amount, $reason, $reference);
    }
}
