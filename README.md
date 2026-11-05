# ARTFORM 3Di — Website (Frontend Phase)

Premium Laravel Blade frontend for ARTFORM 3Di: 3D art, creative manufacturing and drone engineering.

This phase is **frontend only**. There is no database, authentication, cart, payment or admin
functionality yet; those interactions are visual placeholders, ready for the backend phase.

## Stack

Laravel 13 · Blade · Tailwind CSS 4 · Alpine.js (+ collapse, focus) · GSAP + ScrollTrigger ·
Swiper · Lucide · `@google/model-viewer` (lazy-loaded) · Vite

## Run locally

```bash
composer install
npm install
cp .env.example .env && php artisan key:generate
npm run build        # or: npm run dev
php artisan serve
```

Sessions and cache use the file driver, so no database is needed in this phase.

## Pages

| Route              | Name          | View                               |
| ------------------ | ------------- | ---------------------------------- |
| `/`                | `home`        | `frontend/home.blade.php`          |
| `/shop`            | `shop.index`  | `frontend/shop.blade.php`          |
| `/shop/{slug}`     | `shop.show`   | `frontend/product-details.blade.php` |
| `/3d-printing`     | `services`    | `frontend/services.blade.php`      |
| `/drones`          | `drones`      | `frontend/drones.blade.php`        |
| `/about`           | `about`       | `frontend/about.blade.php`         |
| `/contact`         | `contact`     | `frontend/contact.blade.php`       |
| `/login`           | `login`       | `frontend/account/login.blade.php` |
| `/register`        | `register`    | `frontend/account/register.blade.php` |
| `/forgot-password` | `password.request` | `frontend/account/forgot-password.blade.php` |
| `/reset-password/{token?}` | `password.reset` | `frontend/account/reset-password.blade.php` |
| `/account`         | `account`     | `frontend/account/dashboard.blade.php` |
| `/admin`           | `admin.dashboard` | `admin/dashboard.blade.php` (layout: `layouts/admin.blade.php`) |

The shop filters (category, search, sort, price) run client-side and sync to `?category=` and `?q=`,
so links such as `/shop?category=spiritual-art` work.

## Structure

```
app/Http/Controllers/FrontendController.php   Page controllers (swap Catalog for Eloquent later)
app/Support/Catalog.php                       Placeholder products, collections and projects
app/Support/PlaceholderArt.php                On-brand SVG placeholder generator
config/artform.php                            Contact details, socials, hero video paths

resources/views/layouts/app.blade.php
resources/views/components/   header, footer, button, product-card, logo, icon, eyebrow,
                              contact-cta, process-timeline, contact-detail, social-links,
                              search-overlay, quick-view, toast
resources/views/frontend/     one view per page

resources/css/app.css         Design tokens (@theme), components, motion states
resources/js/app.js           Entry point
resources/js/modules/         alpine (stores/components), animations, sliders, hero, transitions, icons

public/assets/images|videos|icons
```

## Replacing placeholder content

- **Contact details and socials**: set `ARTFORM_EMAIL`, `ARTFORM_PHONE`, `ARTFORM_ADDRESS`, `ARTFORM_HOURS`,
  `ARTFORM_INSTAGRAM`, `ARTFORM_LINKEDIN`, `ARTFORM_YOUTUBE` and `ARTFORM_FACEBOOK` in `.env`. Unset values show
  "… to be confirmed" and are never invented.
- **Hero video**: add `public/assets/videos/hero-printing.mp4` (and optionally `hero-printing-mobile.mp4`).
  The homepage switches from the animated canvas print sequence to the footage automatically. See
  `public/assets/videos/README.md` for the shot list.
- **Product and project imagery**: replace the image paths in `app/Support/Catalog.php` (or the files in
  `public/assets/images/`). Placeholders can be regenerated with `php artisan artform:placeholders`.
- **3D models**: set a product's `model` to a `.glb` path to enable the interactive 3D viewer on its page.
- **Accounts**: the account icon opens a sign-in drawer; the login, register, forgot/reset password and
  dashboard screens validate in the browser (`authForm` in `resources/js/modules/alpine.js`) but send nothing.
  Route names match Laravel's auth conventions, so Fortify or Breeze can take over these URLs in the backend phase.
- **Admin dashboard**: `/admin` shows KPIs, a revenue chart, order statuses, recent orders, enquiries and
  top products from `App\Support\AdminDemo` (sample data). It has no login yet — add `auth` + an admin-role
  middleware to the `admin` route group, and swap each `AdminDemo` method for real queries, in the backend phase.
  Other admin sections are listed in the sidebar as "Soon".
- **Team**: the About page renders a team section when a `$team` array is passed to the view.

## Motion and accessibility

Animations respect `prefers-reduced-motion`. Append `?motion=off` to any URL to disable animation
(useful for QA and screenshots). Icons used via `<x-icon>` must be registered in `resources/js/modules/icons.js`.
