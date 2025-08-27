# UI Layout Refactor TODO

Non‑negotiable: no feature/logic changes, no route or payload changes. Structural HTML + external CSS only.

Stylesheets:
- Global: `public/assets/css/app.css` (link on all pages)
- Optional per-page: e.g., `/assets/css/admin_login.css`, `/assets/css/admin_orders.css`, `/assets/css/admin_menu.css`, `/assets/css/kiosk_welcome.css`, etc.
- Link order in views: global first, then page-specific.

Order: Admin → Kiosk → Collection.

## Admin
- [ ] admin_login — Update `app/Views/admin/login.php`
  - Add global stylesheet link; add optional `admin_login.css`; apply admin shell structure; keep existing form fields; remove any “MVP” mentions.
- [ ] admin_orders — Update `app/Views/admin/orders.php`
  - Admin header shell; filters section; grouped sections per status with `<table>` and sticky `<thead>`; add global + optional `admin_orders.css` links; remove “MVP” mentions.
- [ ] admin_menu — Update `app/Views/admin/menu.php`
  - Admin header shell; Categories (add form + table) and Produits (filter + add form + table); sticky `<thead>`; add global + optional `admin_menu.css` links; remove “MVP” mentions.

## Kiosk
- [ ] kiosk_welcome — Update `app/Views/kiosk/welcome.php`
  - No global header; `<main>` with title + start action; add global + optional `kiosk_welcome.css` links.
- [ ] kiosk_categories — Update `app/Views/kiosk/categories.php`
  - `<main>` with `<h1>` + grid of category links; minimal back link; add global + optional `kiosk_categories.css` links.
- [ ] kiosk_products — Update `app/Views/kiosk/products.php`
  - `<main>` with `<h1>` + product grid; back link; add global + optional `kiosk_products.css` links.
- [ ] kiosk_product_detail — Update `app/Views/kiosk/product_detail.php`
  - `<article>`: header (title), figure (image), details (desc, price), actions (qty + add), back link; add global + optional `kiosk_product_detail.css` links.
- [ ] kiosk_cart — Update `app/Views/kiosk/cart.php`
  - `<main>` with `<h1>`, items list, summary (totals), proceed/back; add global + optional `kiosk_cart.css` links.
- [ ] kiosk_checkout — Update `app/Views/kiosk/checkout.php`
  - `<main>` with `<h1>`, order type, payment method, review, actions; add global + optional `kiosk_checkout.css` links.
- [ ] kiosk_confirm — Update `app/Views/kiosk/confirm.php`
  - `<main>` with big order number + summary + new order link; add global + optional `kiosk_confirm.css` links.
- [ ] kiosk_waiting_payment — Update `app/Views/kiosk/waiting_payment.php`
  - `<main>` with order number, amount, status, spinner, back link; add global + optional `kiosk_waiting_payment.css` links.

## Collection
- [ ] display_collection — Update `app/Views/display/collection.php`
  - `<header>` bar; `<main>` with two sections (En préparation/Prêtes) number grids; add global + optional `display_collection.css` links.
