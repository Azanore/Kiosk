# Moroccan Café Kiosk

Self-ordering kiosk for a Moroccan café. Pragmatic PHP 8 + MySQL MVC with a single front controller.

## Prerequisites
- PHP 8.2+
- MySQL 8 (or MariaDB compatible)
- XAMPP or similar stack (Apache + PHP + MySQL)

## Setup
1) Create database and import SQL
- Create a database (e.g., `kiosk`)
- Import DDL and seed:
  - `database/ddl.sql`
  - `database/seed.sql`

2) Configure database connection
- Edit `app/Config/database.php` with your local credentials

3) Configure app
- `app/Config/app.php` contains environment and kiosk settings, including:
  - `env` (`dev`|`prod`), `timezone`, `locale`
  - `kiosk_idle_seconds`, `confirm_return_seconds`, `auto_cancel_minutes`
  - `currency_suffix`, `number_locale`, `cafe_name`, `cafe_address`, `cafe_phone`
  - `payment_provider`: `simulator` (default in-app test terminal) or `terminal` (standalone terminal with admin marking Paid)

4) Run with XAMPP (recommended)
- Place this project under XAMPP `htdocs` (e.g., `C:/xampp/htdocs/Kiosk`)
- Start Apache and MySQL from XAMPP control panel
- Open in browser: `http://localhost/Kiosk/public/`

If using a different web root, ensure DocumentRoot points to `public/`.

## Routing
Single front controller with query param routing: `?r=controller/action`
Examples:
- Kiosk welcome: `?r=kiosk/welcome`
- Admin orders: `?r=dashboard/orders`
- Admin menu: `?r=dashboard/menu`

## Default Admin
- Login: `?r=auth/login`
- Seeded admin user is created by `seed.sql`.
  - If the placeholder hash is present, default password is: `admin123`
  - Update password via DB for production use.

## Flows
Kiosk (client):
- Welcome → Categories → Products → Product detail → Cart → Checkout
- Payment types:
  - Carte:
    - If `payment_provider=simulator` (default): kiosk opens test terminal at `?r=order/startTestPayment&id=...` with Approve/Decline. Approve marks order `paid` and shows confirmation (big number).
    - If `payment_provider=terminal`: kiosk shows waiting screen and polls `?r=order/pollStatus&id=...` every ~3s while staff completes payment and marks Paid in admin.
  - Comptoir: shows confirmation immediately (big number). Staff can mark Paid later in admin.
- Inactivity: kiosk auto-returns to welcome after ~90s. Confirmation auto-returns after ~12s.

Admin (dashboard):
- Orders board with scope filter (Aujourd'hui/Tout), grouped by status
- Clear status badges per order; “Imprimer” hidden for cancelled
- Awaiting payment shows only: “Marquer payé” and “Annuler”
- Other statuses: generic select to update to paid/preparing/ready/completed/cancelled; invalid transitions from awaiting payment are blocked
- Print receipt per order: manual “Imprimer” button on the receipt page
- Print receipt per order: `?r=order/printReceipt&id=...`
- Menu management: `?r=dashboard/menu` to create/edit categories and products, set sort order, toggle activation/availability

## Receipts
- Thermal-friendly HTML: café header, big order number, date/time, items, totals
- Shows payment method and order type. If `Comptoir`, shows “Merci de régler au comptoir”.
- Admin reprint page: `?r=order/printReceipt&id=...` now uses a manual “Imprimer” button (no auto popup). Includes a link back to the dashboard.

## Notes
- No per-item customizations; products have name, image, base price.
- No real card SDK integrated. Use the in-app simulator or mark Paid from admin when using a standalone terminal.
- Sessions are used for admin and cart; use behind a trusted network.
- Prepared statements are used for DB access.

## Troubleshooting
- White page or errors: check `public/index.php` router and PHP error logs
- DB errors: verify credentials in `app/Config/database.php`, run `ddl.sql` and `seed.sql`
- Locale/money formatting: see `app/Services/Format.php`

## QA Checklist (Happy-path)
- Kiosk – Counter:
  1. Add 1–2 items to cart → Checkout → select Comptoir → Confirm
  2. Confirmation shows order number; auto-return to welcome after ~12s
  3. Admin: mark Paid when customer settles
- Kiosk – Card (simulated):
  1. Add items → Checkout → select Carte → Confirm
  2. Waiting screen shows amount and polls every ~3s
  3. Admin: “Marquer payé” → Kiosk redirects to confirmation
- Admin dashboard:
  - Scope filter Aujourd'hui/Tout works; grouped by status; can update statuses with the generic select (except awaiting payment which has dedicated buttons)
  - Reprint is available per order via the manual “Imprimer” button on the receipt page
  - Auto-refresh updates list every 15s
