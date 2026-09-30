# 02. Technology Stack & System Architecture

## 1. Complete Technology Inventory

| Layer | Technology | Version | Purpose / Scope |
| :--- | :--- | :--- | :--- |
| **Backend Runtime** | PHP | `^8.2` | Core server runtime |
| **Backend Framework** | Laravel | `^12.0` (Latest 12.x LTS compatible) | MVC architecture, routing, middleware, migrations, Eloquent ORM |
| **Database** | MySQL / MariaDB (XAMPP default) | 8.0+ / 10.4+ | Primary relational datastore |
| **Persistence Bridge** | Custom `JsonStorageService` | Custom PHP Class | Dual-driver mechanism reading from MySQL DB or JSON fallback |
| **Frontend Templating**| Laravel Blade | Blade engine | Server-rendered component layouts, sections, and partials |
| **Styling / CSS** | Tailwind CSS + Custom CSS | Tailwind Play CDN & Tailwind 4 (`@tailwindcss/vite`) + `public/css/style.css` | Utility styling with CSS Custom Properties (`:root` variables) |
| **JavaScript (Core)** | Vanilla JavaScript (ES6+) | Modern browser native | UI state toggles, lightbox modals, price calculation, tab switching |
| **Frontend Libraries** | Swiper.js | `8.x` | Hero banners, category carousels, and Instagram reels slider |
| **Icons** | Font Awesome Free | `6.4.2` | Application iconography across public and admin interfaces |
| **Admin Rich Text** | CKEditor 5 Classic | `41.1.0` | Rich text WYSIWYG editor for package descriptions and CMS |
| **Admin Charts** | Chart.js | Latest CDN | Real-time analytics charts and booking status visualizations |
| **Bundler / Build** | Vite + `laravel-vite-plugin` | Vite `^7.0.7`, Plugin `^2.0.0` | Asset pipeline for development and production build |
| **Package Managers** | Composer & NPM | Composer 2.x, NPM 10.x | Dependency management for PHP and JavaScript |
| **Testing** | PHPUnit | `^11.5.50` | Feature and Unit regression test suite (13 test files) |

---

## 2. Dependency Audit & Package Inspection

### PHP Dependencies (`composer.json`)
```json
"require": {
    "php": "^8.2",
    "laravel/framework": "^12.0",
    "laravel/tinker": "^2.10.1"
},
"require-dev": {
    "fakerphp/faker": "^1.23",
    "laravel/pail": "^1.2.2",
    "laravel/pint": "^1.24",
    "laravel/sail": "^1.41",
    "mockery/mockery": "^1.6",
    "nunomaduro/collision": "^8.6",
    "phpunit/phpunit": "^11.5.50"
}
```
*Note: No heavy third-party vendor bloat (such as Spatie permissions, Filament, or Livewire). The application was engineered cleanly using native Laravel core capabilities.*

### Node / JavaScript Dependencies (`package.json`)
```json
"devDependencies": {
    "@tailwindcss/vite": "^4.0.0",
    "axios": "^1.11.0",
    "concurrently": "^9.0.1",
    "laravel-vite-plugin": "^2.0.0",
    "tailwindcss": "^4.0.0",
    "vite": "^7.0.7"
}
```

---

## 3. Core Architectural Patterns

### 1. Dual-Storage Bridge Pattern (`JsonStorageService`)
Located at [JsonStorageService.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/app/Services/JsonStorageService.php).
- Configured via `config/artizen.php` and `ARTIZEN_STORAGE_DRIVER=database` (or `json`).
- When set to `database`:
  - `read()` queries Eloquent ORM models (`Category`, `Package`, `PackageTier`, `Booking`, `Faq`, `Testimonial`, `GalleryItem`, `Enquiry`, `CmsSlide`, `Setting`).
  - Catches database errors or empty table conditions gracefully and falls back to reading `storage/app/*.json`.
  - `write()` writes synchronously to both the MySQL database via Eloquent `updateOrCreate()` / `firstOrCreate()` and writes atomic JSON files using temporary file rename (`rename($tempPath, $path)`) to prevent write collisions.
- This pattern guarantees zero downtime even if MySQL connection encounters transient network glitches.

### 2. Notification Dispatch Architecture (`WhatsAppSmsService`)
Located at [WhatsAppSmsService.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/app/Services/WhatsAppSmsService.php).
- Evaluates gateway readiness via `WhatsAppSmsService::isConfigured()`.
- Provides pre-built notification templates:
  - `sendCustomerBookingReceived($booking)`
  - `sendAdminNewBooking($booking)`
  - `sendCustomerBookingConfirmed($booking)`
  - `sendCustomerBookingCancelled($booking)`
- If third-party credentials (`services.whatsapp.api_key` or `services.whatsapp.provider`) are not configured in `.env`, the service logs execution in `storage/logs/laravel.log` with status `NOT_CONFIGURED` without failing the user's booking creation transaction.

### 3. Middleware & Security Pipeline
- **Web Pipeline** ([bootstrap/app.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/bootstrap/app.php)):
  - Global `\App\Http\Middleware\SecurityHeaders::class`: Applies `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `X-XSS-Protection: 1; mode=block`.
  - Rate Limiting (`throttle`):
    - Booking Checkout: `throttle:10,1` (Max 10 requests per minute)
    - Booking Tracker: `throttle:15,1`
    - Contact Form: `throttle:10,1`
    - Admin Login & Registration: `throttle:5,1`
  - Admin Route Protection: `\App\Http\Middleware\AdminMiddleware::class` aliased as `admin`.

---

## 4. Frontend Styling & Asset Pipeline Architecture

### Dual-CSS Architecture (Tailwind + Legacy style.css)
The frontend uses a hybrid styling architecture:
1. **Tailwind CSS Play CDN** loaded in [header.blade.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/resources/views/layouts/header.blade.php) with an inline configuration mapping custom palette tokens (`main-bg`, `surface-bg`, `card-bg`, `artizen-orange`, `gold`) to CSS variables.
2. **`public/css/style.css`** (181 KB): Contains legacy styles, glassmorphism filters, animations, noise backgrounds, and dark mode overrides (`[data-theme="dark"]`).
3. **`resources/css/app.css` & Vite**: Configured with `@tailwindcss/vite` for build compilation.

### JavaScript Script Architecture
1. [main.js](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/public/js/main.js): Mobile navigation drawer toggles, theme switcher (light/dark mode stored in `localStorage`), announcement bar ticker, booking modal listeners, and Swiper initializations.
2. [details.js](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/public/js/details.js): Dynamic tier switching, tier image previews, add-on inclusions toggle, and booking redirection with URL parameters.
3. [reels.js](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/public/js/reels.js): Video reel modal popups and muted autoplay handlers.

---

## 5. Deployment & Environment Configuration

### Server & Hosting Requirements
- **Web Server**: Apache 2.4+ (with `mod_rewrite` enabled) or Nginx.
- **PHP Version**: PHP 8.2 or 8.3 with `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`.
- **Database**: MySQL 8.0+ or MariaDB 10.4+.
- **File Permissions**: `storage/` and `bootstrap/cache/` must be writable (`chmod 775`).
- **Web Root**: Document root must point strictly to `/public` to protect `.env` and application core files.

### Critical Environment Variables Structure (`.env`)
```ini
APP_NAME=ARTIZEN
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=artizen_db
DB_USERNAME=root
DB_PASSWORD=

ARTIZEN_STORAGE_DRIVER=database

# WhatsApp / SMS Gateway Settings (Ready for integration)
WHATSAPP_PROVIDER=
WHATSAPP_API_KEY=
WHATSAPP_FROM_NUMBER=919131668156
SMS_API_KEY=
```
*(Sensitive credentials and actual secrets are excluded).*
