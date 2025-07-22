# ZoneWhois - Domain Information Lookup Service

A comprehensive PHP/MySQL web application for domain information and WHOIS lookup services.

## Features

### Frontend
- **Homepage**: Three-column layout showing Last Added, Top Sites, and Last Visited domains
- **Search**: Real-time domain lookup with validation
- **Domain Pages**: Detailed information including WHOIS data, HTTP status, server info, SSL status, and more
- **Alphabetical Navigation**: Browse domains by letter/number
- **Responsive Design**: Mobile-friendly interface
- **SEO Optimized**: Clean URLs, sitemap generation, meta tags

### Backend (Admin Panel)
- **Domain Management**: Add, edit, delete, and hide domains
- **Data Refresh**: Manual refresh of domain WHOIS and HTTP data
- **Statistics**: View counts, bot detection, trends
- **Contact Management**: Review contact form submissions
- **Data Export**: CSV export functionality

## Technical Stack

- **PHP 8.3**: Vanilla PHP with modern features
- **MySQL**: Database for storing domain and view data
- **cURL**: Real-time domain data fetching
- **WHOIS**: Domain registration information lookup
- **Security**: Rate limiting, CSRF protection, input validation

## Installation

### Prerequisites
- PHP 8.3 or higher
- MySQL 5.7 or higher
- Apache with mod_rewrite enabled
- cURL extension for PHP

### Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/zonewhois.git
   cd zonewhois
   ```

2. **Configure database**
   - Update database credentials in `includes/config.php`
   - Run the setup script:
   ```bash
   php setup_database.php
   ```

3. **Configure web server**
   - Point document root to the project directory
   - Ensure `.htaccess` is enabled for URL rewriting

4. **Set permissions**
   ```bash
   chmod 755 logs/
   chmod 644 assets/img/logo.png
   ```

5. **Test the installation**
   - Visit the homepage
   - Try searching for a domain
   - Access admin panel at `/admin/` (username: admin, password: abcd1234)

## Database Schema

### Core Tables
- `domains`: Domain information and metadata
- `page_views`: View tracking with bot detection
- `categories`: Domain categorization
- `countries`: Country-based filtering
- `contact_submissions`: Contact form data
- `rate_limits`: Rate limiting data

## Configuration

### Site Settings (`includes/config.php`)
- Database connection details
- Site URL and name
- Admin credentials
- Rate limiting settings
- cURL timeout settings

### Security Features
- Input sanitization and validation
- CSRF token protection
- Rate limiting on requests
- Bot detection via User-Agent
- Secure session management

## Usage

### For Users
1. Visit the homepage
2. Enter a domain name in the search bar
3. View comprehensive domain information
4. Browse domains alphabetically or by category

### For Administrators
1. Access `/admin/` with credentials
2. Manage domains (add, edit, delete, hide)
3. View statistics and analytics
4. Refresh domain data manually
5. Export data as CSV

## API Endpoints

### Public
- `GET /` - Homepage
- `GET /domain/{domain}` - Domain information page
- `GET /list/{letter}` - Alphabetical domain listing
- `GET /sitemap.xml` - Auto-generated sitemap

### Admin (Protected)
- `GET /admin/` - Admin dashboard
- `GET /admin/urls` - Domain management
- `GET /admin/stats` - Statistics view
- `GET /admin/contact` - Contact submissions
- `GET /admin/export?type={type}` - Data export

## Security Considerations

- All user input is sanitized and validated
- Rate limiting prevents abuse
- CSRF tokens protect forms
- Bot detection separates human and automated traffic
- Secure session management with timeout
- SQL injection protection via prepared statements

## Performance

- Database indexes on frequently queried columns
- Caching of domain data to reduce external requests
- Optimized queries with proper joins
- Static asset caching via .htaccess
- Gzip compression for text files

## Maintenance

### Logs
- Error logs: `logs/errors.log`
- Security logs: `logs/security.log`
- cURL error logs: `logs/curl_errors.log`

### Database Backups
- Regular backups recommended
- Export functionality available in admin panel

### Monitoring
- Check error logs regularly
- Monitor database size and performance
- Review rate limiting effectiveness

## Development

### Local Development
1. Set up local PHP/MySQL environment
2. Configure database connection
3. Run `setup_database.php`
4. Access via local web server

### Deployment
1. Upload files to web server
2. Configure database connection
3. Set proper file permissions
4. Test all functionality
5. Configure SSL certificate
6. Set up Cloudflare (optional)

## License

This project is proprietary software. All rights reserved.

## Support

For support or questions, contact: support@zonwhois.com

## Changelog

### Version 1.0.0
- Initial release
- Complete frontend and backend functionality
- Admin panel with full management capabilities
- Real-time domain data fetching
- SEO optimization and responsive design 