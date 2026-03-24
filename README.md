# PayRex Laravel Demo

Official demo application for the [PayRex Laravel](https://github.com/whoami15/payrex-laravel) package - explore every feature of the package.

## Requirements

- PHP 8.2+
- Node.js 20+
- MySQL or SQLite
- [PayRex](https://dashboard.payrexhq.com/signin) account with test API keys

## Setup

```sh
git clone git@github.com:whoami15/payrex-laravel-demo.git
cd payrex-laravel-demo
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Add your PayRex API keys to `.env`:

```sh
PAYREX_SECRET_KEY=
PAYREX_PUBLIC_KEY=
PAYREX_WEBHOOK_SECRET=
```

Run migrations and seed:

```sh
php artisan migrate --seed
```

Start the dev server:

```bash
composer dev
```
