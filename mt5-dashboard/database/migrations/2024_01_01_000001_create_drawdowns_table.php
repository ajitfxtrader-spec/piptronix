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
            $table->string('symbol', 20)->nullable();
            $table->decimal('drawdown_amount', 15, 2); // Drawdown in dollars
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->decimal('equity_low', 15, 2);
            $table->integer('martingle_cycle')->default(0);
            $table->decimal('current_lot', 10, 4);
            $table->decimal('total_lots', 10, 4);
            $table->integer('total_trades_in_cycle')->default(0);
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->string('status')->default('closed'); // open, closed
            $table->timestamps();
            
            $table->index(['status', 'drawdown_amount']);
            $table->index(['start_time']);
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
