# 04. Admin Panel Deep Inspection & Audit

## 1. Admin Authentication & Route Protection

* **Login Route**: `GET /admin/login` $\rightarrow$ [AdminAuthController.php@showLoginForm](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/app/Http/Controllers/Admin/AdminAuthController.php)
* **Login Submit**: `POST /admin/login` (Protected by `throttle:5,1`)
* **Logout**: `POST /admin/logout`
* **Protection Middleware**: `\App\Http\Middleware\AdminMiddleware::class`
  * Checks `Auth::check() && Auth::user()->isAdmin()` OR session flag `session('admin_logged_in') === true`.
  * Redirects unauthenticated requests to `/admin/login`.

---

## 2. Admin Management Modules Master Matrix

| Module | Route | Primary Blade Component | Controller & Action | Database Table / Store | CRUD Status | Operational Notes |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **Analytics Dashboard** | `GET /admin/dashboard` | `admin/dashboard.blade.php` | `DashboardController@index` | Aggregated from `bookings` / `bookings.json` | **Implemented (Read/Filter)** | Real-time calculations: Total bookings, Pending value, Confirmed revenue, Chart.js trends, Time filters (Today, 7D, 30D, Month, Custom). |
| **Bookings Manager** | `POST /admin/bookings/update-status` | Inside `dashboard.blade.php` (Tab: Bookings) | `DashboardController@updateBookingStatus` | `bookings`, `customers` | **Implemented (Read, Update Status, Filter)** | Updates status (`Pending`, `Contacted`, `Confirmed`, `Completed`, `Cancelled`). Dispatches WhatsApp/SMS notifications upon confirmation/cancellation. |
| **Categories CRUD** | `POST /admin/categories/save`<br>`POST /admin/categories/delete/{id}` | Inside `dashboard.blade.php` (Tab: Categories) | `DashboardController@saveCategory`<br>`DashboardController@deleteCategory` | `categories` / `categories.json` | **Fully Implemented (Create, Read, Update, Delete)** | Supports title, image upload to `public/uploads/categories`, active flag, display order. |
| **Packages & Tiers CRUD** | `GET /admin/packages/create`<br>`GET /admin/packages/edit/{catId}/{tierIdx}`<br>`POST /admin/packages/save`<br>`POST /admin/packages/delete/{catId}/{tierIdx}` | `admin/packages/edit.blade.php` & `dashboard.blade.php` | `DashboardController@createPackagePage`<br>`DashboardController@editPackagePage`<br>`DashboardController@savePackage`<br>`DashboardController@deletePackage` | `packages`, `package_tiers` / `packages.json` | **Fully Implemented (Create, Read, Update, Delete)** | Multi-tier editing (Essential, Standard, Luxury), inclusions array JSON parsing, multiple image uploads, badge customization. |
| **Customer Directory** | Tab inside `dashboard.blade.php` | `dashboard.blade.php` (Tab: Customers) | `DashboardController@index` | `customers` / `customers.json` | **Implemented (Read & Aggregation)** | Lists customer name, phone, WhatsApp, email, city, total bookings count, and lifetime spend. |
| **FAQ Management** | `POST /admin/faqs/save`<br>`POST /admin/faqs/delete/{id}` | Inside `dashboard.blade.php` (Tab: FAQ) | `DashboardController@saveFaq`<br>`DashboardController@deleteFaq` | `faqs` / `faqs.json` | **Fully Implemented (Create, Read, Update, Delete)** | Question, answer, category tag, active status toggle. |
| **Testimonials / Reviews** | `POST /admin/testimonials/save`<br>`POST /admin/testimonials/delete/{id}` | Inside `dashboard.blade.php` (Tab: Testimonials) | `DashboardController@saveTestimonial`<br>`DashboardController@deleteTestimonial` | `testimonials` / `testimonials.json` | **Fully Implemented (Create, Read, Update, Delete)** | Author, location, event type, review text, 1-5 star rating, avatar image upload. |
| **Gallery Management** | `POST /admin/gallery/save`<br>`POST /admin/gallery/delete/{id}` | Inside `dashboard.blade.php` (Tab: Gallery) | `DashboardController@saveGalleryItem`<br>`DashboardController@deleteGalleryItem` | `gallery_items` / `gallery.json` | **Fully Implemented (Create, Read, Update, Delete)** | Image title, category tag, file upload to `public/uploads/gallery`, active toggle. |
| **Hero Slider & CMS** | `POST /admin/cms/save` | Inside `dashboard.blade.php` (Tabs: Hero / About) | `DashboardController@saveCms` | `cms_slides`, `settings` / `cms.json` | **Fully Implemented (Create, Read, Update)** | Hero slides (badge, title, description, buttons, background image) and About Us USP cards. |
| **Website & SEO Settings**| `POST /admin/settings/save` | Inside `dashboard.blade.php` (Tab: Settings) | `DashboardController@saveSettings` | `settings` / `settings.json` | **Fully Implemented (Read, Update)** | Meta title, meta description, meta keywords, primary WhatsApp phone, support email, office address, logo update. |
| **Enquiries Manager** | `POST /admin/enquiries/delete/{id}` | Inside `dashboard.blade.php` (Tab: Enquiries) | `DashboardController@deleteEnquiry` | `enquiries` / `enquiries.json` | **Implemented (Read, Delete)** | View contact leads submitted from `/contact`, delete obsolete inquiries. |

---

## 3. UI/UX Architecture & Layout Inspection

### 1. Navigation & Tab Switching Mechanism
* The Admin Dashboard utilizes a unified Single-Page Application (SPA) layout powered by client-side JavaScript tab switching in [dashboard.blade.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/resources/views/admin/dashboard.blade.php):
  ```javascript
  function switchTab(tabId) {
      document.querySelectorAll('[data-tab-content]').forEach(el => el.classList.add('hidden'));
      document.getElementById('tab-' + tabId)?.classList.remove('hidden');
      // Syncs active states and browser URL parameters (?tab=...)
  }
  ```
* [admin-sidebar.blade.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/resources/views/layouts/admin-sidebar.blade.php) provides desktop persistent sidebar and mobile slide-over navigation.

### 2. Form Handling & File Uploads
* Forms use standard `enctype="multipart/form-data"` with CSRF token verification.
* File uploads are stored under `public/uploads/{categories|packages|gallery|hero}` and verified using Laravel's file validation (`mimes:jpg,jpeg,png,webp|max:5120`).

---

## 4. Current Admin UI/UX Issues & Deficiencies

1. **Monolithic Blade File Size**: `dashboard.blade.php` contains 3,878 lines of Blade code in a single file. While functional, it makes maintenance complex; it should be decomposed into separate partial views (`admin/partials/tab-bookings.blade.php`, etc.).
2. **Mobile Table Horizontal Overflow**: The Bookings and Customers tables have numerous columns (ID, Customer, Package, Date, Venue, Total, Status, Actions) which create horizontal scrolling friction on mobile devices without card-stack alternative views.
3. **No Bulk Action Controls**: Admins cannot select multiple bookings to bulk-update status (e.g. marking multiple past events as `Completed`).
4. **Hardcoded Session Backdoor**: In `AdminAuthController.php`, any email containing `admin` is automatically authorized, which was created for development convenience but must be hardened for production.
