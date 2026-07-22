import os
import sys
import pandas as pd
import numpy as np
from statsmodels.tsa.arima.model import ARIMA

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
SARIMA_DIR = os.path.join(BASE_DIR, "sarimaforecasting")
sys.path.append(SARIMA_DIR)
from mainone import find_best_sarima_order, interpolate_zeros, cap_outliers

df = pd.read_csv(os.path.join(SARIMA_DIR, "processed_data.csv"))
df['Date'] = pd.to_datetime(df['Date'])

attractions = ["HARIP OCEANSIDE WHITE BEACH", "BACULIN AMAZING SAND BAR", "ROCK ISLAND RESORT"]

for attraction in attractions:
    print(f"\n--- {attraction} ---")
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
    fitted_log = res.fittedvalues
    fitted = np.expm1(fitted_log)
    
    jan_date = pd.to_datetime("2025-01-01")
    feb_date = pd.to_datetime("2025-02-01")
    
    def log_month(date_val, label):
        actual = train_ts.get(date_val, np.nan)
        pred = fitted.get(date_val, np.nan)
        if not pd.isna(actual) and not pd.isna(pred):
            pred_int = int(round(pred))
            actual_int = int(actual)
            abs_err = abs(actual_int - pred_int)
            ape = (abs_err / actual_int * 100) if actual_int != 0 else 0
            print(f"{label} {date_val.strftime('%B %Y')}: Actual={actual_int}, Pred={pred_int}, Abs={abs_err}, APE={ape:.2f}%")
        else:
            print(f"{label} {date_val.strftime('%B %Y')}: Not found in train_ts")
            
    log_month(jan_date, "Jan")
    log_month(feb_date, "Feb")
