# HinaTourist & SARIMA Forecasting System
## System Operations and Setup Guide

| Document Attribute | Details |
| :--- | :--- |
| **Project Title** | HinaTourist: A WEB-BASED VISITOR MONITORING AND FORECASTING SYSTEM FOR HINATUAN TOURISM |
| **Document Version** | 1.0 |
| **Core Frameworks** | Laravel 11, Vite, Alpine.js, Tailwind CSS, Python FastAPI, Statsmodels |
| **Database Engine** | MySQL 8.0+ / MariaDB |

---

## Table of Contents
1. [System Summary & Key Features](#1-system-summary--key-features)
   - [1.1 High-Level Overview](#11-high-level-overview)
   - [1.2 Core Functional Modules](#12-core-functional-modules)
2. [Technical Installation & Environment Setup](#2-technical-installation--environment-setup)
   - [2.1 Prerequisites & System Requirements](#21-prerequisites--system-requirements)
   - [2.2 Web Application Setup (Laravel + Vite)](#22-web-application-setup-laravel--vite)
   - [2.3 Forecasting Service Setup (Python FastAPI + SARIMA)](#23-forecasting-service-setup-python-fastapi--sarima)
   - [2.4 Queue Worker & Background Job Configuration](#24-queue-worker--background-job-configuration)
   - [2.5 Multi-Service Verification](#25-multi-service-verification)
3. [Troubleshooting & Operational Maintenance](#3-troubleshooting--operational-maintenance)

---

## 1. System Summary & Key Features

### 1.1 High-Level Overview
**HinaTourist** is an integrated web-based tourism management, visitor registration, and predictive analytics platform engineered to streamline tourist logging and assist local tourism authorities in data-driven decision-making. 

The system combines a **Progressive Web Application (PWA)** for on-site tourist registration and attendant data collection with an intelligent **SARIMA (Seasonal AutoRegressive Integrated Moving Average) Time-Series Forecasting Microservice**. Together, these systems capture real-time visitor demographics, enable seamless offline data capture in remote destination areas, and deliver predictive modeling of tourist arrival trends across multiple local destinations.

```
       ┌─────────────────────────────────────────────────────────────┐
       │                   HinaTourist Web App                       │
       │     (Laravel 11 + Vite + Alpine.js + Tailwind CSS PWA)      │
       └──────────────┬──────────────────────────────┬───────────────┘
                      │                              │
                      ▼                              ▼
       ┌─────────────────────────────┐┌──────────────────────────────┐
       │      MySQL Database         ││   Python FastAPI Microservice│
       │    (pwasystemapp DB)        ││   (SARIMA Time-Series ML)    │
       └─────────────────────────────┘└──────────────────────────────┘
```

---

### 1.2 Core Functional Modules

#### 1. Contactless Tourist Self-Registration
- **QR Pass Generation:** Tourists scan location-specific QR codes placed at entrance gates, redirecting to `/pass` without requiring an account or application download.
- **Instant Digital Pass:** Displays an encoded pass confirming visitor group details and date of entry.

#### 2. Offline-First PWA Logbook
- **Field Resiliency:** Site attendants stationed at destinations with weak or non-existent cellular coverage can log individual or group arrivals seamlessly.
- **Intelligent Synchronization:** Logs created offline are held securely in client-side storage. The PWA detects restored connectivity and automatically syncs logs to the backend API (`/api/logs`). Manual sync buttons ensure data integrity.

#### 3. Real-Time Admin Analytics & Demographics
- **Demographic Categorization:** Aggregates visitors into specific origin tiers: *Within Province*, *Other Provinces*, and *Foreign Country Residence*.
- **Purpose Breakdown:** Classifies visits into *Vacation or Leisure*, *Business*, and *Others*.
- **Gender Ratios:** Tracks precise male-to-female ratios per spot and across total province counts.

#### 4. Integrated SARIMA Visitor Forecasting Engine
- **Seasonal Time-Series Modeling:** Uses historical arrival data combined with seasonal indicators and Philippine national holiday mappings (e.g., Holy Week, Summer Peak, All Saints' Day, Christmas Season) to forecast future visitor traffic.
- **Outlier Capping & Interpolation:** Preprocesses erratic raw counts using Interquartile Range (IQR) outlier capping and linear zero-value interpolation.
- **FastAPI Server-Side Proxy:** Securely proxies requests from Laravel backend to FastAPI microservice, eliminating cross-origin (CORS) and private network access restrictions.

---

## 2. Technical Installation & Environment Setup

### 2.1 Prerequisites & System Requirements

Ensure the target machine has the following software installed:

* **Operating System:** Windows 10/11, macOS, or Ubuntu 20.04+
* **PHP:** Version `^8.2` (with `pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo` extensions enabled)
* **Composer:** Version `2.x`
* **Node.js:** Version `18.x` or `20.x` LTS (with `npm` `9.x`+)
* **Python:** Version `3.9` to `3.11` (with `pip` and `venv`)
* **Database:** MySQL Server `8.0+` or MariaDB `10.4+` (e.g., via XAMPP, Laragon, or standalone MySQL service)

---

### 2.2 Web Application Setup (Laravel + Vite)

Follow these step-by-step instructions to configure and execute the main HinaTourist web app locally:

#### Step 1: Navigate to Project Directory
Open your terminal (PowerShell, Command Prompt, or Bash) and navigate to the project directory:
```bash
cd path/to/hinatourist
```

#### Step 2: Install PHP Dependencies
Run Composer to install all backend packages:
```bash
composer install
```

#### Step 3: Install Node.js Dependencies
Run NPM to install all frontend build assets and plugins:
```bash
npm install
```

#### Step 4: Configure Environment Variables
Copy the `.env.example` file to create your active `.env` file:
```bash
# On Windows PowerShell:
Copy-Item .env.example .env

# On Linux / macOS:
cp .env.example .env
```

Open the `.env` file in your editor and verify/update the database credentials and SARIMA service URL:
```env
APP_NAME="HinaTourist"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pwasystemapp
DB_USERNAME=root
DB_PASSWORD=

# Session and Queue Drivers
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

# SARIMA Forecasting Microservice URL
SARIMA_SERVICE_URL=http://localhost:8000
```

#### Step 5: Generate Application Key
Generate the Laravel encryption key:
```bash
php artisan key:generate
```

#### Step 6: Create Database & Execute Migrations
1. Ensure your MySQL server is running (e.g., start MySQL in XAMPP / Laragon).
2. Create a database named `pwasystemapp` in your MySQL database management tool (phpMyAdmin, HeidiSQL, or MySQL CLI):
   ```sql
   CREATE DATABASE IF NOT EXISTS pwasystemapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. Run the database migrations and seeders:
   ```bash
   php artisan migrate --seed
   ```

#### Step 7: Build Frontend Assets & Start Web Server
In your development environment, start the Vite build server and the Laravel development server:

* **Terminal Window 1 (Vite Dev Server):**
  ```bash
  npm run dev
  ```
* **Terminal Window 2 (Laravel Artisan Server):**
  ```bash
  php artisan serve --port=8080
  ```
  *(The application will now be accessible at `http://localhost:8080`)*

---

### 2.3 Forecasting Service Setup (Python FastAPI + SARIMA)

The SARIMA forecasting microservice runs independently on Python FastAPI.

#### Step 1: Navigate to the `sarimaforecasting` Subdirectory
```bash
cd sarimaforecasting
```

#### Step 2: Create and Activate a Python Virtual Environment
* **On Windows (PowerShell):**
  ```powershell
  python -m venv venv
  .\venv\Scripts\Activate.ps1
  ```
* **On Linux / macOS:**
  ```bash
  python3 -m venv venv
  source venv/bin/activate
  ```

#### Step 3: Install Required Python Packages
Install the core scientific computing and API packages:
```bash
pip install fastapi uvicorn pandas numpy statsmodels openpyxl pydantic pymysql
```
*(Note: `mainone.py` includes an auto-installer function that will check for and install any missing dependencies automatically upon startup).*

#### Step 4: Start the FastAPI Service
Launch the Uvicorn ASGI server on port `8000`:
```bash
uvicorn mainone:app --reload --host 127.0.0.1 --port 8000
```

> [!NOTE]
> The FastAPI service will initialize and attempt to automatically train models for each attraction site present in `processed_data.csv`. You can test service health by visiting `http://127.0.0.1:8000/attractions` in your web browser.

---

### 2.4 Queue Worker & Background Job Configuration

> [!IMPORTANT]
> **Queue Worker Timeout for SARIMA Retraining:**
> The SARIMA machine learning retraining process executes as an asynchronous background job and can take several minutes to run grid-search optimizations across multiple destinations.
> 
> Laravel's default queue worker timeout is 60 seconds, which will kill the retraining process prematurely.
> 
> **When starting the Laravel Queue Worker, you MUST include the `--timeout=600` parameter:**
> ```bash
> php artisan queue:work --timeout=600
> ```

---

### 2.5 Multi-Service Verification

To ensure all system components are running seamlessly together, confirm the following processes are active:

| Service | Execution Command | Listening URL / Port | Status Check |
| :--- | :--- | :--- | :--- |
| **Laravel App** | `php artisan serve --port=8080` | `http://localhost:8080` | Access home page in browser |
| **Vite Asset Bundler** | `npm run dev` | `http://localhost:5173` | Check browser console for Vite HMR |
| **SARIMA FastAPI** | `uvicorn mainone:app --port 8000` | `http://localhost:8000` | Check `GET /attractions` |
| **Queue Worker** | `php artisan queue:work --timeout=600` | Process Listener | Check terminal for job execution |

---

## 3. Troubleshooting & Operational Maintenance

### 3.1 Common Issues & Solutions

#### Issue 1: Queue Worker Kills Retraining Job Prematurely
* **Symptom:** Retraining logs show `Job Timeout Exceeded` or retrain status hangs indefinitely.
* **Root Cause:** Laravel's default queue timeout (60s) is too short for SARIMA grid search.
* **Resolution:** Ensure the queue worker is executed with `--timeout=600`:
  ```bash
  php artisan queue:work --timeout=600
  ```

#### Issue 2: SARIMA Service Unreachable (502 Bad Gateway)
* **Symptom:** Admin dashboard reports `SARIMA API unreachable`.
* **Root Cause:** FastAPI microservice is offline or running on a non-standard port.
* **Resolution:** Verify that FastAPI is running on port 8000:
  ```bash
  cd sarimaforecasting
  uvicorn mainone:app --reload --port 8000
  ```
  Check `SARIMA_SERVICE_URL` in `.env` matches `http://localhost:8000`.

#### Issue 3: PWA Logs Not Syncing Offline Records
* **Symptom:** Logbook shows unsynced count badge even when connected to Wi-Fi.
* **Root Cause:** Browser service worker is unregistered or blocked by browser privacy settings.
* **Resolution:**
  1. Open Browser DevTools (F12) -> Application -> Service Workers.
  2. Click **Unregister** and refresh the page to reload the latest service worker build.
  3. Tap **"Sync Now"** in the logbook interface.

---

### 3.2 Command Quick Reference

| Operational Task | Command |
| :--- | :--- |
| **Start Laravel Web Server** | `php artisan serve --port=8080` |
| **Start Vite Frontend Bundler** | `npm run dev` |
| **Start Python FastAPI SARIMA** | `cd sarimaforecasting && uvicorn mainone:app --port 8000` |
| **Start Queue Worker (Required Timeout)** | `php artisan queue:work --timeout=600` |
| **Re-run Migrations & Seeders** | `php artisan migrate:refresh --seed` |
| **Clear Application Cache** | `php artisan config:clear && php artisan cache:clear` |

---
*Document prepared for academic submission and operational deployment of HinaTourist.*
