# HinaTourist

**A Web-Based Visitor Monitoring and Forecasting System for Hinatuan Tourism**

HinaTourist is a Progressive Web Application (PWA) for registering and monitoring tourist arrivals across Hinatuan's destinations, with an integrated SARIMA time-series forecasting engine for predicting monthly visitor trends.

---

## Tech Stack

| Layer | Technology |
| :--- | :--- |
| **Backend** | Laravel 11 (PHP 8.2+), Composer |
| **Frontend** | Vite, Tailwind CSS v4, Alpine.js |
| **PWA** | vite-plugin-pwa (Service Worker, Offline Support) |
| **Forecasting** | Python 3.9+, FastAPI, Statsmodels (SARIMA) |
| **Database** | MySQL 8.0+ / MariaDB |
| **Queue** | Laravel Database Queue Worker |

---

## Requirements

- PHP `8.2+` & Composer
- Node.js `18+` & NPM
- Python `3.9–3.11` & pip
- MySQL `8.0+` (via XAMPP, Laragon, or standalone)

---

## Installation

### 1. Create the Database

```sql
CREATE DATABASE hinatourist;
```

### 2. Laravel App Setup

```bash
composer install
npm install
cp .env.example .env          # or: Copy-Item .env.example .env (PowerShell)
php artisan key:generate
php artisan migrate --seed
```

### 3. Python SARIMA Service

```bash
cd sarimaforecasting
python -m venv venv
.\venv\Scripts\Activate.ps1   # Windows — or: source venv/bin/activate (Linux/Mac)
pip install fastapi uvicorn pandas numpy statsmodels openpyxl pydantic pymysql
```

---

## Running the System

Open **4 separate terminal windows**:

```bash
# Terminal 1 — Vite frontend
npm run dev

# Terminal 2 — Laravel web server
php artisan serve --port=8080

# Terminal 3 — SARIMA FastAPI service
cd sarimaforecasting
uvicorn mainone:app --port 8000

# Terminal 4 — Queue worker (required for SARIMA retraining)
php artisan queue:work --timeout=600
```

App is accessible at: **http://localhost:8080**

---

## Documentation

- [System Operations & Setup Guide](SYSTEM_OPERATIONS_GUIDE.md)
- [Quick Setup Guide](QUICK_SETUP_GUIDE.md)
