# UI Layout Plan (Mid-level, Structure-Only)

This plan defines semantic structure and shared patterns only.

- Global stylesheet: `public/assets/css/app.css`
- Optional per-page stylesheets (if needed for page-specific tweaks), e.g.:
  - Admin: `/assets/css/admin_login.css`, `/assets/css/admin_orders.css`, `/assets/css/admin_menu.css`
  - Kiosk: `/assets/css/kiosk_welcome.css`, `/assets/css/kiosk_products.css`, etc.
  - Display/Order: `/assets/css/display_collection.css`, `/assets/css/order_receipt.css`, `/assets/css/order_test_terminal.css`
  - Link order in views: global first, then page css.
- Desktop-only, French/Moroccan currency, no icons
- Remove all “MVP” mentions

## Non‑Negotiable Rules
- Do not change any feature behavior or business logic.
- Do not alter routes, form names, request/response formats, or JS polling intervals.
- Purely structural/semantic HTML and external CSS hookup only.

## Shared
- Admin shell on all admin pages: `<header>` (title + nav: Commandes, Menu, Déconnexion) + `<main>`; no footer.
- Kiosk pages have no global header; focused `<main>` with minimal back links.
- Landmarks: use `<header>`, `<main>`, `<section>`, `<nav>`; page `<h1>` inside `<main>`.
- Tables use sticky `<thead>`; no horizontal scroll—wrap/truncate where needed.
- Small pages rule: if content is static and fits within 100vh, center it both horizontally and vertically (use a centered wrapper) and avoid scroll.

## Admin Style Direction
- Minimalist, clean, light theme; neutral grays with blue accent for primary actions.
- Typography: system UI stack (or Inter), 14–16px body, 1.25–1.5 line-height.
- Spacing scale: 4/8/12/16/24/32; ample whitespace.
- Tables: dense but readable, sticky headers; optional zebra rows.
- Forms: labels above inputs; full-width fields; consistent error text below.
- Badges: gray (neutral), blue (info), green (paid/ready), amber (pending), red (cancelled) with AA contrast.
- Elevation/motion: flat UI, subtle shadows only for overlays; minimal transitions.

### Admin Hard Rules
- 1px borders.
- No border radius for most elements (inputs, buttons, cards, tables); exceptions allowed for tiny UI like badges.
- No shadows (cards, panels, tables). Keep the interface flat.

## Admin (`app/Views/admin/`)
- `login.php`
  - `<main>` > `<section>` > `<h1>` + login `<form>` (email, password) + small error area.
- `orders.php`
  - Admin header; `<main>`:
    - Filters/actions section (search, scope).
    - For each status: `<section>` with `<h2>` + `<table>` (thead sticky) with columns: #, Date, Statut, Paiement, Type, Total, Actions.
- `menu.php`
  - Admin header; `<main>`:
    - Categories: heading, add form, categories `<table>`.
    - Produits: filter select, add form, products `<table>`.

## Kiosk (`app/Views/kiosk/`)
- `welcome.php`: `<main>` > `<section>` > `<h1>` + primary start action.
- `categories.php`: `<main>` > `<h1>` + grid of category links; back link.
- `products.php`: `<main>` > `<h1>` + grid of product cards; back link.
- `product_detail.php`: `<main>` > `<article>`: header (title), figure (image), details (desc, price), actions (qty + add), back link.
- `cart.php`: `<main>` > `<h1>` + items list + summary (totals) + proceed; back link.
- `checkout.php`: `<main>` > `<h1>` + order type section + payment method section + review section + confirm/back actions.
- `confirm.php`: `<main>` > `<section>` > big order number, brief summary, new order link.
- `waiting_payment.php`: `<main>` > `<section>` > order number, amount, status text, spinner container, back link.

## Display (`app/Views/display/`)
- `collection.php`: `<header>` (title + hint); `<main>` with two sections: “En préparation” and “Prêtes” as number grids.

## Order (`app/Views/order/`)
- `receipt.php` (print): `<main>` > `<article>`: café header (name, address, phone), date/time, order number, items, totals (payment + type), counter note when applicable; on-screen actions (Imprimer, Retour). Print via `@media print`, portrait.
- `test_terminal.php`: `<main>` > `<section>` > `<h1>` + order number/total + actions nav (approve/decline). Align with app styling.

## Implementation Notes
- Add `<link rel="stylesheet" href="/assets/css/app.css">` to each view, then an optional page-specific `<link>` as needed.
- Keep POST for mutating actions; GET for navigation.
