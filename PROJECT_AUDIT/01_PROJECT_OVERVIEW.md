# 01. Project Overview & Business Domain

## 1. Executive Summary

**ARTIZEN** is a specialized, instant Event Booking and Experience Platform tailored for celebration management in **Indore, Madhya Pradesh, India**. Unlike traditional multi-vendor e-commerce marketplaces or open cart platforms, Artizen operates as an end-to-end, curated event package booking engine. Customers browse pre-configured setup tiers (decor, acoustic sound rigs, DJ setups, proposal backdrops, lighting, and stage mandaps), configure their venue and guest logistics, and submit a **Booking Request**. 

The platform operates on an **Offline Payment model**: no online payment gateway is integrated. Bookings are submitted in a `Pending` state, after which event coordinators contact the client via Phone/WhatsApp to verify venue logistics, inspect access requirements, collect advance deposits offline, and confirm the reservation.

---

## 2. Business Objectives & Operational Model

```
+-------------------------------------------------------------------------------------------------------+
|                                        ARTIZEN BUSINESS MODEL                                         |
+-------------------------------------------------------------------------------------------------------+
|  [ Browse Packages ]  -->  [ Select Tier / Addons ]  -->  [ Submit Booking Form (No Online Payment) ]  |
|                                                                        |                              |
|                                                                        v                              |
|  [ Offline Settlement ] <-- [ Phone / WhatsApp Verification ] <-- [ Admin Review & Notification ]     |
|            |                                                                                          |
|            v                                                                                          |
|  [ Status: Confirmed ] --> [ On-site Setup Execution ] --> [ Event Completion ]                       |
+-------------------------------------------------------------------------------------------------------+
```

### Core Business Pillars
1. **Curated Event Packages**: Packaged celebration setups eliminating fragmented vendor coordination (lighting, sound, balloons, florals, and stage fabricators).
2. **Instant Lead Capture**: Frictionless booking checkout without upfront credit card / payment gateway drop-offs.
3. **Local Operational Focus**: Hyper-local execution in Indore (covering Vijay Nagar, Palasia, Super Corridor, Rau, Bypass, Scheme 78, Annapurna, etc.) with configurable multi-city scaling capabilities.
4. **Dual-Channel Customer Tracking**: Real-time reference lookup (`/track-booking`) via unique Booking ID (e.g., `BK-8844`) and registered mobile number.
5. **Dynamic Content Management**: Unified admin dashboard to edit hero banners, category tiers, prices, inclusions, FAQs, testimonials, and gallery media.

---

## 3. High-Level System Architecture

The application is architected as a monolithic Laravel 12 application with a Blade templating frontend, client-side Tailwind CSS and vanilla JavaScript interactions, supported by a dual-driver persistence bridge (`JsonStorageService` supporting MySQL Eloquent ORM with automatic JSON file fallback).

```
+------------------------------------------------------------------------------------+
|                                    CLIENT LAYER                                    |
|  +------------------------------------------------------------------------------+  |
|  |  Blade Views / Responsive Layouts (Tailwind CSS CDN + custom style.css)      |  |
|  |  Interactive Components: Swiper.js, Lightbox, Vanilla JS Trackers, Reels     |  |
|  +------------------------------------------------------------------------------+  |
+------------------------------------------+-----------------------------------------+
                                           | HTTP Requests / AJAX
                                           v
+------------------------------------------------------------------------------------+
|                                  CONTROLLER LAYER                                  |
|  +---------------------+  +----------------------+  +---------------------------+  |
|  | HomeController      |  | EventController      |  | BookingController         |  |
|  | GalleryController   |  | ContactController    |  | BookingTrackerController  |  |
|  | AboutController     |  | RegisterController   |  | Admin\DashboardController |  |
|  +---------------------+  +----------------------+  +---------------------------+  |
+------------------------------------------+-----------------------------------------+
                                           |
                                           v
+------------------------------------------------------------------------------------+
|                                   SERVICE LAYER                                    |
|  +-------------------------------------+  +-------------------------------------+  |
|  | JsonStorageService (Storage Bridge) |  | WhatsAppSmsService (Gateway Client) |  |
|  +-------------------------------------+  +-------------------------------------+  |
+------------------------------------+-----+-----------------------------------------+
                                     |
                +--------------------+--------------------+
                |                                         |
                v (Primary Driver)                        v (Sync Fallback)
+------------------------------------+   +------------------------------------+
|          DATABASE LAYER            |   |          FLAT JSON LAYER           |
|  - MySQL 8.x (Eloquent ORM)        |   |  - storage/app/categories.json     |
|  - Tables: categories, packages,   |   |  - storage/app/packages.json       |
|    package_tiers, bookings,        |   |  - storage/app/bookings.json       |
|    customers, faqs, enquiries,     |   |  - storage/app/faqs.json           |
|    testimonials, gallery_items,    |   |  - storage/app/gallery.json        |
|    settings, cms_slides, users     |   |  - storage/app/settings.json       |
+------------------------------------+   +------------------------------------+
```

---

## 4. Key Target Personas & User Journeys

| Persona | Primary Needs | Current Journey |
| :--- | :--- | :--- |
| **Event Host / Customer** | Finding transparent pricing for birthdays, anniversaries, or house party DJ setups in Indore without endless phone inquiries. | Visits Homepage $\rightarrow$ Views Package Gallery / Details $\rightarrow$ Selects Tier $\rightarrow$ Submits Contact & Venue Details $\rightarrow$ Receives Booking ID $\rightarrow$ Tracks via `/track-booking`. |
| **Inquiring Visitor** | Asking custom questions about specific decorators, custom themes, or off-site venues. | Visits `/contact` $\rightarrow$ Submits Inquiry Form $\rightarrow$ Generates Inquiry Reference ID $\rightarrow$ Reaches out via WhatsApp click-to-chat. |
| **Business Admin / Operator** | Monitoring booking pipeline, updating pricing tiers, changing hero banners, contacting leads, and approving bookings. | Logs in via `/admin/login` $\rightarrow$ Accesses Tabbed Dashboard $\rightarrow$ Filters Bookings $\rightarrow$ Updates Status (Pending $\rightarrow$ Contacted $\rightarrow$ Confirmed) $\rightarrow$ Modifies CMS settings. |

---

## 5. Repository File Map & High-Level Directory Overview

```
/Applications/XAMPP/xamppfiles/htdocs/artizen/
├── .agents/                                # Agent system rules & configurations
│   └── rules/
│       ├── artizen-workspace.md            # Workspace conventions & business rules
│       └── instruction.md                  # Development guidelines
├── artizen/                                # Main Laravel 12 Application Root
│   ├── app/
│   │   ├── Console/Commands/               # Artizen CLI (MigrateJsonToDb, BackupJson)
│   │   ├── Http/
│   │   │   ├── Controllers/                # Frontend & Admin Controllers
│   │   │   │   ├── Admin/                  # AdminAuthController, DashboardController
│   │   │   │   └── Auth/                   # RegisterController
│   │   │   └── Middleware/                 # AdminMiddleware, SecurityHeaders
│   │   ├── Models/                         # 12 Eloquent Models (Booking, Customer, Package, etc.)
│   │   ├── Notifications/                  # Mail Notifications (BookingReceived, Confirmed, etc.)
│   │   └── Services/                       # JsonStorageService, WhatsAppSmsService
│   ├── bootstrap/                          # Application bootstrap & middleware aliases
│   ├── config/                             # App, Artizen, Auth, Database, Queue config
│   ├── database/
│   │   ├── migrations/                     # 15 Schema migration files
│   │   └── seeders/                        # AdminSeeder, DatabaseSeeder
│   ├── public/
│   │   ├── assets/images/                  # Static hero, logo, categories, and reel assets
│   │   ├── css/style.css                   # Consolidated custom CSS rules
│   │   ├── js/                             # main.js, details.js, reels.js
│   │   └── uploads/                        # Dynamic admin uploads
│   ├── resources/
│   │   ├── css/app.css                     # Tailwind v4 source
│   │   ├── js/app.js                       # Vite JS entry point
│   │   └── views/                          # Blade templates (about, admin, auth, booking, events, etc.)
│   ├── routes/
│   │   ├── web.php                         # All web application routes
│   │   └── console.php                     # Artisan commands registration
│   ├── storage/
│   │   └── app/                            # Local JSON storage fallback & uploads
│   ├── tests/
│   │   └── Feature/                        # 13 Automated test suites
│   ├── composer.json                       # PHP Dependencies (Laravel 12)
│   ├── package.json                        # Node Dependencies (Vite 7, Tailwind 4)
│   └── vite.config.js                      # Vite Bundler configuration
```
