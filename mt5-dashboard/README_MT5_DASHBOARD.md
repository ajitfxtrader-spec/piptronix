# MT5 EA Drawdown Dashboard

A comprehensive Laravel-based dashboard for tracking MT5 EA drawdowns, martingle cycles, and trading statistics with real-time updates.

## Features

✅ **Top 10 Largest Drawdowns** - Tracks and displays the 10 largest drawdown events (sorted by amount)
✅ **Current Month Statistics** - Real-time total drawdown in dollars for the current month
✅ **Martingle Cycle Tracking** - Monitors martingle cycles, lots, and total lots
✅ **Real-time Updates** - WebSocket-powered live dashboard updates
✅ **RESTful API** - Complete API for integration with any MT5 EA
✅ **Beautiful UI** - Modern, responsive Tailwind CSS interface

## Architecture

### How Laravel Connects with MT5 EA

The connection is established via **HTTP POST requests** from MT5 to Laravel:

```
MT5 EA (MQ5) --[HTTP POST]--> Laravel API --[WebSocket]--> Browser Dashboard
```

1. **MT5 EA** monitors drawdowns and sends data via HTTP POST to Laravel
2. **Laravel** receives, validates, and stores data in SQLite/MySQL database
3. **Laravel Reverb** broadcasts real-time updates via WebSocket
4. **Browser** receives instant updates without page refresh

## Installation

### Prerequisites

- PHP 8.2+
- Composer
- Node.js & NPM (optional, for asset compilation)
- MT5 Terminal (for the EA)

### Setup Laravel Application

```bash
cd /workspace/mt5-dashboard

# Install PHP dependencies (already done)
composer install

# Run migrations
php artisan migrate

# Start Laravel development server
php artisan serve --host=0.0.0.0 --port=8000

# In another terminal, start Reverb (WebSocket server)
php artisan reverb:start
```

### Configure Environment

Edit `.env` file:

```env
APP_URL=http://localhost:8000

# WebSocket Configuration
BROADCAST_DRIVER=reverb

REVERB_APP_ID=mt5-dashboard
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST="localhost"
REVERB_PORT=6001
REVERB_SCHEME=http
```

Generate keys if needed:
```bash
php artisan key:generate
php artisan reverb:install
```

## MT5 EA Integration

### Step 1: Add URL to MT5

1. Open MT5 Terminal
2. Go to **Tools** → **Options** → **Expert Advisors**
3. Check **"Allow WebRequest for listed URL"**
4. Add your Laravel URL: `http://your-server-ip:8000`
5. Click **OK**

### Step 2: Install the EA

1. Copy `mt5-ea/MT5DrawdownTracker.mq5` to MT5 Experts folder:
   ```
   [MT5 Data Folder]/MQL5/Experts/MT5DrawdownTracker.mq5
   ```

2. Compile in MetaEditor (F7)

3. Attach to any chart

### Step 3: Configure EA Parameters

- **DashboardURL**: Your Laravel API endpoint (e.g., `http://192.168.1.100:8000/api/drawdown`)
- **EA_Name**: Identifier for your EA
- **DrawdownThreshold**: Minimum drawdown ($) to trigger reporting
- **EnableTracking**: Toggle tracking on/off

## API Endpoints

### POST `/api/drawdown`
Receive drawdown data from MT5 EA

**Request Body:**
```json
{
  "ea_name": "MyMartingleEA",
  "symbol": "EURUSD",
  "event_date": "2024-01-15 14:30:00",
  "balance": 10000.00,
  "equity": 9850.00,
  "drawdown_amount": 150.00,
  "drawdown_percent": 1.5,
  "martingle_cycle": 3,
  "current_lot": 0.32,
  "total_lots": 0.56,
  "order_type": "BUY",
  "ticket": 123456
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

### GET `/api/drawdowns/top-10`
Get top 10 largest drawdowns

### GET `/api/drawdowns/current-month`
Get current month statistics

### GET `/api/drawdowns/all`
Get all drawdowns with pagination

## Dashboard Access

Open your browser and navigate to:
```
http://localhost:8000
```

You'll see:
- 📊 Monthly summary cards (Total Drawdown, Max Single, Martingle Cycles, Total Lots)
- 🏆 Top 10 Largest Drawdowns table
- 🔌 Real-time connection status

## Database Schema

### `drawdowns` Table
- `id` - Primary key
- `ea_name` - EA identifier
- `symbol` - Trading symbol
- `event_date` - When drawdown occurred
- `balance` - Account balance at event
- `equity` - Account equity at event
- `drawdown_amount` - Drawdown in dollars
- `drawdown_percent` - Drawdown percentage
- `martingle_cycle` - Current martingle cycle count
- `current_lot` - Current position lot size
- `total_lots` - Total lots across all positions
- `order_type` - BUY/SELL
- `ticket` - Position ticket number
- `extra_data` - JSON field for additional data

### `monthly_summaries` Table
Aggregated monthly statistics for quick reporting.

## Real-time Updates

The dashboard uses **Laravel Reverb** (WebSocket server) for real-time updates:

1. When MT5 sends data → Laravel stores it
2. Laravel broadcasts event via WebSocket
3. Browser receives update instantly
4. Dashboard refreshes automatically

**Fallback:** If WebSocket fails, dashboard polls every 5 seconds.

## Testing

### Test API Endpoint

```bash
curl -X POST http://localhost:8000/api/drawdown \
  -H "Content-Type: application/json" \
  -d '{
    "ea_name": "TestEA",
    "symbol": "EURUSD",
    "event_date": "2024-01-15 14:30:00",
    "balance": 10000.00,
    "equity": 9850.00,
    "drawdown_amount": 150.00,
    "drawdown_percent": 1.5,
    "martingle_cycle": 3,
    "current_lot": 0.32,
    "total_lots": 0.56,
    "order_type": "BUY"
  }'
```

### View Dashboard

```bash
http://localhost:8000
```

## Troubleshooting

### MT5 Cannot Connect

1. Ensure Laravel server is running
2. Check firewall allows port 8000
3. Verify URL in MT5 Options matches exactly
4. Use IP address instead of `localhost` if on different machine

### No Real-time Updates

1. Ensure Reverb is running: `php artisan reverb:start`
2. Check browser console for errors
3. Verify PUSHER_APP_KEY in `.env`

### Data Not Showing

1. Check database: `php artisan tinker` → `App\Models\Drawdown::count()`
2. Verify MT5 is sending data (check Experts log)
3. Test API manually with curl

## Production Deployment

For production:

1. Use MySQL instead of SQLite
2. Configure proper WebSocket server (Reverb in production mode)
3. Set up SSL/HTTPS
4. Add authentication to dashboard
5. Configure queue workers for background jobs
6. Set up monitoring and alerts

```bash
# Production commands
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run Reverb in background
nohup php artisan reverb:start --port=6001 &
```

## Security Considerations

- Add API authentication (Sanctum/JWT)
- Implement rate limiting
- Use HTTPS in production
- Validate all incoming data
- Restrict dashboard access with login

## License

MIT License - Feel free to use and modify!

## Support

For issues or questions, check the Laravel logs:
```bash
tail -f storage/logs/laravel.log
```

---

**Built with Laravel 12 + Reverb + Tailwind CSS**
