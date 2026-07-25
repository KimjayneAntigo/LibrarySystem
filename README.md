📚 BookStore Inventory and Sales Management System (BookStoreISMS)

A web-based **BookStore Inventory and Sales Management System** developed using **PHP, MySQL, Bootstrap, HTML, CSS, and JavaScript**. The system helps bookstore owners efficiently manage product inventory, sales transactions, customer records, payment monitoring, loyalty rewards, and business reports through a user-friendly interface.


## Overview

BookStoreISMS is designed to simplify daily bookstore operations by providing an integrated solution for inventory management and customer relationship management (CRM). It enables administrators to monitor products, record sales, track customers, generate reports, and analyze monthly sales performance.


## Features

Dashboard
- Business overview
- Monthly Sales Data (Chart)
- Total Products
- Total Clients
- Total Sales
- Total Revenue

Inventory Management
- Add, Edit, Delete Products
- Product Price Management
- Stock Quantity Monitoring
- Inventory Status
- Product Categories
- Author/Supplier Management
- Search and Filter Products

Client Management
- Add New Clients
- Edit Client Information
- View Client Profile
- Purchase History

Sales Management
- Record Sales
- Select Client
- Select Product
- Automatic Price Retrieval
- Quantity Recording
- Sales History

Payment Management
- Payment Records
- Payment Status
- Mark Payments as Paid
- Penalty/Fine Monitoring

Customer Engagement
- Top Clients Leaderboard
- Loyalty Rewards
- Customer Recognition

Business Reports
- Monthly Sales Report
- Revenue Summary
- Financial Reports
- Business Analytics


Technologies Used

- PHP
- MySQL / MariaDB
- Bootstrap 5
- HTML5
- CSS3
- JavaScript
- XAMPP
- phpMyAdmin

## 📁 Project Structure

```
BookStoreISMS/
│
├── auth/
├── includes/
├── dashboard.php
├── books.php
├── members.php
├── borrow.php
├── return.php
├── fines.php
├── reports.php
├── leaderboard.php
├── badges.php
├── login.php
├── logout.php
├── database.php
├── layout.php
├── sidebar.php
└── index.php
```

---

## ⚙️ Installation Guide

 1. Install XAMPP

Download and install XAMPP from:

https://www.apachefriends.org/

Start:

- Apache
- MySQL

 2. Clone the Repository

```bash
git clone https://github.com/YourUsername/BookStoreISMS.git
```
or download the ZIP file.

 3. Move the Project

Copy the project folder to:

```
C:\xampp\htdocs\
```

Final directory:

```
C:\xampp\htdocs\BookStoreISMS
```

 4. Create the Database

Open:

```
http://localhost/phpmyadmin
```

Create a database named:

```
bookstore_db
```

 5. Import Database

Import:

```
bookstore_db.sql
```

 6. Configure Database

Open:

```
database.php
```

Update:

```php
$host = "localhost";
$user = "root";
$password = "";
$database = "bookstore_db";
$port = 3306;
```

 7. Run the System

Open:

```
http://localhost/BookStoreISMS
```

## 🔑 Default Login

```
Username: admin
Password: admin123
```

*(Change these credentials after deployment for security.)*

## 📸 System Modules

- Dashboard
- Inventory Products
- Clients
- Sales
- Sales History
- Payments
- Top Clients
- Loyalty Rewards
- Financial Reports

## 🎯 Intended Users

- Bookstore Owners
- Bookstore Managers
- Sales Staff
- Customer Relationship Management (CRM) Students

## 🚀 Future Improvements

- Barcode Scanner Integration
- Receipt Printing
- Inventory Forecasting
- Sales Forecast Analytics
- Email Notifications
- Customer Feedback Module
- Multi-user Roles
- Backup and Restore
- Cloud Deployment

## 👨‍💻 Developer

**Kimberly Jayne O. Antigo**

System Developer / Programmer

Bachelor of Science in Information Technology


## This project was developed for educational and academic purposes.
## Feel free to modify and enhance it for learning and research.
