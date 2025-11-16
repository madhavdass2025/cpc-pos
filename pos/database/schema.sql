-- Users and Roles
CREATE TABLE `roles` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `role_name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `roles` (`id`, `role_name`) VALUES
(1, 'Admin'),
(2, 'Pharmacist/Sales'),
(3, 'Inventory Manager');

CREATE TABLE `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products
CREATE TABLE `products` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_name` VARCHAR(255) NOT NULL,
  `generic_name` VARCHAR(255) DEFAULT NULL,
  `description` VARCHAR(100) DEFAULT NULL,
  `hsn_code` VARCHAR(20) DEFAULT NULL,
  `mrp` DECIMAL(10, 2) NOT NULL,
  `tax_excluded_price` DECIMAL(10, 2) NOT NULL,
  `tax_amount` DECIMAL(10, 2) NOT NULL,
  `taxable` VARCHAR(10) NOT NULL DEFAULT 'yes',
  `itax_rate` DECIMAL(5, 2) NOT NULL,
  `cess_rate` DECIMAL(5, 2) NOT NULL,
  `reorder_level` INT(11) NOT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `is_favorite` TINYINT(1) DEFAULT 0,
  `submitted_by` VARCHAR(100) NOT NULL,
  `submitted_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` VARCHAR(100) NOT NULL DEFAULT 'available',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Suppliers
CREATE TABLE `suppliers` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `supplier_name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20),
  `gstin` VARCHAR(15),
  `address` TEXT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Customers
CREATE TABLE `customers` (
  `RegID` int NOT NULL AUTO_INCREMENT,
  `custType` varchar(100) NOT NULL DEFAULT 'regular',
  `RegDt` varchar(100) NOT NULL,
  `RegNo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Pettyp` varchar(100) NOT NULL,
  `petnam` varchar(100) NOT NULL,
  `petclr` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`RegID`),
  `petsex` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `petbred` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `year` varchar(10) NOT NULL,
  `month` varchar(10) NOT NULL,
  `gram` varchar(10) NOT NULL,
  `kg` varchar(10) NOT NULL,
  `petsp` varchar(100) NOT NULL,
  `doctor` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ownnam` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ownadd1` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ownadd2` varchar(100) DEFAULT NULL,
  `ownloc` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ownpin` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ownmob` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ownres` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ownemail` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Reports` tinyint(1) DEFAULT '0',
  `cancel` varchar(100) NOT NULL DEFAULT '0',
  `canceldoneby` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `dt` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- Inventory and Stock
CREATE TABLE `stock_batches` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `product_id` INT NOT NULL,
  `batch_number` VARCHAR(100) NOT NULL,
  `current_qty` INT NOT NULL,
  `mfg_date` DATE,
  `expiry_date` DATE,
  `purchase_price` DECIMAL(10, 2) NOT NULL,
  `tax_rate` DECIMAL(5, 2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `stock_ledger` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `product_id` INT NOT NULL,
  `batch_id` INT NOT NULL,
  `transaction_type` VARCHAR(50) NOT NULL,
  `quantity` INT NOT NULL,
  `reference_id` INT,
  `transaction_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`),
  FOREIGN KEY (`batch_id`) REFERENCES `stock_batches`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sales and Payments
CREATE TABLE `sales_invoices` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `invoice_number` VARCHAR(100) NOT NULL,
  `invoice_date` DATE NOT NULL,
  `customer_id` INT,
  `net_amount` DECIMAL(10, 2) NOT NULL,
  `user_id` INT NOT NULL,
  `payment_status` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`RegID`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `payments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `invoice_id` INT,
  `payment_method` VARCHAR(50) NOT NULL,
  `amount_paid` DECIMAL(10, 2) NOT NULL,
  `cash_amount` DECIMAL(10, 2) DEFAULT 0,
  `card_amount` DECIMAL(10, 2) DEFAULT 0,
  `upi_amount` DECIMAL(10, 2) DEFAULT 0,
  `credit_amount` DECIMAL(10, 2) DEFAULT 0,
  `payment_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`invoice_id`) REFERENCES `sales_invoices`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Purchases
CREATE TABLE `purchase_bills` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `bill_number` VARCHAR(100) NOT NULL,
  `bill_date` DATE NOT NULL,
  `supplier_id` INT NOT NULL,
  `total_amount` DECIMAL(10, 2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `supplier_payments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `supplier_id` INT NOT NULL,
  `payment_date` DATE NOT NULL,
  `amount` DECIMAL(10, 2) NOT NULL,
  `payment_method` VARCHAR(50),
  `notes` TEXT,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Accounts
CREATE TABLE `vouchers` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `voucher_type` VARCHAR(50) NOT NULL,
  `amount` DECIMAL(10, 2) NOT NULL,
  `description` TEXT,
  `voucher_date` DATE NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `customer_ledger` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `customer_id` INT NOT NULL,
  `transaction_type` VARCHAR(50) NOT NULL,
  `reference_id` INT,
  `debit_amount` DECIMAL(10, 2) DEFAULT 0,
  `credit_amount` DECIMAL(10, 2) DEFAULT 0,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`RegID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
