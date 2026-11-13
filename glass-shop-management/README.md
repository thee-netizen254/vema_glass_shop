# Glass Shop Management System (starter)

A PHP + MySQL starter for XAMPP. Includes role-based login, dashboard, product/stock management,
sales, purchases, customer order submission, notification consent fields, audit trail and OTP scaffolding.

## Requirements
- XAMPP with PHP 8.1+ and MySQL/MariaDB
- A modern browser
- Optional: internet connection and an SMS provider account for online SMS/email
- Optional: GSM modem + SIM + compatible local SMS daemon for offline SMS (adapter not included)

## Install
1. Copy this folder into `C:\xampp\htdocs\glass-shop-management`.
2. Start Apache and MySQL in XAMPP.
3. Open `http://localhost/phpmyadmin`, create/import database by importing `database.sql`.
4. Edit `config.php` with your MySQL credentials.
5. Open `http://localhost/glass-shop-management/setup_admin.php` once to create the first admin.
6. Delete `setup_admin.php` after creating the admin.
7. Open `http://localhost/glass-shop-management/`.

## Important
This is a learning/MVP starter, not a production-ready accounting or security system. Before real business use:
- configure HTTPS, backups, strong unique passwords, CSRF protection, rate limiting and secure server hosting;
- finish and test OTP delivery adapters;
- add tax/invoice rules appropriate to your business;
- restrict public access to internal staff pages;
- test stock adjustments and sales with sample data.

## OTP and notifications
Admin/manager accounts are designed to require OTP after password login. This starter creates the OTP and displays it only when `APP_DEBUG_OTP=true` in config (local testing). For real use, connect a vetted SMS/email provider in `send_notification()` in `functions.php`. Do not leave debug OTP enabled in a live system.

For offline SMS, a computer needs a GSM modem/SIM and local modem software. An offline website cannot send WhatsApp messages or email; those require internet. Queue messages while offline and deliver them when connectivity returns.

## GitHub
Use GitHub to store source code and track changes. GitHub Pages cannot run PHP/MySQL. For a public live website, deploy PHP to a PHP-capable host and MySQL to a database server. Keep credentials out of GitHub.
