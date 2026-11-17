# Deploy Edel Bot on Laravel Cloud

Connect this repository’s `main` branch to a new application. Use PHP 8.4 (at least 8.4.1), Node 22.12+ or 24, a dedicated PostgreSQL database, and a dedicated Redis-compatible Valkey instance. Configure an S3-compatible object storage bucket for bot images, especially when web and worker processes run on separate instances.

Set `APP_NAME="Edel Bot"`, `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` to the HTTPS application URL, and `SESSION_SECURE_COOKIE=true`. Keep the environment’s generated `APP_KEY` stable. Set `QUEUE_CONNECTION=redis`, `CACHE_STORE=redis`, `SESSION_DRIVER=redis`, `REDIS_CLIENT=predis`, `REDIS_QUEUE_RETRY_AFTER=660`, and `FILESYSTEM_DISK=s3`. Use the database, cache, and bucket connection variables supplied by your environment.

Use the locked Composer dependencies and build frontend assets with `npm ci && npm run build`. Set the deploy command to:

```sh
php artisan migrate --force
```

On first install, provide `SEED_ADMIN_EMAIL` and `SEED_ADMIN_PASSWORD` (at least 16 characters) and run `php artisan db:seed --force` once from the Cloud command interface. Sign in at `/admin` and enroll in MFA.

Configure these background workers against the same environment resources:

| Queue | Command |
|---|---|
| Interactive | `php artisan queue:work redis --queue=telegram-interactive --timeout=120 --tries=3` |
| Broadcast | `php artisan queue:work redis --queue=telegram-broadcast --timeout=600 --tries=3` |
| Automation | `php artisan queue:work redis --queue=automation --timeout=120 --tries=5` |
| Default | `php artisan queue:work redis --queue=default --timeout=120 --tries=3` |

Enable the Laravel scheduler for this environment. It runs webhook recovery, scheduled broadcasts, automation, and cleanup.

Create the Telegram bot in BotFather and set its own `TELEGRAM_BOT_TOKEN`, `TELEGRAM_BOT_USERNAME` (without @), and independently generated `TELEGRAM_WEBHOOK_SECRET`. Add the bot as administrator of its channel, then set that channel’s ID and URL in **Admin → Settings → Infrastructure**. Register the webhook with `php artisan telegram:set-webhook` and configure the Telegram command menu with `php artisan telegram:setup-menu`.

Verify `/start`, the join check, the referral link’s username, and a test image delivery. If enabling rewards, import a small batch of valid promo codes under **Rewards → Promo Codes**, redeem one with a test wallet, and verify delivery and history.
