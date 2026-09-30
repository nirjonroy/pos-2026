# Final Database Queries

## Project Title

Laravel 9 + MySQL Point of Sale Management System

## Short Database Description

This database supports a modular POS system with authentication, roles and permissions, branches, warehouses, products, inventory, stock movements, purchases, sales, sale returns, payments, expenses, and reporting. The schema is based on the current Laravel migrations and Eloquent model relationships in the project.

## 1. Database Structure

The project database name from `.env` is `pos-2026`.

```sql
CREATE DATABASE IF NOT EXISTS `pos-2026`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `pos-2026`;
```

The full executable `CREATE TABLE` statements for all 34 current tables are provided in:

```sql
-- See database/FINAL_DATABASE_QUERIES.sql section:
-- A. DATABASE STRUCTURE
```

The documented tables are:

```sql
users;
password_resets;
failed_jobs;
personal_access_tokens;
branches;
customer_groups;
categories;
brands;
units;
products;
customers;
suppliers;
warehouses;
inventories;
stock_movements;
stock_adjustments;
stock_adjustment_items;
stock_transfers;
stock_transfer_items;
sales;
sale_items;
payments;
sale_returns;
sale_return_items;
refund_payments;
purchases;
purchase_items;
purchase_payments;
expense_categories;
expenses;
roles;
permissions;
role_user;
permission_role;
```

## 2. Basic CRUD Queries

### 2.1 Branch CRUD

This query inserts a new branch.

```sql
INSERT INTO branches (name, code, phone, email, address, status, created_at, updated_at)
VALUES ('Dhanmondi Branch', 'DHA', '01711000001', 'dhanmondi@example.com', 'Dhanmondi, Dhaka', 1, NOW(), NOW());
```

This query reads branch records.

```sql
SELECT * FROM branches WHERE code = 'DHA';
```

This query updates branch phone information.

```sql
UPDATE branches SET phone = '01711000009', updated_at = NOW() WHERE code = 'DHA';
```

This query deletes an unused branch.

```sql
DELETE FROM branches WHERE code = 'OLD';
```

### 2.2 Warehouse CRUD

This query inserts a warehouse connected to a branch.

```sql
INSERT INTO warehouses (branch_id, name, code, address, status, created_at, updated_at)
VALUES (1, 'Main Warehouse', 'WH-MAIN', 'Back store', 1, NOW(), NOW());
```

This query lists warehouses with branch names.

```sql
SELECT w.*, b.name AS branch_name
FROM warehouses w
INNER JOIN branches b ON b.id = w.branch_id;
```

This query updates warehouse status.

```sql
UPDATE warehouses SET status = 0, updated_at = NOW() WHERE code = 'WH-MAIN';
```

This query deletes an unused warehouse.

```sql
DELETE FROM warehouses WHERE code = 'WH-OLD';
```

### 2.3 Category, Brand and Unit CRUD

These queries create category, brand and unit records.

```sql
INSERT INTO categories (parent_id, name, slug, status, created_at, updated_at)
VALUES (NULL, 'Snacks', 'snacks', 1, NOW(), NOW());

INSERT INTO brands (name, slug, status, created_at, updated_at)
VALUES ('PRAN', 'pran', 1, NOW(), NOW());

INSERT INTO units (name, short_name, status, created_at, updated_at)
VALUES ('Piece', 'pcs', 1, NOW(), NOW());
```

These queries read active category, brand and unit records.

```sql
SELECT * FROM categories WHERE status = 1 ORDER BY name;
SELECT * FROM brands WHERE status = 1 ORDER BY name;
SELECT * FROM units WHERE status = 1 ORDER BY name;
```

These queries update master data.

```sql
UPDATE categories SET name = 'Snack Items', updated_at = NOW() WHERE slug = 'snacks';
UPDATE brands SET status = 0, updated_at = NOW() WHERE slug = 'old-brand';
UPDATE units SET short_name = 'pc', updated_at = NOW() WHERE short_name = 'pcs';
```

These queries delete unused master data.

```sql
DELETE FROM categories WHERE slug = 'old-category';
DELETE FROM brands WHERE slug = 'unused-brand';
DELETE FROM units WHERE short_name = 'old';
```

### 2.4 Product CRUD

This query inserts a product with category, brand and unit references.

```sql
INSERT INTO products
(category_id, brand_id, unit_id, name, sku, barcode, cost_price, selling_price, minimum_stock, description, status, created_at, updated_at)
VALUES
(1, 1, 1, 'PRAN Chanachur 300gm', 'SKU-001', '880000001', 55.00, 70.00, 10.000, 'Demo product', 1, NOW(), NOW());
```

This query reads product details with related master data.

```sql
SELECT p.*, c.name AS category_name, b.name AS brand_name, u.short_name
FROM products p
INNER JOIN categories c ON c.id = p.category_id
LEFT JOIN brands b ON b.id = p.brand_id
INNER JOIN units u ON u.id = p.unit_id;
```

These queries update and delete products.

```sql
UPDATE products SET selling_price = 75.00, updated_at = NOW() WHERE sku = 'SKU-001';
DELETE FROM products WHERE sku = 'SKU-OLD';
```

### 2.5 Customer and Supplier CRUD

These queries insert a customer group, customer and supplier.

```sql
INSERT INTO customer_groups (name, description, status, created_at, updated_at)
VALUES ('Regular', 'Regular customers', 1, NOW(), NOW());

INSERT INTO customers
(customer_group_id, name, phone, email, address, credit_limit, opening_due, status, created_at, updated_at)
VALUES
(1, 'Rahim Ahmed', '01733000001', 'rahim@example.com', 'Dhaka', 5000.00, 0.00, 1, NOW(), NOW());

INSERT INTO suppliers
(name, company_name, phone, email, address, opening_due, status, created_at, updated_at)
VALUES
('Dhaka Consumer Distributors', 'Dhaka Consumer Distributors', '01822000001', 'supplier@example.com', 'Dhaka', 0.00, 1, NOW(), NOW());
```

These queries read customers and suppliers.

```sql
SELECT c.*, cg.name AS group_name
FROM customers c
LEFT JOIN customer_groups cg ON cg.id = c.customer_group_id;

SELECT * FROM suppliers WHERE status = 1;
```

These queries update due amounts.

```sql
UPDATE customers SET opening_due = 250.00, updated_at = NOW() WHERE phone = '01733000001';
UPDATE suppliers SET opening_due = 1200.00, updated_at = NOW() WHERE email = 'supplier@example.com';
```

These queries delete old customer and supplier records.

```sql
DELETE FROM customers WHERE email = 'old.customer@example.com';
DELETE FROM suppliers WHERE email = 'old.supplier@example.com';
```

### 2.6 Inventory CRUD

This query inserts an inventory row.

```sql
INSERT INTO inventories (branch_id, warehouse_id, product_id, quantity, reserved_quantity, created_at, updated_at)
VALUES (1, 1, 1, 100.000, 0.000, NOW(), NOW());
```

This query reads inventory for one branch and product.

```sql
SELECT * FROM inventories WHERE branch_id = 1 AND product_id = 1;
```

This query increases stock quantity.

```sql
UPDATE inventories
SET quantity = quantity + 10, updated_at = NOW()
WHERE branch_id = 1 AND warehouse_id = 1 AND product_id = 1;
```

This query deletes empty inventory rows.

```sql
DELETE FROM inventories WHERE quantity = 0 AND reserved_quantity = 0;
```

### 2.7 Purchase CRUD

These queries create a purchase, purchase item and purchase payment.

```sql
INSERT INTO purchases
(supplier_id, branch_id, warehouse_id, purchase_no, purchase_date, subtotal, discount, tax, total, paid_amount, due_amount, status, note, created_by, created_at, updated_at)
VALUES
(1, 1, 1, 'PUR-20260927-0001', CURDATE(), 1000.00, 0.00, 0.00, 1000.00, 0.00, 1000.00, 'draft', 'Demo purchase', 1, NOW(), NOW());

INSERT INTO purchase_items
(purchase_id, product_id, quantity, unit_cost, discount, tax, total, created_at, updated_at)
VALUES
(1, 1, 10.000, 55.00, 0.00, 0.00, 550.00, NOW(), NOW());

INSERT INTO purchase_payments
(purchase_id, payment_method, amount, reference_no, paid_at, created_by, created_at, updated_at)
VALUES
(1, 'cash', 500.00, 'PP-001', NOW(), 1, NOW(), NOW());
```

This query reads purchase history.

```sql
SELECT p.*, s.name AS supplier_name
FROM purchases p
INNER JOIN suppliers s ON s.id = p.supplier_id
ORDER BY p.purchase_date DESC;
```

These queries update and delete purchases.

```sql
UPDATE purchases SET status = 'received', updated_at = NOW() WHERE purchase_no = 'PUR-20260927-0001';
DELETE FROM purchases WHERE status = 'draft' AND purchase_no = 'PUR-OLD';
```

### 2.8 Sales, Payment and Return CRUD

These queries create a sale, sale item and payment.

```sql
INSERT INTO sales
(branch_id, warehouse_id, customer_id, invoice_no, sale_date, subtotal, discount, tax, total, paid_amount, due_amount, status, created_by, created_at, updated_at)
VALUES
(1, 1, 1, 'SAL-20260927-0001', NOW(), 500.00, 0.00, 0.00, 500.00, 500.00, 0.00, 'completed', 1, NOW(), NOW());

INSERT INTO sale_items
(sale_id, product_id, quantity, unit_price, unit_cost, discount, tax, total, created_at, updated_at)
VALUES
(1, 1, 2.000, 70.00, 55.00, 0.00, 0.00, 140.00, NOW(), NOW());

INSERT INTO payments
(sale_id, payment_method, amount, reference_no, paid_at, received_by, note, created_at, updated_at)
VALUES
(1, 'cash', 500.00, NULL, NOW(), 1, NULL, NOW(), NOW());
```

This query reads sales history.

```sql
SELECT s.*, c.name AS customer_name
FROM sales s
LEFT JOIN customers c ON c.id = s.customer_id
ORDER BY s.sale_date DESC;
```

These queries create a sale return and refund.

```sql
INSERT INTO sale_returns
(sale_id, return_no, branch_id, return_date, total_amount, reason, status, created_by, created_at, updated_at)
VALUES
(1, 'RET-20260927-0001', 1, NOW(), 70.00, 'Damaged item', 'completed', 1, NOW(), NOW());

INSERT INTO sale_return_items
(sale_return_id, sale_item_id, product_id, quantity, unit_price, total, created_at, updated_at)
VALUES
(1, 1, 1, 1.000, 70.00, 70.00, NOW(), NOW());

INSERT INTO refund_payments
(sale_return_id, payment_method, amount, reference_no, refunded_at, created_by, created_at, updated_at)
VALUES
(1, 'cash', 70.00, NULL, NOW(), 1, NOW(), NOW());
```

These queries update and delete sale records.

```sql
UPDATE sales SET status = 'partial', due_amount = 100.00, updated_at = NOW() WHERE invoice_no = 'SAL-20260927-0001';
DELETE FROM sales WHERE status = 'cancelled' AND invoice_no = 'SAL-OLD';
```

### 2.9 Inventory Movement CRUD

These queries create stock movement, stock adjustment and stock transfer records.

```sql
INSERT INTO stock_movements
(branch_id, warehouse_id, product_id, type, quantity, reference_type, reference_id, note, created_by, created_at, updated_at)
VALUES
(1, 1, 1, 'adjustment_in', 5.000, 'App\\Models\\StockAdjustment', 1, 'Manual adjustment', 1, NOW(), NOW());

INSERT INTO stock_adjustments
(branch_id, warehouse_id, adjustment_no, type, reason, note, created_by, status, created_at, updated_at)
VALUES
(1, 1, 'ADJ-20260927-0001', 'increase', 'Counting difference', NULL, 1, 'completed', NOW(), NOW());

INSERT INTO stock_adjustment_items
(stock_adjustment_id, product_id, quantity, unit_cost, created_at, updated_at)
VALUES
(1, 1, 5.000, 55.00, NOW(), NOW());

INSERT INTO stock_transfers
(transfer_no, from_branch_id, to_branch_id, from_warehouse_id, to_warehouse_id, status, transfer_date, note, created_by, created_at, updated_at)
VALUES
('TRF-20260927-0001', 1, 2, 1, 2, 'completed', CURDATE(), 'Transfer stock', 1, NOW(), NOW());

INSERT INTO stock_transfer_items
(stock_transfer_id, product_id, quantity, unit_cost, created_at, updated_at)
VALUES
(1, 1, 3.000, 55.00, NOW(), NOW());
```

This query reads stock movements.

```sql
SELECT * FROM stock_movements ORDER BY created_at DESC;
```

### 2.10 Expense CRUD

These queries create expense category and expense records.

```sql
INSERT INTO expense_categories (name, status, created_at, updated_at)
VALUES ('Shop Rent', 1, NOW(), NOW());

INSERT INTO expenses
(branch_id, expense_category_id, expense_no, expense_date, amount, payment_method, reference_no, description, created_by, created_at, updated_at)
VALUES
(1, 1, 'EXP-20260927-0001', CURDATE(), 25000.00, 'bank', 'BNK-001', 'Monthly rent', 1, NOW(), NOW());
```

This query reads expenses with category names.

```sql
SELECT e.*, ec.name AS category_name
FROM expenses e
INNER JOIN expense_categories ec ON ec.id = e.expense_category_id;
```

These queries update and delete expenses.

```sql
UPDATE expenses SET amount = 26000.00, updated_at = NOW() WHERE expense_no = 'EXP-20260927-0001';
DELETE FROM expenses WHERE expense_no = 'EXP-OLD';
```

### 2.11 User, Role and Permission CRUD

These queries insert role, permission and pivot records.

```sql
INSERT INTO roles (name, slug, description, created_at, updated_at)
VALUES ('Manager', 'manager', 'Operational manager', NOW(), NOW());

INSERT INTO permissions (name, slug, group_name, created_at, updated_at)
VALUES ('Sales View', 'sales.view', 'Sales', NOW(), NOW());

INSERT INTO role_user (role_id, user_id) VALUES (1, 1);
INSERT INTO permission_role (permission_id, role_id) VALUES (1, 1);
```

This query reads users with their roles.

```sql
SELECT u.name, r.name AS role_name
FROM users u
INNER JOIN role_user ru ON ru.user_id = u.id
INNER JOIN roles r ON r.id = ru.role_id;
```

These queries update and delete role-permission data.

```sql
UPDATE roles SET description = 'Updated role description', updated_at = NOW() WHERE slug = 'manager';
DELETE FROM permission_role WHERE permission_id = 1 AND role_id = 1;
```

## 3. Business Queries

### 3.1 Product Search

This query searches active products by SKU, barcode or name.

```sql
SELECT id, name, sku, barcode, selling_price
FROM products
WHERE status = 1
  AND (sku = 'BD-RICE-001' OR barcode = '880100000001' OR name LIKE '%Rice%')
ORDER BY name;
```

### 3.2 Current Stock

This query shows stock by branch and warehouse.

```sql
SELECT b.name AS branch, w.name AS warehouse, p.name AS product, p.sku,
       i.quantity, i.reserved_quantity, (i.quantity - i.reserved_quantity) AS available_quantity
FROM inventories i
INNER JOIN products p ON p.id = i.product_id
INNER JOIN branches b ON b.id = i.branch_id
LEFT JOIN warehouses w ON w.id = i.warehouse_id
ORDER BY b.name, w.name, p.name;
```

### 3.3 Low Stock

This query finds products where quantity is at or below minimum stock.

```sql
SELECT p.name, p.sku, b.name AS branch, w.name AS warehouse, i.quantity, p.minimum_stock
FROM inventories i
INNER JOIN products p ON p.id = i.product_id
INNER JOIN branches b ON b.id = i.branch_id
LEFT JOIN warehouses w ON w.id = i.warehouse_id
WHERE i.quantity > 0 AND i.quantity <= p.minimum_stock;
```

### 3.4 Out of Stock

This query finds stock rows with zero or negative quantity.

```sql
SELECT p.name, p.sku, b.name AS branch, w.name AS warehouse, i.quantity
FROM inventories i
INNER JOIN products p ON p.id = i.product_id
INNER JOIN branches b ON b.id = i.branch_id
LEFT JOIN warehouses w ON w.id = i.warehouse_id
WHERE i.quantity <= 0;
```

### 3.5 Inventory Value

This query calculates inventory cost value and selling value by branch.

```sql
SELECT b.name AS branch,
       SUM(i.quantity * p.cost_price) AS inventory_cost_value,
       SUM(i.quantity * p.selling_price) AS inventory_selling_value
FROM inventories i
INNER JOIN products p ON p.id = i.product_id
INNER JOIN branches b ON b.id = i.branch_id
GROUP BY b.id, b.name;
```

### 3.6 Purchase History and Supplier Due

This query lists purchase history.

```sql
SELECT p.purchase_no, p.purchase_date, s.name AS supplier, b.name AS branch,
       p.total, p.paid_amount, p.due_amount, p.status
FROM purchases p
INNER JOIN suppliers s ON s.id = p.supplier_id
INNER JOIN branches b ON b.id = p.branch_id
ORDER BY p.purchase_date DESC;
```

This query calculates supplier due.

```sql
SELECT s.id, s.name, s.opening_due + COALESCE(SUM(p.due_amount), 0) AS total_supplier_due
FROM suppliers s
LEFT JOIN purchases p ON p.supplier_id = s.id AND p.status <> 'cancelled'
GROUP BY s.id, s.name, s.opening_due
HAVING total_supplier_due > 0;
```

### 3.7 Customer Due

This query calculates customer due from opening due and sale due.

```sql
SELECT c.id, c.name, c.opening_due + COALESCE(SUM(s.due_amount), 0) AS total_customer_due
FROM customers c
LEFT JOIN sales s ON s.customer_id = c.id AND s.status <> 'cancelled'
GROUP BY c.id, c.name, c.opening_due
HAVING total_customer_due > 0;
```

### 3.8 Sales History and Details

This query lists sales history.

```sql
SELECT s.invoice_no, s.sale_date, COALESCE(c.name, 'Walk-in Customer') AS customer,
       b.name AS branch, s.total, s.paid_amount, s.due_amount, s.status, u.name AS cashier
FROM sales s
LEFT JOIN customers c ON c.id = s.customer_id
INNER JOIN branches b ON b.id = s.branch_id
INNER JOIN users u ON u.id = s.created_by
ORDER BY s.sale_date DESC;
```

This query shows sale item details for one invoice.

```sql
SELECT s.invoice_no, p.name AS product, si.quantity, si.unit_price, si.discount, si.tax, si.total
FROM sale_items si
INNER JOIN sales s ON s.id = si.sale_id
INNER JOIN products p ON p.id = si.product_id
WHERE s.invoice_no = 'SAL-20260927-0001';
```

### 3.9 Payment History

This query lists sales payments.

```sql
SELECT pay.payment_method, pay.amount, pay.paid_at, s.invoice_no, u.name AS received_by
FROM payments pay
INNER JOIN sales s ON s.id = pay.sale_id
LEFT JOIN users u ON u.id = pay.received_by
ORDER BY pay.paid_at DESC;
```

### 3.10 Sale Returns

This query lists sale returns.

```sql
SELECT sr.return_no, sr.return_date, s.invoice_no, sr.total_amount, sr.status
FROM sale_returns sr
INNER JOIN sales s ON s.id = sr.sale_id
ORDER BY sr.return_date DESC;
```

### 3.11 Stock Adjustments

This query summarizes stock adjustments.

```sql
SELECT sa.adjustment_no, sa.type, sa.reason, sa.status, b.name AS branch, SUM(sai.quantity) AS total_quantity
FROM stock_adjustments sa
INNER JOIN stock_adjustment_items sai ON sai.stock_adjustment_id = sa.id
INNER JOIN branches b ON b.id = sa.branch_id
GROUP BY sa.id, sa.adjustment_no, sa.type, sa.reason, sa.status, b.name
ORDER BY sa.created_at DESC;
```

### 3.12 Stock Transfers

This query lists stock transfers between branches.

```sql
SELECT st.transfer_no, fb.name AS from_branch, tb.name AS to_branch, st.status, st.transfer_date
FROM stock_transfers st
INNER JOIN branches fb ON fb.id = st.from_branch_id
INNER JOIN branches tb ON tb.id = st.to_branch_id
ORDER BY st.transfer_date DESC;
```

### 3.13 Expenses

This query lists expenses with categories and branches.

```sql
SELECT e.expense_no, e.expense_date, ec.name AS category, b.name AS branch, e.amount, e.payment_method
FROM expenses e
INNER JOIN expense_categories ec ON ec.id = e.expense_category_id
INNER JOIN branches b ON b.id = e.branch_id
ORDER BY e.expense_date DESC;
```

## 4. SQL Concept Queries

### 4.1 INNER JOIN

This query joins products with categories.

```sql
SELECT p.name, c.name AS category
FROM products p
INNER JOIN categories c ON c.id = p.category_id;
```

### 4.2 LEFT JOIN

This query keeps products even if a brand is missing.

```sql
SELECT p.name, b.name AS brand
FROM products p
LEFT JOIN brands b ON b.id = p.brand_id;
```

### 4.3 Multiple JOIN

This query joins sales with customers, branches and users.

```sql
SELECT s.invoice_no, c.name AS customer, b.name AS branch, u.name AS cashier
FROM sales s
LEFT JOIN customers c ON c.id = s.customer_id
INNER JOIN branches b ON b.id = s.branch_id
INNER JOIN users u ON u.id = s.created_by;
```

### 4.4 GROUP BY, SUM, COUNT and ORDER BY

This query summarizes sales by branch.

```sql
SELECT b.name AS branch, COUNT(s.id) AS sale_count, SUM(s.total) AS total_sales
FROM sales s
INNER JOIN branches b ON b.id = s.branch_id
GROUP BY b.id, b.name
ORDER BY total_sales DESC;
```

### 4.5 HAVING

This query filters grouped customer due results.

```sql
SELECT customer_id, SUM(due_amount) AS due_total
FROM sales
WHERE customer_id IS NOT NULL
GROUP BY customer_id
HAVING due_total > 0;
```

### 4.6 LIKE, BETWEEN and IN

These queries demonstrate pattern, range and list filtering.

```sql
SELECT * FROM products WHERE name LIKE '%Oil%';

SELECT * FROM sales
WHERE DATE(sale_date) BETWEEN '2026-09-01' AND '2026-09-30';

SELECT * FROM payments
WHERE payment_method IN ('cash', 'bkash', 'nagad');
```

### 4.7 AVG, MIN and MAX

This query calculates product price statistics.

```sql
SELECT AVG(selling_price) AS average_price,
       MIN(selling_price) AS minimum_price,
       MAX(selling_price) AS maximum_price
FROM products;
```

### 4.8 CASE

This query classifies stock status.

```sql
SELECT p.name, i.quantity, p.minimum_stock,
       CASE
           WHEN i.quantity <= 0 THEN 'Out of Stock'
           WHEN i.quantity <= p.minimum_stock THEN 'Low Stock'
           ELSE 'In Stock'
       END AS stock_status
FROM inventories i
INNER JOIN products p ON p.id = i.product_id;
```

### 4.9 Subquery

This query finds products sold more than 10 units.

```sql
SELECT *
FROM products
WHERE id IN (
    SELECT product_id
    FROM sale_items
    GROUP BY product_id
    HAVING SUM(quantity) > 10
);
```

### 4.10 EXISTS

This query finds products that have available inventory rows.

```sql
SELECT p.*
FROM products p
WHERE EXISTS (
    SELECT 1
    FROM inventories i
    WHERE i.product_id = p.id
      AND i.quantity > 0
);
```

## 5. Report Queries

### 5.1 Profit/Loss Report

This report calculates gross sales, returns, net sales, COGS, gross profit, expenses and net profit.

```sql
SELECT
    COALESCE((SELECT SUM(total) FROM sales WHERE status <> 'cancelled' AND DATE(sale_date) BETWEEN '2026-09-01' AND '2026-09-30'), 0) AS gross_sales,
    COALESCE((SELECT SUM(total_amount) FROM sale_returns WHERE status = 'completed' AND DATE(return_date) BETWEEN '2026-09-01' AND '2026-09-30'), 0) AS returns,
    COALESCE((SELECT SUM(total) FROM sales WHERE status <> 'cancelled' AND DATE(sale_date) BETWEEN '2026-09-01' AND '2026-09-30'), 0)
      - COALESCE((SELECT SUM(total_amount) FROM sale_returns WHERE status = 'completed' AND DATE(return_date) BETWEEN '2026-09-01' AND '2026-09-30'), 0) AS net_sales,
    COALESCE((SELECT SUM(si.quantity * si.unit_cost) FROM sale_items si INNER JOIN sales s ON s.id = si.sale_id WHERE s.status <> 'cancelled' AND DATE(s.sale_date) BETWEEN '2026-09-01' AND '2026-09-30'), 0) AS cogs,
    COALESCE((SELECT SUM(amount) FROM expenses WHERE expense_date BETWEEN '2026-09-01' AND '2026-09-30'), 0) AS expenses;
```

### 5.2 Purchase & Sale Report

This report compares purchases and sales by date.

```sql
SELECT report_date, SUM(purchase_total) AS purchase_total, SUM(sale_total) AS sale_total
FROM (
    SELECT purchase_date AS report_date, SUM(total) AS purchase_total, 0 AS sale_total
    FROM purchases
    WHERE status <> 'cancelled'
    GROUP BY purchase_date
    UNION ALL
    SELECT DATE(sale_date) AS report_date, 0 AS purchase_total, SUM(total) AS sale_total
    FROM sales
    WHERE status <> 'cancelled'
    GROUP BY DATE(sale_date)
) x
GROUP BY report_date
ORDER BY report_date;
```

### 5.3 Tax Report

This report summarizes sales tax and purchase tax.

```sql
SELECT 'sales' AS source, SUM(tax) AS tax_amount FROM sales WHERE status <> 'cancelled'
UNION ALL
SELECT 'purchases' AS source, SUM(tax) AS tax_amount FROM purchases WHERE status <> 'cancelled';
```

### 5.4 Supplier & Customer Report

This report lists supplier and customer contacts together.

```sql
SELECT 'supplier' AS type, name, phone, email, opening_due FROM suppliers
UNION ALL
SELECT 'customer' AS type, name, phone, email, opening_due FROM customers;
```

### 5.5 Customer Group Report

This report summarizes customers by group.

```sql
SELECT cg.name AS group_name, COUNT(c.id) AS customer_count, SUM(c.opening_due) AS opening_due
FROM customer_groups cg
LEFT JOIN customers c ON c.customer_group_id = cg.id
GROUP BY cg.id, cg.name;
```

### 5.6 Stock Report

This report shows current stock by branch, warehouse and product.

```sql
SELECT b.name AS branch, w.name AS warehouse, p.name AS product,
       i.quantity, i.reserved_quantity, p.minimum_stock
FROM inventories i
INNER JOIN branches b ON b.id = i.branch_id
LEFT JOIN warehouses w ON w.id = i.warehouse_id
INNER JOIN products p ON p.id = i.product_id;
```

### 5.7 Stock Adjustment Report

This report shows adjustment items.

```sql
SELECT sa.adjustment_no, sa.type, sa.reason, sa.status, p.name AS product, sai.quantity, sai.unit_cost
FROM stock_adjustments sa
INNER JOIN stock_adjustment_items sai ON sai.stock_adjustment_id = sa.id
INNER JOIN products p ON p.id = sai.product_id;
```

### 5.8 Trending Products

This report orders products by sold quantity.

```sql
SELECT p.id, p.name, p.sku, SUM(si.quantity) AS sold_quantity, SUM(si.total) AS sales_amount
FROM sale_items si
INNER JOIN sales s ON s.id = si.sale_id
INNER JOIN products p ON p.id = si.product_id
WHERE s.status <> 'cancelled'
GROUP BY p.id, p.name, p.sku
ORDER BY sold_quantity DESC;
```

### 5.9 Items Report

This report lists product master data.

```sql
SELECT p.name, p.sku, c.name AS category, b.name AS brand,
       u.short_name AS unit, p.cost_price, p.selling_price
FROM products p
INNER JOIN categories c ON c.id = p.category_id
LEFT JOIN brands b ON b.id = p.brand_id
INNER JOIN units u ON u.id = p.unit_id;
```

### 5.10 Product Purchase Report

This report summarizes purchases by product.

```sql
SELECT p.name, SUM(pi.quantity) AS purchased_quantity, SUM(pi.total) AS purchase_total
FROM purchase_items pi
INNER JOIN purchases pur ON pur.id = pi.purchase_id
INNER JOIN products p ON p.id = pi.product_id
WHERE pur.status <> 'cancelled'
GROUP BY p.id, p.name;
```

### 5.11 Product Sell Report

This report summarizes sales by product.

```sql
SELECT p.name, SUM(si.quantity) AS sold_quantity, SUM(si.total) AS sell_total
FROM sale_items si
INNER JOIN sales s ON s.id = si.sale_id
INNER JOIN products p ON p.id = si.product_id
WHERE s.status <> 'cancelled'
GROUP BY p.id, p.name;
```

### 5.12 Purchase Payment Report

This report groups purchase payments by method.

```sql
SELECT pp.payment_method, SUM(pp.amount) AS amount, COUNT(*) AS payment_count
FROM purchase_payments pp
GROUP BY pp.payment_method;
```

### 5.13 Sell Payment Report

This report groups sale payments by method.

```sql
SELECT p.payment_method, SUM(p.amount) AS amount, COUNT(*) AS payment_count
FROM payments p
GROUP BY p.payment_method;
```

### 5.14 Expense Report

This report summarizes expenses by category.

```sql
SELECT ec.name AS category, SUM(e.amount) AS total_amount, COUNT(e.id) AS expense_count
FROM expenses e
INNER JOIN expense_categories ec ON ec.id = e.expense_category_id
GROUP BY ec.id, ec.name;
```

### 5.15 Register Report

This report summarizes daily received payments by method.

```sql
SELECT DATE(paid_at) AS register_date, payment_method, SUM(amount) AS total_received
FROM payments
GROUP BY DATE(paid_at), payment_method
ORDER BY register_date DESC;
```

### 5.16 Sales Representative Report

This report summarizes sales by cashier or sales user.

```sql
SELECT u.name AS representative, COUNT(s.id) AS sale_count, SUM(s.total) AS sales_total
FROM users u
LEFT JOIN sales s ON s.created_by = u.id AND s.status <> 'cancelled'
GROUP BY u.id, u.name
ORDER BY sales_total DESC;
```

### 5.17 Activity Log Report

This report builds an activity-style list from stock movements, sales and purchases.

```sql
SELECT created_at AS activity_time, 'stock_movement' AS activity_type, type AS description, reference_type, reference_id
FROM stock_movements
UNION ALL
SELECT created_at AS activity_time, 'sale' AS activity_type, invoice_no AS description, 'App\\Models\\Sale' AS reference_type, id AS reference_id
FROM sales
UNION ALL
SELECT created_at AS activity_time, 'purchase' AS activity_type, purchase_no AS description, 'App\\Models\\Purchase' AS reference_type, id AS reference_id
FROM purchases
ORDER BY activity_time DESC;
```

## 6. Transaction Examples

If validation fails, stock becomes negative, or a foreign key is invalid, `ROLLBACK;` should be used instead of `COMMIT;`.

### 6.1 Completing a Sale

This transaction locks inventory, creates sale records, decreases stock, records movement and stores payment.

```sql
START TRANSACTION;

SELECT quantity, reserved_quantity
FROM inventories
WHERE branch_id = 1 AND warehouse_id = 1 AND product_id = 1
FOR UPDATE;

INSERT INTO sales
(branch_id, warehouse_id, customer_id, invoice_no, sale_date, subtotal, discount, tax, total, paid_amount, due_amount, status, created_by, created_at, updated_at)
VALUES
(1, 1, 1, 'SAL-TRX-0001', NOW(), 140.00, 0.00, 0.00, 140.00, 140.00, 0.00, 'completed', 1, NOW(), NOW());

SET @sale_id = LAST_INSERT_ID();

INSERT INTO sale_items
(sale_id, product_id, quantity, unit_price, unit_cost, discount, tax, total, created_at, updated_at)
VALUES
(@sale_id, 1, 2.000, 70.00, 55.00, 0.00, 0.00, 140.00, NOW(), NOW());

UPDATE inventories
SET quantity = quantity - 2.000, updated_at = NOW()
WHERE branch_id = 1 AND warehouse_id = 1 AND product_id = 1
  AND (quantity - reserved_quantity) >= 2.000;

INSERT INTO stock_movements
(branch_id, warehouse_id, product_id, type, quantity, reference_type, reference_id, note, created_by, created_at, updated_at)
VALUES
(1, 1, 1, 'sale', -2.000, 'App\\Models\\Sale', @sale_id, NULL, 1, NOW(), NOW());

INSERT INTO payments
(sale_id, payment_method, amount, reference_no, paid_at, received_by, note, created_at, updated_at)
VALUES
(@sale_id, 'cash', 140.00, NULL, NOW(), 1, NULL, NOW(), NOW());

COMMIT;
```

### 6.2 Receiving a Purchase

This transaction receives purchase stock and creates purchase payment.

```sql
START TRANSACTION;

INSERT INTO purchases
(supplier_id, branch_id, warehouse_id, purchase_no, purchase_date, subtotal, discount, tax, total, paid_amount, due_amount, status, note, created_by, created_at, updated_at)
VALUES
(1, 1, 1, 'PUR-TRX-0001', CURDATE(), 550.00, 0.00, 0.00, 550.00, 550.00, 0.00, 'received', 'Received purchase', 1, NOW(), NOW());

SET @purchase_id = LAST_INSERT_ID();

INSERT INTO purchase_items
(purchase_id, product_id, quantity, unit_cost, discount, tax, total, created_at, updated_at)
VALUES
(@purchase_id, 1, 10.000, 55.00, 0.00, 0.00, 550.00, NOW(), NOW());

UPDATE inventories
SET quantity = quantity + 10.000, updated_at = NOW()
WHERE branch_id = 1 AND warehouse_id = 1 AND product_id = 1;

INSERT INTO stock_movements
(branch_id, warehouse_id, product_id, type, quantity, reference_type, reference_id, note, created_by, created_at, updated_at)
VALUES
(1, 1, 1, 'purchase', 10.000, 'App\\Models\\Purchase', @purchase_id, 'Received purchase', 1, NOW(), NOW());

INSERT INTO purchase_payments
(purchase_id, payment_method, amount, reference_no, paid_at, created_by, created_at, updated_at)
VALUES
(@purchase_id, 'cash', 550.00, NULL, NOW(), 1, NOW(), NOW());

COMMIT;
```

### 6.3 Stock Transfer

This transaction decreases source stock, increases destination stock and records two stock movements.

```sql
START TRANSACTION;

SELECT quantity, reserved_quantity
FROM inventories
WHERE branch_id = 1 AND warehouse_id = 1 AND product_id = 1
FOR UPDATE;

INSERT INTO stock_transfers
(transfer_no, from_branch_id, to_branch_id, from_warehouse_id, to_warehouse_id, status, transfer_date, note, created_by, created_at, updated_at)
VALUES
('TRF-TRX-0001', 1, 2, 1, 2, 'completed', CURDATE(), 'Branch transfer', 1, NOW(), NOW());

SET @transfer_id = LAST_INSERT_ID();

INSERT INTO stock_transfer_items
(stock_transfer_id, product_id, quantity, unit_cost, created_at, updated_at)
VALUES
(@transfer_id, 1, 5.000, 55.00, NOW(), NOW());

UPDATE inventories
SET quantity = quantity - 5.000, updated_at = NOW()
WHERE branch_id = 1 AND warehouse_id = 1 AND product_id = 1
  AND (quantity - reserved_quantity) >= 5.000;

INSERT INTO inventories
(branch_id, warehouse_id, product_id, quantity, reserved_quantity, created_at, updated_at)
VALUES
(2, 2, 1, 5.000, 0.000, NOW(), NOW())
ON DUPLICATE KEY UPDATE quantity = quantity + 5.000, updated_at = NOW();

INSERT INTO stock_movements
(branch_id, warehouse_id, product_id, type, quantity, reference_type, reference_id, note, created_by, created_at, updated_at)
VALUES
(1, 1, 1, 'transfer_out', 5.000, 'App\\Models\\StockTransfer', @transfer_id, 'Branch transfer', 1, NOW(), NOW()),
(2, 2, 1, 'transfer_in', 5.000, 'App\\Models\\StockTransfer', @transfer_id, 'Branch transfer', 1, NOW(), NOW());

COMMIT;
```

### 6.4 Sale Return

This transaction creates sale return records, increases inventory and records a refund.

```sql
START TRANSACTION;

SELECT si.id, si.sale_id, si.product_id, si.quantity,
       COALESCE(SUM(sri.quantity), 0) AS already_returned
FROM sale_items si
LEFT JOIN sale_return_items sri ON sri.sale_item_id = si.id
WHERE si.id = 1
GROUP BY si.id, si.sale_id, si.product_id, si.quantity
FOR UPDATE;

INSERT INTO sale_returns
(sale_id, return_no, branch_id, return_date, total_amount, reason, status, created_by, created_at, updated_at)
VALUES
(1, 'RET-TRX-0001', 1, NOW(), 70.00, 'Customer return', 'completed', 1, NOW(), NOW());

SET @return_id = LAST_INSERT_ID();

INSERT INTO sale_return_items
(sale_return_id, sale_item_id, product_id, quantity, unit_price, total, created_at, updated_at)
VALUES
(@return_id, 1, 1, 1.000, 70.00, 70.00, NOW(), NOW());

UPDATE inventories
SET quantity = quantity + 1.000, updated_at = NOW()
WHERE branch_id = 1 AND warehouse_id = 1 AND product_id = 1;

INSERT INTO stock_movements
(branch_id, warehouse_id, product_id, type, quantity, reference_type, reference_id, note, created_by, created_at, updated_at)
VALUES
(1, 1, 1, 'sale_return', 1.000, 'App\\Models\\SaleReturn', @return_id, 'Customer return', 1, NOW(), NOW());

INSERT INTO refund_payments
(sale_return_id, payment_method, amount, reference_no, refunded_at, created_by, created_at, updated_at)
VALUES
(@return_id, 'cash', 70.00, NULL, NOW(), 1, NOW(), NOW());

COMMIT;
```

## 7. Notes

The complete executable SQL-only version, including all `CREATE TABLE` statements and all query examples, is stored in `database/FINAL_DATABASE_QUERIES.sql`.

```sql
-- No application code or database data was modified for this documentation task.
```
