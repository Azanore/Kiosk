# Moroccan Café Kiosk – Project Defaults

This document codifies the default stack, conventions, and rules for the MVP implementation. It complements `ai_coding_factors.md` and removes ambiguity during development.

## 1) Stack & Runtime
- Language: PHP 8.2 (pure PHP, no framework)
- Web server: Apache (XAMPP)
- Database: MySQL 8.x (InnoDB, utf8mb4)
- Front-end: Vanilla HTML/CSS/JS (no Bootstrap, no build tools)
- Printing: Browser print to thermal; on‑screen fallback
- Timezone: Africa/Casablanca
- Locale/UI: French; currency suffix: "DH"

## 2) Architecture
- Pattern: Conventional MVC
- Entry point: `public/index.php` (single front controller)
- Routing: Query param or path style — default: `?r=controller/action` with optional `id`
- Layers:
  - Controllers: `app/Controllers/*Controller.php`
  - Services: `app/Services/*Service.php`
  - Models: `app/Models/*Model.php`
  - Views: `app/Views/{controller}/{view}.php`
  - Config: `app/Config/*.php`

## 3) Directory Structure (recommended)
```
project/
  public/
    index.php
    assets/
      css/
      js/
      img/
  app/
    Controllers/
    Services/
    Models/
    Views/
    Config/
```

## 4) Database Conventions
- DB name: `kiosk`
- Table names: snake_case plural (e.g., `orders`, `order_items`)
- Engine/charset: `ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci`
- IDs: `BIGINT UNSIGNED` auto‑increment primary keys where appropriate
- Timestamps: `created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP`, `updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP`
- Enums: Use MySQL `ENUM` for bounded fields
  - `orders.status ENUM('awaiting_payment','paid','preparing','ready','completed','cancelled')`
  - `orders.payment_method ENUM('card','counter')`
  - `orders.order_type ENUM('eat_in','takeaway')`
- JSON: `order_items.options_json JSON`
- Display number: `display_number INT`, `display_date DATE`, unique key on (`display_date`, `display_number`), daily reset at local midnight
- Foreign keys: `ON DELETE CASCADE` from `order_items` to `orders`

## 7) Error Handling
- Controller level: try/catch with user‑safe messages
- Display: suppress detailed errors in production; show concise French messages to users
- Fallback: if printer fails, show large order number on screen

## 8) Security (MVP‑minimal)
- Admin auth: email + password using `password_hash()`/`password_verify()` (Argon2id if available)
- Sessions: native PHP sessions; regenerate ID on login; `httponly`, `secure` (when HTTPS), `samesite=Lax`
- Database access: prepared statements only (mysqli/PDO)
- Input: basic sanitization/validation; escape output in views
- CSRF: omitted for MVP (admin only, trusted environment)

## 9) Sessions & State
- Kiosk cart: stored in PHP session
- Admin session: email + id; timeout configurable (default 30 minutes of inactivity)
- Inactivity: kiosk resets after 90s idle; confirmation screen auto‑returns after ~12s

## 10) Payment & Polling
- Terminal: standalone (cashier dashboard marks Paid)
- Polling: kiosk polls `orders.status` every 3s; global timeout 2 minutes; offer Retry/Switch to Counter on failure

## 15) Environment Config
- Config files: `app/Config/app.php`, `app/Config/database.php`
- Sample database config keys: `host`, `port`, `database`, `username`, `password`, `charset=utf8mb4`

## 16) Alignments with docs
- Use MySQL (not PostgreSQL)
- Keep three categories (Hot/Cold Drinks, Pastries) and MVP fields as per `documentation.md`
- Store per‑item options in `order_items.options_json`

---
These defaults are intentionally pragmatic for a single‑kiosk MVP while leaving room to scale to a few kiosks without major refactors.
