<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MT5 EA Drawdown Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://js.pusher.com/8.2/pusher.min.js"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .animate-pulse-slow {
            animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Header -->
    <header class="gradient-bg text-white py-6 shadow-lg">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold">📊 MT5 EA Drawdown Tracker</h1>
            <p class="mt-2 text-purple-100">Real-time monitoring and analytics</p>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <!-- Tab Navigation -->
        <div class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button onclick="switchTab('drawdown')" id="tab-drawdown" class="tab-button active border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        📉 Drawdown Tracker
                    </button>
                    <button onclick="switchTab('trades')" id="tab-trades" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        💼 Trades
                    </button>
                </nav>
            </div>
        </div>

        <!-- Drawdown Tracker Tab Content -->
        <div id="content-drawdown" class="tab-content">
            <!-- Monthly Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Drawdown Card -->
            <div class="bg-white rounded-lg card-shadow p-6 border-l-4 border-red-500">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-500 text-sm font-medium">Current Month Total Drawdown</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1" id="total-drawdown">
                            ${{ number_format($monthlyStats->total_drawdown ?? 0, 2) }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">{{ $currentMonth }}</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Max Single Drawdown Card -->
            <div class="bg-white rounded-lg card-shadow p-6 border-l-4 border-orange-500">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-500 text-sm font-medium">Max Single Drawdown</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1" id="max-drawdown">
                            ${{ number_format($monthlyStats->max_single_drawdown ?? 0, 2) }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">Largest event this month</p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Martingle Cycles Card -->
            <div class="bg-white rounded-lg card-shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-500 text-sm font-medium">Total Martingle Cycles</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1" id="martingle-cycles">
                            {{ number_format($monthlyStats->total_martingle_cycles ?? 0) }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">Cycles this month</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Lots Card -->
            <div class="bg-white rounded-lg card-shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-500 text-sm font-medium">Total Lots Traded</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1" id="total-lots">
                            {{ number_format($monthlyStats->total_lots ?? 0, 4) }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">Lots this month</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top 10 Largest Drawdowns Table -->
        <div class="bg-white rounded-lg card-shadow overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">🏆 Top 10 Largest Drawdowns</h2>
                <p class="text-sm text-gray-500 mt-1">All-time largest drawdown events (sorted by amount)</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Symbol</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Drawdown ($)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Drawdown (%)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Martingle Cycle</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Lot</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Lots</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equity</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="top10-table-body">
                        @forelse($top10Drawdowns as $index => $drawdown)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-600 font-bold text-sm">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $drawdown->event_date->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $drawdown->symbol }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-red-600">
                                ${{ number_format($drawdown->drawdown_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-500">
                                {{ number_format($drawdown->drawdown_percent, 4) }}%
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $drawdown->martingle_cycle }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($drawdown->current_lot, 4) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($drawdown->total_lots, 4) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($drawdown->balance, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($drawdown->equity, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <p class="mt-2 text-sm">No drawdown events recorded yet</p>
                                <p class="text-xs text-gray-400 mt-1">Data will appear here when your MT5 EA sends events</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Connection Info -->
        <div class="bg-white rounded-lg card-shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">🔌 MT5 EA Connection Status</h3>
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse-slow" id="connection-status"></div>
                    <span class="ml-2 text-sm text-gray-600" id="connection-text">Connecting...</span>
                </div>
            </div>
            <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600"><strong>API Endpoint:</strong> <code class="bg-gray-200 px-2 py-1 rounded">POST /api/drawdown</code></p>
                <p class="text-sm text-gray-600 mt-2"><strong>WebSocket Channel:</strong> <code class="bg-gray-200 px-2 py-1 rounded">drawdowns</code></p>
                <p class="text-sm text-gray-600 mt-2"><strong>Server URL:</strong> <code class="bg-gray-200 px-2 py-1 rounded" id="server-url">{{ url('/') }}</code></p>
            </div>
        </div>
        </div>

        <!-- Trades Tab Content -->
        <div id="content-trades" class="tab-content hidden">
            <!-- Sub-tabs for Trades -->
            <div class="mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Sub-tabs">
                        <button onclick="switchSubTab('live')" id="subtab-live" class="subtab-button active border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            🔴 Live Trades
                        </button>
                        <button onclick="switchSubTab('history')" id="subtab-history" class="subtab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            📜 Trade History
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Live Trades Content -->
            <div id="subcontent-live" class="subtab-content">
                <div class="bg-white rounded-lg card-shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Currently Open Positions</h2>
                            <p class="text-sm text-gray-500 mt-1">Real-time live trades from MT5</p>
                        </div>
                        <button onclick="loadLiveTrades()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            🔄 Refresh
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Symbol</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lot Size</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Open Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profit/Loss</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Open Time</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="live-trades-body">
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto" id="live-loading"></div>
                                        <p class="mt-4 text-sm" id="live-loading-text">Loading live trades...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Trade History Content -->
            <div id="subcontent-history" class="subtab-content hidden">
                <div class="bg-white rounded-lg card-shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                            <div>
                                <h2 class="text-xl font-bold text-gray-800">Closed Trade History</h2>
                                <p class="text-sm text-gray-500 mt-1">Historical trades with date filtering</p>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">From Date</label>
                                    <input type="date" id="filter-from-date" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">To Date</label>
                                    <input type="date" id="filter-to-date" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <button onclick="loadTradeHistory()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors mt-5">
                                    🔍 Filter
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Symbol</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lot Size</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Open Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Close Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profit/Loss</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Open Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Close Time</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="history-trades-body">
                                <tr>
                                    <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto" id="history-loading"></div>
                                        <p class="mt-4 text-sm" id="history-loading-text">Loading trade history...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-6 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p class="text-sm text-gray-400">MT5 EA Drawdown Tracker &copy; {{ date('Y') }} | Real-time Analytics Dashboard</p>
        </div>
    </footer>

    <script>
        // Configuration
        const PUSHER_KEY = '{{ env("PUSHER_APP_KEY") }}';
        const PUSHER_CLUSTER = 'mt5';
        const API_URL = '{{ url("/api") }}';

        // Initialize Pusher for real-time updates
        let pusher;
        let channel;

        function initPusher() {
            if (!PUSHER_KEY || PUSHER_KEY === '') {
                console.log('Pusher not configured, using polling fallback');
                startPolling();
                return;
            }

            try {
                pusher = new Pusher(PUSHER_KEY, {
                    cluster: PUSHER_CLUSTER,
                    wsHost: window.location.hostname,
                    wsPort: 6001,
                    forceTLS: false,
                    disableStats: true,
                    enabledTransports: ['ws', 'wss'],
                });

                channel = pusher.subscribe('drawdowns');

                channel.bind('updated', function(data) {
                    console.log('Real-time update received:', data);
                    updateDashboard(data);
                    showNotification('New drawdown event!', data);
                });

                updateConnectionStatus(true);
            } catch (error) {
                console.error('Pusher connection error:', error);
                updateConnectionStatus(false);
                startPolling();
            }
        }

        function updateConnectionStatus(connected) {
            const statusEl = document.getElementById('connection-status');
            const textEl = document.getElementById('connection-text');
            
            if (connected) {
                statusEl.classList.remove('bg-red-500', 'bg-yellow-500');
                statusEl.classList.add('bg-green-500');
                textEl.textContent = 'Connected (Real-time)';
            } else {
                statusEl.classList.remove('bg-green-500', 'bg-yellow-500');
                statusEl.classList.add('bg-red-500');
                textEl.textContent = 'Disconnected (Using polling)';
            }
        }

        function startPolling() {
            console.log('Starting polling fallback (every 5 seconds)');
            setInterval(fetchLatestData, 5000);
        }

        function fetchLatestData() {
            fetch(API_URL + '/drawdowns/top-10')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateTop10Table(data.data);
                    }
                })
                .catch(error => console.error('Polling error:', error));

            fetch(API_URL + '/drawdowns/current-month')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateMonthlyStats(data.data);
                    }
                })
                .catch(error => console.error('Polling error:', error));
        }

        function updateDashboard(data) {
            // Update top 10 table
            fetchLatestData();
        }

        function updateTop10Table(drawdowns) {
            const tbody = document.getElementById('top10-table-body');
            if (!drawdowns || drawdowns.length === 0) return;

            let html = '';
            drawdowns.forEach((drawdown, index) => {
                const eventDate = new Date(drawdown.event_date);
                const formattedDate = eventDate.toISOString().replace('T', ' ').substring(0, 19);
                
                html += `
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-600 font-bold text-sm">
                                ${index + 1}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${formattedDate}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                ${drawdown.symbol}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-red-600">
                            $${parseFloat(drawdown.drawdown_amount).toFixed(2)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-500">
                            ${parseFloat(drawdown.drawdown_percent).toFixed(4)}%
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${drawdown.martingle_cycle}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${parseFloat(drawdown.current_lot).toFixed(4)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${parseFloat(drawdown.total_lots).toFixed(4)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            $${parseFloat(drawdown.balance).toFixed(2)}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            $${parseFloat(drawdown.equity).toFixed(2)}
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
        }

        function updateMonthlyStats(stats) {
            if (!stats) return;

            document.getElementById('total-drawdown').textContent = 
                '$' + parseFloat(stats.total_drawdown || 0).toFixed(2);
            document.getElementById('max-drawdown').textContent = 
                '$' + parseFloat(stats.max_single_drawdown || 0).toFixed(2);
            document.getElementById('martingle-cycles').textContent = 
                parseInt(stats.total_martingle_cycles || 0).toLocaleString();
            document.getElementById('total-lots').textContent = 
                parseFloat(stats.total_lots || 0).toFixed(4);
        }

        function showNotification(title, data) {
            // Simple browser notification
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification(title, {
                    body: `Symbol: ${data.symbol}, Amount: $${data.drawdown_amount}`,
                    icon: '/favicon.ico'
                });
            }
        }

        // Request notification permission
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }

        // Tab switching functions
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active styles from all tab buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('border-indigo-500', 'text-indigo-600');
                button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            });
            
            // Show selected tab content
            document.getElementById(`content-${tabName}`).classList.remove('hidden');
            
            // Add active styles to selected tab button
            const activeButton = document.getElementById(`tab-${tabName}`);
            activeButton.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            activeButton.classList.add('border-indigo-500', 'text-indigo-600');
            
            // Load trades data when switching to trades tab
            if (tabName === 'trades') {
                loadLiveTrades();
            }
        }

        // Sub-tab switching functions for Trades
        function switchSubTab(subTabName) {
            // Hide all sub-tab contents
            document.querySelectorAll('.subtab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active styles from all sub-tab buttons
            document.querySelectorAll('.subtab-button').forEach(button => {
                button.classList.remove('border-indigo-500', 'text-indigo-600');
                button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            });
            
            // Show selected sub-tab content
            document.getElementById(`subcontent-${subTabName}`).classList.remove('hidden');
            
            // Add active styles to selected sub-tab button
            const activeButton = document.getElementById(`subtab-${subTabName}`);
            activeButton.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            activeButton.classList.add('border-indigo-500', 'text-indigo-600');
            
            // Load trade history when switching to history sub-tab
            if (subTabName === 'history') {
                loadTradeHistory();
            }
        }

        // Load live trades
        function loadLiveTrades() {
            const tbody = document.getElementById('live-trades-body');
            const loading = document.getElementById('live-loading');
            const loadingText = document.getElementById('live-loading-text');
            
            loading.classList.remove('hidden');
            loadingText.textContent = 'Loading live trades...';
            
            fetch(API_URL + '/trades/live')
                .then(response => response.json())
                .then(data => {
                    loading.classList.add('hidden');
                    
                    if (!data.success || !data.data || data.data.length === 0) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    <p class="mt-2 text-sm">No live trades currently open</p>
                                </td>
                            </tr>
                        `;
                        return;
                    }
                    
                    let html = '';
                    data.data.forEach(trade => {
                        const profitClass = trade.profit >= 0 ? 'text-green-600' : 'text-red-600';
                        const typeClass = trade.type === 'BUY' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                        const openTime = new Date(trade.open_time).toISOString().replace('T', ' ').substring(0, 19);
                        
                        html += `
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#${trade.ticket}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        ${trade.symbol}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full ${typeClass}">
                                        ${trade.type}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${parseFloat(trade.lot_size).toFixed(2)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${parseFloat(trade.open_price).toFixed(5)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${parseFloat(trade.current_price).toFixed(5)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold ${profitClass}">$${parseFloat(trade.profit).toFixed(2)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${openTime}</td>
                            </tr>
                        `;
                    });
                    
                    tbody.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error loading live trades:', error);
                    loading.classList.add('hidden');
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="mt-2 text-sm text-red-600">Error loading live trades</p>
                                <p class="text-xs text-gray-400 mt-1">${error.message}</p>
                            </td>
                        </tr>
                    `;
                });
        }

        // Load trade history with date filter
        function loadTradeHistory() {
            const fromDate = document.getElementById('filter-from-date').value;
            const toDate = document.getElementById('filter-to-date').value;
            const tbody = document.getElementById('history-trades-body');
            const loading = document.getElementById('history-loading');
            const loadingText = document.getElementById('history-loading-text');
            
            loading.classList.remove('hidden');
            loadingText.textContent = 'Loading trade history...';
            
            // Build URL with query parameters
            let url = API_URL + '/trades/history';
            const params = new URLSearchParams();
            if (fromDate) params.append('from', fromDate);
            if (toDate) params.append('to', toDate);
            if (params.toString()) url += '?' + params.toString();
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    loading.classList.add('hidden');
                    
                    if (!data.success || !data.data || data.data.length === 0) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    <p class="mt-2 text-sm">No trade history found for the selected period</p>
                                </td>
                            </tr>
                        `;
                        return;
                    }
                    
                    let html = '';
                    data.data.forEach(trade => {
                        const profitClass = trade.profit >= 0 ? 'text-green-600' : 'text-red-600';
                        const typeClass = trade.type === 'BUY' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                        const openTime = new Date(trade.open_time).toISOString().replace('T', ' ').substring(0, 19);
                        const closeTime = new Date(trade.close_time).toISOString().replace('T', ' ').substring(0, 19);
                        
                        html += `
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#${trade.ticket}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        ${trade.symbol}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full ${typeClass}">
                                        ${trade.type}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${parseFloat(trade.lot_size).toFixed(2)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${parseFloat(trade.open_price).toFixed(5)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${parseFloat(trade.close_price).toFixed(5)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold ${profitClass}">$${parseFloat(trade.profit).toFixed(2)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${openTime}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${closeTime}</td>
                            </tr>
                        `;
                    });
                    
                    tbody.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error loading trade history:', error);
                    loading.classList.add('hidden');
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="mt-2 text-sm text-red-600">Error loading trade history</p>
                                <p class="text-xs text-gray-400 mt-1">${error.message}</p>
                            </td>
                        </tr>
                    `;
                });
        }

        // Set default date range (last 30 days) on page load
        function setDefaultDateRange() {
            const today = new Date();
            const thirtyDaysAgo = new Date(today);
            thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
            
            document.getElementById('filter-to-date').value = today.toISOString().split('T')[0];
            document.getElementById('filter-from-date').value = thirtyDaysAgo.toISOString().split('T')[0];
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initPusher();
            document.getElementById('server-url').textContent = window.location.origin;
            setDefaultDateRange();
        });
    </script>
</body>
</html>
