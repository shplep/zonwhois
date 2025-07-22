# Tasks for zonwhois.com Development

## Overview
This document outlines the files, functions, database schema, and tasks required to build a PHP/MySQL website at zonwhois.com. The site includes a frontend with a header, homepage, domain pages, and footer, plus an admin backend for managing URLs and viewing stats. Tasks are designed for a coding LLM to implement systematically. The logo is provided, and Cloudflare will be used, eliminating the need for a separate CDN setup.

## Database Schema
- **Table: domains**
  - `id`: INT, AUTO_INCREMENT, PRIMARY KEY
  - `domain_name`: VARCHAR(255), UNIQUE, INDEX
  - `creation_date`: DATE
  - `expiration_date`: DATE
  - `renewal_date`: DATE
  - `registrar`: VARCHAR(100)
  - `meta_description`: TEXT
  - `meta_title`: VARCHAR(255)
  - `meta_keywords`: TEXT
  - `http_status`: INT
  - `server_type`: VARCHAR(100)
  - `content_type`: VARCHAR(100)
  - `load_time`: FLOAT
  - `redirects`: TEXT
  - `ssl_status`: BOOLEAN
  - `status`: ENUM('visible', 'hidden'), DEFAULT 'visible'
  - `last_updated`: TIMESTAMP, DEFAULT CURRENT_TIMESTAMP
- **Table: page_views**
  - `id`: INT, AUTO_INCREMENT, PRIMARY KEY
  - `domain_id`: INT, FOREIGN KEY (domains.id), INDEX
  - `view_timestamp`: TIMESTAMP, DEFAULT CURRENT_TIMESTAMP, INDEX
  - `is_bot`: BOOLEAN
  - `user_agent`: TEXT
- **Table: categories**
  - `id`: INT, AUTO_INCREMENT, PRIMARY KEY
  - `name`: VARCHAR(100), UNIQUE
- **Table: countries**
  - `id`: INT, AUTO_INCREMENT, PRIMARY KEY
  - `name`: VARCHAR(100), UNIQUE
- **Table: contact_submissions**
  - `id`: INT, AUTO_INCREMENT, PRIMARY KEY
  - `name`: VARCHAR(100)
  - `email`: VARCHAR(255)
  - `message`: TEXT
  - `submission_timestamp`: TIMESTAMP, DEFAULT CURRENT_TIMESTAMP
- **Table: domain_categories**
  - `domain_id`: INT, FOREIGN KEY (domains.id)
  - `category_id`: INT, FOREIGN KEY (categories.id)
  - PRIMARY KEY (domain_id, category_id)
- **Table: domain_countries**
  - `domain_id`: INT, FOREIGN KEY (domains.id)
  - `country_id`: INT, FOREIGN KEY (countries.id)
  - PRIMARY KEY (domain_id, country_id)

## File Structure
- **Root**
  - `index.php`: Homepage with three-column layout.
  - `about.php`: About Us page.
  - `categories.php`: Categories listing page.
  - `countries.php`: Countries listing page.
  - `contact.php`: Contact form page.
  - `domain.php`: Dynamic domain details page.
  - `list.php`: Paginated domain list for A-Z, 0-9.
  - `sitemap.php`: Auto-generated sitemap.xml.
  - `.htaccess`: URL rewriting for clean URLs.
- **/admin**
  - `index.php`: Admin dashboard (login-protected).
  - `login.php`: Admin login page.
  - `logout.php`: Admin logout.
  - `urls.php`: Manage URLs (add/edit/delete/hide).
  - `refresh.php`: Trigger domain data refresh.
  - `stats.php`: View page view stats and trends.
  - `export.php`: Export stats as CSV.
  - `contact.php`: View contact form submissions.
- **/includes**
  - `config.php`: Database connection and site settings.
  - `functions.php`: Shared utility functions.
  - `header.php`: Header template (logo, nav).
  - `footer.php`: Footer template (nav, social icons, A-Z/0-9).
  - `db.php`: Database interaction functions.
  - `curl.php`: cURL functions for domain data.
  - `security.php`: Input validation/sanitization.
- **/assets**
  - `/css/style.css`: Site styles (responsive, Tailwind optional).
  - `/js/main.js`: Client-side scripts (search, async loading).
  - `/img/logo.png`: Provided site logo.
  - `/img/social-x.png`, `/img/social-fb.png`, `/img/social-yt.png`: Social media icons.
- **/errors**
  - `404.php`: Custom 404 page.
  - `500.php`: Custom 500 page.

## Functions (in `includes/functions.php` unless specified)
- **Database Functions** (`db.php`):
  - `connect_db()`: Connect to MySQL database.
  - `get_domains($type, $limit)`: Fetch domains (last_added, top, last_visited).
  - `get_domain($domain_name)`: Fetch single domain details.
  - `get_domains_by_letter($letter, $page, $per_page)`: Fetch paginated domains by letter/number.
  - `get_categories()`: Fetch all categories.
  - `get_countries()`: Fetch all countries.
  - `get_domain_views($domain_id, $is_bot)`: Fetch view stats for a domain.
  - `log_view($domain_id, $user_agent)`: Log page view with bot detection.
  - `add_domain($data)`: Add new domain.
  - `update_domain($id, $data)`: Update domain.
  - `delete_domain($id)`: Delete domain.
  - `hide_domain($id, $status)`: Toggle domain visibility.
  - `save_contact_submission($data)`: Save contact form submission.
  - `get_contact_submissions()`: Fetch contact submissions.
- **cURL Functions** (`curl.php`):
  - `fetch_domain_data($domain)`: Fetch domain stats (HTTP status, server, content type, load time, redirects, SSL).
  - `fetch_meta_tags($domain)`: Fetch meta description, title, keywords.
  - `refresh_domain($domain_id)`: Refresh domain data.
- **Security Functions** (`security.php`):
  - `sanitize_input($input)`: Sanitize user input.
  - `validate_domain($domain)`: Validate domain format.
  - `is_admin_logged_in()`: Check admin session.
  - `rate_limit($key, $limit, $period)`: Rate limit requests.
  - `detect_bot($user_agent)`: Identify bots by User-Agent.
- **Utility Functions**:
  - `generate_pagination($total, $page, $per_page)`: Generate pagination links.
  - `generate_sitemap()`: Generate sitemap.xml content.

## Tasks
### 1. Database Setup ✅
- Create MySQL database and tables as per schema.
- Add indexes for performance.
- Set up foreign keys for referential integrity.
- Create initial data (sample domains, categories, countries).

### 2. Configuration ✅
- **File**: `includes/config.php`
  - Define database credentials, site URL, Cloudflare settings.
  - Set up session and security constants.

### 3. Frontend Development ✅
- **Header** (`includes/header.php`):
  - Use provided logo (left) and nav (right: Home, About Us, Categories, Countries, Contact).
  - Ensure responsive design.
- **Footer** (`includes/footer.php`):
  - Create small nav, social media icons (X.com, FB, YT), A-Z/0-9 links.
  - Ensure accessibility (ARIA labels, keyboard nav).
- **Homepage** (`index.php`):
  - Build three-column layout for Last Added, Top, Last Visited (15 domains each).
  - Add search bar with async domain lookup.
- **Domain Page** (`domain.php`):
  - Display domain stats (name, dates, registrar, meta tags, HTTP status, server, content type, load time, redirects, SSL).
  - Option for clickable domain link (admin-controlled).
  - Log view with bot detection.
- **List Page** (`list.php`):
  - Paginated list of domains by letter/number.
  - Include category/country filters.
- **Static Pages**:
  - `about.php`: About Us content.
  - `categories.php`: List categories with domain counts.
  - `countries.php`: List countries with domain counts.
  - `contact.php`: Contact form with validation.
- **Sitemap** (`sitemap.php`):
  - Generate sitemap.xml with all domain and static pages.
- **Error Pages** (`errors/404.php`, `errors/500.php`):
  - Design custom error pages with navigation.

### 4. Backend Development ✅
- **Admin Login** (`admin/login.php`, `admin/logout.php`):
  - Implement username/password auth (consider 2FA).
  - Secure session management.
- **Admin Dashboard** (`admin/index.php`):
  - Overview of domain count, views, recent activity.
- **URL Management** (`admin/urls.php`):
  - CRUD interface for domains (add/edit/delete/hide).
  - Form validation and sanitization.
- **Domain Refresh** (`admin/refresh.php`):
  - Button to trigger `refresh_domain()` for selected domains.
- **Stats** (`admin/stats.php`):
  - Display domain view counts (bot vs. non-bot).
  - Show trends (e.g., views over time).
- **Export** (`admin/export.php`):
  - Export domain and view stats as CSV.
- **Contact Submissions** (`admin/contact.php`):
  - Display paginated list of contact form submissions.

### 5. Security ✅
- Implement `sanitize_input()` and `validate_domain()` for all inputs.
- Set up rate limiting for queries and views.
- Secure cURL with SSL verification and timeouts.
- Use HTTPS for all pages.
- Add CSRF protection for forms.

### 6. Performance ✅
- Cache domain data in `domains` table (update via cron).
- Optimize queries with indexes.
- Leverage Cloudflare for asset delivery (`assets/` folder).
- Implement pagination (`generate_pagination()`).

### 7. SEO ✅
- Enable clean URLs via `.htaccess` (e.g., `/domain/example.com`).
- Generate meta tags for all pages.
- Auto-update sitemap.xml (`generate_sitemap()`).

### 8. Scalability ✅
- Set up cron job for periodic `refresh_domain()` calls.
- Monitor server load (log cURL errors).
- Use MySQL connection pooling.

### 9. Maintenance ✅
- Log cURL errors to file (`logs/curl_errors.log`).
- Schedule daily database backups.
- Monitor database size and optimize tables.

### 10. Legal/Compliance ⚠️
- **Privacy Policy** (`privacy.php`):
  - Detail data collection (views, contact forms).
  - Comply with GDPR/CCPA.
- **Terms of Use** (`terms.php`):
  - Outline site and admin usage rules.

### 11. Assets ✅
- Use provided `logo.png`.
- Create social media icons (`social-x.png`, `social-fb.png`, `social-yt.png`).
- Develop `style.css` (responsive, Tailwind optional).
- Write `main.js` for search and async loading.

### 12. Testing ⚠️
- Test all pages for responsiveness (mobile/desktop).
- Validate accessibility (ARIA, keyboard nav).
- Test database queries for performance.
- Simulate bot and non-bot views.
- Test rate limiting and security measures.

### 13. Deployment ⚠️
- Configure server for PHP/MySQL.
- Set up Cloudflare for caching and asset delivery.
- Deploy `.htaccess` for clean URLs.
- Enable HTTPS with SSL certificate.
- Set up cron jobs for refreshes and backups.