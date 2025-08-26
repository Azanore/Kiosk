# ✅ MVP TODO – Moroccan Café Kiosk

Legend: [planned] | [in_progress] | [done]

---

## Non‑Negotiable Rules (Read First)
- This is an MVP. Deliver minimal, reliable functionality. No extras. [done]
- Code quality: pragmatic and conventional, not perfect. Prefer simplicity. [done]
- Follow `documentation.md` and `PROJECT_DEFAULTS.md` exactly. Deviations require explicit approval. [done]
- Keep UI in French, currency “DH”, timezone Africa/Casablanca. [done]
- Do not ask for confirmation; proceed in the most logical order and update this TODO. [done]

---

## 1) Architecture & Scaffold
- Create directory structure per `PROJECT_DEFAULTS.md` (`public/`, `app/{Controllers,Services,Models,Views,Config}/`, `public/assets/{css,js,img}/`). [done]
- Add `public/index.php` as single front controller with simple router (`?r=controller/action`). [done]
- Add base Controller class and minimal view renderer helper. [done]

## 2) Config & Environment
- Add `app/Config/app.php` (timezone, locale, idle timeouts, formatting). [done]
- Add `app/Config/database.php` (host, port, db, user, pass, charset). [done]
- Set PHP default timezone to Africa/Casablanca at bootstrap. [done]

## 3) Database (DDL + Seed)
- Write MySQL DDL for `users`, `categories`, `products`, `orders`, `order_items` with enums, FKs, unique (`display_date`,`display_number`). [done]
- Create minimal seed: one admin user, 3 categories, a few products with images. [done]

## 4) Kiosk UI (Customer Flow)
- Views: welcome, categories grid, product list, product detail (basic options), cart, checkout, confirmation. [done]
- Session cart: add/update/remove items; compute totals. [done]
- Simple, touch‑friendly CSS; store images in `public/assets/img/`. [done]

## 5) Backend Controllers & Services
- `KioskController`: welcome, categories, products, productDetail, cart, checkout, confirm. [done]
- `OrderController`: pollStatus, printReceipt. (création gérée par `KioskController` via `OrderService`) [done]
- Services: `MenuService`, `OrderService` (display number + daily reset), `ReceiptService`, `AuthService`. [done]

## 6) Admin (Minimal Dashboard)
- `AuthController`: login/logout using `password_hash()/verify()`. [done]
- Orders board: list active orders; update status (paid → preparing → ready → completed; cancel). [done]
- Menu CRUD: categories/products basic create/edit/toggle availability. [done]

## 7) Payment & Polling (Standalone Terminal)
- On checkout (Card): show amount; create order `awaiting_payment`; kiosk polls `/order/pollStatus` every ~3s (2 min timeout). [done]
- On Counter: mark as `awaiting_payment` with method `counter`; show order number immediately. [done]
- Dashboard action to mark order Paid. [done]

## 8) Receipt Printing (Thermal)
- HTML receipt template: logo/name, big order number, date/time, items, totals, method (Card/Counter), “Pay at counter” when applicable, “DH” with French formatting. [done]
- Print via browser; fallback: keep on‑screen number if printer error. [done]

## 9) Localization & Formatting
- Number formatting helper for French style (e.g., 12,50 DH). [done]
- All UI strings in French (short, clear). [done]

## 10) Error Handling & Timeouts
- Inactivity: kiosk resets after ~90s; confirmation auto‑returns after ~12s. [done]
- Polling failure: offer Retry or Switch to Counter. [done]
- Suppress detailed errors in prod; user‑safe messages. [done]

## 11) QA & Handover
- Happy‑path test: card and counter flows end‑to‑end. [done]
- Admin: login, set paid, update statuses, toggle product availability. [done]
- README quick start: import SQL, configure DB, run on XAMPP, default admin. [done]
