import sys
import subprocess
import os

def check_and_install_packages():
    required = ["pandas", "openpyxl"]
    missing = []
    for package in required:
        try:
            __import__(package)
        except ImportError:
            missing.append(package)
    if missing:
        print(f"Installing missing packages for data preparation: {', '.join(missing)}...")
        subprocess.check_call([sys.executable, "-m", "pip", "install"] + missing)

check_and_install_packages()

import pandas as pd
import glob

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
folder = os.path.join(BASE_DIR, 'excel_format_data')
files = glob.glob(os.path.join(folder, '*.xlsx'))

months = ['JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER']

all_data = []

for file in files:
    year = os.path.basename(file).split('.')[0]
    try:
        df = pd.read_excel(file)
    except Exception as e:
        print(f"Error reading {file}: {e}")
        continue
    
    # Normalize column names
    col_map = {}
    for c in df.columns:
        if isinstance(c, str):
            c_upper = c.upper().strip()
            if c_upper == 'NAME OF ATTRACTIONS':
                col_map[c] = 'ATTRACTION'
            elif c_upper in months:
                col_map[c] = c_upper

    df = df.rename(columns=col_map)
    
    if 'ATTRACTION' not in df.columns:
        print(f"No ATTRACTION column found in {file}")
        continue
        
    # Define name mapping for attractions with inconsistent names across years
    NAME_MAPPING = {
        'BACULIN AMAZING SAND': 'BACULIN AMAZING SAND BAR',
        'HARIP OCEANSIDE BEACH': 'HARIP OCEANSIDE WHITE BEACH',
        'HARIP SEA OCEANSIDE WHITE BEACH': 'HARIP OCEANSIDE WHITE BEACH'
    }

    for index, row in df.iterrows():
        attraction = str(row['ATTRACTION']).strip().upper()
        
        # Apply name normalization
        if attraction in NAME_MAPPING:
            attraction = NAME_MAPPING[attraction]
            
        # skip empty rows or totals
        if pd.isna(row['ATTRACTION']) or attraction == 'NAN' or attraction == 'TOTAL' or attraction == 'NONE' or attraction.startswith('SOURCE') or attraction.startswith('HTTP') or attraction.startswith('WWW.'):
            continue
            
        for month_idx, month in enumerate(months):
            if month in df.columns:
                visitors = row[month]
                # Try to convert to float, handle errors
                try:
                    visitors = float(visitors)
                except:
                    visitors = 0
                if pd.isna(visitors):
                    visitors = 0
                    
                date_str = f"{year}-{(month_idx+1):02d}-01"
                all_data.append({
                    'Attraction': attraction,
                    'Date': date_str,
                    'Visitors': visitors
                })

df_all = pd.DataFrame(all_data)
df_all['Date'] = pd.to_datetime(df_all['Date'])
df_all = df_all.sort_values(['Attraction', 'Date'])

processed_data_path = os.path.join(BASE_DIR, 'processed_data.csv')
# output processed data
df_all.to_csv(processed_data_path, index=False)
print("Data processing complete. First 5 rows:")
print(df_all.head())
print(f"Total records: {len(df_all)}")
print(f"Unique attractions: {df_all['Attraction'].nunique()}")