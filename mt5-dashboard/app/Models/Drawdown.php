<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Drawdown extends Model
{
    protected $fillable = [
        'ea_name',
        'symbol',
        'event_date',
        'balance',
        'equity',
        'drawdown_amount',
        'drawdown_percent',
        'martingle_cycle',
        'current_lot',
        'total_lots',
        'order_type',
        'ticket',
        'extra_data',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'balance' => 'decimal:2',
        'equity' => 'decimal:2',
        'drawdown_amount' => 'decimal:2',
        'drawdown_percent' => 'decimal:4',
        'current_lot' => 'decimal:4',
        'total_lots' => 'decimal:4',
        'extra_data' => 'array',
    ];

    public function scopeTop10Largest(Builder $query): Builder
    {
        return $query->orderBy('drawdown_amount', 'desc')->limit(10);
    }

    public function scopeCurrentMonth(Builder $query): Builder
    {
        return $query->whereYear('event_date', Carbon::now()->year)
                     ->whereMonth('event_date', Carbon::now()->month);
    }

    public function scopeForMonth(Builder $query, int $year, int $month): Builder
    {
        return $query->whereYear('event_date', $year)
                     ->whereMonth('event_date', $month);
    }
}
