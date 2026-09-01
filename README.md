# 🌸 Scent — Perfume E-Commerce Platform

A full-featured e-commerce web application for browsing and purchasing perfumes, built as a school project using vanilla PHP, MySQL, HTML, and CSS with a custom MVC-style architecture.

## Overview

Scent lets customers browse perfumes across multiple scent families (aquatic, woody, citrus, oriental, gourmand, chypre, and more) for men, women, and unisex categories, manage a shopping cart and wishlist, and complete orders — while an admin dashboard handles product, order, and customer management on the backend.

## Features

**Customer-facing**
- Browse products by category, brand, gender, and scent family
- Product search
- Shopping cart and checkout flow
- Wishlist
- User registration, login, and profile management
- Order history and order status tracking (delivered / cancelled / pending)
- Promo code support

**Admin dashboard**
- Product management (add/edit inventory)
- Order management
- Customer management
- Brand and category management
- Discount/promo code management
- Sales and inventory overview

## Tech Stack

- **Backend:** PHP (custom MVC structure — Controllers, Models, Views)
- **Database:** MySQL (schema, stored procedures, and triggers included)
- **Frontend:** HTML, CSS, JavaScript
- **Architecture:** Custom router and core application class (no framework)

## Project Structure

```
scent/
├── app/
│   ├── controllers/     # Handles requests (Admin, Cart, Orders, Products, User, Wishlist)
│   ├── core/             # Router and core app logic
│   ├── models/           # Data models (Product, User, Order, Cart, Brand, Category...)
│   ├── views/            # Page templates (admin, cart, orders, products, user...)
│   └── helpers/          # Cookie and validation helpers
├── config/                # Database and session configuration
├── database/              # SQL schema, triggers, stored procedures, seed data
├── public/                # Images and static assets
├── css/                   # Stylesheets
└── index.php              # Application entry point
```

## Getting Started

This project requires a local PHP + MySQL environment (like **XAMPP**) since it's a server-rendered PHP app.

1. **Install [XAMPP](https://www.apachefriends.org/)** (includes PHP, MySQL/MariaDB, and Apache)
2. Clone this repo into your XAMPP `htdocs` folder:
   ```
   git clone https://github.com/rokyyy55/SCENT.git
   ```
3. Start **Apache** and **MySQL** from the XAMPP control panel
4. Create a database in phpMyAdmin and import the schema from `database/schema.sql` (and `triggers.sql`, `procedures.sql`, `insertion.sql` if needed)
5. Update your database credentials in `config/database.php`
6. Visit `http://localhost/scent` in your browser

> ⚠️ Do not open `index.php` directly as a file — it must be served through Apache/XAMPP to work correctly.

## Notes

This was built as a school project to practice full-stack development without a framework — implementing routing, authentication, and a complete e-commerce flow from scratch in raw PHP.

## Author

**Hana Toubal**
