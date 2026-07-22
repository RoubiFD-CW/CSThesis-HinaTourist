import sys
import pandas as pd
import numpy as np
from statsmodels.tsa.arima.model import ARIMA
import warnings

warnings.filterwarnings('ignore')

sys.path.append(r"C:\Users\Ruby\OneDrive\Desktop\Forecast\sarimaforecastingATO")
from mainone import find_best_sarima_order, interpolate_zeros, cap_outliers, calculate_mape

df = pd.read_csv(r"C:\Users\Ruby\OneDrive\Desktop\Forecast\sarimaforecastingATO\processed_data.csv")
df['Date'] = pd.to_datetime(df['Date'])

attractions = [
    "ENCHANTED RIVER",
    "LODESTONE SHORES RESORT",
    "BACULIN AMAZING SAND BAR",
    "DAVINCE HIDDEN PARADISE",
    "HARIP OCEANSIDE WHITE BEACH",
    "ROCK ISLAND RESORT",
    "AMPARITAS INTEGRATED NATURE FARM",
    "SIBADAN FISH CAGE AND RESORT"
]

for attraction in attractions:
    print(f"\n### {attraction.title()}\n")
    attr_df = df[df['Attraction'] == attraction].sort_values('Date')
    attr_df.set_index('Date', inplace=True)
    ts_raw = attr_df['Visitors'].resample('MS').mean().fillna(0)
    
    ts_working = ts_raw.copy()
    zero_count = int((ts_working == 0).sum())
    if zero_count > 0:
        ts_working = interpolate_zeros(ts_working)
    ts_working = cap_outliers(ts_working)
    
    split_idx = int(len(ts_working) * 0.8)
    train_ts = ts_working[:split_idx]
    test_ts = ts_working[split_idx:]
    
    train_ts_log = np.log1p(train_ts)
    best_order, best_seasonal = find_best_sarima_order(train_ts_log)
    
    model = ARIMA(
        train_ts_log,
        order=best_order,
        seasonal_order=best_seasonal,
        enforce_stationarity=False,
        enforce_invertibility=False
    )
    res = model.fit()
    
    # In-sample predictions
    train_fitted_log = res.fittedvalues
    train_fitted = np.expm1(train_fitted_log)
    
    # Out-of-sample predictions
    forecast_log = res.get_forecast(steps=len(test_ts))
    test_fitted = np.expm1(forecast_log.predicted_mean)
    
    # Test MAPE
    test_mape_final = calculate_mape(test_ts, test_fitted)
    
    print("| Month | Actual Visitors | Predicted Visitors | Absolute Error | Absolute Percentage Error (APE) |")
    print("|---|---|---|---|---|")
    
    # We want exactly the last 12 months (e.g., Jan-Dec 2025)
    last_12_months = ts_working.index[-12:]
    
    for date_val in last_12_months:
        if date_val in train_ts.index:
            actual = train_ts[date_val]
            pred = train_fitted[date_val]
        else:
            actual = test_ts[date_val]
            pred = test_fitted[date_val]
            
        actual_int = int(round(actual))
        pred_int = int(round(pred))
        abs_err = abs(actual_int - pred_int)
        ape = (abs_err / actual_int * 100) if actual_int != 0 else 0
        
        month_str = date_val.strftime("%B")
        
        print(f"| {month_str} | {actual_int:,} | {pred_int:,} | {abs_err:,} | {ape:.2f}% |")
        
    print(f"| **Overall Model Benchmark** | — | — | — | **{test_mape_final:.2f}% MAPE** |")

