# Agent Brief — Rent Specialist build

This file is for the sub-agents who are continuing this build. The orchestrator has already written the foundation, the data layer, and the business logic. Two parallel lanes are now being dispatched:

- **Lane A (Designer)** — public site Blade views, Blade components, layout, Indonesian language file, plus Filament theme polish
- **Lane B (Fixer)** — Filament resources, additional Filament pages/widgets, final config files, README, and verification

The orchestrator's work is committed in the repo and the contracts below are authoritative. Do **not** redesign anything described here; build on top of it.

---

## Read first

Before doing anything else, read these in order:

1. `CONTEXT.md` — the domain glossary (Vehicle, Booking, Half-Day, etc.)
2. `SPEC.md` — the v1 scope
3. `docs/adr/0001-whatsapp-first-operator-model.md` — the foundational decision
4. `tailwind.config.js` — the design tokens (the **source of truth** for the visual language)
5. `resources/css/app.css` — the public-site base CSS and component classes
6. `resources/css/filament/admin.css` — the Filament admin theme overrides

---

## Design direction (non-negotiable)

- **Neomorphism.** Soft, extruded surfaces. Two-shadow signature: a light highlight (top-left) and a warm shadow (bottom-right) on every raised element. Inset surfaces for inputs and "pressed" states.
- **Warm cream / clay palette.** Background `#ecebe8` (surface-200). Raised surfaces `#f5f4f1` (surface-100). Inset `#d8d6d2` (surface-300). Primary text `#2c2a28` (surface-800). Accent `#b85a35` (clay-400).
- **No AI-blue gradients. No glossy 3D. No purple/teal "AI" palettes.** No drop shadows on text. No glassmorphism. No floating decorative blobs.
- **Type:** Inter for body, Plus Jakarta Sans for headings. Tight letter-spacing on display text. Generous line-height.
- **Shape language:** rounded corners (the `neu` radius is 20px), pill-shaped status badges, inset photo frames.
- **Micro-interactions:** buttons become inset (pressed) on `:active` and on `:hover` for primary. No bouncy spring animations. Motion is `cubic-bezier(0.4, 0, 0.2, 1)` and short (150ms).

The Tailwind config has the tokens; the CSS files have the component classes. **Use them.** Do not invent new shadow definitions or new colors.

## Copy

- All UI copy is in **Indonesian** (Bahasa Indonesia). No English fallback in v1.
- Tone: warm, polite, professional. Not "AI marketing speak". No exclamation marks in body copy. No emojis.
- Currency: IDR. Format: `Rp 350.000` (period as thousands separator, no decimal). Use the `@rupiah($amount)` Blade directive.

## Component classes to use

These are defined in `resources/css/app.css`. Use them in your templates:

- `.neu-surface` — generic raised card
- `.neu-surface-sm` — smaller raised card
- `.neu-inset` / `.neu-inset-sm` — inset / "carved" surface
- `.neu-btn` — default button (light surface, neutral)
- `.neu-btn-primary` — primary CTA (clay accent)
- `.neu-input` / `.neu-select` — form controls
- `.neu-pill` + `.neu-pill-success` / `-warning` / `-danger` / `-neutral` — status badges
- `.neu-frame` — inset frame for photos
- `.neu-paper` — page background with the subtle paper grid

For Filament, the theme CSS in `resources/css/filament/admin.css` re-binds the default Filament classes to the same tokens. You do not need to write per-widget CSS.

## File map (what the orchestrator already wrote)

```
app/
  Enums/
    BookingStatus.php
    PaymentMethod.php
    StaffRole.php
    VehicleStatus.php
    VehicleType.php
  Http/
    Controllers/
      Controller.php
      HomeController.php
      PageController.php
      VehicleAvailabilityController.php
      VehicleController.php
    Middleware/
      SetLocale.php
  Livewire/
    AvailabilityCalendar.php
  Models/
    Booking.php
    Branch.php
    Customer.php
    PaymentRecord.php
    StaffUser.php
    Vehicle.php
  Providers/
    AppServiceProvider.php       (registers @rupiah, @datetime_id, @date_id directives)
    Filament/AdminPanelProvider.php
  Services/Pricing/
    PricingCalculator.php
    PricingResult.php

bootstrap/
  app.php                        (registers SetLocale middleware)
  providers.php                  (lists AppServiceProvider)

config/
  app.php                        (locale=id, timezone=Asia/Jakarta)
  auth.php                       (StaffUser as the auth model, 'staff' guard)
  business.php                   (business name, address, WhatsApp, hours)
  database.php                   (sqlite default; mysql block commented)
  filesystems.php
  money.php                      (currency code, symbol, subunit divisor)

database/
  migrations/
    0001_01_01_000000_create_staff_users_table.php
    0001_01_01_000001_create_sessions_table.php
    0001_01_01_000002_create_branches_table.php
    0001_01_01_000003_create_vehicles_table.php
    0001_01_01_000004_create_customers_table.php
    0001_01_01_000005_create_bookings_table.php
    0001_01_01_000006_create_payment_records_table.php
  factories/
    BookingFactory.php
    BranchFactory.php
    CustomerFactory.php
    PaymentRecordFactory.php
    StaffUserFactory.php
    VehicleFactory.php
  seeders/
    DatabaseSeeder.php           (1 branch, 1 manager, 1 staff, 10 vehicles)

resources/
  css/
    app.css                      (public site base)
    filament/admin.css           (Filament theme override)
  js/
    app.js                       (Alpine + Livewire bootstrap)
    bootstrap.js                 (axios)

routes/
  console.php
  web.php                        (public routes — do not change paths)

tests/
  Pest.php
  TestCase.php
  Unit/PricingCalculatorTest.php (10 passing scenarios already written)

tailwind.config.js
vite.config.js
postcss.config.js
package.json
composer.json
.env.example
artisan
```

---

## Lane A — Designer (the public site)

You own the public site. Specifically:

### 1. Layouts and components

- `resources/views/layouts/app.blade.php` — the master layout. Extends the `<head>` with the Inter + Plus Jakarta Sans fonts (use `<link rel="preconnect">` + Google Fonts), a navigation bar (logo, "Kendaraan", "Tentang", "Kontak" + a "Chat di WhatsApp" CTA button on the right), the main content area, and a footer with the business contact details.
- `resources/views/components/` — at least:
  - `neu-button.blade.php` (variant: primary|default, accepts `href` or `type`)
  - `neu-input.blade.php` (label, name, type, value, placeholder, required)
  - `neu-card.blade.php` (slot for content; optional title, optional footer slot)
  - `neu-pill.blade.php` (variant: success|warning|danger|neutral, slot for text)
  - `nav-bar.blade.php`
  - `site-footer.blade.php`
  - `whatsapp-cta.blade.php` — phone number from `config('business.whatsapp_number')` or a default, plus prefilled message text

### 2. Public pages

- `resources/views/home.blade.php` — hero with the business name and a clay-CTA "Lihat Kendaraan" button; "Cara pesan" 3-step section ("Pilih kendaraan → Chat WhatsApp → Ambil di tempat"); featured vehicles grid (up to 6 cards from `$featured`).
- `resources/views/vehicles/index.blade.php` — filter chips at the top (Semua, Mobil, SUV, Sepeda Motor) with the count; paginated grid of vehicle cards.
- `resources/views/vehicles/show.blade.php` — gallery (use `$vehicle->photos[0]` as the main image; if empty, render a placeholder with the vehicle's initial in `.neu-frame`), specs grid (`$vehicle->spec('seats')` etc, iterating over `$vehicle->type->specFields()`), price card (daily, weekly, monthly), and a Livewire `<livewire:availability-calendar :vehicle="$vehicle" />`. Bottom CTA: "Chat di WhatsApp" with prefilled text "Halo, saya tertarik dengan {vehicle} untuk tanggal {start} sampai {end}." (dates blank for the user to fill in).
- `resources/views/livewire/availability-calendar.blade.php` — month grid (Senin–Minggu header), each day cell is `.neu-inset-sm` with a different style for past days (faded) and unavailable days (a clay-toned inset), and a nav row above with "‹" and "›" buttons calling `previousMonth` / `nextMonth`. The month label is `Maret 2026` (Indonesian) using the `getMonthLabelProperty`.
- `resources/views/pages/about.blade.php` — short story: 1 paragraph on the business, 3 paragraphs on the WhatsApp-first flow, no marketing fluff.
- `resources/views/pages/contact.blade.php` — address from `config('business.address')`, WhatsApp deep link, hours, an embedded map (just an `<a>` to the `map_url` for v1; no iframe).

### 3. Routes reference

The controller routes already exist; do not change `routes/web.php`. The data your views receive:

- `home()` — `$branch`, `$featured` (collection of Vehicle)
- `vehicles.index()` — `$vehicles` (LengthAwarePaginator of Vehicle), `$activeType` (VehicleType|null), `$typeCounts` (array<string,int>)
- `vehicles.show($vehicle)` — `$vehicle` (Vehicle with branch loaded)
- `pages.about()` — no variables
- `pages.contact()` — `$branch` (Branch|null)
- `vehicles.availability` JSON endpoint — `{ "unavailable": [ { "from": "YYYY-MM-DD", "to": "YYYY-MM-DD" } ] }`

### 4. Indonesian language file (optional but useful)

- `resources/lang/id/app.php` — a small flat array of strings used across the site: "Beranda", "Kendaraan", "Tentang", "Kontak", "Tersedia", "Tidak tersedia", etc. Use it as `__('app.Available')` from Blade. (Laravel 11 ships with a `lang/` folder; create one.)

### 5. Vehicle card design notes

The card sits in `.neu-surface`, has a 16:9 photo at the top (wrapped in `.neu-frame p-2`), then a body with the display name in `font-display`, a small `neu-pill` for type, and the daily rate in a single line. The whole card is a link to `route('vehicles.show', $vehicle)`.

### 6. Things to NOT do

- Do not invent new colors or shadow tokens. Use what `tailwind.config.js` exports.
- Do not use `bg-gradient-to-*` Tailwind utilities anywhere.
- Do not use any icon library beyond `lucide` (added by Filament; you can `<x-filament-icon name="...">`) or simple inline SVG. Avoid emoji.
- Do not write JS for the calendar — it is a Livewire component, the Livewire class is already there.
- Do not add a search/filter on the listing page in v1 (out of scope per SPEC).

---

## Lane B — Fixer (the admin + final wiring)

You own the Filament admin and the rest of the wiring.

### 1. Filament resources

For each of the following, create a `Filament\Resources\{Name}Resource` with full CRUD, filters, and appropriate form fields. The shape of the model is in `app/Models/`. Use the enums for selects.

- `VehicleResource` — list columns: display name, type badge, status badge, daily rate. Form sections: Identity (branch, type, make, model, year, plate, color), Specs (render `$vehicle->type->specFields()` dynamically — for `car`/`suv` show seats/transmission/fuel/doors/ac; for `motorcycle` show engine_cc/transmission/helmet), Pricing (daily, weekly, monthly), Media (multi-photo upload to `public` disk, stored in `$vehicle->photos`), Status (status select + notes), and an Actions group for status changes (set maintenance, release).
- `CustomerResource` — list: name, phone, whatsapp, booking count. Form: name, phone, whatsapp, address, notes. Relation manager for bookings (optional but nice).
- `BookingResource` — list: vehicle, customer, planned window, status, charged amount. Form: customer picker (searchable), vehicle picker (filtered to `isBookable` statuses), branch (auto-set from vehicle's branch), planned pickup/return (`DateTimePicker`), status select. The `charged_amount` field is editable. Add a wizard step or a "Recalculate" action that calls `Booking::recalculateAmounts($calc)` and updates `calculated_base_amount` / `calculated_overage_amount`. The `actual_pickup_at` and `actual_return_at` are separate fields. Use the `BookingStatus` enum's `allowedTransitions()` to constrain state-change actions.
- `PaymentRecordResource` — list: booking, amount, method, received_at, recorded_by. Form: booking picker, amount, received_at, method, reference, notes, recorded_by (auto-set to current staff user, hidden from picker, assigned in `beforeCreate`).
- `StaffUserResource` (Manager-only) — list: name, email, role. Form: name, email, password (Hash::make on save), role. Hide the resource from non-Manager users with `static::canAccess()` checking `auth()->user()->isManager()`.

### 2. Filament dashboard

- `app/Filament/Pages/Dashboard.php` — extends Filament's default Dashboard; override `getWidgets()` to add a custom widget. Or, simpler, add a widget class.
- `app/Filament/Widgets/StatsOverview.php` — 4 stat cards: "Sedang Dirental" (count of bookings in `PickedUp`), "Akan Kembali Hari Ini" (count of bookings with planned_return_at = today and status in {Confirmed, PickedUp}), "Belum Dibayar" (sum of `outstandingAmount()` across active bookings), "Tersedia" (count of vehicles with `status = Available`).

### 3. Config files

The orchestrator wrote `app.php`, `auth.php`, `database.php`, `filesystems.php`, `business.php`, `money.php`. You may need to add or polish:

- `config/filament.php` — only if you need non-default behavior.
- Ensure `config/cache.php`, `config/session.php`, `config/view.php` exist; if not, create minimal ones. (Laravel 11 ships these; only add if you find them missing.)

### 4. Indonesian language file for Filament

- `resources/lang/id/filament.php` — translate the Filament defaults you'll see in the admin. At minimum: navigation group names, resource labels, "Save", "Cancel", "Create", "Edit", "Delete". Use the `filament-spatie-translatable` package is NOT needed for v1; just provide a flat `id/filament.php` array.

### 5. README

- `README.md` — what this is, the architecture in 4 paragraphs, setup steps (`composer install`, `cp .env.example .env`, `php artisan key:generate`, `touch database/database.sqlite`, `php artisan migrate --seed`, `npm install`, `npm run build`, `php artisan storage:link`, `php artisan serve`). Include the demo login credentials (`manager@rent.local` / `staff@rent.local`, both `password`). Include a "Demo flow" section: log in, create a vehicle, create a customer, create a booking, mark picked up, record a payment, mark returned, close. Note: a MySQL variant is documented in `.env.example`.

### 6. Final checks

- After writing everything, run `php -l` on every file you touched. (You can do this via `find . -name "*.php" -not -path "./vendor/*" -not -path "./node_modules/*" | xargs -I{} php -l {}` if `find` works.)
- Confirm the public site loads at `/`, the admin at `/admin`, and the demo data appears on the public site after `php artisan db:seed`.
- Confirm the WhatsApp CTA links to `https://wa.me/{number}` with the right prefilled text.
- If you find a real bug in the orchestrator's code (not just an LSP complaint), fix it but call it out in your report.

### 7. Things to NOT do

- Do not change the public site design tokens, the Blade view paths, the routes, the migration files, the model classes, or the PricingCalculator. Those are contracts.
- Do not add a payment gateway, online booking form, or customer login. They are out of scope.
- Do not introduce additional npm packages beyond what is already in `package.json` unless you have a clear, minimal need (e.g., a small icon set).
- Do not write Blade views. That is Lane A.
