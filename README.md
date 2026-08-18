<div align="center">

# Rental - Fleet and Asset Lease Management System

### *Automated Vehicle Reservations, Contract Agreements, and Maintenance Logs*

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-blue?style=for-the-badge)

---

</div>

## Overview

Rental is an enterprise fleet and equipment lease management application. It simplifies vehicle rental bookings, tracks unit availability calendars, automates legal lease agreements, and logs vehicle maintenance expenses.

---

## Key Features

### 1. Availability Matrix and Booking Calendar
- Real-time schedule calendar showing booked vs available vehicles.
- Prevents double-booking conflicts across overlapping dates.
- Hourly, Daily, and Monthly rental package options.

### 2. Lease Contract and Invoice Generator
- Automatic PDF lease contract agreement generation with digital signature slot.
- Itemized billing (Rental rate + Driver fee + Security deposit + Taxes).
- Payment receipt issuance and overdue rental payment alerts.

### 3. Vehicle Maintenance and Expense Log
- Schedule routine servicing, oil changes, and inspection dates.
- Track maintenance costs per vehicle to analyze ROI and profitability.

---

## Installation and Setup

`ash
git clone https://github.com/raphlv/rental.git
cd rental

composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate

# Import rental_db.sql into MySQL or run artisan migrate:
php artisan migrate --seed
php artisan serve
`

---

## License and Author

Distributed under the MIT License.

Author: Pangeran Ryan Pahlevi (https://github.com/raphlv)  
Email: pangeranryan080504@gmail.com  

---
<div align="center">
  <sub>Automated Sync Enabled for Contribution Tracking | Last Updated: 2026-08-18 14:40:47</sub>
</div>