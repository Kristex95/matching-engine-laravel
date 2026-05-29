<?php

declare(strict_types=1);

use App\Modules\Balances\Domain\Currency;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('account_id')
                ->constrained('accounts')
                ->onDelete('cascade');
            $table->enum('side', ['buy', 'sell']);
            $table->enum('type', ['limit', 'market']);
            $table->enum('currency', Currency::values());
            $table->decimal('price', 24, 8)->unsigned()->nullable(); // NULL for market orders
            $table->decimal('amount', 24, 8)->unsigned();
            $table->decimal('filled_amount', 24, 8)->unsigned()->default(0);
            $table->enum('status', ['pending', 'partially_filled', 'filled', 'canceled'])
                ->default('pending');
            $table->timestamps();

            $table->index(['account_id', 'status']);
            $table->index(['currency', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
