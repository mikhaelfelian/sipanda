# Medkit 3

Medkit 3 is a mini SIMRS (Sistem Informasi Manajemen Rumah Sakit) application developed specifically for Klinik Esensia Semarang.

## Features

- Master data management (obat, kategori, merk, satuan)
- Inventory management with multi-warehouse support
- Purchase order and receiving
- Sales and billing
- Patient records
- Prescription management
- User management with role-based access control
- Reports and analytics

## Technology Stack

- PHP 7.4+ with CodeIgniter 4 framework
- MySQL/MariaDB database
- AdminLTE 3 template
- jQuery and Bootstrap 4

## Installation

1. Clone this repository
2. Run `composer install`
3. Copy `env` to `.env` and configure your database settings
4. Run `php spark migrate` to create database tables
5. Run `php spark db:seed` to populate initial data
6. Configure your web server to point to the `public` directory

## Requirements

- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.2+
- Apache/Nginx web server
- Composer

## Core Framework

This project is built with CodeIgniter 4, which requires:

- PHP 7.4 or higher
- Composer for dependency management
- CodeIgniter 4 framework
- MySQL/MariaDB database

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.

## Authors

- Mikhael Felian Waskito

## Libraries
- CodeIgniter team
- Ion Auth
- AdminLTE contributors
- All open source packages used in this project
- FPDF
- PHPExcel

## Changelog
```
### 2024-10-10
- Added filter by status in obat management
- Fixed bug in stock calculation
- Improved obat form validation
- Added item alias and kandungan fields

### 2024-10-15
- Enhanced obat management features
- Added stockable flag for items
- Improved price formatting
- Added initial stock creation for new items

### 2024-10-16
- Added CRUD operations for obat (medicines)
- Integrated with gudang (warehouse) module
- Added price management features
- Implemented soft deletes

### 2024-10-18
- Initial release
- Basic authentication system
- Admin dashboard
- User management
- Basic inventory system

```
