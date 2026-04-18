<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class MonthlySummary extends Model
{
    protected $fillable = [
        'ea_name',
        'year',
        'month',
        'total_drawdown',
        'max_drawdown',
        'total_martingle_cycles',
        'total_lots_traded',
        'total_trades',
        'daily_breakdown',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'total_drawdown' => 'decimal:2',
        'max_drawdown' => 'decimal:2',
        'total_martingle_cycles' => 'integer',
        'total_lots_traded' => 'decimal:4',
        'total_trades' => 'integer',
        'daily_breakdown' => 'array',
    ];

    public function scopeCurrentMonth(Builder $query): Builder
    {
        return $query->whereYear('created_at', Carbon::now()->year)
                     ->whereMonth('created_at', Carbon::now()->month);
    }

    public function scopeForPeriod(Builder $query, int $year, int $month): Builder
    {
        return $query->where('year', $year)->where('month', $month);
    }
}
