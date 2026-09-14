# Rent Specialist — v1 Spec

A single-operator rental vehicle website with an admin dashboard. Public site is a read-only catalog; all bookings, customer records, and payment records are created by Staff inside the admin. The system processes nothing — it records what staff tell it.

## Operator model

- One Operator (the business), one Brand.
- One fixed Branch (the lot). Branch exists in the data model so a second branch is non-breaking later.
- Two Staff roles: **Manager** (full access, typically the Operator) and **Staff** (operational access, no settings).

## Vehicles

- Three Vehicle Types: `car`, `suv`, `motorcycle`.
- Vehicle attributes are type-conditional:
  - `car` / `suv`: seats, transmission, fuel type, doors, AC.
  - `motorcycle`: engine displacement (cc), transmission (manual/auto/scooter), helmet included.
  - Common: make, model, year, plate number, color, photos, daily rate, optional weekly/monthly rate, notes.
- Vehicle Status: `available` | `on_rent` | `maintenance` | `out_of_service` | `reserved_hold`. `on_rent` is auto-derived from confirmed bookings whose window contains "now"; the rest are manual. Status changes are timestamped and attributed to a Staff User.

## Bookings

- Lifecycle: `draft` → `confirmed` → `picked_up` → `returned` → `closed`. `cancelled` is reachable from any pre-pickup state.
- Booking carries: customer, vehicle, branch, planned pickup/return timestamps, actual pickup/return timestamps, calculated base amount, calculated overage, final charged amount (calculated, editable by Staff), notes.
- Pricing rules: billable duration = max(actual duration, 12 hours), rounded so 12–24h = 1 day, every additional 24h = +1 day. Daily rate from vehicle. If duration ≥ 7 days, weekly rate kicks in; ≥ 30 days, monthly rate kicks in. Overage is the actual return time minus planned return time, billed in hourly increments.
- Staff can override the calculated amount; the original calculation is preserved.
- A booking can be created with a `reserved_hold` even before a customer is attached, to block dates.

## Customers

- Customer records are created and maintained by Staff. No public signup.
- Fields: name, phone number, WhatsApp number, address, free-form notes.
- A Customer has many Bookings; viewing a Customer shows the booking history.
- No ID document / driver's license storage in v1 (verified in person at pickup).

## Payments

- Payment Record is a Staff-entered note on a Booking: amount, received-at, method (`cash` | `bank_transfer` | `e_wallet` | `other`), reference (e.g. transfer ID), notes.
- The system does not process payments. A Booking may have multiple Payment Records (deposit + balance). Booking has a "paid in full" flag derived from the sum of Payment Records.

## Public site (read-only)

- **Home**: hero, featured vehicles, "Chat on WhatsApp" CTA.
- **Vehicles index**: filter by type; shows photo, name, daily rate, status badge.
- **Vehicle detail**: photo gallery, specs, daily/weekly/monthly rates, **availability calendar** (read from confirmed bookings; admin still confirms), "Chat on WhatsApp" button with prefilled message containing vehicle name and date range.
- **Contact**: address, phone, WhatsApp link, map (optional).
- **About / How it works**: short page explaining the WhatsApp-first flow.
- Indonesian language only. IDR currency (Rp) everywhere.

## Admin dashboard (Filament v3)

- **Auth**: staff login (email + password), no public registration.
- **Dashboard**: at-a-glance — vehicles on rent, vehicles in maintenance, bookings today, outstanding payments, upcoming returns.
- **Vehicles**: list/create/edit/delete. Status change actions (set to maintenance, mark out of service, release). Photo upload (multiple). Pricing set per vehicle (daily required, weekly/monthly optional).
- **Customers**: list/create/edit. View booking history per customer.
- **Bookings**: list with filters (status, vehicle, customer, date range). Create booking (pick customer + vehicle + window; auto-calculates amount; staff can override). State transition actions (confirm, mark picked up, mark returned, close, cancel). View payment records, add payment record.
- **Payment records**: list/filter; create/edit per booking.
- **Staff users** (Manager only): list/create/edit; role assignment.
- **Settings** (Manager only): branch details, business hours, WhatsApp number, currency.

## Stack

- Laravel 11 (PHP 8.3).
- Filament v3 for the admin.
- Livewire 3 + Blade for the public site.
- Tailwind CSS.
- MySQL 8 in production, SQLite in local dev.
- Eloquent ORM, migrations, seeders.
- Pest for tests.
- Indonesian locale (`id`), IDR currency, money stored as integer (rupiah has no subunits).
- Single repo, single app, two route groups (`/` public, `/admin` Filament).

## Out of scope for v1

- Online booking form / customer self-serve.
- Online payment processing / payment gateway.
- Customer login / customer portal.
- Multi-branch (data model supports it; UI does not expose it).
- Multi-operator / marketplace.
- Delivery / one-way rentals.
- Dynamic or seasonal pricing.
- Identity document storage.
- Email/SMS notifications beyond Filament's defaults.
- API for third parties.
- Mobile app.

## Open follow-ups (post-v1, not blockers)

- Multi-branch UI (model already supports it).
- Online booking form as a layer on top of the existing booking flow.
- WhatsApp Business API integration (auto-send confirmation message when booking is created).
- English translation as a second language.
- Per-type pricing default + per-vehicle override.
- Vehicle inspection report (pre/post rental) with photo attach.