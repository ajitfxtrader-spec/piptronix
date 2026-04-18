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

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initPusher();
            document.getElementById('server-url').textContent = window.location.origin;
        });
    </script>
</body>
</html>
