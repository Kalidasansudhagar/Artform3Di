# ARTFORM 3Di — Website

Laravel Blade website for ARTFORM 3Di (3D printing, CAD, rapid prototyping, 3D art and drones), with a
working shop (customer accounts, bag, Buy Now, checkout, orders) and an admin panel.

## Stack

Laravel 13 · MySQL / MariaDB · Blade · Tailwind CSS 4 · Alpine.js (+ collapse, focus) · GSAP + ScrollTrigger ·
Swiper · Lucide · `@google/model-viewer` (lazy-loaded) · Vite

## Clone and run (demo machine)

Needs PHP 8.3+, Composer, Node 20+ and MySQL / MariaDB (XAMPP is fine).

```bash
git clone https://github.com/Kalidasansudhagar/Artform3Di.git
cd Artform3Di
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Then:

1. Create an empty MySQL database called `artform3di` (phpMyAdmin → New), and check `DB_USERNAME` /
   `DB_PASSWORD` in `.env` (XAMPP default: `root` with no password).
2. In `.env` set `ADMIN_EMAIL` and `ADMIN_PASSWORD` — this becomes the admin login.
3. Create the tables and content, build the assets and start the site:

```bash
php artisan migrate --seed
npm run build
php artisan serve
```

Open **http://localhost:8000** (website) and **http://localhost:8000/admin/login** (admin).

`migrate --seed` creates the tables, the admin account, 12 sample products, the projects and the contact
details. Re-running the seeder is safe. The built assets in `public/build` are committed, so `npm run build`
is only needed after changing CSS/JS.

## Demo walkthrough

1. **Admin → Products → Add product**: name, category, price, images, “Show in the shop” ticked → Save.
2. **Website → Shop**: the new product is listed; open it.
3. Choose a quantity → **Add to Cart** (bag count updates) or **Buy Now** (adds it and goes to checkout).
4. Checkout asks you to **sign in or create an account** (header person icon → Create an account), then
   returns to checkout. Enter the delivery address, choose *Cash on delivery* or *UPI / bank transfer* →
   **Place order**.
5. The customer sees the order confirmation and, under **My Account**, their orders, wishlist, saved
   address and account details.
6. **Admin → Orders**: the new order (with a badge in the sidebar and “Orders to confirm” on the dashboard).
   Open it, change the status (Pending → Confirmed → Printing → Shipped → Delivered); the customer sees the
   new status in My Account. **Admin → Customers** lists the registered account.

No payment is taken online: orders are confirmed by the studio first, then paid by cash on delivery or
UPI / bank transfer. Products without a price show “Price on request” and a **Request a Quote** button
instead of Add to Cart / Buy Now.

## Website shop

| Feature | How it works |
| --- | --- |
| Add to Cart / Buy Now | Posts to `/cart`; the bag is kept in the session. Prices and availability are always read from the database, so hidden, deleted or price-on-request products drop out of the bag. |
| Bag `/cart` | Change quantities, remove items, see the total. |
| Checkout `/checkout` | Signed-in customers only; the address is saved to the account for next time. |
| Accounts | Register, sign in (rate-limited), sign out, My Account (orders, wishlist, address, details, password). |
| Wishlist | Heart icon on any product; saved in the browser and shown in My Account → Wishlist. |
| Forgot / reset password | Screens only for now — no email is sent (needs mail settings). |

## Admin panel

Sign in at **`/admin/login`** with the admin account from `.env`, then change the password in **My profile**.

| Section | What it does |
| --- | --- |
| Dashboard | Live enquiry figures (7 days → 12 months), enquiries awaiting reply, orders to confirm, products in shop |
| Orders | Every shop order — filter by status, search, see items / customer / address, update status, internal notes, contact by email / WhatsApp / phone |
| Enquiries | Every website quote/contact form submission — search, filter by status, add notes, reply, delete |
| Customers | Registered shop accounts (orders, amount spent) and everyone who has sent an enquiry |
| Projects | Add / edit / hide / delete the project tiles on the Home and 3D Printing pages (with image upload) |
| Products | Add / edit / hide / delete shop products (up to 3 images, price or “price on request”, badge, specs) |
| Settings | Phone, email, address, hours and social links used across the website |
| My profile | Name, sign-in email and password |

Security: admin routes require a signed-in user with `is_admin` (customers get “403 Forbidden”); admin and
customer sign-in are rate-limited, the website forms are throttled and the contact form has a honeypot field.
Customers can only open their own orders. Uploaded images are stored in `public/uploads` (git-ignored, so they
are not part of a clone) — no storage symlink is needed on shared hosting.

To create another admin: `php artisan tinker` → `App\Models\User::create([...])->forceFill(['is_admin' => true])->save();`

## Deploying to Hostinger

1. In hPanel create a MySQL database + user, then set `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (host `localhost`)
   and `ADMIN_EMAIL` / `ADMIN_PASSWORD` in the server's `.env`, with `APP_ENV=production` and `APP_DEBUG=false`.
2. Over SSH in the project folder: `composer install --no-dev --optimize-autoloader`, `php artisan migrate --seed --force`,
   `php artisan optimize`.
3. Make sure `public/uploads` is writable.

## Tests

Tests use a separate MySQL database, `artform3di_test` (create it once, see `phpunit.xml`): `php artisan test`.
They cover the admin panel, dynamic content, and the full shop flow (register → bag → Buy Now → checkout →
admin order).

## Pages

| Route | Name | View |
| --- | --- | --- |
| `/` | `home` | `frontend/home.blade.php` |
| `/shop` | `shop.index` | `frontend/shop.blade.php` |
| `/shop/{slug}` | `shop.show` | `frontend/product-details.blade.php` |
| `/3d-printing` | `services` | `frontend/services.blade.php` |
| `/drones` | `drones` | `frontend/drones.blade.php` |
| `/about` | `about` | `frontend/about.blade.php` |
| `/contact` | `contact` | `frontend/contact.blade.php` |
| `/cart` | `cart.index` | `frontend/cart.blade.php` |
| `/checkout` | `checkout` | `frontend/checkout.blade.php` |
| `/login` | `login` | `frontend/account/login.blade.php` |
| `/register` | `register` | `frontend/account/register.blade.php` |
| `/account` | `account` | `frontend/account/dashboard.blade.php` |
| `/account/orders/{order}` | `account.orders.show` | `frontend/account/order.blade.php` |
| `/forgot-password` | `password.request` | `frontend/account/forgot-password.blade.php` |
| `/admin` | `admin.dashboard` | `admin/dashboard.blade.php` (layout: `layouts/admin.blade.php`) |

The shop filters (category, search, sort, price) run client-side and sync to `?category=` and `?q=`,
so links such as `/shop?category=spiritual-art` work.

## Structure

```
app/Http/Controllers/          FrontendController (pages), AccountController (customer accounts),
                               CartController, CheckoutController, EnquiryController, Admin/*
app/Models/                    User, Product, Project, Order, OrderItem, Enquiry, Setting
app/Support/Catalog.php        Products / projects in the array shape the views use, plus seed data
app/Support/Cart.php           Session bag
app/Support/AdminStats.php     Dashboard figures
config/artform.php             Default contact details, socials, hero video paths (overridden by Admin → Settings)

resources/views/layouts/       app (website) and admin
resources/views/components/    header, footer, product-card, quick-view, toast, form-field, admin/* …
resources/views/frontend/      one view per page, account/* for customer pages
resources/views/admin/         admin pages

resources/css/app.css          Design tokens (@theme), components, motion states
resources/js/modules/alpine.js Alpine stores and components (bag, wishlist, auth and contact forms)
```

## Replacing placeholder content

- **Products and projects**: manage them in the admin panel (images upload to `public/uploads`).
- **Contact details and socials**: Admin → Settings. Empty values show “… to be confirmed” and are never invented.
- **Hero video**: add `public/assets/videos/hero-printing.mp4` (and optionally `hero-printing-mobile.mp4`).
  See `public/assets/videos/README.md`.
- **3D models**: set a product's `model` to a `.glb` path to enable the interactive 3D viewer on its page.

## Motion and accessibility

Animations respect `prefers-reduced-motion`. Append `?motion=off` to any URL to disable animation
(useful for QA and screenshots). Icons used via `<x-icon>` must be registered in `resources/js/modules/icons.js`.
