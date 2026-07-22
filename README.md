# HinaTourist

> **A Web-Based Visitor Monitoring and Forecasting System for Hinatuan Tourism**

HinaTourist is an integrated web platform and Progressive Web Application (PWA) designed to streamline tourist registration, provide offline-first visitor logbooks for site attendants, and deliver predictive visitor arrival analytics using a seasonal **SARIMA (Seasonal AutoRegressive Integrated Moving Average)** time-series forecasting microservice.

---

## 🌟 Key Capabilities & Features

* **Contactless Public QR Visitor Pass:** Tourists scan gate QR codes to quickly generate a digital entry pass (`/pass`) without logging in.
* **Offline-First PWA Logbook:** Site attendants log arrivals in areas with poor cellular reception. Logs buffer locally in IndexedDB and automatically sync to the database when reconnected.
* **Real-Time Analytics Dashboard:** Interactive demographic breakdowns showing gender ratios, visitor origin tiers (*Within Province*, *Other Provinces*, *Foreign Residents*), and visit reasons (*Vacation/Leisure*, *Business*, *Others*).
* **Integrated SARIMA Forecasting System:** Seasonal time-series modeling predicting monthly visitor counts per destination, complete with holiday adjustments and outlier handling.

---

## 🛠️ Tech Stack & System Architecture

| Tier | Technologies |
| :--- | :--- |
| **Backend Web Framework** | Laravel 11 (PHP 8.2+) |
| **Frontend UI & PWA** | Vite, Tailwind CSS, Alpine.js, Chart.js, `vite-plugin-pwa` |
| **Forecasting Engine** | Python 3.9+, FastAPI, Uvicorn, Statsmodels, Pandas, NumPy |
| **Database** | MySQL 8.0+ / MariaDB |
| **Queue & Background Jobs** | Laravel Queue Worker (`database` driver) |

---

## 🚀 Quick Start Guide

### 1. Prerequisites
Ensure you have installed:
* PHP `8.2+` & Composer `2.x`
* Node.js `18.x+` (LTS) & NPM
* Python `3.9+` (with `pip` and `venv`)
* MySQL Server (via XAMPP, Laragon, or standalone)

---

### 2. Installation & Setup

#### A. Database Setup
Create a MySQL database named `hinatourist`:
```sql
CREATE DATABASE hinatourist;
```

#### B. Laravel Web Application
In the project root directory:
```bash
# 1. Install PHP dependencies
composer install

# 2. Install Node.js dependencies
npm install

# 3. Create .env file
# PowerShell: Copy-Item .env.example .env  |  Linux/Mac: cp .env.example .env

# 4. Generate encryption key
php artisan key:generate

# 5. Run migrations and database seeders
php artisan migrate --seed
```

#### C. SARIMA Python Microservice
In the `sarimaforecasting/` directory:
```bash
cd sarimaforecasting

# Create and activate virtual environment
python -m venv venv
.\venv\Scripts\Activate.ps1   # PowerShell (Windows)
# source venv/bin/activate    # Linux / Mac

# Install Python packages
pip install fastapi uvicorn pandas numpy statsmodels openpyxl pydantic pymysql
```

---

### 3. Execution Commands (Multi-Terminal)

Run the following processes in separate terminal windows to launch the system:

| Terminal Window | Process | Command | Port / URL |
| :--- | :--- | :--- | :--- |
| **Terminal 1** | **Vite Assets** | `npm run dev` | Background |
| **Terminal 2** | **Laravel App** | `php artisan serve --port=8080` | `http://localhost:8080` |
| **Terminal 3** | **FastAPI Service** | `cd sarimaforecasting && uvicorn mainone:app --port 8000` | `http://localhost:8000` |
| **Terminal 4** | **Queue Worker** | `php artisan queue:work --timeout=600` | Background Jobs |

> [!IMPORTANT]
> **Queue Worker Timeout:**
> When running the Queue Worker, you **MUST** use `--timeout=600`. The SARIMA retraining job performs complex time-series grid-search training and will fail if killed by Laravel's default 60-second timeout.

---

## 📂 Project Structure

```
hinatourist/
├── app/                        # Controllers, Models, & Middleware
├── database/                   # Migrations & Seeders
├── public/                     # Public assets & PWA Manifest
├── resources/                  # Blade Views & CSS/JS sources
├── routes/                     # Web & API routes (includes SARIMA proxy)
├── sarimaforecasting/          # Python FastAPI SARIMA Microservice
│   ├── mainone.py              # FastAPI server & ML logic
│   ├── prepare_data.py         # Data preprocessing
│   └── processed_data.csv      # Historical visitor dataset
├── QUICK_SETUP_GUIDE.md        # Short installation guide
└── SYSTEM_OPERATIONS_GUIDE.md  # Detailed operations manual
```

---

## 📄 Detailed Documentation

* **[Quick Setup Guide](QUICK_SETUP_GUIDE.md):** 2-page condensed setup instructions.
* **[System Operations Guide](SYSTEM_OPERATIONS_GUIDE.md):** Complete technical reference manual.

---
*Developed for Hinatuan Tourism Visitor Monitoring and Predictive Analytics.*
