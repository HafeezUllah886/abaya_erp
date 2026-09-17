# Muslim Abaya Sellers ERP - Implementation Plan

A complete ERP for Muslim Abaya sellers. The system manages raw material (fabric, thread, lace, etc.) purchasing from vendors/suppliers, issuing raw material to tailors for stitching via issue vouchers, receiving finished Abayas back from tailors into stock, tracking stock for both raw materials and finished Abayas, taking customer orders (with customer name, phone, quantity, advance payment, balance amount, and order status), direct point-of-sale from displayed products, full accounting/finance for business/customers/suppliers/tailors/investors (ledgers, adjustments, fund transfers, payments/receipts, business expenses), business reports and dashboard insights, and password-protected destructive actions.

---

## 1. Module List & Scope

### 1.1. Raw Material Management
* **Purpose:** Manage catalog and stock for fabrics, threads, and accessories.
* **Key Entities:** Raw Material Categories, Raw Material Items, Units.
* **Key User Flows:** Add new material, adjust stock manually (requires **password re-confirmation** for write-offs), view current stock balance.
* **Baseline Reuse:** Reuses the `products` and `stock` logic from Jamil Oil, renaming "Products" to "Raw Materials" in this context. 

### 1.2. Product/Abaya Management
* **Purpose:** Manage catalog and stock for finished Abayas.
* **Key Entities:** Abaya Categories, Abaya Items.
* **Key User Flows:** Add new Abaya styles, view finished stock. (Simple flat structure, no variants, to keep the app easy to use).
* **Baseline Reuse:** Directly reuses the Jamil Oil `products` module logic.

### 1.3. Purchasing
* **Purpose:** Procure raw materials from suppliers.
* **Key Entities:** Suppliers (Accounts), Purchase Invoices, Purchase Details, Purchase Returns.
* **Key User Flows:** Create purchase invoice (updates raw material stock, credits supplier ledger). Process return (debits supplier, reduces stock - requires **password re-confirmation**).
* **Baseline Reuse:** Reuses `purchase` and `purchase_details` logic, adapted for Raw Materials. 

### 1.4. Issue Voucher to Tailor
* **Purpose:** Dispatch raw materials to tailors for manufacturing.
* **Key Entities:** Tailors (Accounts), Issue Vouchers, Issue Voucher Details.
* **Key User Flows:** Select tailor -> select raw materials & quantities -> issue. Status transitions (Issued / Partially Received / Received).
* **Baseline Reuse:** Net-new build, architecturally similar to a "Sale" that moves stock out to track a pending manufacturing job.

### 1.5. Abaya Receiving from Tailor
* **Purpose:** Receive finished goods and calculate final cost.
* **Key Entities:** Receiving Vouchers, Receiving Details.
* **Key User Flows:** Select pending Issue Voucher -> input received Abaya quantities and agreed stitching fee (which covers tailor's labor and any own materials used) -> system calculates cost -> credits tailor ledger for stitching charges -> debits finished goods stock.
* **Baseline Reuse:** Net-new build.

### 1.6. Customer Orders
* **Purpose:** Take bespoke or pre-orders for customers, with support for partial fulfillments.
* **Key Entities:** Customers (Accounts), Orders, Order Details, Advance Payments, Order Fulfillments.
* **Key User Flows:** Create order -> record advance payment -> partially fulfill order (deliver some items) -> mark as Completed/Cancelled. Order cancellation requires **password re-confirmation**.
* **Baseline Reuse:** Net-new build for the Order lifecycle, but payment logic reuses `paymentReceiving` and central `transactions`.

### 1.7. Direct POS Sales
* **Purpose:** Sell finished Abayas directly from the showroom.
* **Key Entities:** POS Invoices, Invoice Details.
* **Key User Flows:** Scan/select finished Abaya -> calculate total -> accept payment -> print receipt. Delete/void invoice requires **password re-confirmation**.
* **Baseline Reuse:** Reuses Jamil Oil `sale` and `sale_details` heavily.

### 1.8. Accounts & Finance
* **Purpose:** Full ledger management and double-entry style accounting adapted to the baseline's single-table approach.
* **Key Entities:** Chart of Accounts (Business, Customers, Suppliers, Tailors, Investors), Transactions, Vouchers (Receipts, Payments, Transfers, Adjustments), Expenses.
* **Key User Flows:** Receive payment from customer, pay supplier/tailor, record utility expense, manual ledger adjustment (requires **password re-confirmation**).
* **Baseline Reuse:** High reuse of `accounts`, `transactions`, `expenses`, and `transfers`.

### 1.9. Reports
* **Purpose:** Business intelligence.
* **Key Entities:** Date-filtered reports.
* **Key User Flows:** View P&L, stock valuation, ledger statements, pending tailor vouchers.
* **Baseline Reuse:** High reuse of the underlying query patterns and `generalHelper.php` calculation logic.

### 1.10. Dashboard
* **Purpose:** High-level daily summary.
* **Key Metrics:** Today's Sales, Pending Orders, Low Stock Alerts, Cash/Bank Balances.
* **Baseline Reuse:** Reuse `DashboardController` structure.

### 1.11. Security
* **Purpose:** Protect critical operations.
* **Key User Flows:** Any route performing delete, write-off, ledger reversal, or cancellation triggers the password confirmation gate.
* **Baseline Reuse:** Direct reuse of `confirmPasswordController`.

### 1.12. Suggested Additional Features
1. **Barcode/QR Labeling:** Generate and print tags for finished Abayas to speed up POS.
2. **WhatsApp/SMS Integration:** Automatically notify customers when their order status changes to "Completed".
3. **Tailor Performance Analytics:** Report showing average turnaround time and rejection/defect rates per tailor.
4. **Low-Stock/Reorder Alerts:** Dashboard notifications when raw materials (like specific laces or black fabric) fall below a threshold.
5. **Investor Profit-Share Ledger:** Automatically distribute a percentage of monthly net profit to specific investor accounts.

---

## 2. Data Model / ERD Outline

*   **Users:** id, name, email, password, role.
*   **Accounts:** id, type (business, supplier, customer, tailor, investor), name, phone, balance.
*   **Transactions:** id, account_id, cr (credit), db (debit), date, notes, ref_id (from `refs`).
*   **Categories:** id, name, type (raw_material, finished_abaya).
*   **Raw_Materials:** id, category_id, name, unit, cost_price.
*   **Abayas:** id, category_id, design_name, sku, retail_price.
*   **Stocks:** id, item_type (Morph: RawMaterial/Abaya), item_id, cr, db, date, ref_id.
*   **Purchases:** id, supplier_id, date, total_amount, ref_id.
*   **Purchase_Details:** id, purchase_id, raw_material_id, qty, unit_price, amount.
*   **Issue_Vouchers:** id, tailor_id, date, status (issued, partially_received, received).
*   **Issue_Voucher_Details:** id, issue_voucher_id, raw_material_id, qty, cost_at_issue.
*   **Receive_Vouchers:** id, issue_voucher_id, date, stitching_charges_total.
*   **Receive_Voucher_Details:** id, receive_voucher_id, abaya_id, qty, stitching_cost_per_unit, total_calculated_cost.
*   **Orders:** id, customer_id, date, total_amount, advance_paid, status (pending, partially_fulfilled, completed, cancelled).
*   **Order_Details:** id, order_id, abaya_id, qty, fulfilled_qty, price, amount.
*   **Order_Fulfillments:** id, order_id, date, delivery_note.
*   **Order_Fulfillment_Details:** id, order_fulfillment_id, order_detail_id, qty.
*   **Sales:** id, customer_id (nullable for walk-in), date, total_amount, ref_id.
*   **Sale_Details:** id, sale_id, abaya_id, qty, price, amount.
*   **Expenses:** id, category_id, account_id (e.g., Cash/Bank paid from), amount, date, ref_id.

---

## 3. Costing & Ledger Logic

*   **Costing Engine (Moving Average):** To provide the most accurate Profit/Loss view for this manufacturing workflow, we will use Moving Average Cost.
    *   **Raw Materials:** `avgPurchasePrice()` recalculates the average cost per unit dynamically based on historical purchases.
    *   **Finished Abayas:** When issuing raw materials, the system snapshots the *current average cost* of those materials. When receiving finished Abayas, the system calculates: `(Total Cost of Issued Raw Materials + Agreed Stitching Fee from Tailor) / Quantity Received`. This batch cost is then factored into the Moving Average Cost of that Abaya item in stock.
*   **Accounting Ledger Engine:** Retains Jamil Oil's single `transactions` table approach (`cr` / `db`) linked to `accounts` for simplicity and baseline compatibility.
    *   **Purchase:** Credit Supplier Account (Increase Payable).
    *   **Tailor Receive:** Credit Tailor Account for stitching charges (Increase Payable).
    *   **Order Advance:** Debit Cash Account, Credit Customer Account (Liability).
    *   **Sale/Order Completion:** Debit Customer Account (Increase Receivable), Credit Sales Revenue (implied by P&L logic).
    *   **Payment to Supplier/Tailor:** Debit Supplier/Tailor Account, Credit Cash Account.
    *   **Receipt from Customer:** Credit Customer Account, Debit Cash Account.
    *   **Expense:** Credit Cash Account, Debit Expense Category (implied).

---

## 4. Reused vs. New Build Table

| Feature | Reused From Baseline | Modification Needed | Net-New Build | Est. Effort (S/M/L) |
| :--- | :--- | :--- | :--- | :--- |
| **Auth / Security Gate** | `AuthController`, `confirmPasswordController` | None (keep as-is) | - | S |
| **Raw Material Catalog** | `products` | Rename, remove irrelevant fields | - | S |
| **Abaya Catalog** | `products` | Adapt fields, keep simple (no variants) | - | S |
| **Stock Ledger** | `stock`, `generalHelper` | Make polymorphic (raw vs finished) | - | M |
| **Finance Ledgers** | `transactions`, `accounts` | Add Account Types (Tailor, Investor) | - | S |
| **Purchasing** | `purchase`, `purchase_details`| Point to Raw Materials | - | S |
| **Tailor Issue Voucher** | - | - | Full Module | L |
| **Tailor Receive Voucher**| - | - | Full Module (Cost calc logic) | L |
| **Customer Orders** | - | - | Full Module (Partial fulfillments) | L |
| **POS / Sales** | `sale`, `sale_details` | Point to Abayas | - | M |
| **Reports & Dashboard** | Helpers & Controllers | Update queries for new modules | - | M |

---

## 5. Build Phasing / Milestones

*   **Phase 1: Core Architecture & Catalogs.** Scaffold Laravel 13 app, migrate Auth, setup Accounts, Raw Materials, and Abaya catalogs.
*   **Phase 2: Stock & Purchasing.** Implement polymorphic stock ledger and the Raw Material Purchase flow.
*   **Phase 3: Manufacturing Flow.** Build Issue Vouchers and Receive Vouchers (calculating finished goods cost and updating tailor ledgers).
*   **Phase 4: Sales & Orders.** Implement Customer Orders (with advance payments & partial fulfillment) and direct POS Sales.
*   **Phase 5: Finance & Accounting.** Implement payments, receipts, fund transfers, manual adjustments, and expenses.
*   **Phase 6: Reporting & Polish.** Build Dashboard, Reports (P&L, stock value, statements), and enforce the Password Confirmation Gate on all destructive actions.

---

## 6. Confirmed Project Assumptions (Based on User Feedback)

1.  **Variants Tracking:** No variant tracking. Abayas are managed as simple, flat products to keep the app easy to use.
2.  **Order Fulfillment:** Orders can be fulfilled partially.
3.  **Costing:** Moving Average Cost will be used across Raw Materials and Finished Abayas to provide accurate Profit & Loss views.
4.  **Multi-Branch:** Single branch/warehouse only.
5.  **Tailor Materials:** Tailors may use their own materials in addition to issued materials. The Receive Voucher will capture the total agreed stitching fee (which covers their labor and any self-supplied materials) to factor into the final Abaya cost.

---

## 7. Ready to Build?

Before we begin writing code, please confirm:
- [ ] Do you approve of the updated Data Model and Costing Logic?
- [ ] Are we ready to initialize Phase 1 scaffolding?
