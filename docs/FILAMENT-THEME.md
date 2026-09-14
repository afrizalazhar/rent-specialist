# Filament Admin Theme Guide

This guide helps you (or another non-developer) find and edit the Filament admin theme for **Rent Specialist**.

The theme is built to be modular: each UI area lives in its own CSS file, and the entry file only imports them. Filament's chrome (sidebar shell, topbar shell, page header shell, layout base) is **not** customized — Filament's defaults render it. Only the descendants are restyled.

---

## Quick map: "I want to change X"

| I want to change... | Start here |
|---|---|
| Page background, fonts, base neumorphism utilities | `resources/css/filament/_base.css` |
| Sidebar item colors, hover, active state | `resources/css/filament/_sidebar.css` |
| Top bar button / nav item / user-menu avatar style | `resources/css/filament/_topbar.css` |
| Page heading / title typography | `resources/css/filament/_header.css` |
| Dashboard stat cards (color, accent stripe, icon) | `resources/css/filament/_stats.css` + `app/Filament/Widgets/StatsOverview.php` |
| Widget section cards, chart canvas | `resources/css/filament/_widgets.css` |
| Dashboard layout / welcome text | `app/Filament/Pages/Dashboard.php` + `resources/views/filament/pages/dashboard.blade.php` |
| Recent bookings table widget | `app/Filament/Widgets/RecentBookings.php` |
| Fleet status bars widget | `app/Filament/Widgets/VehicleStatusBreakdown.php` + `resources/views/filament/widgets/vehicle-status-breakdown.blade.php` |
| Revenue chart widget | `app/Filament/Widgets/RevenueChart.php` |
| Buttons | `resources/css/filament/_buttons.css` |
| Badges | `resources/css/filament/_badges.css` |
| Form inputs, selects, textareas | `resources/css/filament/_forms.css` |
| Tables | `resources/css/filament/_tables.css` |
| Modals / dialogs | `resources/css/filament/_modals.css` |
| Toast notifications | `resources/css/filament/_notifications.css` |
| Pagination | `resources/css/filament/_pagination.css` |
| Login page | `resources/views/filament/pages/auth/login.blade.php` + `resources/views/filament/layouts/login.blade.php` + `resources/css/filament/_login.css` |
| Dark mode look | `resources/css/filament/_dark.css` |
| Primary color, sidebar behavior, max width | `app/Providers/Filament/AdminPanelProvider.php` |
| Fonts | `tailwind.config.js` (`theme.extend.fontFamily`) — body font comes from Filament's `->font()` setting |
| Translations / labels | `lang/en/filament.php` |

---

## What this theme does NOT do

- It does **not** copy or modify Filament's layout / sidebar / topbar / header Blade views. Those live in `vendor/filament/filament/resources/views/...` and are battle-tested. Modifying them breaks Alpine Float teleports (user-menu dropdown), sidebar click behavior, and responsive breakpoints — which is exactly what an earlier attempt did. **Keep it that way.**
- It does **not** add `overflow: hidden` or `overflow-x-clip` to the topbar or sidebar wrappers. Those properties clip Alpine Float's teleported dropdowns.

If you ever need to change the sidebar/topbar structure (e.g. add a custom header), copy the relevant vendor view into `resources/views/vendor/filament/...` and edit it — but be aware of the side effects. Prefer CSS for chrome look-and-feel.

---

## How the theme is built

### Entry CSS file

`resources/css/filament/admin.css` is the entry point Vite builds. It contains only `@import` statements and the three Tailwind directives:

```css
@import "./_base.css";
@import "./_sidebar.css";
/* ... one partial per UI area ... */

@tailwind base;
@tailwind components;
@tailwind utilities;
```

**Do not add long rules to `admin.css`.** Put them in the partial that matches the UI area.

### CSS partials

Every partial starts with a comment that says what it controls and which Filament classes it targets. Open the partial and edit the values inside. Most colors are written as plain hex values so you can change them without knowing Tailwind.

### Specificity: `.fi-body` prefix

Every override selector is prefixed with `.fi-body`. Because `@import` must come before `@tailwind`, the partial rules are inlined before Tailwind utilities. The `.fi-body` prefix raises specificity (one extra class) so our overrides reliably win against Tailwind utilities of the same name.

### Tailwind config

`tailwind.config.js` tells Tailwind which files to scan. It includes `resources/css/filament/*.css`. It also has a `safelist` array. The safelist keeps Filament classes in the compiled CSS even when Tailwind cannot find them in the scanned source (for example, classes used only as CSS selectors in our partials). If you add a new override in a CSS partial and the style does not appear, add the class name to the safelist.

### PostCSS import

We added `postcss-import` so `admin.css` can `@import` partials. It is installed via `npm install`.

---

## How to extend the theme

### Restyle a new widget

1. Add the PHP class in `app/Filament/Widgets/`.
2. Register it in `app/Providers/Filament/AdminPanelProvider.php` inside `->widgets([...])`.
3. If the widget uses a custom Blade view, put the view under `resources/views/filament/widgets/`.
4. Style it by adding rules to the matching partial (usually `_widgets.css` or `_stats.css`).
5. If you reference a new Filament class, add it to `tailwind.config.js` `safelist`.

### Add a new Filament descendant override

1. Find the class name in the browser inspector (it usually starts with `fi-`).
2. Confirm it is a **descendant**, not a chrome wrapper (chrome wrappers are `.fi-sidebar`, `.fi-topbar`, `.fi-header`, `.fi-layout`, `.fi-main*`, `.fi-logo` — leave those alone).
3. Add your CSS rule to the matching partial, prefixed with `.fi-body` for specificity.
4. Add the class name to `tailwind.config.js` `safelist`.
5. Run `npm run build` and `php artisan view:clear`.

### Change the color palette

The palette is defined in two places:

1. `tailwind.config.js` under `theme.extend.colors` — gives you Tailwind utility classes like `bg-surface-100`, `text-sage-400`.
2. The actual CSS rules in `resources/css/filament/_*.css` — use hex values directly for Filament overrides.

To change a color everywhere, update both places.

### Change fonts

1. Edit `app/Providers/Filament/AdminPanelProvider.php` `->font('Inter')` for the body font.
2. Edit `tailwind.config.js` `theme.extend.fontFamily` for the display font.
3. The heading font is **Plus Jakarta Sans**. The body font is whatever Filament is configured with.

---

## Color palette reference

| Token | Use |
|---|---|
| `surface-50` to `surface-100` | White / raised card backgrounds |
| `surface-200` | Page background |
| `surface-300` | Borders, dividers |
| `surface-500` | Muted text |
| `surface-700` | Body text |
| `surface-900` | Headings, primary accent |
| `clay-400` / `surface-900` | Primary black accent |
| `sage-400` / `sage-500` | Success / available |
| `amber-400` / `amber-500` | Warning / on rent |
| `rust-400` / `rust-500` | Danger / out of service, outstanding balance |

---

## Shadow reference

| Shadow | Use |
|---|---|
| `shadow-neu` | Large raised surfaces |
| `shadow-neu-sm` | Cards, buttons, inputs |
| `shadow-neu-lg` | Modals, big emphasis |
| `shadow-neu-inset` | Inset panels |
| `shadow-neu-inset-sm` | Small inset inputs |
| `shadow-neu-pressed` | Active/pressed state |
| `shadow-neu-clay` | Primary buttons |

These come from `tailwind.config.js` `theme.extend.boxShadow`.

---

## Important caveats

### Do not copy or override Filament's chrome Blade views

A previous attempt copied `vendor/filament/filament/resources/views/components/sidebar/index.blade.php` and similar into `resources/views/vendor/filament/components/...` to customize them. This **broke** the admin:

- The sidebar background was set to transparent on desktop, making it look invisible.
- The mobile responsive breakpoints were re-implemented by hand and broke.
- The user-menu dropdown could not teleport correctly because a wrapper had `overflow-x-clip`.

The theme was rewritten to **not** copy chrome Blade views. **Do not reintroduce them.** If you need a chrome-level change, edit the CSS partial that matches the area, or change the panel provider config (`AdminPanelProvider.php`).

### Clear caches after editing

```bash
npm run build
php artisan view:clear
```

If you add new classes to the safelist, also run `php artisan filament:cache-components` if Filament has cached components.

### Translations fall back to the key

The app uses `__('filament.Some Key')`. If a key is missing from `lang/en/filament.php`, Filament displays the key itself. Add missing translations there when you see raw keys in the UI.

### Widget order

Widgets appear on the dashboard in the order they are listed in `AdminPanelProvider->widgets([...])`. Each widget also has `protected static ?int $sort = N;` as a backup. If the order looks wrong, check both places.

### Widget responsive columns

Each widget sets `$columnSpan` as either a scalar (`'full'` or `1`) or a breakpoint array. The dashboard grid (`Dashboard::getColumns()`) defaults to 1 col on mobile / 2 cols on `md`+. Widgets should be `'full'` on mobile (so they stack) and `1` on `md`+ (so they sit side-by-side). See `RevenueChart.php` and `RecentBookings.php` for the pattern.

---

## Verification checklist

When you finish editing the theme:

- [ ] `npm run build` completes without errors.
- [ ] `php artisan view:clear` runs successfully.
- [ ] The sidebar is visible on desktop and toggleable on mobile.
- [ ] The user menu dropdown opens and closes cleanly (not stuck open).
- [ ] Sidebar links are clickable on desktop and mobile.
- [ ] Mobile sidebar toggle works.
- [ ] No `.fi-...` classes you styled are missing from the compiled CSS (check `safelist`).

If something looks broken, the first thing to check is whether the missing class is in `tailwind.config.js` `safelist`. The second thing to check is whether you accidentally re-introduced a chrome Blade view under `resources/views/vendor/filament/`.