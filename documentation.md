# ☕ Moroccan Café Self-Ordering Kiosk – MVP Specification

This document defines the minimum viable product (MVP) for a self‑ordering kiosk for a Moroccan café. It focuses on essentials only: a simple, reliable customer flow, basic admin operations, receipt printing, and a minimal database. The backend is a small PHP MVC with query‑param routing via `public/index.php`.

---

## 🎯 Project Scope

* Single kiosk, in‑café ordering only.
* Categories: Hot Drinks, Cold Drinks, Pastries. (No sandwiches, no packs.)
* Customers browse products, pay (Card or Counter/Cash), get an order number; the kiosk shows a confirmation screen (large order number). Admin can reprint a receipt from the dashboard.
* Admin: login, manage menu (basic CRUD + availability), view active orders and update statuses.

---

## ✅ MVP Functional Requirements

### Customer (Kiosk)

* Browse categories (Hot Drinks, Cold Drinks, Pastries).
* View products (name, image, price).
* Product detail (no per‑item customizations in MVP).
* Add to cart, edit quantity/remove, see totals.
* Choose order type: Eat‑In or Takeaway.
* Pay method: Card or Counter (cash at cashier).
* Show confirmation screen with large order number; no auto print popup.

### Écran de collecte

* Écran client toujours allumé (navigateur) affichant les numéros « En préparation » et « Prêtes ».
* Rafraîchissement automatique toutes les 3 secondes et son léger quand une commande devient « Prête » (fichier audio placeholder prêt).
* Message permanent: « Récupérez votre reçu au kiosque ».

### Admin (Web Dashboard)

* Login (email + password).
* Active orders board with status updates.
* Manage categories and products (name, price, image, availability).

---

## 🧭 UI Flow (Kiosk)

1) Welcome
   * Buttons: Start Order
   * On start → Categories

2) Categories
   * Grid: Hot Drinks, Cold Drinks, Pastries
   * Tap → Product List
   * Cart preview (count + total)

3) Product List → Product Detail
   * Product detail shows relevant customizations only
   * Add to Cart → back to Product List

4) Cart
   * Items with customizations, qty edit, remove
   * Subtotal and total
   * Proceed to Checkout

5) Checkout
   * Order Type: Eat‑In | Takeaway
   * Payment: Card | Counter (cash)
   * On confirm: create order with status=awaiting_payment and assign display number

6) Payment and Confirmation
  * Card (two modes, configured by `app/Config/app.php` → `payment_provider`):
    * `simulator` (default): in‑app test terminal at `?r=order/startTestPayment&id=...` with Approve/Decline actions. On Approve → order is marked `paid` and kiosk shows the big Order Number.
    * `terminal`: standalone terminal flow. Kiosk shows "Waiting for card payment"; staff marks Paid in admin. Kiosk polls `?r=order/pollStatus&id=...` every ~3s. On Paid → show the big Order Number. On timeout/failure → offer Retry or Switch to Counter.
  * Counter:
    * Immediately show the big Order Number with clear message. Cashier/staff can mark Paid later in the dashboard.
  * Receipt: auto‑printed by the kiosk (no on‑screen print button or popup). If printer fails, the on‑screen number is the fallback; staff assists.
  * Auto return to Welcome after 10–15s (configurable via `confirm_return_seconds`).

---

## 🧾 Receipt

Printed fields:

* Café name/logo
* Order number (large)
* Date/time
* Items + qty
* Total + payment method (Card/Counter)
* Statut de paiement: « Payé » (carte) ou « En attente — payer au comptoir » (cash)
* Amounts displayed in MAD (suffix "DH") using French number format (e.g., 12,50 DH)
* For Counter payments, include a clear label: "Pay at counter"
* Thank you/footer

Behavior:
* Admin reprint page `?r=order/printReceipt&id=...` renders the receipt and exposes a manual "Imprimer" button (no auto popup). A link back to the dashboard is provided.
* Kiosk confirmation shows the order number; staff can assist if printing is needed.

---

## 🧺 Menu (Morocco‑focused)

MVP shows product name, image, and base price only. Per‑item customizations are out of scope for this MVP and can be added later.

### Hot Drinks

* Espresso
  * Size: single | double
  * Sugar: 0–3
  * Extra: extra shot (+)

* Nos Nos (Noss‑Noss)
  * Size: small | large
  * Sugar: 0–3

* Café Crème / Café au Lait
  * Size: small | large
  * Sugar: 0–3
  * Milk: regular | skim

* Moroccan Mint Tea (Atay b’naanaa)
  * Size: glass | pot
  * Sweetness: no sugar | less | normal

* Black Tea
  * Size: glass | pot
  * Sugar: 0–3

### Cold Drinks

* Fresh Orange Juice
  * Size: small | large
  * Sugar: no | yes
  * Ice: no | yes

* Avocado Smoothie
  * Size: small | large
  * Sugar: no | yes
  * Milk: no | yes

* Banana Milkshake
  * Size: small | large
  * Sugar: no | yes

* Bottled Water
  * Size: small | medium

* Soft Drink (can)
  * No customizations

### Pastries

* Croissant
  * Warm: no | yes

* Pain au Chocolat
  * Warm: no | yes

* Msemen
  * Warm: no | yes
  * Spread: none | honey | cheese

* Baghrir
  * Warm: no | yes
  * Spread: none | honey | amlou

---

## 🗄️ Database Schema (Minimal)

### `users` (admin login)

* `id` (PK)
* `email` (varchar, UNIQUE)
* `password_hash` (varchar)
* `is_active` (boolean)
* `created_at` (timestamp)
* `updated_at` (timestamp)

### `categories`

* `id` (PK)
* `name` (varchar)
* `is_active` (boolean)

### `products`

* `id` (PK)
* `category_id` (FK → categories.id)
* `name` (varchar)
* `description` (text, optional)
* `base_price` (decimal(6,2))
* `image_url` (varchar, optional)
* `is_available` (boolean)

### `orders`

* `id` (PK)
* `display_number` (int)  // daily reset queue number printed on receipt
* `display_date` (date)
* `status` (enum: awaiting_payment, paid, preparing, ready, completed, cancelled)
* `payment_method` (enum: card, counter)
* `order_type` (enum: eat_in, takeaway)
* `total_price` (decimal(7,2))
* `created_at` (timestamp)
* `updated_at` (timestamp)
* `paid_at` (timestamp, nullable)

### `order_items`

* `id` (PK)
* `order_id` (FK → orders.id)
* `product_id` (FK → products.id)
* `product_name` (varchar)  // snapshot at time of order
* `quantity` (int)
* `price_each` (decimal(6,2))
* `line_total` (decimal(7,2))
* `options_json` (json)  // reserved for future customizations; unused in current MVP (stored as NULL)

Notes:
* No separate options tables in MVP. When/if customizations are added, selections can be stored as JSON on the order item; for now `options_json` remains NULL.
* Constraints: FK `order_items.order_id` ON DELETE CASCADE; `products.category_id` ON DELETE RESTRICT; `order_items.product_id` ON DELETE RESTRICT; non-negative checks on prices; `quantity >= 1`; unique (`display_date`,`display_number`).
* Default ordering: items are ordered alphabetically by `name` ASC.
* MySQL specifics: InnoDB engine; charset `utf8mb4` (`utf8mb4_unicode_ci`). Use `TIMESTAMP DEFAULT CURRENT_TIMESTAMP` and `ON UPDATE CURRENT_TIMESTAMP` for audit fields.
* Data types: use `ENUM` for bounded fields (e.g., `orders.status`, `orders.payment_method`, `orders.order_type`); `JSON` type for `order_items.options_json`.
* Timezone: Africa/Casablanca for timestamps and daily reset of `display_number` (enforced in app logic).

---

## 🔧 Technical Notes

### Hardware
* Touchscreen kiosk
* Thermal receipt printer
* Standalone card terminal (from local bank/acquirer)
* Network (Wi‑Fi/LAN)

### Software
* Kiosk UI: HTML, CSS, minimal JavaScript (touch‑optimized)
* Backend: Pure PHP MVC with query‑param routing via `public/index.php` (e.g., `?r=controller/action`)
* Database: MySQL 8
* Admin Dashboard: HTML/CSS/JS served by the same PHP backend (basic auth, orders board, menu CRUD)

### Localization & Timezone
* UI language: French; currency suffix "DH" with French number formatting (e.g., 12,50 DH). See `app/Config/app.php` keys `currency_suffix` and `number_locale`.
* Timezone: Africa/Casablanca (used for timestamps and daily order-number reset). See `app/Config/app.php` key `timezone`.

### Error Handling
* Card payment: either simulated in‑app (`payment_provider=simulator`) or via standalone terminal with admin marking Paid (`payment_provider=terminal`). Kiosk may poll order status via `?r=order/pollStatus&id=...`.
* Reprint via admin receipt page if needed; confirmation screen serves as fallback
* Inactivity timeout → cancel order and reset to Welcome

---

## 🔄 State Model (Unified)

orders.status transitions:
* awaiting_payment → paid → preparing → ready → completed
* cancelled (explicit cancel or timeout)

Auto‑cancel rule:
* Orders that remain `awaiting_payment` older than configurable minutes are automatically marked `cancelled` when the admin orders page loads. The window is set by `app/Config/app.php` → `auto_cancel_minutes` (default 15).

---

## 📋 Admin/Manager (Minimal)

* Login
* Active orders board (grouped by status, search by number, scope Today/All, auto‑refresh 15s)
  * For `awaiting_payment`: only two buttons are shown — “Marquer payé” and “Annuler”.
  * For other statuses: MVP generic select to set status to paid/preparing/ready/completed/cancelled. Invalid transitions from awaiting_payment are blocked server‑side.
  * Status badges are shown per order; “Imprimer” is hidden for cancelled.
* Menu management: add/edit products and categories; toggle activation/availability via “Activer/Désactiver” buttons. Status badges replace activation checkboxes.
  * Product toggle redirects to unfiltered menu; save product keeps filter after edit but not after create.

---

## 📈 Admin Stats Dashboard (using current schema only)

The following KPIs and reports can be built now using only the existing tables/fields (`orders`, `order_items`, `products`, `categories`). Unless stated, consider only paid/fulfilled orders i.e., `orders.status IN ('paid','preparing','ready','completed')` and exclude `cancelled`.

### KPIs (essentials)
* **Sales today**
* **Orders today**
* **Average ticket size (AOV)**
* **Paid rate** (share of non-cancelled orders that are paid/in-progress)

### Mix/Breakdowns (essentials)
* **By order type**: Eat‑In vs Takeaway
* **By payment method**: Card vs Counter
* **By category**: revenue and quantity
* **Top products**: top 5 by revenue (last 7 days)
* **Busy hours**: orders by hour (today)

### Dashboard UI (simple)
* **Top tiles**: Sales today | Orders today | AOV | Paid rate.
* **Charts**: Sales by hour (today), Category mix, Top products.
* **Tables**: Recent orders with status; Top 10 items last 7 days.
* **Filters**: Date range, order type, payment method, status.

### Notes
* Use `order_items.product_name` for product labels (snapshot at sale time).
* Exclude `cancelled` from sales metrics; include statuses at/after `paid`.

---

## ✅ Summary

This MVP keeps the scope tight and practical for a Moroccan café: three core categories, simple per‑item customizations, card or counter payment, receipt printing, and a minimal schema. It avoids complexity so the app can be built and used quickly, with room to extend later if needed.
