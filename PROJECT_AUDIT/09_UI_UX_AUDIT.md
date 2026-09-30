# 09. UI/UX Design & Usability Audit

## 1. Design System & Aesthetic Overview

| Dimension | Current State | Audit Assessment |
| :--- | :--- | :--- |
| **Color Palette** | Primary: `#EA741D` (Artizen Orange), Backgrounds: `#0C0C0E` / `#FFFFFF`, Surface: `#121214` / `#F9FAFB`. | Good brand identity, but token naming in CSS has aliases (`gold` and `artizen-orange` map to the same `#EA741D` hex), causing developer ambiguity. |
| **Typography** | Plus Jakarta Sans (Headings), Inter (Body), Instrument Sans (Tailwind v4 base). | Three different sans-serif font families are imported across different files, creating unnecessary font payload and slight rendering discrepancies. |
| **Styling Stack** | Tailwind Play CDN + `style.css` (181 KB) + Tailwind v4 Vite config. | Fragmented styling pipeline. Styles are split between inline CDN classes and an extensive stylesheet containing legacy overrides. |
| **Dark / Light Mode** | Stored in `localStorage`, classes toggled on `<html>`. | Works functionally, but some input fields in admin and modals have hardcoded light backgrounds that clash in dark mode. |

---

## 2. Major UI/UX Issues & Recommended Improvements

### Issue 1: Hybrid CSS Engine & Style Collisions
* **Current Implementation**: The website loads Tailwind Play CDN in [header.blade.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/resources/views/layouts/header.blade.php) while also loading an external 181 KB stylesheet [style.css](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/public/css/style.css).
* **Problem**: Tailwind CDN dynamically parses classes in real-time in the browser, while `style.css` uses `!important` flags that override Tailwind utility classes inconsistently.
* **User Impact**: Inconsistent layout rendering on slower mobile connections while CDN script evaluates.
* **Recommended Improvement**: Compile all styling through the existing Vite build pipeline (`npm run build`) using standard Bootstrap 5 or compiled Tailwind CSS without browser-side runtime compilers.
* **Relevant Files**: [resources/views/layouts/header.blade.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/resources/views/layouts/header.blade.php), [public/css/style.css](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/public/css/style.css).

---

### Issue 2: Navigation Mega-Menu Usability on Tablet & Mobile
* **Current Implementation**: The "Packages" menu item in [navbar.blade.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/resources/views/layouts/navbar.blade.php) opens a 2-column mega menu with `group-hover:opacity-100`.
* **Problem**: Hover-activated mega menus do not work reliably on touch screens (tablets and hybrid laptops); hovering triggers an accidental click or fails to open.
* **User Impact**: Tablet users struggle to explore specific categories directly from the header.
* **Recommended Improvement**: Convert hover trigger to an explicit click/tap accordion on touch viewports and simplify dropdown items.
* **Relevant Files**: [resources/views/layouts/navbar.blade.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/resources/views/layouts/navbar.blade.php).

---

### Issue 3: Monolithic Booking Checkout Form Friction
* **Current Implementation**: [create.blade.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/resources/views/booking/create.blade.php) renders 13+ input fields on a single long scrolling page.
* **Problem**: Long single-step forms create visual intimidation for first-time mobile visitors, increasing abandonment rate.
* **User Impact**: Potential drop-off when users are confronted with all venue and contact fields at once.
* **Recommended Improvement**: Implement a modern, multi-step progress bar (Step 1: Event & Date Selection $\rightarrow$ Step 2: Venue Address $\rightarrow$ Step 3: Customer Details & Confirmation).
* **Relevant Files**: [resources/views/booking/create.blade.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/resources/views/booking/create.blade.php).

---

### Issue 4: Admin Dashboard Monolithic Layout & Table Density
* **Current Implementation**: [dashboard.blade.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/resources/views/admin/dashboard.blade.php) renders all 9 admin management modules inside a single 3,878-line file.
* **Problem**: Bookings and Customers tables have excessive column width on screens under 1280px width, causing horizontal scrollbars.
* **User Impact**: Business owners checking booking requests on a smartphone or tablet cannot easily view customer phone numbers or trigger status changes without scrolling horizontally.
* **Recommended Improvement**: Implement responsive card-view stacking for mobile viewports while retaining standard tables for desktop.
* **Relevant Files**: [resources/views/admin/dashboard.blade.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/resources/views/admin/dashboard.blade.php).

---

### Issue 5: Repetitive Ticker Announcement Bar
* **Current Implementation**: The top announcement bar in [navbar.blade.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/resources/views/layouts/navbar.blade.php) repeats the phrase *"PLAN, BOOK & RELAX | Instant Event Booking is Now Live"* four times in a continuous CSS marquee.
* **Problem**: Text repetition is visually noisy and distracts from the core branding.
* **User Impact**: High visual distraction at the very top of the viewport on small mobile devices.
* **Recommended Improvement**: Display a single, clean dynamic promo banner (e.g. *"🎉 Indore Celebrations: Book sound rigs & decor in 2 minutes"*) with a dismiss button.
* **Relevant Files**: [resources/views/layouts/navbar.blade.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/resources/views/layouts/navbar.blade.php).

---

### Issue 6: Add-On Customization Disconnect in Details View
* **Current Implementation**: In [details.blade.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/resources/views/events/details.blade.php), inclusion checkboxes are rendered, but checking them does not add calculated prices into the "Book Now" query string before redirecting to `/booking`.
* **Problem**: Users customize inclusions expecting the final quote to reflect their selections, but the booking checkout page only receives the base tier price.
* **User Impact**: Customer confusion regarding whether their selected add-ons were recorded.
* **Recommended Improvement**: Bind add-on selections to dynamic URL query parameters or session storage so they carry through to the checkout summary and database `notes` / `surcharge_amount`.
* **Relevant Files**: [resources/views/events/details.blade.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/resources/views/events/details.blade.php), [public/js/details.js](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/public/js/details.js).
