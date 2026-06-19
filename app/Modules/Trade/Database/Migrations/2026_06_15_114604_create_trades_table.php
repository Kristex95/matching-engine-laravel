<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('trades', function (Blueprint $table): void {
            $table->id();
            $table->string('taker_order_id');
            $table->string('maker_order_id');
            $table->decimal('price', 16, 8);
            $table->decimal('amount', 16, 8);
            $table->string('side'); // buy or sell
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
