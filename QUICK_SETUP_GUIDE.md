# HinaTourist — Installation & Quick Setup Guide

> **Project Title:** HinaTourist: A Web-Based Visitor Monitoring and Forecasting System for Hinatuan Tourism  
> **Document Version:** 1.0 (Quick Setup & Run Guide)

---

## 1. Prerequisites & System Requirements

Ensure the following software is installed on your computer before starting:

* **PHP:** `8.2+` (with `pdo_mysql`, `mbstring`, `curl` extensions)
* **Composer:** `2.x`
* **Node.js:** `18.x` or `20.x` LTS (includes `npm`)
* **Python:** `3.9` to `3.11` (includes `pip` and `venv`)
* **Database:** MySQL Server `8.0+` or MariaDB (via XAMPP, Laragon, or standalone)

---

## 2. Quick Setup & Installation Steps

### Step 1: Database Setup
1. Start your **MySQL Server** (e.g., in XAMPP or Laragon).
2. Create a database named `pwasystemapp`:
   ```sql
   CREATE DATABASE pwasystemapp;
   ```

---

### Step 2: Web Application Setup (Laravel + Vite)
Open your terminal in the `hinatourist` root folder:

```bash
# 1. Install PHP dependencies
composer install

# 2. Install Node.js dependencies
npm install

# 3. Create .env configuration file
# On Windows PowerShell:
Copy-Item .env.example .env
# On Linux / Mac:
# cp .env.example .env

# 4. Generate Application Key
php artisan key:generate

# 5. Run Database Migrations & Seeders
php artisan migrate --seed
```

---

### Step 3: SARIMA Forecasting Service Setup (Python FastAPI)
Open a new terminal window inside the `sarimaforecasting` directory:

```bash
# 1. Navigate to directory
cd sarimaforecasting

# 2. Create & Activate Virtual Environment
# Windows PowerShell:
python -m venv venv
.\venv\Scripts\Activate.ps1

# Linux / Mac:
# python3 -m venv venv
# source venv/bin/activate

# 3. Install required Python packages
pip install fastapi uvicorn pandas numpy statsmodels openpyxl pydantic pymysql

# 4. Start the FastAPI Service (Port 8000)
uvicorn mainone:app --reload --host 127.0.0.1 --port 8000
```

---

### Step 4: Run the Application (Multi-Terminal Commands)

To run the complete system, launch the following 3 commands in separate terminal windows:

| Terminal Window | Service | Command | Access URL |
| :--- | :--- | :--- | :--- |
| **Terminal 1** | **Vite Asset Server** | `npm run dev` | Running in background |
| **Terminal 2** | **Laravel Web App** | `php artisan serve --port=8080` | `http://localhost:8080` |
| **Terminal 3** | **Python SARIMA API** | `uvicorn mainone:app --port 8000` | `http://localhost:8000` |
| **Terminal 4** *(Required)* | **Queue Worker** | `php artisan queue:work --timeout=600` | Background processing |

> [!IMPORTANT]
> Always run the Queue Worker with `--timeout=600` so SARIMA model retraining jobs do not time out.

---

## 3. How to Use & Test the System

### A. Tourist Workflow (Public Entry)
1. Open `http://localhost:8080/pass` in any browser.
2. Select destination, enter male/female headcount, origin type, and visit purpose.
3. Click **Generate Visitor Pass** to receive the encoded QR entry pass.

### B. Site Attendant Workflow (Logbook & Offline PWA)
1. Navigate to `http://localhost:8080/login`.
2. Log in using attendant credentials.
3. Go to **Visitor Logbook** (`/logbook`) to record tourist arrivals.
4. **PWA / Offline Mode:** Install the app on mobile ("Add to Home Screen"). Logbook entries made without internet are saved locally in IndexedDB and automatically synced to the server when connection is restored.

### C. Admin Workflow (Analytics & Forecasting)
1. Log in with admin credentials at `http://localhost:8080/login`.
2. **Dashboard Statistics (`/admin/statistics`):** View real-time totals, gender distribution, demographic origins (*Within Province*, *Other Provinces*, *Foreign Residents*), and visit reasons.
3. **User Management (`/admin/users`):** Add or verify site attendant accounts.
4. **SARIMA Forecasting (`/admin/dashboard`):** View predicted visitor counts per tourist spot and click **"Retrain SARIMA Model"** to update ML models with recent arrival data.
