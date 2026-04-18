//+------------------------------------------------------------------+
//|                                           MT5DrawdownTracker.mq5 |
//|                                  MT5 EA Drawdown Tracking Module |
//|                                                                  |
//| This EA module tracks drawdowns and sends data to Laravel        |
//| dashboard via HTTP POST requests.                                |
//+------------------------------------------------------------------+
#property copyright "MT5 Dashboard"
#property version   "1.00"
#property description "Tracks drawdowns and sends to Laravel dashboard"

#include <WinInet.mqh>

//--- Input parameters for connection
input string   DashboardURL       = "http://localhost:8000/api/drawdown";  // Laravel API endpoint
input string   EA_Name            = "MyMartingleEA";                       // EA identifier
input int      DrawdownThreshold  = 10;                                    // Minimum drawdown $ to report
input bool     EnableTracking     = true;                                  // Enable/disable tracking

//--- Global variables
double         g_maxBalance        = 0;
double         g_currentBalance    = 0;
int            g_martingleCycle    = 0;
double         g_totalLots         = 0;
datetime       g_lastReportTime    = 0;
bool           g_initialized       = false;

//--- WinInet constants
#define INTERNET_FLAG_NO_CACHE_WRITE  0x04000000
#define INTERNET_OPEN_TYPE_PRECONFIG  0
#define INTERNET_SERVICE_HTTP         3
#define HTTP_VERB_POST                "POST"
#define HTTP_CONTENT_TYPE             "Content-Type: application/json"

//+------------------------------------------------------------------+
//| Expert initialization function                                   |
//+------------------------------------------------------------------+
int OnInit()
{
    Print("MT5 Drawdown Tracker initializing...");
    Print("Dashboard URL: ", DashboardURL);
    
    // Initialize balance tracking
    g_maxBalance = AccountInfoDouble(ACCOUNT_BALANCE);
    g_currentBalance = g_maxBalance;
    g_initialized = true;
    
    Print("MT5 Drawdown Tracker initialized successfully!");
    return(INIT_SUCCEEDED);
}

//+------------------------------------------------------------------+
//| Expert deinitialization function                                 |
//+------------------------------------------------------------------+
void OnDeinit(const int reason)
{
    Print("MT5 Drawdown Tracker stopped. Reason: ", reason);
}

//+------------------------------------------------------------------+
//| Expert tick function                                             |
//+------------------------------------------------------------------+
void OnTick()
{
    if(!EnableTracking) return;
    
    // Update current balance
    double currentBalance = AccountInfoDouble(ACCOUNT_BALANCE);
    double currentEquity = AccountInfoDouble(ACCOUNT_EQUITY);
    
    // Track max balance
    if(currentBalance > g_maxBalance)
    {
        g_maxBalance = currentBalance;
    }
    
    // Calculate drawdown
    double drawdownAmount = g_maxBalance - currentEquity;
    double drawdownPercent = (g_maxBalance > 0) ? (drawdownAmount / g_maxBalance * 100) : 0;
    
    // Calculate total lots from open positions
    double totalLots = CalculateTotalLots();
    
    // Detect martingle cycle (based on position count or lot size progression)
    int martingleCycle = DetectMartingleCycle();
    
    // Report if drawdown exceeds threshold and not reported recently
    if(drawdownAmount >= DrawdownThreshold && (TimeCurrent() - g_lastReportTime) > 60)
    {
        SendDrawdownData(
            _Symbol,
            currentBalance,
            currentEquity,
            drawdownAmount,
            drawdownPercent,
            martingleCycle,
            PositionGetDouble(POSITION_VOLUME),
            totalLots,
            "BUY/SELL"
        );
        
        g_lastReportTime = TimeCurrent();
    }
}

//+------------------------------------------------------------------+
//| Calculate total lots from all open positions                     |
//+------------------------------------------------------------------+
double CalculateTotalLots()
{
    double total = 0;
    int positionsCount = PositionsTotal();
    
    for(int i = 0; i < positionsCount; i++)
    {
        ulong ticket = PositionGetTicket(i);
        if(ticket > 0)
        {
            total += PositionGetDouble(POSITION_VOLUME);
        }
    }
    
    g_totalLots = total;
    return total;
}

//+------------------------------------------------------------------+
//| Detect martingle cycle based on position patterns                |
//+------------------------------------------------------------------+
int DetectMartingleCycle()
{
    int buyPositions = 0;
    int sellPositions = 0;
    double totalBuyLots = 0;
    double totalSellLots = 0;
    
    int positionsCount = PositionsTotal();
    
    for(int i = 0; i < positionsCount; i++)
    {
        if(PositionSelectByTicket(PositionGetTicket(i)))
        {
            double volume = PositionGetDouble(POSITION_VOLUME);
            ENUM_POSITION_TYPE type = (ENUM_POSITION_TYPE)PositionGetInteger(POSITION_TYPE);
            
            if(type == POSITION_TYPE_BUY)
            {
                buyPositions++;
                totalBuyLots += volume;
            }
            else if(type == POSITION_TYPE_SELL)
            {
                sellPositions++;
                totalSellLots += volume;
            }
        }
    }
    
    // Martingle cycle detection: multiple positions in same direction
    int maxPositions = MathMax(buyPositions, sellPositions);
    
    if(maxPositions > 1)
    {
        g_martingleCycle = maxPositions;
    }
    else
    {
        g_martingleCycle = 0;
    }
    
    return g_martingleCycle;
}

//+------------------------------------------------------------------+
//| Send drawdown data to Laravel dashboard                          |
//+------------------------------------------------------------------+
bool SendDrawdownData(
    string symbol,
    double balance,
    double equity,
    double drawdownAmount,
    double drawdownPercent,
    int martingleCycle,
    double currentLot,
    double totalLots,
    string orderType
)
{
    // Build JSON payload
    string jsonPayload = StringFormat(
        "{"
        "\"ea_name\":\"%s\","
        "\"symbol\":\"%s\","
        "\"event_date\":\"%s\","
        "\"balance\":%.2f,"
        "\"equity\":%.2f,"
        "\"drawdown_amount\":%.2f,"
        "\"drawdown_percent\":%.4f,"
        "\"martingle_cycle\":%d,"
        "\"current_lot\":%.4f,"
        "\"total_lots\":%.4f,"
        "\"order_type\":\"%s\","
        "\"ticket\":%d"
        "}",
        EA_Name,
        symbol,
        TimeToString(TimeCurrent(), TIME_DATE|TIME_SECONDS),
        balance,
        equity,
        drawdownAmount,
        drawdownPercent,
        martingleCycle,
        currentLot,
        totalLots,
        orderType,
        (int)PositionGetInteger(POSITION_TICKET)
    );
    
    Print("Sending drawdown data: ", jsonPayload);
    
    // Parse URL
    string host, path;
    if(!ParseURL(DashboardURL, host, path))
    {
        Print("Error: Invalid dashboard URL");
        return false;
    }
    
    // Create HTTP request using WinInet
    bool result = SendHTTPRequest(host, path, jsonPayload);
    
    if(result)
    {
        Print("✓ Drawdown data sent successfully!");
    }
    else
    {
        Print("✗ Failed to send drawdown data");
    }
    
    return result;
}

//+------------------------------------------------------------------+
//| Parse URL into host and path                                     |
//+------------------------------------------------------------------+
bool ParseURL(string url, string &host, string &path)
{
    // Remove protocol
    string cleanUrl = StringReplace(url, "http://", "");
    cleanUrl = StringReplace(cleanUrl, "https://", "");
    
    // Split host and path
    int slashPos = StringFind(cleanUrl, "/");
    
    if(slashPos == -1)
    {
        host = cleanUrl;
        path = "/";
    }
    else
    {
        host = StringSubstr(cleanUrl, 0, slashPos);
        path = StringSubstr(cleanUrl, slashPos);
    }
    
    // Remove port from host if present
    int colonPos = StringFind(host, ":");
    if(colonPos != -1)
    {
        host = StringSubstr(host, 0, colonPos);
    }
    
    return true;
}

//+------------------------------------------------------------------+
//| Send HTTP POST request                                           |
//+------------------------------------------------------------------+
bool SendHTTPRequest(string host, string path, string data)
{
    // Note: For production use, consider using more robust HTTP library
    // This is a simplified example
    
    char postData[];
    uchar utf8Data[];
    StringToBuffer(data, utf8Data);
    ArrayCopy(postData, utf8Data);
    
    string headers = "Content-Type: application/json\r\n";
    string additionalHeaders = "Accept: application/json\r\n";
    
    int timeout = 5000;
    string responseHeaders, responseData;
    
    int resultCode = WebRequest(
        "POST", 
        DashboardURL,
        NULL,
        NULL,
        timeout,
        postData,
        responseData,
        responseHeaders,
        additionalHeaders
    );
    
    if(resultCode == 200 || resultCode == 201)
    {
        Print("Server response: ", responseData);
        return true;
    }
    else
    {
        Print("HTTP Error code: ", GetLastError());
        return false;
    }
}

//+------------------------------------------------------------------+
//| Helper function to convert string to byte array                  |
//+------------------------------------------------------------------+
void StringToBuffer(string str, uchar &buffer[])
{
    uchar temp[];
    StringToShortArray(str, temp);
    ArrayResize(buffer, ArraySize(temp));
    ArrayCopy(buffer, temp);
}

//+------------------------------------------------------------------+
//| Event handler for position changes                               |
//+------------------------------------------------------------------+
void OnTrade()
{
    // Recalculate totals when positions change
    CalculateTotalLots();
    DetectMartingleCycle();
}
//+------------------------------------------------------------------+
