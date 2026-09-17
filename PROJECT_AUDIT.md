# Project Audit: Jamil Oil (Baseline for Abaya ERP)

## 1. Project Overview
* **Laravel Version:** 13.8 (based on `laravel/framework: ^13.8`)
* **PHP Version:** 8.3
* **Key Packages:** `laravel/tinker`, `laravel/boost`, `laravel/pail`, `laravel/pint`, `pestphp/pest`.
* **Frontend Stack:** Tailwind CSS v4.0.0, Vite. (No heavy JS frameworks like Vue/React; likely uses standard Blade + Tailwind).
* **Auth System:** Custom authentication via `AuthController` (login/logout). Includes a specific `confirmPasswordController` for protecting destructive actions, which is a great pattern to reuse.

## 2. Routes
* **Main Files:** `web.php` which requires module-specific route files:
  * `products_mgmt.php` (Products management)
  * `finance.php` (Accounts, Adjustments, Transfers, Expenses, Payments)
  * `settings.php` (Application settings/users)
  * `purchase.php` (Purchase invoices)
  * `sale.php` (Sales invoices)
  * `reports.php` (Various reports)
  * `demand.php` (Order/Demand tracking)
* **Middleware:** Core application routes are grouped under the `auth` middleware in `web.php`.

## 3. Database / Models
* **Key Models & Tables:**
  * **Auth:** `User` (`users` table).
  * **Catalog:** `products` (`products` table).
  * **Finance:** `accounts`, `transactions`, `accountAdjustments`, `expenses`, `expenseCategories`, `issuePayment`, `paymentReceiving`, `transfers`.
  * **Stock:** `stock`, `stockAdjustments`.
  * **Purchase/Sale:** `purchase`, `purchase_details`, `sale`, `sale_details`, `salePayments`.
  * **Other:** `ref` (reference number generator), `dailysheet`, `demands`, `demandDeliveries`.
* **Patterns:**
  * Schema focuses on distinct transaction tables rather than a polymorphic unified voucher system.
  * No heavy use of UUIDs or multi-tenancy evident.
  * Uses standard integer IDs and timestamp tracking.

## 4. Controllers
* **Types:** Standard controllers handling CRUD operations.
  * `ProductsController`, `PurchaseController`, `SaleController`, `DemandController`, `AccountsController`.
* **Delegation:** Logic is often delegated to global helper functions (e.g., `generalHelper.php`, `financeHelper.php`) instead of Service or Action classes.
* **Validation:** Seems to rely on inline validation within controller methods rather than dedicated Form Requests (to be improved in new project).

## 5. Views
* **Directories:** Structured by module: `/auth`, `/demands`, `/finance`, `/product_mgmt`, `/purchase`, `/sale`, `/reports`, `/settings`, `/profile`.
* **Layouts:** Shared layout in `/layout`.
* **Components:** Reusable UI elements are present (likely Tailwind-based tables and forms).

## 6. Helpers / Libraries / Traits
* **Global Helpers (autoloaded via composer):**
  * `generalHelper.php`: Handles reference generation (`getRef()`), stock ledger tracking (`createStock()`, `getStock()`), and costing (`avgPurchasePrice()`, `avgSalePrice()`).
  * `financeHelper.php`: Handles financial ledger posting (`createTransaction()`, `getAccountBalance()`, `spotBalance()`).
  * `dashboardHelper.php`: Aggregates metrics for the dashboard.
* **Important Patterns:**
  * **Password Confirmation:** Existing flow for destructive actions.
  * **Reference IDs:** A centralized `ref` table generates sequential voucher/transaction IDs.

## 7. Accounting/Finance Engine
* **Ledgers:** Implemented via the `transactions` table (`account_id`, `cr`, `db`, `date`, `refID`). Balances are calculated dynamically (`sum(cr) - sum(db)`).
* **Vouchers:** Payments, receipts, transfers, and expenses each have their own tables but post to the central `transactions` table.
* **Stock Ledger:** Tracked in the `stock` table (`product_id`, `cr`, `db`). Balances dynamically calculated.
* **Costing:** Uses Average Purchase Price (`avgPurchasePrice` function dynamically calculates cost based on historical purchases).

## 8. Reports & Dashboard
* **Reports:** Likely built using Query Builder and Eloquent, filtered by date ranges (implied by helpers like `avgSalePrice($from, $to, $id)`).
* **Dashboard:** Driven by `DashboardController` and `dashboardHelper.php` to show high-level metrics.

## 9. Reusability Assessment (For Abaya ERP)
| Component | Reusability Tag | Notes |
| :--- | :--- | :--- |
| **Auth System & Layouts** | Reuse as-is | The login and Blade/Tailwind shell are perfect starting points. |
| **Password Confirmation** | Reuse as-is | Essential for the required security on destructive actions. |
| **Product Management** | Reuse with modification | Adapt from "oil products" to raw materials & Abaya variants (sizes, colors). |
| **Purchase Module** | Reuse with modification | Needs to handle raw materials instead of generic products. |
| **Stock Ledger Engine** | Reuse with modification | The `stock` (cr/db) table pattern is excellent and should be kept. |
| **Accounting Engine** | Reuse with modification | The `transactions` (cr/db) pattern is solid. Will expand chart of accounts to include Tailors and Investors. |
| **Sales (POS & Orders)** | Net-New Build / Modify | Needs heavy adaptation to handle advance payments, balances, and direct POS. |
| **Tailor / Manufacturing** | Not applicable / Build new | The Issue/Receive voucher system for tailors is completely new logic. |
| **Reporting / Dashboard** | Reuse with modification | The underlying metric calculation pattern is reusable, but specific metrics will change. |
