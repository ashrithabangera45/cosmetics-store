# 💄 Cosmetics Store - E-Commerce Platform

A full-stack e-commerce application for selling cosmetics with separate **Admin** and **User** interfaces.

## 🛠️ Tech Stack

- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **Backend:** PHP
- **Database:** MySQL
- **Server:** Apache/Nginx

## 📁 Project Structure

```
cosmetics-store/
├── frontend/
│   ├── pages/
│   │   ├── index.html          # Home page
│   │   ├── login.html          # User login
│   │   ├── register.html       # User registration
│   │   ├── products.html       # Products listing
│   │   ├── product-detail.html # Single product details
│   │   ├── cart.html           # Shopping cart
│   │   ├── checkout.html       # Checkout page
│   │   ├── order-status.html   # Order tracking
│   │   └── admin/
│   │       ├── admin-login.html
│   │       ├── admin-dashboard.html
│   │       ├── add-product.html
│   │       ├── manage-products.html
│   │       ├── manage-categories.html
│   │       ├── manage-orders.html
│   │       └── view-users.html
│   ├── css/
│   │   ├── style.css           # Main styles
│   │   ├── admin-style.css     # Admin panel styles
│   │   └── responsive.css      # Responsive design
│   ├── js/
│   │   ├── main.js             # Main JavaScript
│   │   ├── auth.js             # Authentication logic
│   │   ├── cart.js             # Cart functionality
│   │   ├── admin.js            # Admin functions
│   │   └── api.js              # API calls
│   └── assets/
│       ├── images/
│       └── icons/
├── backend/
│   ├── api/
│   │   ├── auth.php            # Authentication endpoints
│   │   ├── products.php        # Product management
│   │   ├── categories.php      # Category management
│   │   ├── cart.php            # Cart operations
│   │   ├── orders.php          # Order management
│   │   └── users.php           # User management
│   ├── config/
│   │   ├── config.php          # Database configuration
│   │   └── Database.php        # Database class
│   ├── controllers/
│   │   ├── ProductController.php
│   │   ├── OrderController.php
│   │   ├── UserController.php
│   │   └── CartController.php
│   ├── models/
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Order.php
│   │   └── Category.php
│   └── middleware/
│       └── auth.php            # Authentication middleware
├── database/
│   └── schema.sql              # Database schema
├── .gitignore
├── .env.example
└── README.md
```

## 🚀 Quick Start

### Prerequisites
- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx server

### Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/ashrithabangera45/cosmetics-store.git
   cd cosmetics-store
   ```

2. **Set up database:**
   - Import `database/schema.sql` into MySQL
   - Update database credentials in `backend/config/config.php`

3. **Configure environment:**
   ```bash
   cp .env.example .env
   # Edit .env with your settings
   ```

4. **Start development server:**
   ```bash
   php -S localhost:8000
   ```

5. **Access the application:**
   - User: `http://localhost:8000/frontend/pages/index.html`
   - Admin: `http://localhost:8000/frontend/pages/admin/admin-login.html`

## 📋 Features

### User Side
- ✅ User Registration & Login
- ✅ Browse & Search Products
- ✅ Add to Cart
- ✅ Checkout
- ✅ Order Tracking

### Admin Side
- ✅ Add/Update/Delete Products
- ✅ Manage Categories
- ✅ View & Manage Orders
- ✅ View Registered Users

## 🔐 Security
- Password hashing with bcrypt
- Input validation & sanitization
- CSRF protection
- SQL injection prevention (Prepared statements)

## 📝 License
MIT License

## 👨‍💻 Author
ashrithabangera45

---

**Happy Coding! 🎉**