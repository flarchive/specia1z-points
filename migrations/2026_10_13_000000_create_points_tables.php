<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        $schema->create('points', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary();
            $table->bigInteger('balance')->default(0);
        });

        $schema->create('point_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->bigInteger('amount');
            $table->string('reason', 100);
            $table->string('reference', 191)->nullable()->unique();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    },

    'down' => function (Builder $schema) {
        $schema->dropIfExists('point_transactions');
        $schema->dropIfExists('points');
    },
];
