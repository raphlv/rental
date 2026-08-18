<div align="center">

# Fleet & Asset Rental Management System

### *Comprehensive Vehicle & Asset Lease Management*

![PHP](https://img.shields.io/badge/PHP-informational?style=for-the-badge&logo=PHP&logoColor=white) ![Laravel](https://img.shields.io/badge/Laravel-informational?style=for-the-badge&logo=Laravel&logoColor=white) ![MySQL](https://img.shields.io/badge/MySQL-informational?style=for-the-badge&logo=MySQL&logoColor=white) ![Bootstrap](https://img.shields.io/badge/Bootstrap-informational?style=for-the-badge&logo=Bootstrap&logoColor=white) ![JavaScript](https://img.shields.io/badge/JavaScript-informational?style=for-the-badge&logo=JavaScript&logoColor=white)

![Build Status](https://img.shields.io/badge/Build-Passing-brightgreen?style=for-the-badge)
![License](https://img.shields.io/badge/License-MIT-blue?style=for-the-badge)
![Maintained](https://img.shields.io/badge/Maintained-Yes-orange?style=for-the-badge)

---

</div>

## ðŸ“Œ Overview

Rental is an enterprise inventory and booking management platform designed for vehicle rental services, equipment leases, schedule tracking, and automated billing.

Developed to provide a robust, clean, and production-ready architecture tailored for Rental & Asset Management requirements.

---

## âœ¨ Key Features

- **Vehicle & Asset Availability Matrix Calendar**
- **Automated Lease Contract Agreement & PDF Invoice Generator**
- **Dynamic Daily/Weekly Package Rate Calculator**
- **Vehicle Maintenance Log & Operational Expense Tracker**
- **Customer Verification & Driving License Archiving**

---

## ðŸ› ï¸ Technology Stack

| Component | Technologies Used |
| :--- | :--- |
| **Backend & Framework** | PHP / Node.js / Laravel / Modular Architecture |
| **Frontend** | HTML5, CSS3, JavaScript (ES6+), Bootstrap / Tailwind CSS |
| **Database** | MySQL / MariaDB / Relational Schema |
| **Tools & Version Control** | Git, Composer, NPM, Laragon / Web Server |

---

## ðŸ“‚ Project Architecture

`
rental/
â”œâ”€â”€ app/               # Core application logic & controllers
â”œâ”€â”€ config/            # System & environment configuration
â”œâ”€â”€ database/          # Database migrations, seeders & schema
â”œâ”€â”€ public/            # Public web assets (CSS, JS, Images)
â”œâ”€â”€ resources/         # Views, templates & raw assets
â”œâ”€â”€ routes/            # Web and API routing definitions
â”œâ”€â”€ storage/           # Logs, cache & application uploads
â”œâ”€â”€ README.md          # Project documentation
â””â”€â”€ .gitignore         # Git repository exclusions
`

---

## ðŸš€ Getting Started

### Prerequisites

Ensure you have the following installed on your local environment:
- **PHP** >= 8.0 or **Node.js** >= 16.x
- **Composer** / **NPM**
- **MySQL** / **MariaDB**
- Web Server (**Laragon** / **XAMPP** / **Apache** / **Nginx**)

### Installation Steps

1. **Clone the repository**
   `ash
   git clone https://github.com/raphlv/rental.git
   cd rental
   `

2. **Install Dependencies**
   `ash
   composer install
   # or
   npm install
   `

3. **Environment Configuration**
   Copy the .env.example file and configure your database settings:
   `ash
   cp .env.example .env
   `

4. **Database Setup & Migration**
   `ash
   php artisan migrate --seed
   `

5. **Run Local Development Server**
   `ash
   php artisan serve
   # or start via Laragon virtual host: http://rental.test
   `

---

## ðŸ¤ Contributing

Contributions, issues, and feature requests are welcome! Feel free to check the [issues page](https://github.com/raphlv/rental/issues).

1. Fork the Project
2. Create your Feature Branch (git checkout -b feature/AmazingFeature)
3. Commit your Changes (git commit -m 'Add some AmazingFeature')
4. Push to the Branch (git checkout -b feature/AmazingFeature)
5. Open a Pull Request

---

## ðŸ“ License & Author

Distributed under the **MIT License**. See LICENSE for more information.

ðŸ‘¤ **Author**: [Pangeran Ryan Pahlevi](https://github.com/raphlv)  
âœ‰ï¸ **Email**: [pangeranryan080504@gmail.com](mailto:pangeranryan080504@gmail.com)  

---
<div align="center">
  <sub>Automated Sync Enabled for Contribution Tracking | Last Updated: 2026-08-18 14:20:38</sub>
</div>
