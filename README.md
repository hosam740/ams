<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>




<!-- ## Asset Management System (AMS)

A Laravel-based web application for real estate property management, rental tracking, and automated payment processing.

--- -->

## Project Overview

The **Asset Management System (AMS)** is designed to streamline property management operations.  
It provides property managers with the tools to:

- Manage their real estate portfolio.  
- Track rental units.  
- Handle tenant relationships.  
- Automate contract and payment workflows.

The system uses a flexible polymorphic architecture that allows it to manage different asset types.  
Currently, it focuses on real estate properties, with the ability to expand to stocks, vehicles, or other asset types in the future.

---

## Core Entities

### Users
System users with two primary roles:

- **Managers:** Property owners with full control over their assets.
- **Agents:** Assigned representatives with limited access to specific properties.

---

### Assets (Polymorphic)
A flexible parent entity that can represent any asset type.  
Currently implemented for:

- Real estate properties.
- Future-ready for stocks, vehicles, or other asset types.

---

### Properties
Real estate holdings with location tracking:

- Country, City, Neighborhood.
- Total area measurement.
- Contains multiple rental units.

---

### Units
Individual rentable or sellable spaces within properties:

- **Types:** Villa, Apartment, Office, Warehouse, Store.
- **Status:** Available, Rented, Sold, Under Maintenance.
- Detailed specifications (area, description).

---

### Tenants
Individuals or entities renting units:

- Personal information and documentation.
- National ID and contact details.
- Multiple contract history per tenant.

---

### Contracts
Rental agreements between managers and tenants:

- Date range management (start, end, termination).
- Payment plan configuration (monthly, quarterly, semiannual, annual).
- **Status:** Pending, Active, Expired, Terminated.
- Automatic payment schedule generation.

---

### Payments
Individual payment installments for each contract:

- Sequential numbering per contract.
- Due dates and actual payment tracking.
- **Status:** Pending, Paid, Overdue, Cancelled.
- Amount tracking (expected vs. actual).
