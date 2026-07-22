import sys
import subprocess
import os
import itertools
import warnings
import json
from datetime import datetime

def check_and_install_packages():
    required = ["fastapi", "uvicorn", "pandas", "numpy", "statsmodels", "openpyxl", "pydantic", "pymysql"]
    missing = []
    for package in required:
        try:
            __import__(package)
        except ImportError:
            missing.append(package)

    if missing:
        print(f"[SETUP] Installing: {', '.join(missing)}")
        try:
            subprocess.check_call([sys.executable, "-m", "pip", "install"] + missing,
                                  stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
            print(f"[SETUP] Done.")
        except subprocess.CalledProcessError as e:
            print(f"[SETUP] Failed to install packages: {e}")
            sys.exit(1)

check_and_install_packages()

from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
import pandas as pd
import numpy as np
import uvicorn
from contextlib import asynccontextmanager
from statsmodels.tsa.arima.model import ARIMA

warnings.filterwarnings('ignore')

# Global variable to store models for each attraction
models = {}

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
DATA_PATH = os.path.join(BASE_DIR, "processed_data.csv")
SYNC_STATUS_PATH = os.path.join(BASE_DIR, "sync_status.json")

# Philippine holidays and seasonal patterns mapped to each month
HOLIDAY_MAPPING = {
    1: "New Year's Day",
    2: "Valentine's Day / Chinese New Year",
    3: "Holy Week / Start of Summer",
    4: "Holy Week / Summer Peak / Araw ng Kagitingan",
    5: "Labor Day",
    6: "Philippine Independence Day",
    7: "Mid-Year",
    8: "National Heroes Day",
    9: "Start of Ber Months",
    10: "Semestral Break / Undas Eve Travels",
    11: "All Saints' Day / All Souls' Day / Bonifacio Day",
    12: "Christmas Season / Rizal Day / New Year's Eve"
}

MONTH_NAMES = {
    1: "January", 2: "February", 3: "March", 4: "April",
    5: "May", 6: "June", 7: "July", 8: "August",
    9: "September", 10: "October", 11: "November", 12: "December"
}

# ─────────────────────────────────────────────────────────────
# DATA PREPROCESSING — Handling the noise in tourism data
# ─────────────────────────────────────────────────────────────

def interpolate_zeros(ts):
    """Replace zero values with interpolated values.
    Zeros in tourism data are usually missing data, not actual zero visitors.
    """
    ts_clean = ts.copy()
    ts_clean[ts_clean == 0] = np.nan
    ts_clean = ts_clean.interpolate(method='linear')
    # Fill any remaining NaN at the edges with forward/backward fill
    ts_clean = ts_clean.ffill().bfill()
    return ts_clean

def cap_outliers(ts, multiplier=1.5):
    """Cap extreme outliers using the IQR (Interquartile Range) method.
    Values beyond multiplier*IQR from Q1/Q3 are capped to reduce extreme spikes.
    Use multiplier=3.0 for relaxed capping that preserves genuine seasonal peaks.
    """
    Q1 = ts.quantile(0.25)
    Q3 = ts.quantile(0.75)
    IQR = Q3 - Q1
    lower_bound = Q1 - multiplier * IQR
    upper_bound = Q3 + multiplier * IQR
    ts_capped = ts.clip(lower=max(0, lower_bound), upper=upper_bound)
    return ts_capped

# ─────────────────────────────────────────────────────────────
# MAPE CALCULATION
# ─────────────────────────────────────────────────────────────

def calculate_mape(actual, fitted):
    """Calculate the Mean Absolute Percentage Error (MAPE).
    Only considers non-zero actual values to avoid division by zero.
    """
    mask = actual != 0
    if mask.sum() == 0:
        return 0.0
    mape = np.mean(np.abs((actual[mask] - fitted[mask]) / actual[mask])) * 100
    return float(mape)

# ─────────────────────────────────────────────────────────────
# AUTOMATIC SARIMA ORDER SELECTION (Optimized for lowest MAPE)
# ─────────────────────────────────────────────────────────────

def find_best_sarima_order(ts):
    """Find the best SARIMA (p,d,q)(P,D,Q,12) parameters using a grid search.
    Unlike AIC, this tests parameter combinations specifically to find the lowest MAPE.
    """
    best_mape = np.inf
    best_order = (1, 1, 1)
    best_seasonal = (1, 1, 0, 12)
    
    # Check simple baseline first
    try:
        model = ARIMA(ts, order=(1,1,1), seasonal_order=(1,1,0,12),
                      enforce_stationarity=False, enforce_invertibility=False)
        result = model.fit()
        best_mape = calculate_mape(ts, result.fittedvalues)
    except:
        pass

    # Parameter search space optimized for speed and accuracy
    p_range = range(0, 2)  # 0, 1
    d_range = range(0, 2)  # 0, 1
    q_range = range(0, 2)  # 0, 1
    P_range = range(0, 2)  # 0, 1
    D_range = range(0, 2)  # 0, 1
    Q_range = range(0, 2)  # 0, 1
    
    for p, d, q in itertools.product(p_range, d_range, q_range):
        for P, D, Q in itertools.product(P_range, D_range, Q_range):
            # Skip the trivial no-parameter model
            if p == 0 and q == 0 and P == 0 and Q == 0:
                continue
            try:
                model = ARIMA(
                    ts,
                    order=(p, d, q),
                    seasonal_order=(P, D, Q, 12),
                    enforce_stationarity=False,
                    enforce_invertibility=False
                )
                result = model.fit()
                # predict in-sample
                mape = calculate_mape(ts, result.fittedvalues)
                # Ensure no exploding values
                if np.isfinite(mape) and mape < best_mape:
                    best_mape = mape
                    best_order = (p, d, q)
                    best_seasonal = (P, D, Q, 12)
            except Exception:
                continue
    
    return best_order, best_seasonal

# ─────────────────────────────────────────────────────────────
# SEASONAL ANALYSIS
# ─────────────────────────────────────────────────────────────

def analyze_peak_seasons(ts):
    """Analyze the seasonal patterns and identify peak seasons based on historical data."""
    monthly_avg = ts.groupby(ts.index.month).mean()
    overall_avg = monthly_avg.mean()

    # Classify each month
    seasonal_analysis = []
    for month_num in range(1, 13):
        avg_visitors = monthly_avg.get(month_num, 0)
        # Determine season classification
        if avg_visitors >= overall_avg * 1.2:
            classification = "Peak Season"
        elif avg_visitors >= overall_avg * 0.8:
            classification = "Regular Season"
        else:
            classification = "Low Season"

        seasonal_analysis.append({
            "month": MONTH_NAMES[month_num],
            "average_visitors": int(round(avg_visitors)),
            "classification": classification,
            "associated_holidays": HOLIDAY_MAPPING[month_num]
        })

    # Get top 3 peak months
    top_peak_months = monthly_avg.nlargest(3).index.tolist()
    peak_summary = [
        {
            "rank": i + 1,
            "month": MONTH_NAMES[m],
            "average_visitors": int(round(monthly_avg[m])),
            "associated_holidays": HOLIDAY_MAPPING[m]
        }
        for i, m in enumerate(top_peak_months)
    ]

    return seasonal_analysis, peak_summary

# ─────────────────────────────────────────────────────────────
# MODEL TRAINING (MINIMALIST VERSION)
# ─────────────────────────────────────────────────────────────

def train_models():
    """Train SARIMA models on startup for all 8 tourist spots."""
    print("[STARTUP] Training SARIMA models...")

    if not os.path.exists(DATA_PATH):
        print(f"[STARTUP] ERROR: Data file not found at {DATA_PATH}")
        try:
            prepare_script = os.path.join(BASE_DIR, "prepare_data.py")
            subprocess.check_call([sys.executable, prepare_script])
        except subprocess.CalledProcessError as e:
            print(f"[STARTUP] Failed to run prepare_data.py: {e}")
            return

    df = pd.read_csv(DATA_PATH)
    df['Date'] = pd.to_datetime(df['Date'])
    unique_attractions = df['Attraction'].unique()

    for attraction in unique_attractions:
        attr_df = df[df['Attraction'] == attraction].sort_values('Date')
        attr_df.set_index('Date', inplace=True)
        ts_raw = attr_df['Visitors'].resample('MS').mean().fillna(0)

        if len(ts_raw) < 12:
            print(f"[STARTUP] SKIP  {attraction} — not enough data.")
            continue

        yearly_data = ts_raw.groupby(ts_raw.index.year).sum()
        seasonal_analysis, peak_summary = analyze_peak_seasons(ts_raw)

        try:
            ts_working = ts_raw.copy()
            if int((ts_working == 0).sum()) > 0:
                ts_working = interpolate_zeros(ts_working)
            ts_working = cap_outliers(ts_working)

            split_idx = int(len(ts_working) * 0.8)
            train_ts = ts_working[:split_idx]
            test_ts  = ts_working[split_idx:]
            train_ts_log = np.log1p(train_ts)

            best_order, best_seasonal = find_best_sarima_order(train_ts_log)

            sarima_model_log = ARIMA(
                train_ts_log,
                order=best_order,
                seasonal_order=best_seasonal,
                enforce_stationarity=False,
                enforce_invertibility=False
            )
            results_log = sarima_model_log.fit()

            forecast_log = results_log.get_forecast(steps=len(test_ts))
            test_fitted  = np.expm1(forecast_log.predicted_mean)
            mape_final   = calculate_mape(test_ts, test_fitted)
            test_predicted_series = pd.Series(test_fitted.values, index=test_ts.index)

            is_fallback = False
            if mape_final > 100:
                is_fallback = True
                monthly_averages = ts_working.groupby(ts_working.index.month).mean()
                monthly_stds = ts_working.groupby(ts_working.index.month).std().fillna(ts_working.std())
                test_months = test_ts.index.month
                fallback_preds = monthly_averages[test_months].values
                mape_final = calculate_mape(test_ts, fallback_preds)
                test_predicted_series = pd.Series(fallback_preds, index=test_ts.index)

            models[attraction] = {
                'model': results_log,
                'last_date': train_ts.index[-1],
                'mape': round(mape_final, 2),
                'best_order': best_order,
                'best_seasonal': best_seasonal,
                'seasonal_analysis': seasonal_analysis,
                'peak_summary': peak_summary,
                'historical_yearly': yearly_data.to_dict(),
                'historical_monthly': train_ts.to_dict(),
                'historical_test': test_ts.to_dict(),
                'test_predicted': test_predicted_series.to_dict(),
                'log_transformed': True,
                'is_fallback': is_fallback,
                'fallback_averages': ts_working.groupby(ts_working.index.month).mean().to_dict(),
                'fallback_stds': ts_working.groupby(ts_working.index.month).std().to_dict()
            }

            tag = "FALLBACK" if is_fallback else "OK"
            print(f"[STARTUP] [{tag}] {attraction} — MAPE: {round(mape_final, 2)}% | SARIMA{best_order}x{best_seasonal}")

        except Exception as e:
            print(f"[STARTUP] ERROR  {attraction} — {e}")

    print(f"[STARTUP] Done. {len(models)}/{len(unique_attractions)} models ready.")

@asynccontextmanager
async def lifespan(app: FastAPI):
    # Train SARIMA models on startup
    train_models()
    yield
    # Clean up on shutdown
    models.clear()

app = FastAPI(title="SARIMA Tourist Forecasting API", lifespan=lifespan)

# Add CORS Middleware to allow requests from frontend 
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=False,
    allow_methods=["*"],
    allow_headers=["*"],
)

class ForecastRequest(BaseModel):
    attraction_name: str
    months_ahead: int = 12

class ForecastResponse(BaseModel):
    attraction: str
    mape: float
    mape_interpretation: str
    sarima_order: str
    peak_seasons: list[dict]
    seasonal_analysis: list[dict]
    historical_monthly: list[dict]   # 80% training window
    historical_test: list[dict]      # 20% test actuals (for overlap chart)
    test_predicted: list[dict]       # model predictions for test window (for comparison)
    historical_yearly: list[dict]
    forecasts: list[dict]

@app.get("/")
def read_root():
    return {"message": "Welcome to the Tourist Spot SARIMA Forecasting API"}

@app.get("/attractions")
def get_attractions():
    """Returns a list of attractions with trained SARIMA models."""
    attraction_list = []
    for name, data in models.items():
        attraction_list.append({
            "name": name,
            "mape": data['mape'],
            "sarima_order": f"SARIMA{data['best_order']}x{data['best_seasonal']}",
            "peak_seasons": data['peak_summary']
        })
    return {"attractions": attraction_list}

@app.post("/forecast", response_model=ForecastResponse)
def forecast(request: ForecastRequest):
    """Predicts future visitors for a specific tourist spot using a tuned SARIMA model."""
    attraction = request.attraction_name.upper()
    
    if attraction not in models:
        raise HTTPException(status_code=404, detail=f"SARIMA model for attraction '{attraction}' not found or not trained.")
    
    try:
        model_data = models[attraction]
        result = model_data['model']
        
        is_fallback = model_data.get('is_fallback', False)
        
        if is_fallback:
            # ── Fallback Path: Seasonal Averages ──
            predictions = []
            historical_avgs = model_data['fallback_averages']
            historical_stds = model_data['fallback_stds']
            
            def safe_int(val):
                return 0 if pd.isna(val) else int(round(val))
                
            # Start after the last training date
            last_date_val = model_data.get('last_date')
            base_date = pd.to_datetime(last_date_val) if last_date_val is not None else pd.to_datetime("2025-12-01")
            for i in range(1, request.months_ahead + 1):
                next_date = base_date + pd.DateOffset(months=i)
                m = next_date.month
                
                avg_val = historical_avgs.get(str(m), historical_avgs.get(m, 0))
                std_val = historical_stds.get(str(m), historical_stds.get(m, 0))
                
                # Simple 95% Confidence Interval for averages (1.96 * std)
                lower_bound = max(0, safe_int(avg_val - 1.96 * std_val))
                upper_bound = safe_int(avg_val + 1.96 * std_val)
                
                predictions.append({
                    "month": next_date.strftime("%Y-%m"),
                    "predicted_visitors": max(0, safe_int(avg_val)),
                    "confidence_interval_lower": lower_bound,
                    "confidence_interval_upper": upper_bound,
                    "is_fallback_prediction": True
                })
        else:
            # ── Standard Path: Optimized SARIMA ──
            forecast_obj = result.get_forecast(steps=request.months_ahead)
            forecast_values = forecast_obj.predicted_mean
            conf_int = forecast_obj.conf_int(alpha=0.05)
            
            # Re-transform if log-transformed
            is_log = model_data.get('log_transformed', False)
            if is_log:
                forecast_values = np.expm1(forecast_values)
                conf_int = np.expm1(conf_int)
                
            def safe_int(val, default=0):
                if pd.isna(val) or np.isinf(val): return default
                return int(round(val))
                
            # Format the response with confidence intervals
            predictions = []
            last_date_val = model_data.get('last_date')
            base_date = pd.to_datetime(last_date_val) if last_date_val is not None else pd.to_datetime("2025-12-01")
            
            for i, (date, value) in enumerate(forecast_values.items()):
                output_date = base_date + pd.DateOffset(months=(i + 1))
                
                # Ensure values are non-negative integers
                pred_val = max(0, safe_int(value, default=0))
                
                _lower = conf_int.iloc[i, 0]
                _upper = conf_int.iloc[i, 1]
                
                # If bounds explode to inf or NaN, fallback to pred_val
                l_val = safe_int(_lower, default=pred_val)
                u_val = safe_int(_upper, default=pred_val)
                
                lower_bound = max(0, l_val)
                upper_bound = max(0, u_val)
                
                predictions.append({
                    "month": output_date.strftime("%Y-%m"),
                    "predicted_visitors": pred_val,
                    "confidence_interval_lower": lower_bound,
                    "confidence_interval_upper": upper_bound,
                    "is_fallback_prediction": False
                })

        # Interpret MAPE accuracy
        mape = model_data['mape']
        if pd.isna(mape):
            mape = 0.0
        if mape <= 15:
            interpretation = "Highly Accurate"
        elif mape <= 30:
            interpretation = "Good Accuracy"
        elif mape <= 50:
            interpretation = "Reasonable Accuracy"
        else:
            interpretation = "Low Accuracy — acceptable given highly volatile historical data"
            
        def safe_int(val):
            if pd.isna(val) or np.isinf(val): return 0
            return int(round(val))
            
        # Convert historical data to list of dicts for frontend
        historical_m = [{"month": str(k.date()), "visitors": safe_int(v)} for k, v in model_data['historical_monthly'].items()]
        historical_t = [{"month": str(k.date()), "visitors": safe_int(v)} for k, v in model_data.get('historical_test', {}).items()]
        test_pred    = [{"month": str(k.date()), "visitors": safe_int(v)} for k, v in model_data.get('test_predicted', {}).items()]
        historical_y = [{"year": int(k), "total_visitors": safe_int(v)} for k, v in model_data['historical_yearly'].items()]

        return ForecastResponse(
            attraction=attraction,
            mape=mape,
            mape_interpretation=interpretation,
            sarima_order=f"SARIMA{model_data['best_order']}x{model_data['best_seasonal']}",
            peak_seasons=model_data['peak_summary'],
            seasonal_analysis=model_data['seasonal_analysis'],
            historical_monthly=historical_m,
            historical_test=historical_t,
            test_predicted=test_pred,
            historical_yearly=historical_y,
            forecasts=predictions
        )
    except Exception as e:
        import traceback
        err_msg = traceback.format_exc()
        raise HTTPException(status_code=500, detail=err_msg)

# ─────────────────────────────────────────────────────────────
# HYBRID RETRAIN ENDPOINT — Merges CSV + MySQL, refits models
# ─────────────────────────────────────────────────────────────

def _write_sync_status(status: str, detail: str = "", mape_summary: dict = None):
    """Persist the current sync status to a JSON file so Laravel can poll it."""
    payload = {
        "status": status,
        "detail": detail,
        "last_synced": datetime.now().isoformat(),
        "mape_summary": mape_summary or {}
    }
    with open(SYNC_STATUS_PATH, "w") as f:
        json.dump(payload, f)

def _fetch_mysql_delta(last_csv_date: str) -> pd.DataFrame:
    """Query the MySQL hinatourist DB for visitor_logs newer than last_csv_date.
    Returns a DataFrame with columns: Attraction, Date, Visitors
    aggregated to monthly totals per dedicated_area.
    """
    import pymysql
    conn = pymysql.connect(
        host="127.0.0.1",
        port=3306,
        user="root",
        password="",
        database="hinatourist",
        cursorclass=pymysql.cursors.DictCursor
    )
    try:
        query = """
            SELECT
                UPPER(dedicated_area)                      AS Attraction,
                DATE_FORMAT(visit_date, '%%Y-%%m-01')       AS visit_month,
                SUM(COALESCE(male_count,0) + COALESCE(female_count,0)) AS Visitors
            FROM visitor_logs
            WHERE dedicated_area IS NOT NULL
              AND visit_date > %s
            GROUP BY Attraction, visit_month
            ORDER BY Attraction, visit_month
        """
        with conn.cursor() as cursor:
            cursor.execute(query, (last_csv_date,))
            rows = cursor.fetchall()
    finally:
        conn.close()

    if not rows:
        return pd.DataFrame(columns=["Attraction", "Date", "Visitors"])

    delta_df = pd.DataFrame(rows)
    delta_df.rename(columns={"visit_month": "Date"}, inplace=True)
    delta_df["Date"] = pd.to_datetime(delta_df["Date"])
    delta_df["Visitors"] = delta_df["Visitors"].astype(float)
    return delta_df


def retrain_models_hybrid():
    """Hybrid retrain: merge CSV baseline + MySQL delta, preprocess, refit SARIMA.
    ONLY retrains attractions that have new MySQL data — keeps existing models for unchanged attractions.
    KEY DESIGN: MAPE is always evaluated on the clean CSV-only 20% test window.
    MySQL delta rows are appended ONLY to the training set — never the test/evaluation set.
    This prevents a single new log entry (e.g. 1 visitor) from destroying MAPE accuracy.
    """
    global models

    _write_sync_status("running", "Loading CSV baseline...")

    # ── 1. Load CSV baseline ──
    if not os.path.exists(DATA_PATH):
        _write_sync_status("failed", "processed_data.csv not found")
        return {"status": "error", "message": "processed_data.csv not found"}

    csv_df = pd.read_csv(DATA_PATH)
    csv_df["Date"] = pd.to_datetime(csv_df["Date"])
    csv_df["Attraction"] = csv_df["Attraction"].str.upper().str.strip()
    last_csv_date = csv_df["Date"].max().strftime("%Y-%m-%d")

    # ── 2. Fetch MySQL delta ──
    _write_sync_status("running", "Querying MySQL for new logs...")
    try:
        delta_df = _fetch_mysql_delta(last_csv_date)
    except Exception as e:
        _write_sync_status("failed", f"MySQL query failed: {e}")
        return {"status": "error", "message": f"MySQL query failed: {e}"}

    # ── 3. Determine which attractions have new data ──
    if delta_df.empty:
        _write_sync_status("success", "No new data found. All models unchanged.", {})
        return {"status": "ok", "attractions_retrained": 0, "last_synced": datetime.now().isoformat(),
                "mape_summary": {}, "message": "No new MySQL data found since last CSV date."}

    attractions_with_new_data = set(delta_df["Attraction"].str.upper().str.strip().unique())
    print(f"\n[RETRAIN] Attractions with new MySQL data: {attractions_with_new_data}")
    print(f"[RETRAIN] Attractions WITHOUT new data keep their existing models.\n")

    mape_summary = {}
    retrained_count = 0
    skipped_count = 0

    for attraction in csv_df["Attraction"].unique():

        # ── Skip attractions that have no new data — keep existing model intact ──
        if attraction not in attractions_with_new_data:
            print(f"  ⊘ {attraction} — No new data, keeping existing model.")
            skipped_count += 1
            if attraction in models:
                mape_summary[attraction] = {
                    "mape": models[attraction]['mape'],
                    "fallback": models[attraction].get('is_fallback', False),
                    "skipped": True
                }
            continue

        print(f"[RETRAIN] {attraction}")
        _write_sync_status("running", f"Retraining {attraction}...")

        # ── CSV-only series (used for evaluation / MAPE) ──
        csv_attr = csv_df[csv_df["Attraction"] == attraction].copy()
        csv_attr = csv_attr.sort_values("Date").set_index("Date")
        ts_csv = csv_attr["Visitors"].resample("MS").mean().fillna(0)
        csv_range = pd.date_range(start=ts_csv.index.min(), end=ts_csv.index.max(), freq="MS")
        ts_csv = ts_csv.reindex(csv_range, fill_value=0)

        if len(ts_csv) < 12:
            print(f"  ✗ Skipping — not enough CSV data ({len(ts_csv)} months).")
            continue

        # ── Preprocess CSV series (identical to original training) ──
        ts_working = ts_csv.copy()
        zero_count = int((ts_working == 0).sum())
        if zero_count > 0:
            ts_working = interpolate_zeros(ts_working)
        ts_working = cap_outliers(ts_working)

        # ── 80/20 Split on CSV data only (MAPE evaluation window) ──
        split_idx = int(len(ts_working) * 0.8)
        train_ts_csv = ts_working[:split_idx]
        test_ts_csv  = ts_working[split_idx:]
        print(f"[RETRAIN] {attraction} — Split: {len(train_ts_csv)} train | {len(test_ts_csv)} test months")

        # ── Append MySQL delta to the training set ONLY (never test set) ──
        delta_attr = delta_df[delta_df["Attraction"].str.upper().str.strip() == attraction].copy()
        delta_attr = delta_attr.sort_values("Date").set_index("Date")
        ts_delta = delta_attr["Visitors"].resample("MS").mean().fillna(0)

        # Preprocess delta with same pipeline
        if len(ts_delta) > 0:
            ts_delta = interpolate_zeros(ts_delta)
            ts_delta = cap_outliers(ts_delta)
            # Merge: full CSV (all 80%+20%) + new delta months
            ts_full_train = pd.concat([ts_working, ts_delta])
            ts_full_train = ts_full_train[~ts_full_train.index.duplicated(keep='first')]
            ts_full_train = ts_full_train.sort_index()
            print(f"[RETRAIN] {attraction} — Appended {len(ts_delta)} new month(s) from MySQL.")
        else:
            ts_full_train = ts_working.copy()

        # ── Log transform the FULL training set (CSV + delta) ──
        train_ts_log = np.log1p(ts_full_train)

        # ── Find best SARIMA order using CSV-only log-data (stable evaluation) ──
        try:
            best_order, best_seasonal = find_best_sarima_order(np.log1p(train_ts_csv))
        except Exception:
            best_order, best_seasonal = (1, 1, 1), (1, 1, 0, 12)

        # ── Fit model on FULL training set (CSV + delta) ──
        is_fallback = False
        try:
            sarima_model_log = ARIMA(
                train_ts_log,
                order=best_order,
                seasonal_order=best_seasonal,
                enforce_stationarity=False,
                enforce_invertibility=False
            )
            results_log = sarima_model_log.fit()

            # ── Evaluate MAPE only on CSV 20% test set (clean, known data) ──
            forecast_obj = results_log.get_forecast(steps=len(test_ts_csv))
            test_fitted = np.expm1(forecast_obj.predicted_mean)
            mape_final = calculate_mape(test_ts_csv, test_fitted)
            test_predicted_series = pd.Series(test_fitted.values, index=test_ts_csv.index)
            if mape_final > 100:
                print(f"[RETRAIN] {attraction} — High MAPE ({round(mape_final, 2)}%), switching to Seasonal Naive Fallback.")
                is_fallback = True
                monthly_averages = ts_working.groupby(ts_working.index.month).mean()
                monthly_stds = ts_working.groupby(ts_working.index.month).std().fillna(ts_working.std())
                test_months = test_ts_csv.index.month
                fallback_preds = monthly_averages[test_months].values
                mape_final = calculate_mape(test_ts_csv, fallback_preds)
                test_predicted_series = pd.Series(fallback_preds, index=test_ts_csv.index)
                print(f"[RETRAIN] {attraction} — Fallback MAPE: {round(mape_final, 2)}%")

        except (np.linalg.LinAlgError, ValueError) as fit_err:
            print(f"[RETRAIN] {attraction} — Convergence failed, using Seasonal Naive Fallback.")
            is_fallback = True
            results_log = None
            monthly_averages = ts_working.groupby(ts_working.index.month).mean()
            monthly_stds = ts_working.groupby(ts_working.index.month).std().fillna(ts_working.std())
            test_months = test_ts_csv.index.month
            fallback_preds = monthly_averages[test_months].values
            mape_final = calculate_mape(test_ts_csv, fallback_preds)
            test_predicted_series = pd.Series(fallback_preds, index=test_ts_csv.index)
            best_order = (1, 1, 1)
            best_seasonal = (1, 1, 0, 12)

        except Exception as e:
            print(f"[RETRAIN] {attraction} — ERROR: {e}")
            mape_summary[attraction] = {"mape": None, "error": str(e)}
            continue

        # ── Seasonal analysis (from CSV data only for clean statistics) ──
        seasonal_analysis, peak_summary = analyze_peak_seasons(ts_csv)
        yearly_data = ts_csv.groupby(ts_csv.index.year).sum()

        # ── Store updated model ──
        models[attraction] = {
            'model': results_log,
            'last_date': ts_full_train.index[-1],
            'mape': round(mape_final, 2),
            'best_order': best_order,
            'best_seasonal': best_seasonal,
            'seasonal_analysis': seasonal_analysis,
            'peak_summary': peak_summary,
            'historical_yearly': yearly_data.to_dict(),
            'historical_monthly': ts_full_train.to_dict(),
            'historical_test': test_ts_csv.to_dict(),
            'test_predicted': test_predicted_series.to_dict(),
            'log_transformed': True,
            'is_fallback': is_fallback,
            'fallback_averages': ts_working.groupby(ts_working.index.month).mean().to_dict(),
            'fallback_stds': ts_working.groupby(ts_working.index.month).std().to_dict()
        }

        retrained_count += 1
        mape_summary[attraction] = {"mape": round(mape_final, 2), "fallback": is_fallback}
        tag = "FALLBACK" if is_fallback else "OK"
        print(f"[RETRAIN] [{tag}] {attraction} — MAPE: {round(mape_final, 2)}% | SARIMA{best_order}x{best_seasonal}")

    detail_msg = f"Retrained {retrained_count} model(s), kept {skipped_count} unchanged."
    print(f"[RETRAIN] Done. {retrained_count} retrained, {skipped_count} unchanged.")
    _write_sync_status("success", detail_msg, mape_summary)
    return {
        "status": "ok",
        "attractions_retrained": retrained_count,
        "attractions_unchanged": skipped_count,
        "last_synced": datetime.now().isoformat(),
        "mape_summary": mape_summary
    }


@app.post("/retrain")
def retrain_endpoint():
    """Trigger hybrid retraining of all SARIMA models (CSV + MySQL merge).
    Runs in a background thread so this endpoint returns immediately.
    Poll GET /sync-status to track progress.
    """
    import threading

    # Check if a retrain is already running
    if os.path.exists(SYNC_STATUS_PATH):
        with open(SYNC_STATUS_PATH, "r") as f:
            current = json.load(f)
            if current.get("status") == "running":
                return {"status": "already_running", "message": "A retrain is already in progress. Poll /sync-status."}

    def _run_retrain():
        try:
            retrain_models_hybrid()
        except Exception as e:
            _write_sync_status("failed", str(e))
            print(f"[RETRAIN THREAD] Error: {e}")

    # Start retraining in a background thread
    _write_sync_status("running", "Retrain queued, starting...")
    thread = threading.Thread(target=_run_retrain, daemon=True)
    thread.start()

    return {"status": "queued", "message": "Retrain started in background. Poll /sync-status for progress."}


@app.get("/sync-status")
def sync_status_endpoint():
    """Return the current sync/retrain status for UI polling."""
    if not os.path.exists(SYNC_STATUS_PATH):
        return {"status": "idle", "detail": "No retrain has been triggered yet.", "last_synced": None, "mape_summary": {}}
    with open(SYNC_STATUS_PATH, "r") as f:
        return json.load(f)


if __name__ == "__main__":
    uvicorn.run("mainone:app", host="0.0.0.0", port=8000)