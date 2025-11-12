<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>


## Asset Management System

A Laravel-based web application for real estate property management, rental tracking, and automated payment processing.


## Project Overview

The Asset Management System (AMS) is designed to streamline property management operations. It provides property managers with tools to manage their real estate portfolio, track rental units, handle tenant relationships, and automate contract and payment workflows.

The system uses a flexible polymorphic architecture that allows it to manage different asset types (currently focused on real estate properties, with the ability to expand to stocks, vehicles, etc.).


## Core Entities

Users 
System users with two primary roles:

Managers: Property owners with full control over their assets
Agents: Assigned representatives with limited access to specific properties



Assets (Polymorphic)
A flexible parent entity that can represent any asset type. Currently implemented for:

Real estate properties
Future-ready for stocks, vehicles, or other asset types



Properties
Real estate holdings with location tracking:

Country, City, Neighborhood hierarchy
Geographic coordinates support
Total area measurement
Contains multiple rental units



Units
Individual rentable or sellable spaces within properties:

Types: Villa, Apartment, Office, Warehouse, Store
Status: Available, Rented, Sold, Under Maintenance
Detailed specifications (area, description)



Tenants
Individuals or entities renting units:

Personal information and documentation
National ID and contact details
Multiple contract history per tenant



Contracts
Rental agreements between managers and tenants:

Date range management (start, end, termination)
Payment plan configuration (monthly, quarterly, semiannual, annual)
Status: Pending, Active, Expired, Terminated
Automatic payment schedule generation



Payments
Individual payment installments for each contract:

Sequential numbering per contract
Due dates and actual payment tracking
Status: Pending, Paid, Overdue, Cancelled
Amount tracking (expected vs. actual)

<!-- ## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT). -->
