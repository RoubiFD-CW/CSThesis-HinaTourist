import urllib.request
import json
import ssl
import datetime

def fetch_data(attraction):
    print(f"\n### {attraction.title()}\n")
    url = "http://127.0.0.1:8000/forecast"
    payload = {"attraction_name": attraction, "months_ahead": 12}
    data = json.dumps(payload).encode('utf-8')
    req = urllib.request.Request(url, data=data, headers={'Content-Type': 'application/json'})
    
    try:
        context = ssl._create_unverified_context()
        with urllib.request.urlopen(req, context=context) as response:
            res_data = json.loads(response.read().decode())
    except Exception as e:
        print(f"Error fetching data for {attraction}: {e}")
        return

    hist_monthly = {item['month']: item.get('visitors', item.get('visitors', 0)) for item in res_data.get('historical_monthly', [])}
    hist_test = {item['month']: item.get('visitors', 0) for item in res_data.get('historical_test', [])}
    test_pred = {item['month']: item.get('visitors', item.get('predicted', 0)) for item in res_data.get('test_predicted', [])}
    forecast = {item['month']: item.get('predicted', item.get('visitors', 0)) for item in res_data.get('forecasts', [])}

    print("| Month | Actual Visitors | Predicted Visitors | Absolute Error | Absolute Percentage Error (APE) |")
    print("|---|---|---|---|---|")
    
    all_2025_months = [f"2025-{str(i).zfill(2)}-01" for i in range(1, 13)]
    
    for month in all_2025_months:
        actual = hist_test.get(month, hist_monthly.get(month))
        predicted = test_pred.get(month, forecast.get(month))
        
        month_str = datetime.datetime.strptime(month, "%Y-%m-%d").strftime("%B %Y")
        
        if actual is not None and predicted is not None:
            abs_err = abs(actual - predicted)
            ape = (abs_err / actual) * 100 if actual != 0 else 0
            print(f"| {month_str} | {int(actual):,} | {int(predicted):,} | {int(abs_err):,} | {ape:.2f}% |")
        else:
            actual_str = f"{int(actual):,}" if actual is not None else "Missing"
            pred_str = f"{int(predicted):,}" if predicted is not None else "Missing"
            
            # If for some reason predicting missing or actual missing, let's format it properly
            # if we have actual but no pred, usually they want the dash
            print(f"| {month_str} | {actual_str} | {pred_str} | - | - |")
        
for place in ["HARIP OCEANSIDE WHITE BEACH", "BACULIN AMAZING SAND BAR", "ROCK ISLAND RESORT"]:
    fetch_data(place)
