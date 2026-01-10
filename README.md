# 📚 E-Stationary Stop
### A Comprehensive Web-Based Inventory & E-Commerce System

![PHP](https://img.shields.io/badge/Backend-Core%20PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/Database-MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![HTML5](https://img.shields.io/badge/Frontend-HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![Status](https://img.shields.io/badge/Status-Completed-success?style=for-the-badge)

<br />

> **E-Stationary Stop** is a robust, full-stack e-commerce solution designed to digitize local stationery businesses. It features a seamless customer shopping experience and a powerful admin dashboard for inventory management.

---

## 🌟 Features

### 🛒 User Module (Frontend)
* **Dynamic Product Catalog:** Browse products filtered by categories (Pens, Notebooks, Art Supplies).
* **Live Search:** Real-time search bar to find products instantly.
* **Product Customization:** Specialized input field to add **Custom Text** (e.g., Name/Logo) on specific items like diaries.
* **Smart Gallery:** View products with a main image and interactive thumbnail switcher.
* **Session-Based Cart:** Persistent shopping cart that retains items during navigation.
* **Secure Checkout:** Complete checkout process with validation for stock, address, and COD payment.

### 🛠 Admin Module (Backend)
* **Dashboard:** Analytics view showing total products, orders, and stock alerts.
* **CRUD Operations:** Full Create, Read, Update, Delete functionality for products.
* **Multi-Image System:** Support for uploading multiple images per product with auto-renaming logic (time-stamped filenames) to prevent conflicts.
* **Order Management:** View detailed order status, customer shipping info, and purchased items.
* **Featured Products:** Toggle "Featured" status to showcase specific items on the Homepage.

### 🔧 Technical Utilities
* **Image Diagnostic Tool:** Built-in tool (`check_images.php`) to detect broken image links between the database and server folder.
* **Security:** Uses `Prepared Statements` (SQL) everywhere to prevent SQL Injection.
* **Sanitization:** `rawurlencode()` used for handling filenames with spaces or special characters.

---

## 📂 Project Structure

```text
e-stationary-stop/
├── admin/                  # Admin Panel Files
│   ├── add_product.php     # Form to add/edit products
│   ├── product_handler.php # Logic for saving products & images
│   └── index.php           # Dashboard overview
├── assets/
│   ├── css/                # Stylesheets
│   └── images/             # Product images storage
├── includes/
│   ├── db_connect.php      # Database connection file
│   ├── header.php          # Navbar & Navigation
│   └── footer.php          # Footer section
├── checkout.php            # Order placement logic
├── product_details.php     # Single product view with gallery
├── products.php            # Main catalog page
├── index.php               # Homepage
├── check_images.php        # (Utility) Debugging tool for images
├── e_stationary_stop.sql   # Database Import File
└── README.md               # Documentation

💾 Database Schema
The system uses a relational MySQL database named e_stationary_stop with the following key tables:

users: Stores customer login credentials and profiles.

products: Main product details (ID, Name, Price, Stock, Main Image).

product_images: Stores additional gallery images linked via product_id.

categories: Product categories (e.g., Office, School).

orders: Master order records (Total Amount, Shipping Address, Status).

order_items: Junction table linking Orders to specific Products and Quantities.


⚙️ Installation Guide
Follow these steps to set up the project locally.

1. Requirements
XAMPP (or WAMP/MAMP) installed.

A web browser.

2. Setup Files
Download or Clone this repository.

Extract the folder and rename it to e-stationary-stop.

Move the folder to your server directory:

Windows: C:\xampp\htdocs\

Mac/Linux: /var/www/html/

3. Database Configuration
Open XAMPP Control Panel and start Apache and MySQL.

Go to http://localhost/phpmyadmin.

Create a new database named e_stationary_stop.

Click Import tab.

Choose the e_stationary_stop.sql file located in the project folder.

Click Go.

4. Running the Project
Open your browser and visit: http://localhost/e-stationary-stop/

To access the Admin Panel, visit: http://localhost/e-stationary-stop/admin/


<img width="1899" height="907" alt="Screenshot 2026-01-10 125510" src="https://github.com/user-attachments/assets/031a0c41-2b3f-498f-af9b-0c12ba5e2f4c" />
<img width="1896" height="905" alt="Screenshot 2026-01-10 125534" src="https://github.com/user-attachments/assets/3846bb6d-dbf1-4f6e-b408-4c4fe218e6d0" />
<img width="1904" height="912" alt="Screenshot 2026-01-10 125659" src="https://github.com/user-attachments/assets/281965da-dcd8-4619-a9de-54de5b596638" />
<img width="1897" height="910" alt="Screenshot 2026-01-10 125729" src="https://github.com/user-attachments/assets/a159fda0-8109-4fc6-9b66-9fa252127e38" />
