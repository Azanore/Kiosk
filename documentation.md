# ☕ Moroccan Café Self-Ordering Kiosk – MVP Specification

This document defines the minimum viable product (MVP) for a self‑ordering kiosk for a Moroccan café. It focuses on essentials only: a simple, reliable customer flow, basic admin operations, receipt printing, and a minimal database.

---

## 🎯 Project Scope

* Single kiosk, in‑café ordering only.
* Categories: Hot Drinks, Cold Drinks, Pastries. (No sandwiches, no packs.)
* Customers browse, customize basics, pay (Card or Counter/Cash), get an order number and printed receipt.
* Admin: login, manage menu (basic CRUD + availability), view active orders and update statuses.

---

## ✅ MVP Functional Requirements

### Customer (Kiosk)

* Browse categories (Hot Drinks, Cold Drinks, Pastries).
* View products (name, image, price).
* Product detail with basic, per‑item customizations (see Menu below).
* Add to cart, edit quantity/remove, see totals.
* Choose order type: Eat‑In or Takeaway.
* Pay method: Card (via standalone terminal) or Counter (cash at cashier).
* Show confirmation screen with order number and print receipt.

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
   * Card:
     * Show amount and "Waiting for card payment" screen
     * Dashboard marks Paid in the dashboard; kiosk polls order status every ~3s (timeout after ~2 minutes). On Paid → show Order Number and print receipt. On timeout/failure → offer Retry or Switch to Counter.
     * If failure: offer Retry or Switch to Counter
   * Counter:
     * Immediately show Order Number and print receipt with "Pay at counter"; cashier marks Paid later in the dashboard.
   * Show big Order Number
   * Print receipt (thermal). If printer fails, show on‑screen number; staff can assist.
   * Auto return to Welcome after 10–15s

---

## 🧾 Receipt Printing (MVP)

Printed fields:

* Café name/logo
* Order number (large)
* Date/time
* Items + qty
* Total + payment method (Card/Counter)
* Amounts displayed in MAD (suffix "DH") using French number format (e.g., 12,50 DH)
* For Counter payments, include a clear label: "Pay at counter"
* Thank you/footer

If printer error: show error toast and keep the on‑screen number visible.

---

## 🧺 Menu (Morocco‑focused)

Customizations are minimal and per‑item. Prices may adjust for size/extras.

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
* `sort_order` (int, optional)

### `products`

* `id` (PK)
* `category_id` (FK → categories.id)
* `name` (varchar)
* `description` (text, optional)
* `base_price` (decimal(6,2))
* `image_url` (varchar, optional)
* `is_available` (boolean)
* `sort_order` (int, optional)

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
* `options_json` (json)  // chosen customizations per item

Notes:
* No separate options tables in MVP. All selections stored as JSON on the order item. Price modifiers are baked into `price_each`/`line_total` at checkout.
* Constraints: FK `order_items.order_id` ON DELETE CASCADE; non-negative checks on prices; `quantity >= 1`; unique (`display_date`,`display_number`).
* Default ordering: if `sort_order` is NULL, order by `name` ASC; if set, order by `sort_order` ASC then `name` ASC.
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
* Backend: Pure PHP (REST endpoints)
* Database: MySQL 8
* Admin Dashboard: HTML/CSS/JS served by the same PHP backend (basic auth, orders board, menu CRUD)

### Localization & Timezone
* UI language: French; currency suffix "DH" with French number formatting (e.g., 12,50 DH)
* Timezone: Africa/Casablanca (used for timestamps and daily order-number reset)

### Error Handling
* Card payment with standalone terminal (local Morocco): kiosk shows amount and waits while the dashboard marks Paid; kiosk polls order status. On Paid → proceed; on failure → allow Retry or Switch to Counter.
* Printer error → show on‑screen order number; staff assists
* Inactivity timeout → cancel order and reset to Welcome

---

## 🔄 State Model (Unified)

orders.status transitions:
* awaiting_payment → paid → preparing → ready → completed
* cancelled (explicit cancel or timeout)

---

## 📋 Admin/Manager (Minimal)

* Login
* Active orders board (update statuses)
* Menu management: add/edit/remove products, toggle availability

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
