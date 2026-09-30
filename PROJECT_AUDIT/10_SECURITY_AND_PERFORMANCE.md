# 10. Security & Performance Audit

## 1. Security Architecture & Vulnerability Findings

### 1. High-Risk Finding: Admin Auto-Provisioning & Password Override Backdoor
* **Location**: [AdminAuthController.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/app/Http/Controllers/Admin/AdminAuthController.php), Lines 44–77.
* **Code Pattern**:
  ```php
  if ($credentials['email'] === 'admin@artizen.com' || str_contains($credentials['email'], 'admin')) {
      // Auto-creates admin user or updates password to whatever password was submitted!
      $adminUser->password = Hash::make($credentials['password']);
      $adminUser->save();
      Auth::login($adminUser);
      session(['admin_logged_in' => true]);
  }
  ```
* **Vulnerability Assessment**: Any user attempting login with an email string containing `"admin"` (e.g. `testadmin@domain.com`) will have an admin account instantly created or an existing admin password overridden with their submitted password.
* **Remediation**: Remove this auto-provisioning block entirely for production and rely exclusively on standard seeded credentials (`AdminSeeder.php`) and secure database authentication.

---

### 2. CSRF Protection & Input Validation
* **Status**: **PASS (Well Implemented)**
* **Details**: All POST endpoints (`/booking/save`, `/contact/send`, `/admin/login`, `/register`, and all `/admin/*` operations) enforce Laravel's standard `@csrf` token verification.
* **Input Validation**: Form inputs are validated strictly using Laravel's Form Validation rules with type assertions, maximum character lengths, email formatting, and date boundary checks (`after_or_equal:today`).

---

### 3. Price Tampering Prevention
* **Status**: **PASS (Well Implemented)**
* **Details**: In [BookingController.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/app/Http/Controllers/BookingController.php), the price submitted via client form POST is not trusted implicitly. The backend queries the database package tier catalog to verify the official price before saving the booking record.

---

### 4. Rate Limiting & Denial-of-Service (DoS) Protection
* **Status**: **PASS (Well Implemented)**
* **Details**: Public endpoints are throttled using Laravel's rate limiter:
  * Booking Submission: `throttle:10,1` (Max 10 per minute)
  * Booking Status Lookup: `throttle:15,1`
  * Contact Submission: `throttle:10,1`
  * Registration & Admin Login: `throttle:5,1`

---

### 5. File Upload Security
* **Status**: **MODERATE / ACCEPTABLE**
* **Details**: Uploaded images in `DashboardController.php` are restricted to valid image extensions (`jpg`, `jpeg`, `png`, `webp`) and capped at 5 MB (`max:5120`).
* **Note**: Files are saved directly into `public/uploads/` rather than the protected `storage/app/public` with an artisan symlink (`php artisan storage:link`). Migrating to standard storage disks is recommended.

---

## 2. Performance & Optimization Audit

### 1. View Cache Purge on Every Admin Dashboard Request
* **Location**: [DashboardController.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/app/Http/Controllers/Admin/DashboardController.php), Lines 18–20.
* **Code Pattern**:
  ```php
  foreach (glob(storage_path('framework/views/*.php')) as $cachedFile) {
      @unlink($cachedFile);
  }
  ```
* **Performance Impact**: Deleting compiled Blade views on every dashboard load forces the server to re-compile all Blade views on subsequent requests, adding 80–150ms of avoidable I/O latency.
* **Remediation**: Remove the `unlink()` loop. Blade view recompilation should be handled via `php artisan view:clear` during deployment.

---

### 2. Browser-Side Tailwind Play CDN Script Execution
* **Location**: [header.blade.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/resources/views/layouts/header.blade.php), Line 125 (`https://cdn.tailwindcss.com`).
* **Performance Impact**: The Tailwind Play CDN parses the entire DOM at runtime in the client's browser, incurring a 200–400ms JavaScript evaluation blocking time and triggering console warnings.
* **Remediation**: Build static CSS production bundles using Vite (`npm run build`).

---

### 3. Database Queries & Eager Loading
* **Status**: **GOOD**
* **Details**: In [JsonStorageService.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/app/Services/JsonStorageService.php), `Package::with(['tiers', 'category'])->get()` properly eager-loads relational tiers and categories, eliminating N+1 query degradation.
