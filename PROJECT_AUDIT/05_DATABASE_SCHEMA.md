# 05. Database Architecture & Schema Inspection

## 1. Complete Database Entity-Relationship Diagram (Mermaid)

```mermaid
erDiagram
    USERS {
        bigint id PK
        string name
        string email UK
        timestamp email_verified_at
        string password
        boolean is_admin
        string remember_token
        timestamps created_at_updated_at
    }

    CATEGORIES {
        bigint id PK
        string title
        string slug UK
        integer count
        text image
        boolean active
        integer display_order
        timestamps created_at_updated_at
    }

    PACKAGES {
        bigint id PK
        bigint category_id FK
        string title
        string slug UK
        text description
        text image
        string tag
        json gallery
        boolean active
        timestamps created_at_updated_at
    }

    PACKAGE_TIERS {
        bigint id PK
        bigint package_id FK
        string name
        string slug
        decimal price
        text description
        text image
        json inclusions
        boolean active
        integer display_order
        timestamps created_at_updated_at
    }

    CUSTOMERS {
        bigint id PK
        string name
        string mobile UK_idx
        string whatsapp
        string email
        text address
        string area
        string city
        string state
        string pincode
        integer total_bookings
        decimal total_spend
        timestamps created_at_updated_at
    }

    BOOKINGS {
        string id PK "BK-XXXX"
        bigint customer_id FK
        bigint package_id FK
        string customer_name
        string customer_mobile idx
        string customer_whatsapp
        string customer_email
        string package_name
        string category_name
        string tier_name
        string event_date idx
        string event_time
        integer guest_count
        text address
        string landmark
        string area idx
        string city
        string state
        string pincode
        text notes
        decimal package_price
        decimal surcharge_amount
        decimal total_amount idx
        string status idx
        json notifications_sent
        timestamps created_at_updated_at
    }

    ENQUIRIES {
        string id PK "ENQ-XXXX"
        string name
        string phone
        string email
        string subject
        text message
        string status
        timestamps created_at_updated_at
    }

    FAQS {
        bigint id PK
        text question
        text answer
        string category
        boolean is_active
        timestamps created_at_updated_at
    }

    TESTIMONIALS {
        bigint id PK
        string author
        string location
        string event_type
        text review
        integer rating
        text avatar
        boolean is_active
        timestamps created_at_updated_at
    }

    GALLERY_ITEMS {
        bigint id PK
        string title
        string category
        text image
        boolean is_active
        timestamps created_at_updated_at
    }

    SETTINGS {
        bigint id PK
        string key UK
        longtext value
        string group
        timestamps created_at_updated_at
    }

    CMS_SLIDES {
        bigint id PK
        string badge
        string title
        text description
        text image
        string button1_text
        string button1_link
        string button2_text
        string button2_link
        integer display_order
        boolean is_active
        timestamps created_at_updated_at
    }

    CATEGORIES ||--o{ PACKAGES : "has many"
    PACKAGES ||--o{ PACKAGE_TIERS : "has many"
    PACKAGES ||--o{ BOOKINGS : "referenced by"
    CUSTOMERS ||--o{ BOOKINGS : "places many"
```

---

## 2. Table Specifications & Column Dictionary

### 1. `categories`
* **Migration**: [2026_08_14_000001_create_categories_table.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/database/migrations/2026_08_14_000001_create_categories_table.php)
* **Model**: [Category.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/app/Models/Category.php)
* **Columns**:
  * `id` (`BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`)
  * `title` (`VARCHAR(255)`, NOT NULL)
  * `slug` (`VARCHAR(255)`, UNIQUE)
  * `count` (`INT`, DEFAULT 0)
  * `image` (`TEXT`, NULLABLE)
  * `active` (`BOOLEAN`, DEFAULT TRUE, INDEX)
  * `display_order` (`INT`, DEFAULT 0)
  * `created_at`, `updated_at` (`TIMESTAMP`)

### 2. `packages`
* **Migration**: [2026_08_14_000002_create_packages_table.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/database/migrations/2026_08_14_000002_create_packages_table.php)
* **Model**: [Package.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/app/Models/Package.php)
* **Foreign Keys**: `category_id` references `categories.id` (`ON DELETE SET NULL`).
* **Columns**: `id`, `category_id`, `title`, `slug` (UNIQUE), `description` (`TEXT`), `image` (`TEXT`), `tag` (`VARCHAR(255)`), `gallery` (`JSON`), `active` (`BOOLEAN`), timestamps.

### 3. `package_tiers`
* **Migration**: [2026_08_14_000003_create_package_tiers_table.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/database/migrations/2026_08_14_000003_create_package_tiers_table.php)
* **Model**: [PackageTier.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/app/Models/PackageTier.php)
* **Foreign Keys**: `package_id` references `packages.id` (`ON DELETE CASCADE`).
* **Columns**: `id`, `package_id`, `name`, `slug`, `price` (`DECIMAL(10,2)`), `description`, `image`, `inclusions` (`JSON`), `active` (`BOOLEAN`), `display_order` (`INT`), timestamps.

### 4. `customers`
* **Migration**: [2026_08_14_000004_create_customers_table.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/database/migrations/2026_08_14_000004_create_customers_table.php)
* **Model**: [Customer.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/app/Models/Customer.php)
* **Columns**:
  * `id` (`BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`)
  * `name` (`VARCHAR(255)`)
  * `mobile` (`VARCHAR(20)`, INDEX)
  * `whatsapp` (`VARCHAR(20)`, NULLABLE)
  * `email` (`VARCHAR(255)`, NULLABLE, INDEX)
  * `address` (`TEXT`, NULLABLE)
  * `area` (`VARCHAR(255)`, NULLABLE)
  * `city` (`VARCHAR(100)`, DEFAULT 'Indore')
  * `state` (`VARCHAR(100)`, DEFAULT 'Madhya Pradesh')
  * `pincode` (`VARCHAR(10)`, NULLABLE)
  * `total_bookings` (`INT`, DEFAULT 0)
  * `total_spend` (`DECIMAL(10,2)`, DEFAULT 0.00)
  * timestamps.

### 5. `bookings`
* **Migration**: [2026_08_14_000005_create_bookings_table.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/database/migrations/2026_08_14_000005_create_bookings_table.php)
* **Model**: [Booking.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/app/Models/Booking.php)
* **Primary Key**: `id` (`VARCHAR(50)`, Non-incrementing string, e.g. `BK-8844`).
* **Foreign Keys**:
  * `customer_id` references `customers.id` (`ON DELETE SET NULL`)
  * `package_id` references `packages.id` (`ON DELETE SET NULL`)
* **Columns**: `id`, `customer_id`, `package_id`, `customer_name`, `customer_mobile` (INDEX), `customer_whatsapp`, `customer_email`, `package_name`, `category_name`, `tier_name`, `event_date` (INDEX), `event_time`, `guest_count`, `address`, `landmark`, `area` (INDEX), `city`, `state`, `pincode`, `notes`, `package_price` (`DECIMAL(10,2)`), `surcharge_amount` (`DECIMAL(10,2)`), `total_amount` (`DECIMAL(10,2)`, INDEX), `status` (`VARCHAR(50)`, DEFAULT 'Pending', INDEX), `notifications_sent` (`JSON`), timestamps.

### 6. `enquiries`
* **Migration**: [2026_08_14_000009_create_enquiries_table.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/database/migrations/2026_08_14_000009_create_enquiries_table.php)
* **Model**: [Enquiry.php](file:///Applications/XAMPP/xamppfiles/htdocs/artizen/artizen/app/Models/Enquiry.php)
* **Primary Key**: `id` (`VARCHAR(50)`, e.g. `ENQ-1234`).
* **Columns**: `id`, `name`, `phone`, `email`, `subject`, `message`, `status` (`DEFAULT 'Unread'`), timestamps.

### 7. Other Supporting Tables
* `faqs`: `id`, `question`, `answer`, `category`, `is_active`, timestamps.
* `testimonials`: `id`, `author`, `location`, `event_type`, `review`, `rating`, `avatar`, `is_active`, timestamps.
* `gallery_items`: `id`, `title`, `category`, `image`, `is_active`, timestamps.
* `settings`: `id`, `key` (UNIQUE), `value` (`LONGTEXT`), `group`, timestamps.
* `cms_slides`: `id`, `badge`, `title`, `description`, `image`, `button1_text`, `button1_link`, `button2_text`, `button2_link`, `display_order`, `is_active`, timestamps.
* `users`: `id`, `name`, `email` (UNIQUE), `password`, `is_admin` (`BOOLEAN`), timestamps.

---

## 3. Data Integrity & Schema Evaluation

1. **Snapshot Data vs Foreign References**: The `bookings` table stores both foreign keys (`customer_id`, `package_id`) and frozen text snapshots (`customer_name`, `package_name`, `category_name`, `tier_name`, `package_price`). This is a **correct design pattern for booking systems** to preserve historical pricing integrity if package prices change in the future.
2. **String Primary Key Optimization**: `bookings.id` uses `VARCHAR(50)` format `BK-XXXX`. While readable for customer receipts and tracking, indexing on numeric bigint or standard UUID with a separate human-readable reference number would improve B-tree index performance at high scale.
3. **JSON Fields**: `inclusions` in `package_tiers`, `gallery` in `packages`, and `notifications_sent` in `bookings` use MySQL native `JSON` columns with Eloquent `array` casting.
4. **Soft Deletes**: Currently, tables do not implement Laravel's `SoftDeletes` trait (`deleted_at`). Implementing Soft Deletes is recommended for future booking and package management to prevent accidental record loss.
