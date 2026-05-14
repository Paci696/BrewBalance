# BrewBalance: Inventory-First POS System

## 🚀 Overview
BrewBalance is a PHP-based Point of Sale system designed for small cafes. Unlike basic POS systems, it utilizes a **Bill of Materials (BOM)** logic to automatically deduct raw ingredients from stock upon every sale.

## 🛠 Features
- **Persistent Cart System:** Handle multiple items in a single transaction.
- **Dynamic Stock Deduction:** Real-time recipe-to-ingredient mapping.
- **Role-Based Access:** Separate dashboards for Admin (Management) and Staff (POS).
- **Inventory Alerts:** Visual cues for low-stock ingredients.

## 📦 Installation
1. Clone this repository.
2. Import `database.sql` into your MySQL server (phpMyAdmin).
3. Configure your database credentials in `connection/db_connect.php`.
4. Ensure the `pictures/` folder has write permissions for image uploads.
