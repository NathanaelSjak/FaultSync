# Environment Configuration Example

Copy this content to your `.env` file:

```env
APP_NAME="FaultSync Bank App"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost:8000
APP_LOCALE=id
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
APP_MAINTENANCE_STORE=database

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# Database Configuration
# Default: SQLite (recommended for development)
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
DB_FOREIGN_KEYS=true

# Alternative: MySQL Configuration (uncomment to use)
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=faultsync
# DB_USERNAME=root
# DB_PASSWORD=
# DB_SOCKET=
# DB_CHARSET=utf8mb4
# DB_COLLATION=utf8mb4_unicode_ci

# Session Configuration
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

# Queue & Cache
BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=file
CACHE_PREFIX=

# Redis (optional)
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Memcached (optional)
MEMCACHED_HOST=127.0.0.1

# Mail Configuration (optional)
MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# AWS S3 (optional)
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

# Vite
VITE_APP_NAME="${APP_NAME}"
```

## Setup Instructions

1. **Copy the example file:**
   ```bash
   cp ENV_EXAMPLE.md .env
   ```
   Or manually create `.env` file and copy the content above.

2. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```
   This will automatically fill in the `APP_KEY` value.

3. **Create SQLite Database (if using SQLite):**
   ```bash
   touch database/database.sqlite
   ```

4. **Run Migrations:**
   ```bash
   php artisan migrate
   ```

5. **Start the Server:**
   ```bash
   php artisan serve
   ```

## Important Notes

- **APP_KEY**: Must be generated using `php artisan key:generate`
- **Database**: Default is SQLite (no setup needed). For MySQL, uncomment the MySQL section and configure accordingly.
- **APP_DEBUG**: Set to `false` in production
- **APP_ENV**: Set to `production` when deploying

