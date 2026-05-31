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
        Schema::create('active_orders', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('account_id')
                ->constrained('accounts')
                ->onDelete('cascade');
            $table->enum('side', ['buy', 'sell']);
            $table->enum('type', ['limit', 'market']);
            $table->string('currency', 10);
            $table->decimal('price', 24, 8)->unsigned()->nullable(); // NULL for market orders
            $table->decimal('amount', 24, 8)->unsigned();
            $table->decimal('filled_amount', 24, 8)->unsigned()->default(0);
            $table->enum('status', ['new', 'partially_filled'])
                ->default('new');
            $table->timestamps();

            $table->index('account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('active_orders');
    }
};
