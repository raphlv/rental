<div align="center">

# ðŸš— Rental â€” Fleet & Asset Lease Management System

### *Automated Vehicle Reservations, Contract Agreements, & Maintenance Logs*

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-blue?style=for-the-badge)

---

</div>

## ðŸ“Œ About Rental

**Rental** is an enterprise fleet and equipment lease management application. It simplifies vehicle rental bookings, tracks unit availability calendars, automates legal lease agreements, and logs vehicle maintenance expenses.

---

## âœ¨ Key Features

### ðŸ“… 1. Availability Matrix & Booking Calendar
- Real-time schedule calendar showing booked vs available vehicles.
- Prevents double-booking conflicts across overlapping dates.
- Hourly, Daily, and Monthly rental package options.

### ðŸ“„ 2. Lease Contract & Invoice Generator
- Automatic PDF lease contract agreement generation with digital signature slot.
- Itemized billing (Rental rate + Driver fee + Security deposit + Taxes).
- Payment receipt issuance and overdue rental payment alerts.

### ðŸ”§ 3. Vehicle Maintenance & Expense Log
- Schedule routine servicing, oil changes, and inspection dates.
- Track maintenance costs per vehicle to analyze ROI and profitability.

---

## ðŸš€ Installation & Setup

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

## ðŸ“ License & Author

Distributed under the **MIT License**.

ðŸ‘¤ **Author**: [Pangeran Ryan Pahlevi](https://github.com/raphlv)  
âœ‰ï¸ **Email**: [pangeranryan080504@gmail.com](mailto:pangeranryan080504@gmail.com)  

---
<div align="center">
  <sub>Automated Sync Enabled for Contribution Tracking | Last Updated: 2026-08-18 14:37:15</sub>
</div>
