CREATE DATABASE IF NOT EXISTS glass_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE glass_shop;

CREATE TABLE users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 email VARCHAR(190) UNIQUE,
 phone VARCHAR(25) UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 role ENUM('admin','manager','staff','customer') NOT NULL DEFAULT 'customer',
 notify_email TINYINT(1) NOT NULL DEFAULT 0,
 notify_sms TINYINT(1) NOT NULL DEFAULT 0,
 notify_whatsapp TINYINT(1) NOT NULL DEFAULT 0,
 consent_asked TINYINT(1) NOT NULL DEFAULT 0,
 active TINYINT(1) NOT NULL DEFAULT 1,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE products (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(150) NOT NULL,
 category VARCHAR(100) NOT NULL,
 description TEXT,
 sku VARCHAR(60) UNIQUE,
 thickness_mm DECIMAL(8,2) NULL,
 width_mm DECIMAL(10,2) NULL,
 height_mm DECIMAL(10,2) NULL,
 unit VARCHAR(30) NOT NULL DEFAULT 'piece',
 cost_price DECIMAL(12,2) NOT NULL DEFAULT 0,
 selling_price DECIMAL(12,2) NOT NULL DEFAULT 0,
 quantity DECIMAL(12,2) NOT NULL DEFAULT 0,
 reorder_level DECIMAL(12,2) NOT NULL DEFAULT 0,
 image_url VARCHAR(500),
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE sales (
 id INT AUTO_INCREMENT PRIMARY KEY,
 receipt_no VARCHAR(40) NOT NULL UNIQUE,
 customer_name VARCHAR(150),
 customer_phone VARCHAR(25),
 total DECIMAL(12,2) NOT NULL,
 payment_method VARCHAR(40) NOT NULL,
 created_by INT NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(created_by) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE sale_items (
 id INT AUTO_INCREMENT PRIMARY KEY,
 sale_id INT NOT NULL,
 product_id INT NOT NULL,
 quantity DECIMAL(12,2) NOT NULL,
 unit_price DECIMAL(12,2) NOT NULL,
 line_total DECIMAL(12,2) NOT NULL,
 FOREIGN KEY(sale_id) REFERENCES sales(id),
 FOREIGN KEY(product_id) REFERENCES products(id)
) ENGINE=InnoDB;

CREATE TABLE purchases (
 id INT AUTO_INCREMENT PRIMARY KEY,
 supplier_name VARCHAR(150) NOT NULL,
 supplier_phone VARCHAR(25),
 invoice_no VARCHAR(80),
 total DECIMAL(12,2) NOT NULL,
 purchased_by INT NOT NULL,
 purchased_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(purchased_by) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE purchase_items (
 id INT AUTO_INCREMENT PRIMARY KEY,
 purchase_id INT NOT NULL,
 product_id INT NOT NULL,
 quantity DECIMAL(12,2) NOT NULL,
 unit_cost DECIMAL(12,2) NOT NULL,
 line_total DECIMAL(12,2) NOT NULL,
 FOREIGN KEY(purchase_id) REFERENCES purchases(id),
 FOREIGN KEY(product_id) REFERENCES products(id)
) ENGINE=InnoDB;

CREATE TABLE customer_orders (
 id INT AUTO_INCREMENT PRIMARY KEY,
 customer_id INT NULL,
 customer_name VARCHAR(150) NOT NULL,
 phone VARCHAR(25) NOT NULL,
 notes TEXT,
 status ENUM('pending','approved','in_progress','ready','completed','cancelled') DEFAULT 'pending',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(customer_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE order_items (
 id INT AUTO_INCREMENT PRIMARY KEY,
 order_id INT NOT NULL,
 product_id INT NOT NULL,
 quantity DECIMAL(12,2) NOT NULL,
 requested_measurements VARCHAR(255),
 FOREIGN KEY(order_id) REFERENCES customer_orders(id),
 FOREIGN KEY(product_id) REFERENCES products(id)
) ENGINE=InnoDB;

CREATE TABLE otp_codes (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 code_hash VARCHAR(255) NOT NULL,
 expires_at DATETIME NOT NULL,
 used TINYINT(1) NOT NULL DEFAULT 0,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE notification_queue (
 id INT AUTO_INCREMENT PRIMARY KEY,
 channel ENUM('sms','email','whatsapp') NOT NULL,
 recipient VARCHAR(190) NOT NULL,
 message TEXT NOT NULL,
 status ENUM('queued','sent','failed') DEFAULT 'queued',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE stock_movements (
 id INT AUTO_INCREMENT PRIMARY KEY,
 product_id INT NOT NULL,
 movement_type ENUM('sale','purchase','adjustment') NOT NULL,
 quantity_change DECIMAL(12,2) NOT NULL,
 reference_id INT NULL,
 note VARCHAR(255),
 user_id INT NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(product_id) REFERENCES products(id),
 FOREIGN KEY(user_id) REFERENCES users(id)
) ENGINE=InnoDB;
