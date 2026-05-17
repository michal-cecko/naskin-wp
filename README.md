# Naskin — WordPress theme

WordPress theme for **Naskin**, a beauty salon in Považská Bystrica (SK). Custom multi-role admin dashboard, online booking flow, SMS reminders, FB integration.

> Built on top of the **Wordpresaurus** boilerplate ([repo](https://github.com/michal-cecko/wordpresaurus-wp-plugin)) — a custom in-house MU plugin that brings Laravel-style Eloquent (via Corcel), Blade-like views, and config files to WordPress theme development.

## What it does

- **Multi-role admin** — owner, manager, employee, customer views; capability-gated
- **Online booking** — service catalogue, employee availability, slot selection, confirmation flow
- **SMS notifications** — BulkGate integration for booking confirmations + reminders
- **CRM** — customer records with visit history and notes
- **Cron-driven jobs** — reminder dispatch, ICS generation; auth via WP constants

## Stack

- **WordPress** + **PHP 8.x**
- **Wordpresaurus** MU plugin (illuminate/view, illuminate/database, jgrossi/corcel)
- **BulkGate** PHP SDK for SMS
- **Tailwind** + **PostCSS** for styles
- **Composer** for PHP deps, **npm** for asset build

## Layout

```
.
├── functions.php
├── index.php
├── style.css
├── composer.json              # bulkgate/php-sdk dep
├── config/                    # appointments, integrations, mail, sms, socials, theme
├── theme/                     # PSR-4 Theme\ namespace (Services, Models, Resources, etc.)
├── resources/                 # views + assets (sass/js source)
├── dist/                      # built assets (gulp output)
├── inc/                       # WP-specific includes
├── database/                  # custom migrations / sql
└── tailwind.config.js
```

## Required `wp-config.php` constants

The theme reads all credentials from `define(...)` constants — never hardcoded:

```php
// SMS via BulkGate
define('NASKIN_BULKGATE_APP_TOKEN',  '<token>');
define('NASKIN_BULKGATE_APP_ID',     33177);
define('NASKIN_BULKGATE_ANDROID_KEY','<key>');

// reCAPTCHA v3
define('NASKIN_RECAPTCHA_SECRET',    '<secret>');
define('NASKIN_RECAPTCHA_SITE_KEY',  '<site-key>');

// Cron endpoints (gate /wp-json/api/v1/notify-customers and /generate-ics)
define('NASKIN_CRON_NOTIFICATIONS_TOKEN', '<random>');
define('NASKIN_CRON_ICS_TOKEN',           '<random>');
```

All settings degrade to empty strings if unset, so endpoints fail closed.

## Build

```bash
composer install
npm install
npm run build              # production assets → dist/
npm run dev                # watch mode
```

## License

[MIT](LICENSE) © Michal Čečko
