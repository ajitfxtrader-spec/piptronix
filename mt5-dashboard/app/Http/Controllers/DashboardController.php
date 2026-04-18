<?php

namespace App\Http\Controllers;

use App\Models\Drawdown;
use App\Models\MonthlySummary;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard
     */
    public function index(Request $request)
    {
        $eaName = $request->input('ea_name');
        
        // Get top 10 largest drawdowns (largest first)
        $top10Drawdowns = Drawdown::getTop10Largest();
        
        // Get current month total drawdown
        $currentMonthTotal = Drawdown::getCurrentMonthTotal($eaName);
        
        // Get current month events with details
        $currentMonthEvents = Drawdown::getCurrentMonthEvents($eaName);
        
        // Get monthly summary
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        $monthlySummary = null;
        
        if ($eaName) {
            $monthlySummary = MonthlySummary::where('ea_name', $eaName)
                ->where('year', $currentYear)
                ->where('month', $currentMonth)
                ->first();
        }
        
        // Calculate statistics
        $stats = [
            'current_month_total' => $currentMonthTotal,
            'current_month_events' => $currentMonthEvents->count(),
            'avg_drawdown' => $currentMonthEvents->count() > 0 
                ? $currentMonthTotal / $currentMonthEvents->count() 
                : 0,
            'max_drawdown_this_month' => $currentMonthEvents->max('drawdown_amount') ?? 0,
            'total_lots_this_month' => $currentMonthEvents->sum('total_lots'),
            'total_martingle_cycles_this_month' => $currentMonthEvents->sum('martingle_cycle'),
        ];
        
        return view('dashboard.index', compact(
            'top10Drawdowns',
            'currentMonthTotal',
            'currentMonthEvents',
            'monthlySummary',
            'stats',
            'eaName'
        ));
    }

    /**
     * API endpoint to receive drawdown data from MT5 EA
     */
    public function storeDrawdown(Request $request)
    {
        $validated = $request->validate([
            'ea_name' => 'nullable|string|max:255',
            'symbol' => 'nullable|string|max:20',
            'drawdown_amount' => 'required|numeric|min:0',
            'balance_before' => 'required|numeric',
            'balance_after' => 'required|numeric',
            'equity_low' => 'required|numeric',
            'martingle_cycle' => 'integer|min:0',
            'current_lot' => 'required|numeric|min:0',
            'total_lots' => 'required|numeric|min:0',
            'total_trades_in_cycle' => 'integer|min:0',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date',
            'status' => 'in:open,closed',
        ]);

        $drawdown = Drawdown::create($validated);

        // Update monthly summary
        $startTime = Carbon::parse($validated['start_time']);
        $summary = MonthlySummary::getOrCreate(
            $validated['ea_name'] ?? 'Unknown',
            $startTime->year,
            $startTime->month
        );
        
        if ($validated['status'] === 'closed') {
            $summary->updateWithDrawdown(
                $validated['drawdown_amount'],
                $validated['total_lots'],
                $validated['martingle_cycle']
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Drawdown recorded successfully',
            'data' => $drawdown,
        ], 201);
    }

    /**
     * API endpoint to get top 10 drawdowns
     */
    public function getTop10()
    {
        $drawdowns = Drawdown::getTop10Largest();
        
        return response()->json([
            'success' => true,
            'data' => $drawdowns,
        ]);
    }

    /**
     * API endpoint to get current month summary
     */
    public function getCurrentMonthSummary(Request $request)
    {
        $eaName = $request->input('ea_name');
        
        $total = Drawdown::getCurrentMonthTotal($eaName);
        $events = Drawdown::getCurrentMonthEvents($eaName);
        
        return response()->json([
            'success' => true,
            'data' => [
                'year' => Carbon::now()->year,
                'month' => Carbon::now()->month,
                'total_drawdown' => $total,
                'total_events' => $events->count(),
                'events' => $events,
            ],
        ]);
    }
}
