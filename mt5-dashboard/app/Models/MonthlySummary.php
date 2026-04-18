<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlySummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'ea_name',
        'year',
        'month',
        'total_drawdown',
        'max_drawdown',
        'total_drawdown_events',
        'total_lots_traded',
        'total_martingle_cycles',
    ];

    protected $casts = [
        'total_drawdown' => 'decimal:2',
        'max_drawdown' => 'decimal:2',
        'total_lots_traded' => 'decimal:4',
    ];

    /**
     * Get or create monthly summary for EA
     */
    public static function getOrCreate($eaName, $year, $month)
    {
        return self::firstOrCreate(
            [
                'ea_name' => $eaName,
                'year' => $year,
                'month' => $month,
            ],
            [
                'total_drawdown' => 0,
                'max_drawdown' => 0,
                'total_drawdown_events' => 0,
                'total_lots_traded' => 0,
                'total_martingle_cycles' => 0,
            ]
        );
    }

    /**
     * Update summary with new drawdown data
     */
    public function updateWithDrawdown($drawdownAmount, $lots, $martingleCycle)
    {
        $this->increment('total_drawdown_events');
        $this->total_drawdown += $drawdownAmount;
        
        if ($drawdownAmount > $this->max_drawdown) {
            $this->max_drawdown = $drawdownAmount;
        }
        
        $this->total_lots_traded += $lots;
        
        if ($martingleCycle > 0) {
            $this->total_martingle_cycles += $martingleCycle;
        }
        
        $this->save();
    }
}
