# PanenSegar — Daily Pre-Order Fresh Produce Dropshipping Platform

A Laravel-based daily pre-order (PO) dropshipping application connecting local farmers directly with customers. Built with a focus on backend security, transactional integrity, role-based access control (RBAC), and automated daily harvest operations.

## Key Engineering Highlights

* **5-Layer Payment Security (Midtrans Snap Sandbox)**
  * **SHA-512 Signature Verification:** Validates every incoming webhook payload using `hash_equals()` to prevent spoofed payment notifications.
  * **Pessimistic Row Locking:** Uses `DB::transaction()` with `lockForUpdate()` to eliminate race conditions and double-processing.
  * **Server-Side Amount Verification:** Re-calculates subtotals, shipping fees, and cross-checks webhook `gross_amount` against server records.
  * **Idempotency & Audit Trail:** Stores raw gateway payloads keyed by unique `gateway_transaction_id` so duplicate webhooks are safely ignored.
  * **Strict State Machine:** Enforces valid order transitions (`pending` → `paid` → `harvested` / `expired` / `need_refund`).
* **Authentication, RBAC & Anti-IDOR**
  * Session-based authentication with `session()->regenerate()` to prevent session fixation.
  * Hardcoded server-side `customer` role assignment on registration to block privilege escalation.
  * Custom `EnsureUserHasRole` middleware protecting farmer/admin routes and strict ownership checks preventing IDOR on order receipts.
* **Farmer Harvest Aggregation & Automated Scheduler**
  * SQL aggregation (`SUM` + `GROUP BY`) scoped per farmer to calculate daily harvest quantities and base-price farmer payouts exclusively from paid orders.
  * `orders:expire-unpaid` scheduled every 15 minutes to release quota held by unpaid orders.
  * `harvest:summary` scheduled daily at 20:05 WIB (post-cutoff) to compile next-day harvest recaps.

## Tech Stack

* **Backend:** Laravel, PHP, SQLite
* **Frontend:** Blade Templates, Tailwind CSS
* **Payment Gateway:** Midtrans Snap API (Sandbox)
* **Testing:** PHPUnit Feature Tests

## Local Setup

1. Clone the repository and install dependencies:
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate