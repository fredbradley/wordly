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

# Seed the word bank (374 built-in words)
php artisan db:seed --class=DailyWordSeeder --force

# Assign the next 60 days of daily words from the bank
php artisan wordly:assign-daily-word --days=60
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

### 8. Cron — Laravel scheduler
The Laravel scheduler must run every minute. It drives two things:
- **01:00 daily** — `wordly:assign-daily-word` picks a random unused word from
  the `words` table and inserts tomorrow's entry into `daily_words`.
- Any future scheduled tasks you add to `routes/console.php`.

Add a single crontab entry for the `www-data` user (or whichever user runs PHP):

```bash
crontab -e -u www-data
```

Add this line:
```
* * * * * cd /var/www/wordly && php artisan schedule:run >> /dev/null 2>&1
```

**Verify it's working** — after a minute, check the scheduler ran:
```bash
php artisan schedule:list        # shows registered commands and their next run time
tail -f storage/logs/laravel.log # watch for any scheduler errors
```

**Managing the word bank via Artisan** (requires SSH access):
```bash
# Add a new word to the pool
php artisan wordly:word add tiger

# Remove a word (soft-deleted — historical game records are preserved)
php artisan wordly:word remove tiger

# Re-adding a soft-deleted word restores it
php artisan wordly:word add tiger

# Manually top up the schedule (e.g. after adding a batch of new words)
php artisan wordly:assign-daily-word --days=30
```

> **Note:** If the word bank runs low and the cron cannot assign a word for
> tomorrow, an error is logged. Add more words with `wordly:word add` and
> run `wordly:assign-daily-word` manually to backfill.

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

## Reverb WebSocket server (real-time activity feed)

Reverb must run as a persistent background process. Use Supervisor:

```ini
[program:reverb]
command=php /var/www/wordly/artisan reverb:start --host=0.0.0.0 --port=8080
directory=/var/www/wordly
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/reverb.log
```

Set `REVERB_HOST` to your domain in `.env` and `VITE_REVERB_HOST` before building assets.
For SSL, set `REVERB_SCHEME=https` and proxy port 8080 through Nginx with TLS.

---

## Post-deploy updates
```bash
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm ci && npm run build
php artisan optimize
```
