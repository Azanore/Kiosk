# Moroccan Café Kiosk – MVP

Minimal self-ordering kiosk for a Moroccan café. Pragmatic PHP 8 + MySQL MVC with a single front controller.

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
- `app/Config/app.php` contains timezone, locale, money formatting, kiosk idle timeout, and café details used on receipts:
  - `cafe_name`, `cafe_address`, `cafe_phone`

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
  - Carte: creates order as `awaiting_payment` and shows waiting screen. Kiosk polls every ~3s. Admin marks Paid to simulate terminal success.
  - Comptoir: shows confirmation immediately; receipt can be printed from Admin.
- Inactivity: kiosk auto-returns to welcome after ~90s. Confirmation auto-returns after ~12s.

Admin (dashboard):
- Orders board with scope filter (Aujourd'hui/Tout), grouped by status
- Quick action: “Marquer payé” when awaiting payment
- Status updates: paid → preparing → ready → completed; cancel supported
- Print receipt per order: `?r=order/printReceipt&id=...`
- Menu management: `?r=dashboard/menu` to create/edit categories and products, set order, toggle availability

## Receipts
- Thermal-friendly HTML: café header, big order number, date/time, items, totals
- Shows payment method and order type. If `Comptoir`, shows “Merci de régler au comptoir”.
- Browser auto-print on load.

## Notes (MVP)
- No real card SDK integrated; card flow is simulated via admin “Marquer payé”.
- Sessions used for admin and cart; no CSRF (MVP scope). Use behind trusted network.
- Prepared statements for DB access.

## Troubleshooting
- White page or errors: check `public/index.php` router and PHP error logs
- DB errors: verify credentials in `app/Config/database.php`, run `ddl.sql` and `seed.sql`
- Locale/money formatting: see `app/Services/Format.php`

## QA Checklist (Happy-path)
- Kiosk – Counter:
  1. Add 1–2 items to cart → Checkout → select Comptoir → Confirm
  2. Confirmation shows order number; auto-return to welcome after ~12s
  3. Admin: print receipt; receipt shows café header, items, totals, Comptoir note
- Kiosk – Card (simulated):
  1. Add items → Checkout → select Carte → Confirm
  2. Waiting screen shows amount and polls every ~3s
  3. Admin: “Marquer payé” → Kiosk redirects to confirmation
- Admin dashboard:
  - Scope filter Aujourd'hui/Tout works; grouped by status; can update statuses
  - Receipt prints and fallback is visible if print fails
  - Auto-refresh updates list every 15s
