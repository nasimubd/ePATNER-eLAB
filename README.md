# PulseForge

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?logo=opensourceinitiative&logoColor=white)](./LICENSE)
[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20.svg?logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4.svg?logo=php&logoColor=white)](https://www.php.net)
[![Version](https://img.shields.io/badge/version-0.1.0-blue.svg?logo=git&logoColor=white)](https://github.com/nasimubd/PulseForge/releases)
[![Conventional Commits](https://img.shields.io/badge/commits-conventional-fe5196.svg)](https://www.conventionalcommits.org/)

**Open-source healthcare operations platform for hospitals, clinics, diagnostic centres, and other care facilities.**

PulseForge brings together patient administration, clinician scheduling, laboratory workflows, medical billing, financial ledgers, ward and operating-theatre bookings, and operational administration in one Laravel application.

Developed and maintained by [MD NASIM](https://github.com/nasimubd).

---

## Why PulseForge

Healthcare teams need patient, diagnostic, financial, and facility workflows to share the same operational context. PulseForge provides a connected model instead of isolated registers and manual reconciliation.

```text
                       PulseForge
                            │
        ┌───────────────────┼───────────────────┐
        │                   │                   │
   Clinical care       Diagnostics          Operations
        │                   │                   │
 Patients · Doctors    Lab tests · Reports  Wards · OT · Staff
 Appointments          Templates            Bookings
 Waiting lists         Medicines            Print requests
        │                   │                   │
        └───────────────────┼───────────────────┘
                            │
                      Financial control
             Invoices · Ledgers · Transactions · Payments
```

## Core capabilities

### Platform administration

* Multi-business healthcare-facility administration
* Role-based super-admin, administrator, manager, and laboratory workflows
* Staff and user-account management
* Global settings and letterhead administration
* Subscription status, payment submission, and approval workflows

### Patient and clinical operations

* Patient registration and facility-scoped patient identifiers
* Patient demographics, contact details, and profile images
* Doctor management, appointments, time slots, and schedule exceptions
* Calendar and waiting-list workflows

### Laboratory management

* Lab-test catalogue and associated medicines
* Reusable report templates, sections, and fields
* Patient lab tests and lab reports
* Lab-report printing and letterhead support
* Common-medicine administration and import/export workflows

### Billing and financial workflows

* Medical invoices and invoice line items
* Full, partial, and outstanding-payment tracking
* Care-of / commission relationships
* Facility ledgers
* Payment, receipt, journal, and contra transactions

### Facility and booking operations

* Ward services and service availability
* Operating-theatre rooms and services
* Capacity-aware ward booking
* Operating-theatre booking with equipment, preparation, and cleanup windows
* Booking status history and cancellation handling

### Reporting and print control

* Administrative dashboards and chart endpoints
* Top-sheet reporting and patient exports
* Invoice print requests and print allowances

---

## Healthcare operations model

```text
Business / Facility
   │
   ├── Users and Staff
   ├── Doctors
   │    ├── Time slots
   │    └── Schedule exceptions
   ├── Patients
   │    ├── Appointments
   │    ├── Waiting-list entries
   │    ├── Lab tests and reports
   │    └── Medical invoices
   ├── Laboratory
   │    ├── Lab tests
   │    ├── Report templates
   │    └── Reports
   ├── Financial operations
   │    ├── Ledgers
   │    ├── Transactions
   │    └── Payments
   └── Facility bookings
        ├── Ward services
        ├── OT rooms
        └── OT services
```

## Role model

PulseForge separates platform administration from facility operations through Laravel middleware and Spatie Laravel Permission.

```text
Super Admin
    │
    ├── Businesses / facilities
    ├── System settings
    ├── Subscriptions and payments
    ├── Common medicines
    └── Administrative accounts
            │
            ▼
Facility administration
    │
    ├── Patients and doctors
    ├── Appointments and waiting lists
    ├── Laboratory workflows
    ├── Invoices and ledgers
    ├── Ward and OT bookings
    └── Operational reporting
```

## Technology

### Backend

* PHP 8.2+
* Laravel 12
* Laravel Breeze, Pail, Pint, Sail, and Tinker
* Spatie Laravel Permission
* Maatwebsite Laravel Excel
* Barryvdh Laravel DomPDF
* Intervention Image
* Picqer barcode generator

### Frontend

* Vite
* Tailwind CSS
* Alpine.js
* Axios
* PostCSS
* Laravel Vite Plugin

### Testing

* PHPUnit
* Laravel feature and unit tests

## Architecture

```text
app/
├── Console/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   ├── Business/
│   │   └── SuperAdmin/
│   ├── Middleware/
│   └── Requests/
├── Models/
├── Policies/
└── Services/

database/
├── factories/
├── migrations/
└── seeders/

resources/
├── css/
├── js/
└── views/

routes/
├── auth.php
└── web.php
```

## Installation

### Requirements

* PHP 8.2 or later
* Composer
* Node.js and npm
* SQLite, MySQL, or another Laravel-supported database
* Git

### Setup

```bash
git clone https://github.com/nasimubd/PulseForge.git
cd PulseForge
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run build
```

Configure the database connection and other environment values in `.env`, then start the application:

```bash
php artisan serve
```

For frontend development:

```bash
npm run dev
```

For local demonstration data only, review the seeders before running them. Do not use development seed credentials in a production deployment.

## Development and testing

Run the local development processes together:

```bash
composer run dev
```

Run the test suite with:

```bash
php artisan test
```

## Project status

**PulseForge v0.1.0** documents the current open-source baseline. It includes healthcare-facility administration, patient and doctor management, scheduling, laboratory reporting, billing and ledger workflows, ward and operating-theatre booking, subscriptions, and print controls.

## Roadmap

Potential future areas include standards-based APIs, external laboratory integrations, patient-facing access, expanded clinical and pharmacy workflows, operational analytics, mobile applications, notifications, and deployment automation. These are future directions, not claims about functionality in the current release.

## Contributing

Contributions are welcome.

1. Create a focused branch.
2. Keep the change scoped to the problem being solved.
3. Add or update tests where appropriate.
4. Run the test suite and verify frontend assets build successfully.
5. Clearly explain the problem and implementation in the pull request.

## Security and clinical use

Do not commit credentials, patient data, production databases, private environment files, or other sensitive information.

This repository has not been represented as certified, compliant, or approved for any particular jurisdiction or clinical setting. Before using it with real patient data, perform an appropriate security, privacy, regulatory, deployment, backup, and access-control review for the intended environment.

For security vulnerabilities, use a private disclosure process rather than publishing exploitable details in a public issue.

## Maintainer

[MD NASIM](https://github.com/nasimubd)

## License

PulseForge is released under the MIT License.

Copyright © 2026 **MD NASIM**.

See the [`LICENSE`](./LICENSE) file for the complete license text.

## Citation

If you use PulseForge in research, academic work, technical documentation, or another software project, please cite the repository.

```bibtex
@software{pulseforge,
  title = {PulseForge: Open-source healthcare operations platform for hospitals and clinics},
  author = {MD NASIM},
  version = {0.1.0},
  year = {2026},
  url = {https://github.com/nasimubd/PulseForge}
}
```

## Project

**PulseForge** — Healthcare Operations Platform

Developed and maintained by [MD NASIM](https://github.com/nasimubd).

Copyright © 2026 MD NASIM.
Licensed under the MIT License.
