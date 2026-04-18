<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('drawdowns', function (Blueprint $table) {
            $table->id();
            $table->string('ea_name')->nullable();
            $table->string('symbol')->default('');
            $table->datetime('event_date');
            $table->decimal('balance', 15, 2);
            $table->decimal('equity', 15, 2);
            $table->decimal('drawdown_amount', 15, 2);
            $table->decimal('drawdown_percent', 8, 4);
            $table->integer('martingle_cycle')->default(0);
            $table->decimal('current_lot', 10, 4)->default(0);
            $table->decimal('total_lots', 10, 4)->default(0);
            $table->string('order_type')->nullable();
            $table->integer('ticket')->nullable();
            $table->json('extra_data')->nullable();
            $table->timestamps();
            
            $table->index(['event_date']);
            $table->index(['drawdown_amount']);
            $table->index(['symbol']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drawdowns');
    }
};
