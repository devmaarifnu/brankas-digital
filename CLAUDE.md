# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**SIPINTER LP Ma'arif NU PBNU** — Sistem Informasi Pendataan Terintegrasi. A Laravel 10 web application for integrated educational data management, connecting schools (Satuan Pendidikan / SATPEN) under LP Ma'arif NU with the central organization (PBNU) through multi-level administration.

## Stack

- **Backend**: Laravel 10, PHP 8.1+, MySQL 8.0+
- **Frontend**: Blade templates, Bootstrap 5, Vite 4, jQuery, ApexCharts
- **Auth**: Session-based (web) + Laravel Sanctum (API)
- **Notable packages**: `maatwebsite/excel`, `endroid/qr-code`, `phpoffice/phpword`, `alkoumi/laravel-hijri-date`

## Common Commands

```bash
# Development
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
php artisan serve          # runs on http://localhost:8000
npm run dev                # Vite dev server (run alongside artisan serve)

# Build
npm run build              # production asset build

# Testing
php artisan test                        # all tests
php artisan test tests/Feature/ExampleTest.php  # single test file
vendor/bin/phpunit --filter TestName    # single test by name

# Code style
vendor/bin/pint             # fix PHP code style (Laravel Pint)
vendor/bin/pint --test      # check without fixing

# Production caching
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Architecture

### User Roles & Middleware

Five roles enforced via dedicated middleware in `app/Http/Middleware/`:

| Role | Scope | Middleware |
|------|-------|-----------|
| Super Admin | PBNU central, full access | `SuperAdmin` |
| Admin Pusat | Central office | `OnlyAdmin` |
| Admin Wilayah | Provincial level | `OnlyAdminWilayah` |
| Admin Cabang | District level | `OnlyAdminCabang` |
| Operator | Individual school | `OnlyOperator` |

`MustLogin` middleware gates all protected routes. Role checks are applied per route group in `routes/web.php`.

### Core Modules

- **SATPEN** — School/institution master data
- **PTK** — Teachers & education personnel (Pendidik dan Tenaga Kependidikan)
- **NPYP** — Student records (Nomor Pokok Yayasan Pendidikan)
- **OSS** — Business licensing data (Online Single Submission)
- **BHPNU** — Management fee / iuran payments
- **Coretax** — Tax compliance data

### Key Directories

- `app/Http/Controllers/Admin/` — Admin panel controllers, one per module
- `app/Http/Controllers/Api/` — API endpoints (minimal, mostly for mobile)
- `app/Models/` — 34+ Eloquent models; primary keys are named `id_*` (e.g., `id_satpen`, `id_user`)
- `app/Exports/` — Excel export classes using `maatwebsite/excel`
- `app/Helpers/` — Service helpers: `DapoMaarifNU`, `DapoKemdikbud` (external data sync), `Gdrive`, `GenerateQr`, `MailService`, `ReferensiKemdikbud`
- `app/Observers/GlobalModelObserver` — Single observer wired to all models for audit logging
- `resources/views/` — Blade views organized by module (admin/, satpen/, ptk/, bhpnu/, oss/, coretax/, etc.)
- `routes/web.php` — Primary route file (372 lines); `routes/api.php` is minimal

### Data Flow Patterns

External data can be pulled from two sources via helpers:
- **Dapodik (Kemdikbud)** — `DapoKemdikbud` helper scrapes/fetches MoE national school data
- **Dapo Ma'arif** — `DapoMaarifNU` helper syncs internal network data

Excel exports follow a consistent pattern: controller calls an `Export` class in `app/Exports/`, returns `Excel::download(new SomeExport($params), 'filename.xlsx')`.

### Environment Variables to Set

Beyond standard Laravel vars, ensure these are configured in `.env`:

```
MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_USERNAME=...
MAIL_PASSWORD=...

# Google Drive (for file storage helpers)
GOOGLE_DRIVE_CLIENT_ID=
GOOGLE_DRIVE_CLIENT_SECRET=
GOOGLE_DRIVE_REFRESH_TOKEN=
GOOGLE_DRIVE_FOLDER_ID=
```
