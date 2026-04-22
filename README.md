# Sales Page Generator (Laravel + React + Gemini)

A full-stack web application that transforms raw product or service information into a structured, persuasive sales page.

This project uses:
- Laravel 13 (backend)
- Inertia.js + React (frontend)
- Supabase Postgres (database)
- Gemini API (AI copy generation)
- Vercel (deployment target)

## Features

- User authentication (register, login, logout) via Laravel Breeze.
- Product/service input form with structured fields:
  - Product/service name
  - Description
  - Key features (multi-input)
  - Target audience
  - Price
  - Unique selling points (multi-input)
- AI sales-page generation through Gemini API.
- Structured generated output sections:
  - Headline
  - Subheadline
  - Product description
  - Benefits
  - Features breakdown
  - Social proof placeholder
  - Pricing display
  - Call-to-action (CTA)
- Live landing-page style preview.
- Saved pages management:
  - List
  - View details
  - Re-generate
  - Delete
- Ownership authorization (users can only access their own pages).

## Architecture

- Laravel monolith with Inertia React.
- Main domain model: `sales_pages` (belongs to `users`).
- AI orchestration in `SalesPageGeneratorService`.
- Policy-based access control with `SalesPagePolicy`.

## Tech Stack

- PHP 8.5+
- Laravel 13
- React 18 + Inertia.js
- Tailwind CSS
- PostgreSQL (Supabase)
- Gemini Generative Language API

## Project Structure (Key Files)

- Routes: `routes/web.php`
- Controller: `app/Http/Controllers/SalesPageController.php`
- Service: `app/Services/SalesPageGeneratorService.php`
- Model: `app/Models/SalesPage.php`
- Migration: `database/migrations/2026_04_22_034650_create_sales_pages_table.php`
- Dashboard UI: `resources/js/Pages/Dashboard.jsx`
- Preview component: `resources/js/Components/SalesPages/SalesPagePreview.jsx`
- Saved pages UI: `resources/js/Pages/SalesPages/Index.jsx`, `Show.jsx`

## Environment Variables

Copy and fill:

```bash
cp .env.example .env
```

Required variables:

```env
APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=pgsql
DB_HOST=db.YOUR_PROJECT_REF.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres.YOUR_PROJECT_REF
DB_PASSWORD=your-supabase-db-password
DB_SSLMODE=require

GEMINI_API_KEY=your-gemini-api-key
GEMINI_MODEL=gemini-3-flash-preview
```

## Local Setup

1. Install PHP dependencies

```bash
composer install
```

2. Install frontend dependencies

```bash
npm install
```

3. Generate app key (if needed)

```bash
php artisan key:generate
```

4. Run migrations

```bash
php artisan migrate
```

5. Seed demo accounts and sample pages

```bash
php artisan db:seed
```

6. Run development servers

```bash
php artisan serve
npm run dev
```

## Demo Seed Accounts

All seeded users use password: `password`

- `owner@salespage.local`
- `marketer@salespage.local`
- `viewer@salespage.local`

## Testing

Run tests:

```bash
php artisan test
```

Note: ensure your PHP environment has the required DB/PDO drivers enabled.

## AI Integration Notes

The app uses Gemini Interactions API (`/v1beta/interactions`) and expects JSON-formatted generation output.

If generation fails:
- User-facing message remains generic.
- Detailed error is written to Laravel logs:

```text
storage/logs/laravel.log
```

## Deployment (Vercel + Supabase)

Current `vercel.json` is configured for:
- PHP runtime via `vercel-php`
- Frontend build output from Vite (`public/build`)

Important:
- `npm run build` is included in Vercel build.
- `php artisan migrate` is **not** automatically run by `vercel.json` and should be handled separately.
- `php artisan serve` is **not** used on Vercel.

## Security Notes

- Never commit `.env`.
- Keep API keys and database credentials in Vercel/Supabase environment settings.
- Enforce HTTPS in production.

## License

This project is open-sourced under the MIT License.
