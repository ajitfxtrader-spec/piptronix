# MT5 EA Dashboard - Drawdown Tracker

A Laravel-based dashboard to track drawdowns from MT5 Expert Advisors (EAs) with detailed martingle cycle tracking.

## Features

- **Top 10 Largest Drawdowns**: Displays the 10 largest drawdown events, sorted by amount (largest first)
- **Current Month Tracking**: Shows total drawdown in dollars for the current month
- **Martingle Cycle Tracking**: Tracks martingle levels, cycles, and lot sizes
- **Detailed Event Logs**: View all drawdown events with date, symbol, balance, equity, and lot information
- **RESTful API**: Easy integration with MT5 EAs via HTTP POST requests

## Project Structure

```
mt5-dashboard/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── DashboardController.php    # Main controller
│   └── Models/
│       ├── Drawdown.php                   # Drawdown model
│       └── MonthlySummary.php             # Monthly summary model
├── database/
│   └── migrations/
│       ├── 2024_01_01_000001_create_drawdowns_table.php
│       └── 2024_01_01_000002_create_monthly_summaries_table.php
├── resources/
│   └── views/
│       └── dashboard/
│           └── index.blade.php            # Dashboard view
├── routes/
│   └── web.php                            # Route definitions
└── mq5-example/
    └── MT5DrawdownTracker.mq5             # MT5 EA integration example
```

## Installation

### Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL/PostgreSQL
- Node.js & NPM (optional, for asset compilation)

### Steps

1. **Install Dependencies**
   ```bash
   cd mt5-dashboard
   composer install
   ```

2. **Configure Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Configuration**
   
   Update `.env` file with your database credentials:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=mt5_dashboard
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

4. **Run Migrations**
   ```bash
   php artisan migrate
   ```

5. **Start Development Server**
   ```bash
   php artisan serve
   ```

6. **Access Dashboard**
   
   Open your browser and navigate to: `http://localhost:8000`

## API Endpoints

### 1. Store Drawdown Data (POST)
**Endpoint:** `/api/drawdown`

**Purpose:** Receive drawdown data from MT5 EA

**Request Body:**
```json
{
  "ea_name": "MyMartingleEA",
  "symbol": "EURUSD",
  "drawdown_amount": 150.50,
  "balance_before": 10000.00,
  "balance_after": 9849.50,
  "equity_low": 9849.50,
  "martingle_cycle": 3,
  "current_lot": 0.08,
  "total_lots": 0.15,
  "total_trades_in_cycle": 5,
  "start_time": "2024-01-15 10:30:00",
  "end_time": "2024-01-15 11:45:00",
  "status": "closed"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Drawdown recorded successfully",
  "data": { ... }
}
```

### 2. Get Top 10 Drawdowns (GET)
**Endpoint:** `/api/drawdowns/top-10`

**Response:** Array of top 10 largest drawdowns

### 3. Get Current Month Summary (GET)
**Endpoint:** `/api/drawdowns/current-month?ea_name=MyMartingleEA`

**Response:**
```json
{
  "success": true,
  "data": {
    "year": 2024,
    "month": 1,
    "total_drawdown": 450.75,
    "total_events": 5,
    "events": [ ... ]
  }
}
```

## MT5 EA Integration

### Setup in MetaTrader 5

1. **Copy the MQ5 File**
   - Copy `MT5DrawdownTracker.mq5` to your MetaTrader 5 Experts folder
   - Path: `MQL5/Experts/`

2. **Configure WebRequest**
   - In MT5, go to: `Tools` > `Options` > `Expert Advisors`
   - Check "Allow WebRequest for listed URL"
   - Add your dashboard URL: `http://your-laravel-dashboard.com`

3. **Update Configuration**
   ```mql5
   string g_dashboard_url = "http://your-laravel-dashboard.com/api/drawdown";
   string g_ea_name = "YourEAName";
   int    g_magic_number = 123456;
   ```

4. **Include in Your EA**
   ```mql5
   #include <MT5DrawdownTracker.mq5>
   ```

5. **Call Functions**
   - The tracker automatically monitors drawdowns on each tick
   - Call `TriggerManualSend()` if you need manual control

### Tracked Metrics

- **Drawdown Amount**: Maximum equity drop in dollars
- **Date & Time**: When the drawdown started and ended
- **Martingle Cycle**: Current martingle level
- **Lots**: Current lot size and total lots in cycle
- **Trades in Cycle**: Number of trades in the martingle cycle
- **Balance & Equity**: Before, after, and lowest equity points

## Dashboard Features

### Statistics Cards
- Current Month Total Drawdown ($)
- Maximum Drawdown This Month ($)
- Total Lots Traded This Month
- Total Martingle Cycles This Month

### Top 10 Table
Shows the 10 largest drawdowns with:
- Rank (with medal colors for top 3)
- Date and time
- Symbol
- Drawdown amount
- Martingle cycle number
- Current and total lots
- Trades in cycle

### Current Month Events
Detailed table of all drawdown events in the current month with complete metrics.

## Database Schema

### drawdowns Table
- `id`: Primary key
- `ea_name`: Name of the EA
- `symbol`: Trading symbol
- `drawdown_amount`: Drawdown in dollars
- `balance_before`: Balance before drawdown
- `balance_after`: Balance after drawdown
- `equity_low`: Lowest equity point
- `martingle_cycle`: Martingle level
- `current_lot`: Current lot size
- `total_lots`: Total lots in cycle
- `total_trades_in_cycle`: Number of trades
- `start_time`: When drawdown started
- `end_time`: When drawdown ended
- `status`: open/closed

### monthly_summaries Table
Aggregated monthly statistics for quick reporting.

## Customization

### Adjust Drawdown Threshold
In `MT5DrawdownTracker.mq5`:
```mql5
// Minimum drawdown to track (default: $10)
if(current_equity < current_balance - 10)

// Minimum drawdown to send (default: $5)
if(drawdown_amount > 5)
```

### Change Martingle Settings
```mql5
double g_base_lot = 0.01;              // Starting lot
double g_martingle_multiplier = 2.0;   // Multiplier per level
int    g_max_martingle_level = 5;      // Maximum levels
```

## Security Considerations

1. **API Authentication**: Add API token authentication for production
2. **HTTPS**: Use HTTPS in production environments
3. **Input Validation**: All inputs are validated server-side
4. **Rate Limiting**: Consider adding rate limiting for API endpoints

## Troubleshooting

### MT5 WebRequest Errors
- Ensure URL is added to allowed WebRequest URLs in MT5
- Check firewall settings
- Verify dashboard is accessible from MT5 machine

### No Data Showing
- Check if MT5 is sending data (check MT5 Experts log)
- Verify database connection
- Check Laravel logs: `storage/logs/laravel.log`

### Drawdown Not Tracking
- Adjust threshold values in MQ5 file
- Ensure EA is running on a chart
- Check that magic number matches your EA

## License

MIT License - Feel free to use and modify for your trading needs.

## Support

For issues or questions, please check the documentation or review the code comments.
