# 🏛️ EaseDocument — Official Barangay Document Services & Management Portal

[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B%20%7C%208.x-777BB4?style=flat&logo=php&logoColor=white)](https://www.php.net/)
[![Database](https://img.shields.io/badge/Database-MySQL%20%2F%20MariaDB-4479A1?style=flat&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![TailwindCSS](https://img.shields.io/badge/Styling-TailwindCSS-06B6D4?style=flat&logo=tailwindcss&logoColor=white)](https://tailwindcss.com/)
[![Security](https://img.shields.io/badge/Security-Rate%20Limited%20%7C%20CSRF%20%7C%20Bcrypt-2ea44f?style=flat&logo=shield)](https://github.com/)
[![License](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

**EaseDocument** is a comprehensive, production-ready e-governance and document issuance platform designed specifically for Philippine **Barangay Local Government Units (LGUs)**. 

The system transitions traditional manual desk processing to an automated, transparent, and secure digital portal—allowing constituents to apply for official barangay documents online while providing barangay officials with a streamlined administrative dashboard for verification, processing, and one-click official document generation.

---

## Table of Contents

- [Key Highlights](#-key-highlights)
- [System Architecture & Portals](#-system-architecture--portals)
  - [1. Public Citizen Portal](#1-public-citizen-portal)
  - [2. Resident Portal](#2-resident-portal)
  - [3. Administrative Management Portal](#3-administrative-management-portal)
- [Document Services Supported](#-document-services-supported)
- [Security & Architecture Standards](#-security--architecture-standards)
- [Technology Stack](#-technology-stack)
- [Directory Structure](#-directory-structure)
- [Database Schema Overview](#-database-schema-overview)
- [Installation & Setup Guide](#-installation--setup-guide)
  - [Prerequisites](#prerequisites)
  - [Step-by-Step Installation](#step-by-step-installation)
  - [Environment Configuration](#environment-configuration)
  - [Creating the First Administrator](#creating-the-first-administrator)
- [Configuration & Environment Variables](#-configuration--environment-variables)
- [Contributing & Development Guidelines](#-contributing--development-guidelines)
- [License](#-license)

---

## Key Highlights

- **Automated Document Processing**: End-to-end management for Barangay Clearances, Indigency Certificates, Residency Certifications, and Barangay IDs.
- *Digital Resident Verification**: Identity validation workflow requiring government-issued IDs and residency proofs before issuance.
- **Philippine Standard Geographic Code (PSGC)**: Seamless cascading dropdowns for Region, Province, City/Municipality, and Barangay powered by local JSON datasets.
- **Printable Document Templates**: Pre-formatted, print-ready document layouts with barangay letterheads, dry seal placeholders, resident photographs, and authorized signatures.
- **Community Announcement Bulletin**: Public information board featuring categorized advisories (Emergency, Health, General, Event), pinned broadcasts, and image attachments.
- **Flexible Fulfillment**: Support for on-site barangay pickup or door-to-door delivery with automatic fee calculations.
- **Enterprise-Grade Security**: Brute-force rate limiting, password hashing via Bcrypt, session fixation protection, and script execution prevention in upload directories.
---

## System Architecture & Portals

EaseDocument is partitioned into three dedicated layers tailored for public access, verified constituents, and authorized barangay personnel:

```
┌────────────────────────────────────────────────────────────────────────┐
│                        EaseDocument Web System                         │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
       ┌────────────────────────────┼────────────────────────────┐
       ▼                            ▼                            ▼
┌───────────────┐           ┌───────────────┐           ┌─────────────────┐
│ Public Portal │           │ Resident Hub  │           │   Admin Suite   │
│  (index.html) │           │  (resident/)  │           │    (admin/)     │
├───────────────┤           ├───────────────┤           ├─────────────────┤
│ • Services    │           │ • Online Form │           │ • KPI Analytics │
│ • Requirements│           │ • Live Status │           │ • ID Validation │
│ • Public Feed │           │ • ID Uploads  │           │ • Request Queue │
│ • Guidelines  │           │ • Profile Mgt │           │ • Doc Generator │
│ • FAQ/Contact │           │ • Bulletins   │           │ • Announcements │
└───────────────┘           └───────────────┘           └─────────────────┘
```

### 1. Public Citizen Portal (`index.html`)
- **Interactive Service Catalog**: Outlines available clearances, requirements, processing fees, and standard turnaround times.
- **Requirements Checklist**: Informs residents what IDs and documents are necessary prior to applying.
- **Latest Barangay Bulletins**: Real-time broadcast of official notices, weather alerts, and community assemblies.
- **Responsive Navigation**: Accessibility-focused navigation optimized for mobile devices, tablets, and desktop workstations.

### 2. Resident Portal (`resident/`, `login.html`)
- **Self-Service Registration**: Multi-step registration incorporating cascading PSGC address selectors and valid ID file attachments.
- **Document Request Wizard**:
  - Request type selection with custom purpose fields.
  - Digital signature capture and 1x1 photo upload.
  - Delivery method selection (Pick-up at Barangay Hall or Home Delivery).
- **My Requests Tracker (`MyRequest.php`)**:
  - Real-time status indicators: `Pending`, `Processing`, `Approved`, `Released`, `Declined`.
  - Transaction reference codes (`cr_code`) for tracking and audit.
  - Breakdown of fees (service fee, delivery charges, total).
- **Constituent Bulletins (`announcements.php`)**: Category-filtered updates, pinned important notices, and viewable media.
- **Profile & Security Settings (`account_setting.php`)**: Update contact information, address details, and update passwords securely.

### 3. Administrative Management Portal (`admin/`, `Admin.html`)
- **Executive Analytics Dashboard (`index.php`)**:
  - Real-time counters: Total Residents, Pending Verifications, Active Requests, and Completed Documents.
  - Status breakdown charts and recent application streams.
- **Resident Verification Hub (`resident.php`)**:
  - Audit submitted resident applications and inspect uploaded IDs.
  - Toggle resident status (`1 = Verified`, `2 = Not Verified/Pending`, `0 = Archived`).
- **Centralized Document Request Manager (`request.php`, `view_orders.php`)**:
  - Filter applications by document type, processing status, and date.
  - Approve or decline requests with custom audit notes and remarks.
- **One-Click Official Document Generator (`templates/`)**:
  - Instant generation of printable official documents:
    - `barangay_clearance.php`: Official Barangay Clearance with control numbering.
    - `barangay_id.php`: Dual-sided Barangay ID with photo, contact info, and signatures.
    - `barangay_indigency.php`: Certificate of Indigency for scholarship/medical assistance.
    - `barangay_residency.php`: Certificate of Residency for formal proofs.
- **Announcement Management System (`announcements.php`)**:
  - Rich announcement publisher with categories (Advisory, Health, General, Calamity).
  - Pin crucial updates to the top of constituent feeds.
  - Image banner uploads with automatic sanitization.

---

## Document Services Supported

| Document Type | Purpose / Typical Usage | Required Attachments |
|---|---|---|
| **Barangay Clearance** | Job employment, business permits, government transactions, postal ID | Valid ID, 1x1 ID Picture, Proof of Residency |
| **Certificate of Indigency** | Medical assistance, educational scholarship, financial aid, PAO legal aid | Valid ID, Proof of Residency |
| **Certificate of Residency** | Bank account opening, proof of billing address, police clearance | Valid ID, Proof of Residency |
| **Barangay Identification Card** | Official local ID card, emergency contact, local senior/youth perks | 1x1 ID Picture, Digital Signature, Valid ID |

---

## Security & Architecture Standards

EaseDocument adheres to web application security practices:

- **Brute-Force & Rate Limiting**:
  - Built-in sliding window rate limiter (`checkRateLimit`) tracking client IP addresses.
  - Blocks automated credential-stuffing attacks after 5 failed attempts with a 5-minute lockout timer.
- **Cryptographic Security**:
  - All resident and administrator passwords are encrypted using PHP's native `password_hash()` (Bcrypt).
  - Secure verification using `password_verify()`.
- **Session Hijacking & Fixation Defense**:
  - Calls `session_regenerate_id(true)` upon every authenticated state change.
  - Sanitized session state tracking.
- **Hardened Upload Directory (`uploads/.htaccess`)**:
  - Strict Apache directive prevents PHP and CGI script execution within `uploads/`.
  - Enforces strict whitelist of allowable media formats (`jpg`, `jpeg`, `png`, `webp`, `pdf`).
  - Sets security headers including `X-Content-Type-Options: nosniff`.
- **SQL Injection Prevention**:
  - Database queries are parameterized using `mysqli` and `PDO` prepared statements.
- **Environment Isolation**:
  - Sensitive database credentials are isolated in `.env` and loaded securely via `config/config.php`.

---

## 💻 Technology Stack

### Backend
- **Language**: PHP 7.4+ / PHP 8.x
- **Database**: MySQL 5.7+ / MariaDB 10.4+
- **Architecture**: Modular Object-Oriented PHP (`global_class`, `db_connect`)
- **API Endpoints**: RESTful JSON response controllers (`controller.php`)

### Frontend
- **CSS Framework**: Tailwind CSS (Utility-First Responsive Design)
- **UI Components & Icons**: Heroicons / FontAwesome / Inter Typography
- **Client Notifications**: AlertifyJS (Toast notifications & confirmation dialogues)
- **Geographic Data**: Philippine Standard Geographic Code (PSGC) JSON library

---

## Directory Structure

```text
EaseDocument/
├── .env.example              # Template for environment variables
├── .htaccess                 # Root Apache security configuration
├── Admin.html                # Administrator login entrypoint
├── index.html                # Public-facing citizen portal landing page
├── login.html                # Resident login & registration modal
│
├── admin/                    # Administrative Control Suite
│   ├── index.php             # Executive dashboard & metrics
│   ├── resident.php          # Resident verification & management
│   ├── request.php           # Centralized document request queue
│   ├── view_orders.php       # Detailed order view & status management
│   ├── announcements.php     # Announcement publisher & CMS
│   ├── settings.php          # Admin account & profile settings
│   ├── logout.php            # Secure administrative sign-out
│   ├── backend/              # Admin-specific classes and API endpoints
│   ├── components/           # Shared UI navigation & sidebar components
│   └── templates/            # Printable official document generators
│       ├── barangay_clearance.php
│       ├── barangay_id.php
│       ├── barangay_indigency.php
│       └── barangay_residency.php
│
├── resident/                 # Constituent Portal
│   ├── index.php             # Document request catalog & application forms
│   ├── MyRequest.php         # Request status tracker & history
│   ├── announcements.php     # Public bulletin feed for residents
│   ├── account_setting.php   # Resident profile & password management
│   ├── logout.php            # Secure resident sign-out
│   ├── backend/              # Resident-specific classes & controllers
│   └── components/           # Resident header, navigation, and footer
│
├── backend/                  # Shared Core Backend Logic
│   ├── class.php             # Main controller logic, auth, rate limiting
│   ├── db.php                # Database connection initialization
│   └── end-points/           # JSON API endpoints
│       └── controller.php
│
├── config/                   # Configuration Loader
│   ├── config.php            # Parses .env file and sets environment constants
│   └── database.php          # PDO / MySQLi connection helpers
│
├── core/                     # Modular Security & Utility Classes
│   ├── Auth.php              # Authentication and session manager
│   ├── Csrf.php              # CSRF token generation & validation
│   ├── Database.php          # PDO singleton wrapper
│   ├── FileUploader.php      # Upload validator, sanitization & storage
│   └── Response.php          # Standardized JSON response emitter
│
├── database/                 # Database Schema & Migrations
│   ├── schema.sql            # Full MySQL database table structures
│   └── .htaccess             # Protects SQL dumps from direct HTTP access
│
├── ph-json/                  # PSGC Philippine Geographic Datasets
│   ├── region.json           # Regions of the Philippines
│   ├── province.json         # Provinces
│   ├── city.json             # Cities and Municipalities
│   └── barangay.json         # Barangays
│
└── uploads/                  # User Uploads Directory (Protected via .htaccess)
    ├── announcements/        # Announcement banners and attachments
    ├── clearance/            # Barangay clearance request attachments
    ├── id/                   # Barangay ID pictures and signatures
    ├── residency/            # Residency certificates proofs
    ├── resident/             # Resident profile pictures
    └── resident_id/          # Resident valid government ID uploads
```

---

## Database Schema Overview

The system operates around 4 primary tables defined in `database/schema.sql`:

1. **`user`**: Stores administrative and staff credentials, roles (`user_type`), and account states.
2. **`resident`**: Stores resident information including complete demographic profile, PSGC address breakdown, ID attachments, and verification status (`r_status`: `0`=Archived, `1`=Verified, `2`=Pending).
3. **`centralize_request`**: Stores document requests, unique tracking codes (`cr_code`), uploaded signatures/photos, fulfillment type, fee breakdowns, and real-time status (`Pending`, `Processing`, `Approved`, `Released`, `Declined`).
4. **`announcements`**: Community bulletin board entries with titles, rich content, category tags, pinned flag (`is_pinned`), and banner images.

---

## Installation & Setup Guide

### Prerequisites
- **PHP**: Version 7.4 or 8.x with `mysqli`, `pdo_mysql`, `mbstring`, and `fileinfo` extensions enabled.
- **Web Server**: Apache 2.4+ (or Nginx) / XAMPP / WAMP / PHP Built-in Server.
- **Database**: MySQL 5.7+ or MariaDB 10.4+.

---

### Step-by-Step Installation

#### 1. Clone or Download the Repository
```bash
git clone https://github.com/CharlesMiranda13/EaseDoc.git
cd EaseDoc
```

#### 2. Configure Environment Variables
Copy the `.env.example` template to `.env` in the root directory:

**Windows (PowerShell):**
```powershell
Copy-Item .env.example .env
```

**Linux / macOS:**
```bash
cp .env.example .env
```

Open `.env` and configure your database credentials:
```ini
DB_HOST=localhost
DB_USER=root
DB_NAME=u800275806_easedocument
DB_PASS=
```

#### 3. Import Database Schema
1. Open your database management tool (e.g., **phpMyAdmin** or MySQL CLI).
2. Create a new database named `u800275806_easedocument` (or match the name configured in your `.env`):
   ```sql
   CREATE DATABASE IF NOT EXISTS `u800275806_easedocument` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
   ```
3. Import the file located at [`database/schema.sql`](file:///database/schema.sql).

**Using MySQL CLI:**
```bash
mysql -u root -p u800275806_easedocument < database/schema.sql
```

#### 4. Configure Directory Permissions
Ensure that the web server has write permissions to the `uploads/` directory and its subfolders:

**Linux / macOS:**
```bash
chmod -R 775 uploads/
chown -R www-data:www-data uploads/
```

#### 5. Launch the Application

##### Option A: Using PHP Built-in Server (Development)
```powershell
php -S localhost:8000
```
Navigate to `http://localhost:8000` in your web browser.

##### Option B: Using XAMPP / WAMP / Apache
1. Move the `EaseDocument` directory into your web root (e.g., `C:\xampp\htdocs\EaseDocument`).
2. Start Apache and MySQL in your XAMPP Control Panel.
3. Access the portal at `http://localhost/EaseDocument/index.html`.

---

### Creating the First Administrator

Because administrator passwords require Bcrypt hashing, generate your initial admin account using the following SQL snippet or via a small PHP helper:

```sql
INSERT INTO `user` (`user_id`, `user_fname`, `user_mname`, `user_lname`, `user_email`, `user_password`, `user_type`, `user_status`) 
VALUES (
  1, 
  'Admin', 
  '', 
  'Officer', 
  'admin@easedocument.gov.ph', 
  -- Default password below corresponds to: Admin@12345
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
  'Admin', 
  'Active'
);
```

> [!TIP]
> After logging in at `Admin.html`, immediately navigate to **Settings** (`admin/settings.php`) to update your administrative email and password.

---

## Configuration & Environment Variables

All global configuration values are set inside the `.env` file at the root:

| Key | Description | Default Value |
|---|---|---|
| `DB_HOST` | Database host server | `localhost` |
| `DB_USER` | MySQL database user | `root` |
| `DB_NAME` | MySQL database name | `u800275806_easedocument` |
| `DB_PASS` | MySQL database password | *(empty for local XAMPP)* |

---


---

## Contributing & Development Guidelines

1. **Branching Strategy**: Create feature branches from `main` (`feature/your-feature-name`).
2. **Prepared Statements**: Never concatenate SQL queries directly. Always utilize prepared statements (`bind_param` or PDO equivalents).
3. **Password Security**: Never store plain text passwords; always use `password_hash()` with `PASSWORD_DEFAULT` or `PASSWORD_BCRYPT`.
4. **Rate Limiting**: Always register new authentication or sensitive actions with `$this->checkRateLimit()`.

---

## License

This project is licensed under the **MIT License**. You are free to modify, deploy, and distribute this software for community and local government operations.

---

*Developed for Philippine Barangay Local Government Units.*  
*EaseDocument — Digitizing Barangay Public Services.*
