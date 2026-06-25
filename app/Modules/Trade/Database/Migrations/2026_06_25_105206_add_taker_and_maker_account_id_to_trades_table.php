<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('trades', function (Blueprint $table): void {
            $table->foreignId('taker_account_id')->after('id')->nullable()->constrained('accounts')->onDelete('cascade');
            $table->foreignId('maker_account_id')->after('taker_account_id')->nullable()->constrained('accounts')->onDelete('cascade');

            $table->index(['taker_account_id', 'maker_account_id']);

            $table->index('maker_account_id');
        });
    }

    public function down(): void
    {
        Schema::table('trades', function (Blueprint $table): void {
            $table->dropIndex(['taker_account_id', 'maker_account_id']);
            $table->dropIndex(['maker_account_id']);

            $table->dropForeign(['taker_account_id']);
            $table->dropForeign(['maker_account_id']);
            $table->dropColumn(['taker_account_id', 'maker_account_id']);
        });
    }
};
