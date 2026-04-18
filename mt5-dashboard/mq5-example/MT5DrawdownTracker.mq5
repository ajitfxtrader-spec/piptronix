//+------------------------------------------------------------------+
//|                                              MT5DrawdownTracker.mq5 |
//|                                    MT5 EA Drawdown Tracking Module |
//+------------------------------------------------------------------+
#property copyright "MT5 EA Dashboard"
#property link      ""
#property version   "1.00"

#include <WebRequest.mqh>

//--- Configuration
string g_dashboard_url = "http://your-laravel-dashboard.com/api/drawdown";
string g_ea_name = "MyMartingleEA";
int    g_magic_number = 123456;

//--- Drawdown tracking variables
double g_balance_before = 0;
double g_equity_low = 0;
double g_max_drawdown = 0;
datetime g_drawdown_start_time = 0;
int    g_martingle_cycle = 0;
double g_current_lot = 0;
double g_total_lots_in_cycle = 0;
int    g_trades_in_cycle = 0;
bool   g_tracking_active = false;

//--- Martingle settings
double g_base_lot = 0.01;
double g_martingle_multiplier = 2.0;
int    g_max_martingle_level = 5;

//+------------------------------------------------------------------+
//| Expert initialization function                                   |
//+------------------------------------------------------------------+
int OnInit()
{
    Print("MT5 Drawdown Tracker initialized");
    Print("Dashboard URL: ", g_dashboard_url);
    
    // Initialize tracking variables
    ResetDrawdownTracking();
    
    return(INIT_SUCCEEDED);
}

//+------------------------------------------------------------------+
//| Expert deinitialization function                                 |
//+------------------------------------------------------------------+
void OnDeinit(const int reason)
{
    // Send final data on shutdown if tracking active
    if(g_tracking_active && g_drawdown_start_time > 0)
    {
        CloseAndSendDrawdown();
    }
}

//+------------------------------------------------------------------+
//| Expert tick function                                             |
//+------------------------------------------------------------------+
void OnTick()
{
    double current_equity = AccountInfoDouble(ACCOUNT_EQUITY);
    double current_balance = AccountInfoDouble(ACCOUNT_BALANCE);
    
    // Check if we should start tracking a new drawdown
    if(!g_tracking_active)
    {
        // Start tracking when equity drops below balance by threshold (e.g., $10)
        if(current_balance > 0 && current_equity < current_balance - 10)
        {
            StartDrawdownTracking(current_balance, current_equity);
        }
    }
    else
    {
        // Update tracking
        UpdateDrawdownTracking(current_equity);
        
        // Check if drawdown has recovered
        if(current_equity >= g_balance_before)
        {
            CloseAndSendDrawdown();
        }
    }
    
    // Monitor martingle cycles
    MonitorMartingleCycles();
}

//+------------------------------------------------------------------+
//| Start tracking a new drawdown event                              |
//+------------------------------------------------------------------+
void StartDrawdownTracking(double balance, double equity)
{
    g_tracking_active = true;
    g_balance_before = balance;
    g_equity_low = equity;
    g_max_drawdown = balance - equity;
    g_drawdown_start_time = TimeCurrent();
    g_current_lot = 0;
    g_total_lots_in_cycle = 0;
    g_trades_in_cycle = 0;
    
    Print("Drawdown tracking started - Balance: ", DoubleToString(balance, 2), 
          ", Equity: ", DoubleToString(equity, 2));
}

//+------------------------------------------------------------------+
//| Update drawdown tracking                                         |
//+------------------------------------------------------------------+
void UpdateDrawdownTracking(double current_equity)
{
    if(current_equity < g_equity_low)
    {
        g_equity_low = current_equity;
        g_max_drawdown = g_balance_before - g_equity_low;
    }
}

//+------------------------------------------------------------------+
//| Close drawdown and send to dashboard                             |
//+------------------------------------------------------------------+
void CloseAndSendDrawdown()
{
    if(!g_tracking_active) return;
    
    double drawdown_amount = g_balance_before - g_equity_low;
    datetime end_time = TimeCurrent();
    
    // Only send if significant drawdown (> $5)
    if(drawdown_amount > 5)
    {
        SendDrawdownToDashboard(drawdown_amount, end_time);
    }
    
    ResetDrawdownTracking();
}

//+------------------------------------------------------------------+
//| Reset drawdown tracking variables                                |
//+------------------------------------------------------------------+
void ResetDrawdownTracking()
{
    g_tracking_active = false;
    g_balance_before = 0;
    g_equity_low = 0;
    g_max_drawdown = 0;
    g_drawdown_start_time = 0;
    g_martingle_cycle = 0;
    g_current_lot = 0;
    g_total_lots_in_cycle = 0;
    g_trades_in_cycle = 0;
}

//+------------------------------------------------------------------+
//| Monitor martingle cycles and lot sizes                           |
//+------------------------------------------------------------------+
void MonitorMartingleCycles()
{
    int total_positions = PositionsTotal();
    double total_lots = 0;
    int martingle_level = 0;
    
    for(int i = PositionsTotal() - 1; i >= 0; i--)
    {
        ulong ticket = PositionGetTicket(i);
        if(ticket > 0)
        {
            if(PositionGetString(POSITION_SYMBOL) == _Symbol && 
               PositionGetInteger(POSITION_MAGIC) == g_magic_number)
            {
                double lots = PositionGetDouble(POSITION_VOLUME);
                total_lots += lots;
                
                // Calculate martingle level based on lot size
                if(lots > g_base_lot)
                {
                    int level = (int)MathLog(lots / g_base_lot) / MathLog(g_martingle_multiplier);
                    if(level > martingle_level) martingle_level = level;
                }
                
                g_trades_in_cycle++;
            }
        }
    }
    
    g_current_lot = (total_lots > 0 && g_trades_in_cycle > 0) ? total_lots / g_trades_in_cycle : 0;
    g_total_lots_in_cycle = total_lots;
    g_martingle_cycle = martingle_level;
}

//+------------------------------------------------------------------+
//| Send drawdown data to Laravel dashboard                          |
//+------------------------------------------------------------------+
void SendDrawdownToDashboard(double drawdown_amount, datetime end_time)
{
    string symbol = _Symbol;
    
    // Build JSON payload
    string json_data = StringFormat(
        "{" +
        "\"ea_name\": \"%s\"," +
        "\"symbol\": \"%s\"," +
        "\"drawdown_amount\": %.2f," +
        "\"balance_before\": %.2f," +
        "\"balance_after\": %.2f," +
        "\"equity_low\": %.2f," +
        "\"martingle_cycle\": %d," +
        "\"current_lot\": %.4f," +
        "\"total_lots\": %.4f," +
        "\"total_trades_in_cycle\": %d," +
        "\"start_time\": \"%s\"," +
        "\"end_time\": \"%s\"," +
        "\"status\": \"closed\"" +
        "}",
        g_ea_name,
        symbol,
        drawdown_amount,
        g_balance_before,
        AccountInfoDouble(ACCOUNT_BALANCE),
        g_equity_low,
        g_martingle_cycle,
        g_current_lot,
        g_total_lots_in_cycle,
        g_trades_in_cycle,
        TimeToString(g_drawdown_start_time, TIME_DATE|TIME_SECONDS),
        TimeToString(end_time, TIME_DATE|TIME_SECONDS)
    );
    
    Print("Sending drawdown data: ", json_data);
    
    // Send HTTP POST request
    char post_data[];
    StringToBuffer(json_data, post_data);
    
    string response_headers;
    int response_code = WebRequest("POST", g_dashboard_url, NULL, 5000, post_data, response_headers);
    
    if(response_code == 200 || response_code == 201)
    {
        Print("Drawdown sent successfully to dashboard");
    }
    else
    {
        Print("Error sending drawdown to dashboard. Response code: ", response_code);
        Print("Make sure to add your dashboard URL to Tools > Options > Expert Advisors > Allow WebRequest");
    }
}

//+------------------------------------------------------------------+
//| Helper function to convert string to char array                  |
//+------------------------------------------------------------------+
void StringToBuffer(string str, char &buffer[])
{
    uchar chars[];
    StringToCharArray(str, chars, 0, WHOLE_ARRAY, CP_UTF8);
    ArrayResize(buffer, ArraySize(chars));
    ArrayCopy(buffer, chars);
}

//+------------------------------------------------------------------+
//| Manual trigger to send current state (call from EA logic)        |
//+------------------------------------------------------------------+
void TriggerManualSend()
{
    if(g_tracking_active && g_drawdown_start_time > 0)
    {
        CloseAndSendDrawdown();
    }
}
