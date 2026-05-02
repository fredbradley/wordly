# Deploying Wordly

## Recommended platforms

| Platform | Notes |
|----------|-------|
| **Laravel Forge + DigitalOcean/Hetzner** | Best DX, one-click deploy, auto-SSL |
| **Ploi** | Cheaper Forge alternative |
| **Railway / Render** | Git-push deploy, free tier available |
| **VPS (bare)** | Full control, more setup |

---

## Quick deploy checklist

### 1. Server prerequisites
- PHP 8.2+
- Composer
- Node 20+ (for asset build)
- SQLite (`apt install sqlite3 php-sqlite3`)
- Nginx or Apache
- SSL certificate (Let's Encrypt via Certbot or Forge)

### 2. Upload / clone code
```bash
git clone https://github.com/you/wordly.git /var/www/wordly
cd /var/www/wordly
```

### 3. Install dependencies
```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
```

### 4. Environment
```bash
cp .env.production .env
# Edit .env — set APP_URL, APP_KEY, MAIL settings
php artisan key:generate
```

### 5. Database
```bash
touch database/database.sqlite
php artisan migrate --force
php artisan db:seed --class=DailyWordSeeder --force
```

### 6. Permissions
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data .
```

### 7. Optimise
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache 2>/dev/null || true
```

### 8. Cron (for scheduled word refresh)
Add to crontab (`crontab -e`):
```
* * * * * cd /var/www/wordly && php artisan schedule:run >> /dev/null 2>&1
```

### 9. Queue worker (optional, for future email features)
```bash
php artisan queue:work --daemon
```
Use Supervisor to keep it alive.

---

## Nginx config
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/wordly/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht { deny all; }
}
```
Then run `certbot --nginx -d yourdomain.com` for SSL.

---

## Post-deploy updates
```bash
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm ci && npm run build
php artisan optimize
```
