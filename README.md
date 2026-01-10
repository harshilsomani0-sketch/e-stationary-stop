<div align="center">

  <img src="assets/images/logo.png" alt="Logo" width="100" height="100">
  <h1 align="center">E-Stationary Stop</h1>

  <p align="center">
    <strong>A Comprehensive Web-Based Inventory & E-Commerce System</strong>
    <br />
    Digitizing local stationery businesses with a robust PHP/MySQL Architecture.
    <br />
    <br />
    <a href="#-demo">View Demo</a>
    ·
    <a href="#-features">Report Bug</a>
    ·
    <a href="#-features">Request Feature</a>
  </p>

  <p align="center">
    <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP" />
    <img src="https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
    <img src="https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white" alt="HTML5" />
    <img src="https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white" alt="CSS3" />
    <img src="https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap" />
  </p>
</div>

<br />

## 📙 About The Project

**E-Stationary Stop** is a full-stack e-commerce solution designed to bridge the gap between traditional stationery retail and digital convenience. It features a seamless customer shopping experience and a powerful admin dashboard for inventory management.

Key technical highlights include a **Custom Multi-Image Upload System**, **Session-Based Cart Logic**, and **Security-First Architecture** using Prepared Statements.

---

## 📸 Screen Previews

<table align="center">
  <tr>
    <td align="center" width="50%">
      <strong>🏠 Home Page</strong><br>
      <img src="assets/images/screenshot_home.png" alt="Home Page" width="100%">
    </td>
    <td align="center" width="50%">
      <strong>🛍 Product Details</strong><br>
      <img src="assets/images/screenshot_details.png" alt="Details" width="100%">
    </td>
  </tr>
  <tr>
    <td align="center" width="50%">
      <strong>⚙️ Admin Dashboard</strong><br>
      <img src="assets/images/screenshot_admin.png" alt="Admin" width="100%">
    </td>
    <td align="center" width="50%">
      <strong>🛒 Shopping Cart</strong><br>
      <img src="assets/images/screenshot_cart.png" alt="Cart" width="100%">
    </td>
  </tr>
</table>

---

## 🌟 Key Features

### 🛒 User Module
* **Dynamic Product Catalog:** Filter products by Category (Pens, Notebooks, etc.).
* **Live Search:** AJAX-based real-time search functionality.
* **Customization Engine:** Allows users to add custom text to specific products (e.g., embossing names on diaries).
* **Smart Gallery:** Interactive thumbnail switcher for product images.
* **Secure Checkout:** Validation for stock levels, address format, and COD payment.

### 🛠 Admin Module
* **Analytics Dashboard:** Real-time view of Total Orders, Products, and Revenue.
* **Inventory Control:** Full CRUD (Create, Read, Update, Delete) capabilities.
* **Advanced Image Handling:** * Support for **Multiple Images** per product.
  * Auto-renaming logic (`timestamp_filename.jpg`) to prevent server conflicts.
* **Order Management:** View customer details and update order status.

---

## 🚀 Getting Started

Follow these steps to set up the project locally.

### Prerequisites
* **XAMPP** (Apache & MySQL)
* **Web Browser** (Chrome/Edge)

### Installation

1. **Clone the Repo**
   ```sh
   git clone [https://github.com/YOUR_USERNAME/e-stationary-stop.git](https://github.com/YOUR_USERNAME/e-stationary-stop.git)

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
