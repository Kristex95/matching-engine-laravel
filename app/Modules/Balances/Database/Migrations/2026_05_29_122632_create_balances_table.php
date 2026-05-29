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
        Schema::create('balances', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('account_id')
                ->constrained('accounts')
                ->onDelete('cascade');
            $table->enum('currency', Currency::values());
            $table->decimal('available', 24, 8)->unsigned()->default(0.00000000);
            $table->decimal('locked', 24, 8)->unsigned()->default(0.00000000);
            $table->timestamps();

            $table->unique(['account_id', 'currency']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balances');
    }
};
