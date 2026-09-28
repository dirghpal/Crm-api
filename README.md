# 🥬 Bhajiwala Backend

A complete **Grocery & Vegetable E-commerce REST API Backend** built with **Laravel 11** and **PHP**.

Bhajiwala Backend provides APIs for customer authentication, products, categories, cart, wishlist, orders, stock management, and admin management.

---

## 📸 Project Preview

![Bhajiwala Backend](./banner.png)

---

## 🚀 Features

### 👤 Authentication
- User Registration
- User Login
- Laravel Sanctum Authentication
- Logout
- User Profile
- Update Profile
- Bearer Token Authentication

### 🛍️ Product Management
- Product Listing
- Product Details
- Product Search
- Category Filtering
- Price Filtering
- Featured Products
- Product Sorting
- Pagination
- Product CRUD
- Product Image Management

### 📂 Category Management
- Category Listing
- Category Details
- Create Category
- Update Category
- Delete Category
- Category Image Management
- Search & Sorting

### 🛒 Cart
- Add Product to Cart
- View Cart
- Update Quantity
- Remove Product from Cart
- User-specific Cart

### ❤️ Wishlist
- Add Product to Wishlist
- View Wishlist
- Remove Product from Wishlist
- Duplicate Wishlist Prevention

### 📦 Orders
- Create Order from Cart
- Order History
- Order Details
- Order Status
- Order Items
- Automatic Total Calculation
- Stock Validation
- Automatic Stock Reduction
- Cart Clearing After Order
- Database Transaction Support

### 🔐 Admin APIs
- Admin Authentication
- Admin Middleware
- Role-based Authorization
- Admin Dashboard
- Product Management
- Category Management
- Order Management
- Order Status Update
- Product Delete
- Category Delete

### 📊 Admin Dashboard

Dashboard provides:

- Total Users
- Total Products
- Total Categories
- Total Orders
- Total Sales
- Pending Orders
- Shipped Orders
- Delivered Orders

---

## 🛠️ Tech Stack

| Technology | Usage |
|---|---|
| PHP | Backend Language |
| Laravel 11 | Backend Framework |
| MySQL | Database |
| Laravel Sanctum | API Authentication |
| REST API | API Architecture |
| Postman | API Testing |
| Composer | Dependency Management |
| Git & GitHub | Version Control |

---

## 📁 Project Structure

```text
bhajiwala-backend/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── Admin/
│   │   │       │   ├── CategoryController.php
│   │   │       │   ├── ProductController.php
│   │   │       │   ├── OrderController.php
│   │   │       │   └── DashboardController.php
│   │   │       │
│   │   │       ├── AuthController.php
│   │   │       ├── CategoryController.php
│   │   │       ├── ProductController.php
│   │   │       ├── CartController.php
│   │   │       ├── WishlistController.php
│   │   │       └── OrderController.php
│   │   │
│   │   ├── Middleware/
│   │   ├── Requests/
│   │   └── Exceptions/
│   │
│   ├── Models/
│   │   ├── User.php
│   │   ├── Category.php
│   │   ├── Product.php
│   │   ├── Cart.php
│   │   ├── Wishlist.php
│   │   ├── Order.php
│   │   └── OrderItem.php
│   │
│   └── Helpers/
│       └── ApiHelper.php
│
├── database/
│   ├── migrations/
│   └── seeders/
│
├── routes/
│   └── api.php
│
├── bootstrap/
│   └── app.php
│
├── config/
├── public/
├── resources/
├── storage/
├── .env.example
├── composer.json
└── README.md
🔒 Security

The project includes:

Laravel Sanctum Authentication
Admin Middleware
Role-based Authorization
User Ownership Validation
Request Validation
Custom API Exceptions
Protected Customer APIs
Protected Admin APIs
Database Transactions

Admin access requires:

role = admin
📡 API Response Format
Success Response
{
    "status": 1,
    "msg": "Product List",
    "data": {}
}
Validation Error
{
    "status": 0,
    "msg": "Validation Error",
    "errors": {}
}
API Error
{
    "status": 0,
    "msg": "Unauthorized"
}
⚡ Installation
1. Clone Repository
git clone YOUR_GITHUB_REPOSITORY_URL
2. Open Project
cd bhajiwala-backend
3. Install Dependencies
composer install
4. Create Environment File
cp .env.example .env
5. Generate Application Key
php artisan key:generate
6. Configure Database

Update your .env file:

DB_DATABASE=bhajiwala
DB_USERNAME=root
DB_PASSWORD=
7. Run Migrations
php artisan migrate
8. Start Laravel Server
php artisan serve

API will run on:

http://127.0.0.1:8000
🧪 API Testing

The APIs were tested using Postman.

Recommended testing flow:

Register
   ↓
Login
   ↓
Copy Bearer Token
   ↓
Test Protected APIs
   ↓
Create Cart
   ↓
Add Products
   ↓
Place Order
   ↓
Check Order

For Admin APIs:

Login as Admin
       ↓
Copy Admin Token
       ↓
Access /api/admin/*
📊 Main Modules
Authentication
      │
      ├── Register
      ├── Login
      ├── Profile
      └── Logout
      │
      ▼
Products ─── Categories
      │
      ▼
    Cart
      │
      ├── Wishlist
      │
      ▼
    Orders
      │
      ▼
Admin Management
      │
      ├── Dashboard
      ├── Products
      ├── Categories
      └── Orders
🎯 Project Objective

Bhajiwala was developed as a practical e-commerce backend project to implement real-world backend development concepts.

The project focuses on:

REST API Development
Authentication
Authorization
CRUD Operations
MySQL Database
Eloquent Relationships
Cart Management
Wishlist Management
Order Management
Stock Management
API Validation
Database Transactions
Admin APIs
Error Handling
👨‍💻 Developer
Dirghpal Suthar

Backend Developer

Skills
PHP
Laravel
REST API
MySQL
Python
FastAPI
Android
Kotlin
Jetpack Compose
Git & GitHub
⭐ Future Improvements
Online Payment Gateway
Coupon & Discount System
Product Reviews & Ratings
Address Management
Delivery Management
Push Notifications
Order Cancellation Rules
API Rate Limiting
Swagger / OpenAPI Documentation
📌 Status

Completed Backend Project

Built with ❤️ using Laravel 11


