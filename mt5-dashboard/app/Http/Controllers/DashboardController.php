<?php

namespace App\Http\Controllers;

use App\Models\Drawdown;
use App\Models\MonthlySummary;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with top 10 drawdowns and current month stats
     */
    public function index()
    {
        $top10Drawdowns = Drawdown::top10Largest()->get();
        
        $currentMonth = Carbon::now();
        $monthlyStats = Drawdown::currentMonth()
            ->select(
                DB::raw('SUM(drawdown_amount) as total_drawdown'),
                DB::raw('MAX(drawdown_amount) as max_single_drawdown'),
                DB::raw('SUM(martingle_cycle) as total_martingle_cycles'),
                DB::raw('SUM(total_lots) as total_lots'),
                DB::raw('COUNT(*) as total_events')
            )
            ->first();
        
        return view('dashboard', [
            'top10Drawdowns' => $top10Drawdowns,
            'monthlyStats' => $monthlyStats,
            'currentMonth' => $currentMonth->format('F Y'),
        ]);
    }

    /**
     * Store a new drawdown event from MT5 EA
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ea_name' => 'nullable|string|max:255',
            'symbol' => 'required|string|max:20',
            'event_date' => 'required|date',
            'balance' => 'required|numeric|min:0',
            'equity' => 'required|numeric|min:0',
            'drawdown_amount' => 'required|numeric|min:0',
            'drawdown_percent' => 'required|numeric|min:0|max:100',
            'martingle_cycle' => 'integer|min:0',
            'current_lot' => 'numeric|min:0',
            'total_lots' => 'numeric|min:0',
            'order_type' => 'nullable|string|max:50',
            'ticket' => 'nullable|integer',
            'extra_data' => 'nullable|array',
        ]);

        $drawdown = Drawdown::create($validated);

        // Update monthly summary
        $eventDate = Carbon::parse($validated['event_date']);
        $this->updateMonthlySummary($eventDate, $validated);

        // Broadcast real-time update
        broadcast(new \App\Events\DrawdownUpdated($drawdown))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Drawdown recorded successfully',
            'data' => $drawdown,
        ], 201);
    }

    /**
     * Get top 10 largest drawdowns (API endpoint)
     */
    public function top10()
    {
        $drawdowns = Drawdown::top10Largest()->get();
        
        return response()->json([
            'success' => true,
            'data' => $drawdowns,
        ]);
    }

    /**
     * Get current month statistics (API endpoint)
     */
    public function currentMonth()
    {
        $currentMonth = Carbon::now();
        
        $stats = Drawdown::currentMonth()
            ->select(
                DB::raw('SUM(drawdown_amount) as total_drawdown'),
                DB::raw('MAX(drawdown_amount) as max_single_drawdown'),
                DB::raw('SUM(martingle_cycle) as total_martingle_cycles'),
                DB::raw('SUM(total_lots) as total_lots'),
                DB::raw('COUNT(*) as total_events'),
                DB::raw('AVG(drawdown_percent) as avg_drawdown_percent')
            )
            ->first();
        
        return response()->json([
            'success' => true,
            'month' => $currentMonth->format('F Y'),
            'data' => $stats,
        ]);
    }

    /**
     * Get all drawdowns with pagination (API endpoint)
     */
    public function all(Request $request)
    {
        $perPage = $request->get('per_page', 50);
        
        $drawdowns = Drawdown::orderBy('event_date', 'desc')
            ->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $drawdowns,
        ]);
    }

    /**
     * Update or create monthly summary
     */
    private function updateMonthlySummary(Carbon $date, array $data)
    {
        $year = $date->year;
        $month = $date->month;
        $eaName = $data['ea_name'] ?? 'Unknown';

        $summary = MonthlySummary::forPeriod($year, $month)
            ->where('ea_name', $eaName)
            ->first();

        if (!$summary) {
            $summary = new MonthlySummary();
            $summary->ea_name = $eaName;
            $summary->year = $year;
            $summary->month = $month;
            $summary->total_drawdown = 0;
            $summary->max_drawdown = 0;
            $summary->total_martingle_cycles = 0;
            $summary->total_lots_traded = 0;
            $summary->total_trades = 0;
            $summary->daily_breakdown = [];
        }

        $summary->total_drawdown += $data['drawdown_amount'];
        $summary->max_drawdown = max($summary->max_drawdown, $data['drawdown_amount']);
        $summary->total_martingle_cycles += ($data['martingle_cycle'] ?? 0);
        $summary->total_lots_traded += ($data['total_lots'] ?? 0);
        $summary->total_trades += 1;

        // Update daily breakdown
        $dayKey = $date->format('Y-m-d');
        $dailyBreakdown = $summary->daily_breakdown ?? [];
        
        if (!isset($dailyBreakdown[$dayKey])) {
            $dailyBreakdown[$dayKey] = [
                'count' => 0,
                'total_drawdown' => 0,
                'max_drawdown' => 0,
            ];
        }
        
        $dailyBreakdown[$dayKey]['count'] += 1;
        $dailyBreakdown[$dayKey]['total_drawdown'] += $data['drawdown_amount'];
        $dailyBreakdown[$dayKey]['max_drawdown'] = max(
            $dailyBreakdown[$dayKey]['max_drawdown'], 
            $data['drawdown_amount']
        );
        
        $summary->daily_breakdown = $dailyBreakdown;
        $summary->save();
    }
}
