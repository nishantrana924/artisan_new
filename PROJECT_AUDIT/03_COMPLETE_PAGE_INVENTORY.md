# 03. Complete Website Page Inventory

## 1. Public Website Page Matrix

| # | Page Name | Route / URL | HTTP | Controller & Action | Primary Blade View | Data Source |
| :- | :--- | :--- | :--- | :--- | :--- | :--- |
| **1** | **Homepage** | `/` | `GET` | `HomeController@index` | `resources/views/home/index.blade.php` | `categories.json`, `packages.json`, `cms.json` |
| **2** | **About Us** | `/about-us` | `GET` | `AboutController@index` | `resources/views/about/index.blade.php` | `cms.json` (About block) |
| **3** | **Packages Catalog** | `/events` | `GET` | `EventController@index` | `resources/views/events/index.blade.php` | `categories.json`, `packages.json` |
| **4** | **Category Details** | `/category/{slug}` | `GET` | `EventController@categoryShow` | `resources/views/events/category.blade.php` | `categories.json`, `packages.json` |
| **5** | **Package Details** | `/event/{slug}` | `GET` | `EventController@show` | `resources/views/events/details.blade.php` | `packages.json` |
| **6** | **Gallery** | `/gallery` | `GET` | `GalleryController@index` | `resources/views/gallery/index.blade.php` | `gallery.json`, `packages.json`, `categories.json` |
| **7** | **Booking Checkout** | `/booking` | `GET` | `BookingController@index` | `resources/views/booking/create.blade.php` | `categories.json`, `packages.json` |
| **8** | **Booking Submit** | `/booking/save` | `POST` | `BookingController@store` | Redirects to `booking.success` | Validated input $\rightarrow$ `bookings` table / JSON |
| **9** | **Booking Success** | `/booking/success` | `GET` | `BookingController@success` | `resources/views/booking/success.blade.php` | `bookings.json` / DB by `id` |
| **10** | **Track Booking** | `/track-booking` | `GET` | `BookingTrackerController@showForm` | `resources/views/booking/track.blade.php` | Form view / Query string lookup |
| **11** | **Track Search** | `/track-booking/search` | `POST` | `BookingTrackerController@search` | `resources/views/booking/track.blade.php` | `bookings.json` / DB lookup |
| **12** | **Contact & Inquiry** | `/contact` | `GET` | `ContactController@index` | `resources/views/contact/index.blade.php` | Hardcoded contact info & JSON |
| **13** | **Contact Submit** | `/contact/send` | `POST` | `ContactController@store` | Redirects with flash message | Validated input $\rightarrow$ `enquiries` table / JSON |
| **14** | **Registration** | `/register` | `GET` | `RegisterController@showRegistrationForm` | `resources/views/auth/register.blade.php` | Static form |
| **15** | **Register Submit**| `/register` | `POST` | `RegisterController@register` | Redirects to `booking.index` | `users` table |

---

## 2. Detailed Page Breakdown & Audit

### 1. Homepage (`/`)
* **Source Blade Files**:
  * Root: [resources/views/home/index.blade.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/resources/views/home/index.blade.php)
  * Partials:
    * `home/hero.blade.php` (Swiper Hero Banner)
    * `home/categories.blade.php` (Category Grid / Slider)
    * `home/featured-events.blade.php` (Top Selling Packages Grid)
    * `home/about.blade.php` (Brand Value Propositions & Badges)
    * `home/reels.blade.php` (Video Reels Showcase Modal)
    * `home/testimonials.blade.php` (Client Reviews Slider)
    * `home/faq.blade.php` (Accordion FAQs)
* **User Interactions**: Hero slide navigation, category filtering, package card lightbox trigger, reel modal popup, FAQ accordion expand/collapse, instant WhatsApp enquiry button.
* **Backend Connectivity**: Receives `$cms`, `$categories`, `$packages` populated from `JsonStorageService`.
* **Current UI/UX Problems**:
  * The top announcement bar marquee text is repetitive.
  * Mixing Tailwind CDN utility classes with hardcoded `:root` CSS creates inconsistent spacing across mobile viewports.
  * Mega-menu dropdown in navigation has dense typography and tight hover zones on tablet screens.

---

### 2. Packages Catalog (`/events`)
* **Source Blade File**: [resources/views/events/index.blade.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/resources/views/events/index.blade.php)
* **Controller**: [EventController.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/app/Http/Controllers/EventController.php)
* **Purpose**: Comprehensive catalog displaying all available packages across categories (Birthdays, House Parties, Proposals, Anniversaries, Weddings, Acoustic/DJ).
* **Key Components**:
  * Category Filter Pills (All, Birthdays, House Party, Proposals, Weddings, Corporate, DJ).
  * Package Cards: Image hover swap (secondary image preview), pricing badge, tier inclusions list, "View Package" and "Book Now" CTA buttons.
* **Data Handling**: Dynamic slug normalization map (`slugNormMap`) in `EventController` bridges category slugs with tier titles.
* **Current UI/UX Problems**:
  * Filter pills do not support URL history state push without page reload when clicked via JavaScript.
  * Image carousels on cards lack thumbnail indicators on smaller mobile screens.

---

### 3. Category Details Page (`/category/{slug}`)
* **Source Blade File**: [resources/views/events/category.blade.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/resources/views/events/category.blade.php)
* **Purpose**: Displays packages belonging to a specific celebration category (e.g. `/category/birthdays`).
* **Sections**:
  * Category Hero Header with background image and count badge.
  * Tier comparison grid (Essential vs Best Seller vs Premium).
  * Related categories quick-access sidebar.
* **Current UI/UX Problems**:
  * Missing breadcrumbs back to `/events`.
  * Mobile layout renders full-width cards that consume high vertical scroll space.

---

### 4. Package Details & Tier Customizer (`/event/{slug}`)
* **Source Blade File**: [resources/views/events/details.blade.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/resources/views/events/details.blade.php)
* **JavaScript Engine**: [details.js](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/public/js/details.js)
* **Key Features**:
  * Image Gallery with main preview and interactive thumbnail swatches.
  * Interactive Tier Switcher (Essential, Standard, Luxury) updating pricing and feature list dynamically without page reload.
  * Inclusions Checklist (Sound rig, lights, backdrop size, operator included).
  * Direct "Book This Package" CTA passing selected tier index and price as query params to `/booking`.
  * Direct WhatsApp Inquiry CTA with prefilled message containing package title and tier.
* **Current UI/UX Problems**:
  * If a package has no tier images, fallback placeholder styling is abrupt.
  * Add-on checkboxes in the details view are visual-only in some tiers and do not dynamically recalculate final checkout price before redirecting to the booking form.

---

### 5. Booking Checkout Request Form (`/booking`)
* **Source Blade File**: [resources/views/booking/create.blade.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/resources/views/booking/create.blade.php)
* **Controller**: [BookingController.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/app/Http/Controllers/BookingController.php)
* **Form Structure**:
  1. **Customer Information**: Full Name, Mobile Number, WhatsApp Number, Email Address.
  2. **Event Details**: Event Type/Category, Selected Package/Tier, Event Date (restricted to $\ge$ today), Event Time Slot, Guest Count.
  3. **Venue Information**: Address, Area (dropdown/input for Indore localities), Landmark, City (default: Indore), State (default: Madhya Pradesh), Pincode.
  4. **Additional Notes**: Special requests / instructions.
  5. **Order Summary Card**: Sticky pricing breakdown, "No Online Payment Required" badge, Advance calculation indicator.
* **Backend Validation**: Protected with `throttle:10,1`, strict regex, and server-side package price verification to prevent client-side HTML price tampering.
* **Current UI/UX Problems**:
  * Long single-page form on mobile requires substantial vertical scrolling.
  * Area input is a free-text field instead of an autocomplete list of Indore neighborhoods (Vijay Nagar, Palasia, etc.).

---

### 6. Booking Success Page (`/booking/success`)
* **Source Blade File**: [resources/views/booking/success.blade.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/resources/views/booking/success.blade.php)
* **Key Components**:
  * Booking Reference ID Highlight (e.g., `BK-8844`) with single-click copy button.
  * Status Timeline (Request Received $\rightarrow$ Verification Call $\rightarrow$ Booking Confirmed $\rightarrow$ Setup Delivery).
  * Direct link to `/track-booking?id=BK-XXXX&mobile=...`.
  * Offline Payment Notice explaining the 20% advance collection upon verification call.
* **Current UI/UX Problems**:
  * Lacks a direct "Add to Google Calendar / Apple Calendar" button for the customer.

---

### 7. Real-Time Booking Status Tracker (`/track-booking`)
* **Source Blade File**: [resources/views/booking/track.blade.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/resources/views/booking/track.blade.php)
* **Controller**: [BookingTrackerController.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/app/Http/Controllers/BookingTrackerController.php)
* **Verification Logic**: Dual-factor lookup requiring **BOTH** Booking ID (e.g., `BK-8844`) and the registered mobile number (last 10 digits sanitized) to prevent unauthorized snooping into other customer bookings.
* **Visual Output**: Interactive multi-step timeline badge (Pending $\rightarrow$ Contacted $\rightarrow$ Confirmed $\rightarrow$ Completed / Cancelled), venue details, package breakdown, offline balance note.
* **Current UI/UX Problems**:
  * If a user lands from the success page with only `?id=...`, they must manually re-enter their phone number for security. An inline OTP or session auto-fill would improve UX.

---

### 8. Contact & Inquiry Page (`/contact`)
* **Source Blade File**: [resources/views/contact/index.blade.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/resources/views/contact/index.blade.php)
* **Key Components**: Office address in Vijay Nagar, Indore, phone & WhatsApp contact cards, operating hours, and inquiry submission form.
* **Backend Connectivity**: Stores submissions with unique IDs (`ENQ-XXXX`) in `enquiries` table / `enquiries.json`.
* **Current UI/UX Problems**:
  * Google Maps iframe embed is static without dynamic marker centering.

---

### 9. Customer Registration (`/register`)
* **Source Blade File**: [resources/views/auth/register.blade.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/resources/views/auth/register.blade.php)
* **Controller**: [RegisterController.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/app/Http/Controllers/Auth/RegisterController.php)
* **Purpose**: Allows standard user registration with Name, Email, Password, Password Confirmation.
* **Current UI/UX & Architectural Gap**:
  * Currently, registration redirects immediately to `/booking.index` rather than a dedicated Customer Profile or Dashboard.
  * There is no Customer Login page (`/login` only exists for `/admin/login`).
  * No Google OAuth button is currently present.
