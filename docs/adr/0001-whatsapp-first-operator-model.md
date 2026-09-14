# WhatsApp-first operator model

The Operator runs a small vehicle rental business (cars, SUVs, motorcycles) where customers contact the business over WhatsApp to arrange a rental. The system supports this by being a public read-only catalog and an internal admin tool, **not** an online booking platform. Concretely: no public booking form, no customer self-serve account, no online payment processing. All Bookings, Customer records, and Payment Records are created by Staff inside the admin, reflecting what was negotiated over WhatsApp.

## Why

A small single-operator rental business in our market does not have the volume or the customer behaviour to justify online booking and online payment. Customers prefer to negotiate via WhatsApp (price flexibility, ask questions, get a personal reply). Forcing them into a self-serve form would add friction and lose the personal relationship that drives repeat business. We optimise for the staff workflow: the admin is the system of record, and the website's only job is to show the fleet and route customers to WhatsApp.

## Consequences

- The public site has no booking form, no login, no checkout. It is a static-feeling catalog with WhatsApp CTAs.
- Payment Records are notes, not transactions. The system never moves money. A Booking has zero or more Payment Records; a "paid in full" flag is derived.
- The Customer entity is owned and maintained by Staff, not by customers themselves. There is no Customer login, no Customer signup, no Customer dashboard.
- The availability calendar on the public site is *advisory*: it reads from confirmed bookings, but Staff can still create a booking that conflicts with the calendar. The calendar helps customers self-qualify; the admin is the source of truth.
- Adding online booking / payment later is a non-breaking addition: the Booking flow already lives in the admin, and adding a public form on top reuses it. Adding a payment gateway later adds a real charge path alongside the existing Payment Record.
- Customer data captured is minimal (name, phone, WhatsApp, address). Identity documents and driver's license info are verified in person at pickup and are not stored in the system.