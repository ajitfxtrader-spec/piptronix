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
        Schema::create('monthly_summaries', function (Blueprint $table) {
            $table->id();
            $table->string('ea_name')->nullable();
            $table->integer('year');
            $table->integer('month');
            $table->decimal('total_drawdown', 15, 2)->default(0);
            $table->decimal('max_drawdown', 15, 2)->default(0);
            $table->integer('total_martingle_cycles')->default(0);
            $table->decimal('total_lots_traded', 10, 4)->default(0);
            $table->integer('total_trades')->default(0);
            $table->json('daily_breakdown')->nullable();
            $table->timestamps();
            
            $table->unique(['year', 'month', 'ea_name']);
            $table->index(['year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_summaries');
    }
};
