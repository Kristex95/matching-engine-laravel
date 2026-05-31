<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->index('account_id');
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->dropIndex(['account_id', 'status']);
            $table->dropIndex(['currency', 'status']);
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->uuid('uuid')
                ->unique()
                ->after('id');

            $table->string('currency', 10)
                ->change();

            $table->enum('status', [
                'new',
                'partially_filled',
                'filled',
                'canceled',
                'rejected',
            ])
                ->default('new')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->index(['account_id', 'status']);
            $table->index(['currency', 'status']);

            $table->dropIndex(['account_id']);
            $table->dropUnique(['uuid']);
            $table->dropColumn('uuid');

            $table->enum('status', [
                'pending',
                'partially_filled',
                'filled',
                'canceled',
            ])
                ->default('new')
                ->change();
        });
    }
};
