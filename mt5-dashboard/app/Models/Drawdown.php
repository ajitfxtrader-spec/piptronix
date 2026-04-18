<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Drawdown extends Model
{
    use HasFactory;

    protected $fillable = [
        'ea_name',
        'symbol',
        'drawdown_amount',
        'balance_before',
        'balance_after',
        'equity_low',
        'martingle_cycle',
        'current_lot',
        'total_lots',
        'total_trades_in_cycle',
        'start_time',
        'end_time',
        'status',
    ];

    protected $casts = [
        'drawdown_amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'equity_low' => 'decimal:2',
        'current_lot' => 'decimal:4',
        'total_lots' => 'decimal:4',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Get top 10 largest drawdowns ordered by amount (largest first)
     */
    public static function getTop10Largest()
    {
        return self::where('status', 'closed')
            ->orderBy('drawdown_amount', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get total drawdown for current month in dollars
     */
    public static function getCurrentMonthTotal($eaName = null)
    {
        $query = self::where('status', 'closed')
            ->whereYear('start_time', Carbon::now()->year)
            ->whereMonth('start_time', Carbon::now()->month);
        
        if ($eaName) {
            $query->where('ea_name', $eaName);
        }
        
        return $query->sum('drawdown_amount');
    }

    /**
     * Get all drawdown events for current month with details
     */
    public static function getCurrentMonthEvents($eaName = null)
    {
        $query = self::where('status', 'closed')
            ->whereYear('start_time', Carbon::now()->year)
            ->whereMonth('start_time', Carbon::now()->month)
            ->orderBy('start_time', 'desc');
        
        if ($eaName) {
            $query->where('ea_name', $eaName);
        }
        
        return $query->get();
    }

    /**
     * Scope to filter by current month
     */
    public function scopeCurrentMonth($query)
    {
        return $query->whereYear('start_time', Carbon::now()->year)
            ->whereMonth('start_time', Carbon::now()->month);
    }

    /**
     * Calculate martingle cycle details
     */
    public function getMartingleDetailsAttribute()
    {
        return [
            'cycle_number' => $this->martingle_cycle,
            'current_lot' => $this->current_lot,
            'total_lots' => $this->total_lots,
            'trades_in_cycle' => $this->total_trades_in_cycle,
        ];
    }
}
