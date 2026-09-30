# 12. Master Project Technical Summary & Architecture Guide

## 1. What This Project Actually Does

**ARTIZEN** is an event setup and package booking application for **Indore, Madhya Pradesh**. It enables hosts to browse complete event setups (Birthday decor, high-bass house party sound rigs, romantic proposal setups, wedding stages, acoustic setups) and submit booking requests with venue logistics (Indore area, address, date, time slot, guest count).

### Operational Core:
* **No Online Payment Gateway**: Customers pay an advance deposit offline upon phone/WhatsApp confirmation by the event manager.
* **Dual-Driver Persistence**: The platform utilizes a custom bridge (`JsonStorageService`) supporting relational MySQL Eloquent tables (`categories`, `packages`, `package_tiers`, `bookings`, `customers`, etc.) while maintaining flat JSON backups in `storage/app/`.
* **Lead Tracking**: Customers track booking requests at `/track-booking` using their unique Booking ID (e.g., `BK-8844`) and registered phone number.
* **Unified Admin Panel**: Business owners manage bookings, change statuses (`Pending` $\rightarrow$ `Contacted` $\rightarrow$ `Confirmed` $\rightarrow$ `Completed` $\rightarrow$ `Cancelled`), update package prices, and edit CMS content.

---

## 2. Technical Architecture & Implementation Summary

```
+-----------------------------------------------------------------------------------------------+
|                                      ARCHITECTURE MATRIX                                      |
+-------------------+---------------------------------------------------------------------------+
| Backend           | Laravel 12 (PHP 8.2+), MVC Architecture, Eloquent ORM                     |
| Database          | MySQL 8.x / MariaDB with 12 core tables & 15 migrations                    |
| Storage Bridge    | `JsonStorageService` (MySQL DB primary with auto JSON sync fallback)      |
| Frontend          | Blade Views + Tailwind Play CDN + Custom CSS (181 KB) + Vanilla JavaScript|
| UI Components     | Swiper.js (Sliders), CKEditor 5 (CMS editor), Chart.js (Analytics)        |
| Notifications     | Laravel Mail (`BookingReceivedNotification`) + `WhatsAppSmsService` stub  |
| Admin Middleware  | `AdminMiddleware` checking `isAdmin()` and session tokens                 |
+-------------------+---------------------------------------------------------------------------+
```

---

## 3. Existing vs Missing Capabilities

| Functional Domain | Implemented | Missing / Requires Development |
| :--- | :--- | :--- |
| **Public Catalog** | Homepage, Categories, Packages, Details, Tier Switcher, Gallery, About Us, Contact. | Clean compiled CSS without Play CDN overhead, mobile-friendly mega menu. |
| **Booking Flow** | 13-field checkout form, server-side price validation, atomic ID generation (`BK-XXXX`), tracking page. | Multi-step progress bar, Indore locality autocomplete dropdown. |
| **Admin Panel** | Analytics dashboard, booking status updater, categories CRUD, package/tier editor, FAQs, reviews, gallery, CMS settings. | Decomposition of monolithic 3,878-line Blade file into modular partials. |
| **Authentication** | Admin login (`/admin/login`), user registration (`/register`). | Customer Login (`/login`), Google OAuth (Socialite), Password Reset, Customer Dashboard. |
| **WhatsApp System** | Click-to-chat links (`wa.me`), notification service structure. | Meta WhatsApp Cloud API credentials, interactive `[Approve]` / `[Reject]` templates, Webhook endpoint and background queue processor. |

---

## 4. Critical Technical Limitations & Risks

1. **Admin Auto-Provisioning Backdoor**: [AdminAuthController.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/app/Http/Controllers/Admin/AdminAuthController.php) auto-authenticates any email string containing `"admin"`, overriding existing passwords. **Must be removed before production deployment.**
2. **View Cache Flush on Every Admin Request**: `DashboardController.php` deletes compiled Blade views on every dashboard load, adding unnecessary disk I/O latency.
3. **Hybrid Styling Runtime**: Using Tailwind Play CDN in production causes runtime DOM parsing overhead.
4. **Disconnected User $\leftrightarrow$ Customer Tables**: `users` (credentials) and `customers` (CRM records) are not linked via a foreign key `user_id`.

---

## 5. Recommended Development Roadmap

```
+-----------------------------------------------------------------------------------------+
|                                RECOMMENDED DEV SEQUENCE                                 |
+-----------------------------------------------------------------------------------------+
|                                                                                         |
|  PHASE 1: Security Hardening & Asset Clean-up                                           |
|  - Remove admin auto-provisioning backdoor in AdminAuthController.                      |
|  - Remove view unlinking in DashboardController.                                        |
|  - Switch Tailwind Play CDN to compiled Vite CSS bundle / Bootstrap 5.                  |
|                                                                                         |
|  PHASE 2: Customer Authentication & Google OAuth                                        |
|  - Install `laravel/socialite`.                                                         |
|  - Add `google_id`, `avatar` to `users` and link `customers.user_id` -> `users.id`.     |
|  - Create `/login`, `/auth/google/redirect`, and `/auth/google/callback`.               |
|                                                                                         |
|  PHASE 3: Customer Portal & Dashboard                                                   |
|  - Build Customer Dashboard layout (`/customer/dashboard`).                             |
|  - Display active and past celebration bookings with live status timelines.             |
|                                                                                         |
|  PHASE 4: UI/UX Redesign & Multi-step Booking Form                                      |
|  - Redesign public views with modern, premium styling (clean cards, smooth animations). |
|  - Convert single-page booking form into an intuitive 3-step wizard.                   |
|                                                                                         |
|  PHASE 5: Meta WhatsApp Cloud API & Webhook Automation                                  |
|  - Configure Meta WhatsApp Business Cloud API.                                          |
|  - Create `POST /api/webhooks/whatsapp` with signature verification.                    |
|  - Implement interactive WhatsApp approval buttons and automatic database status sync.  |
|                                                                                         |
+-----------------------------------------------------------------------------------------+
```

---

## 6. Questions Requiring Client Clarification

1. **Meta WhatsApp Business Account**: Does the business currently have an active Meta Business Manager account with a verified WhatsApp phone number?
2. **Preferred Transactional Email Provider**: Which SMTP service should be integrated (Amazon SES, SendGrid, Mailgun, or standard cPanel SMTP)?
3. **Multi-City Expansion Schedule**: Are additional cities (Bhopal, Ujjain, Jabalpur) planned for immediate rollout, or should Indore remain the sole active city during launch?
4. **Customer Login Requirement**: Should customers be required to sign in with Google *before* submitting a booking request, or should guest booking continue to be supported with optional Google login?
