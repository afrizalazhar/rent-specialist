# Rent Specialist

A single-operator rental vehicle website with an admin dashboard for managing vehicles, bookings, and customers. Vehicles are passenger cars, SUVs, and motorcycles, all picked up and returned at a single fixed lot.

The public site is a **read-only vehicle catalog**. Customers browse vehicles and contact the operator over WhatsApp. The operator (or staff) creates the booking, the customer record, and the payment record inside the admin. There is no online booking form, no online payment, and no customer self-serve account in v1.

## Language

**Vehicle**:
A rentable unit of inventory owned by the operator. Each vehicle has a fixed type that determines which attribute fields apply and which booking rules apply to it. Each Vehicle carries a daily rate and optional weekly and monthly rates (the weekly/monthly rates are discount tiers on the same vehicle, not separate units).
_Avoid_: Car (too narrow — motorcycles are also vehicles here)

**Vehicle Type**:
The category of a Vehicle. One of `car`, `suv`, or `motorcycle`. A discriminator that gates which attribute fields and booking rules apply.
_Avoid_: Category (too generic), class

**Vehicle Status**:
The operational state of a Vehicle. One of `available`, `on_rent` (auto-set while a confirmed booking is in its window), `maintenance` (in the shop, not bookable), `out_of_service` (longer-term off the catalog, e.g. awaiting part), or `reserved_hold` (Staff has blocked specific dates without a customer). Status changes are timestamped and attributed to a Staff User.
_Avoid_: State (too generic)

**Branch**:
A pickup/return location owned by the operator. v1 has exactly one branch; the concept exists in the data model so adding a second branch is non-breaking.
_Avoid_: Location (too generic), store, lot

**Booking**:
A reservation of a single Vehicle at a single Branch for a defined window, **created by Staff inside the admin** (not by the customer online). The booking lifecycle is `draft` → `confirmed` → `picked_up` → `returned` → `closed`, with `cancelled` reachable from any pre-pickup state. A Booking records pickup and return timestamps (actual, not planned) and the final charged amount (calculated from the window but editable by Staff).
_Avoid_: Reservation, order, rental

**Rental Day**:
A 24-hour unit of booking duration. Priced at the Vehicle's daily rate.
_Avoid_: Day (ambiguous about the half-day floor)

**Half-Day**:
A 12-hour minimum billable window. Any booking shorter than 12 hours is still billed for 12 hours; any booking between 12 and 24 hours is billed for one Rental Day. The system calculates billable duration from the booking's start and end timestamps; Staff can override the calculated amount.
_Avoid_: Minimum rental (vague)

**Overage Charge**:
Extra charge billed when a vehicle is returned later than the booking's planned return time, computed in hourly increments on top of the booking's base amount.
_Avoid_: Late fee (too generic)

**Customer**:
A person who rents vehicles. Customer records are **created and maintained by Staff** in the admin (no public self-signup). A Customer record stores the customer's name, phone number, WhatsApp number, address, and a history of their Bookings. The system does not store identity documents or driver's license information in v1.
_Avoid_: User (too generic), account, buyer

**Operator**:
The single business entity that owns the fleet, sets pricing, and runs the admin dashboard. The Operator is the business; they are not a user in the system.
_Avoid_: Admin, owner, company, tenant

**Staff User**:
An employee of the Operator who logs into the admin dashboard. Has a role (`manager` or `staff`). Distinct from a Customer.
_Avoid_: Admin user, back-office user

**Manager**:
A Staff User with full access to the admin: vehicle setup, pricing, staff management, and bookings. There is typically one Manager (the Operator themselves).
_Avoid_: Super admin, owner user

**Payment Record**:
A Staff-entered note attached to a Booking recording that money was received, in what amount, when, and by which method. The system does **not** process payments — it only records them. A Booking may have multiple Payment Records (e.g., a deposit then a balance).
_Avoid_: Transaction, charge (implies the system moved money)