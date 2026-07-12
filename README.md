# Mythos Task

Mythos Task is a task-earning platform: users fund their wallet via M-Pesa, start paid tasks, claim daily rewards, build a referral team, and withdraw earnings. It ships with a full admin panel for managing users, transactions, tasks, coupons, and platform settings.

## Tech stack

- **Backend:** Laravel 13 (PHP 8.3), MySQL, session-based auth
- **Frontend:** [Inertia.js](https://inertiajs.com) + Vue 3 + TypeScript — a server-routed SPA, no separate API layer
- **UI:** Tailwind CSS v4 + [shadcn-vue](https://www.shadcn-vue.com) (reka-ui primitives), [Sonner](https://www.shadcn-vue.com/docs/components/sonner) for toast notifications
- **Routing helper:** [Ziggy](https://github.com/tighten/ziggy) — use Laravel's `route()` names directly from Vue
- **Payments:** PalPluss (M-Pesa STK push for deposits, B2C payouts for withdrawals)

## Features

**User-facing**
- Registration with referral codes, login, password recovery request flow
- Dashboard with wallet balance, earnings summary, and active-task overview
- Tasks (start a task, view daily rewards, compare all tasks)
- Daily reward claiming per active task
- Deposits (M-Pesa STK push) and withdrawals (to a saved M-Pesa account)
- Referral team view with per-member deposit stats and an invite link
- Coupon redemption
- Profile settings (phone number, password change)

**Admin**
- Dashboard KPIs (deposits, withdrawals, balances, user counts)
- User management (activate/deactivate, promote to admin, reset password, search)
- Deposit/withdrawal review (manual deposit entry, approve/reject withdrawals)
- Password recovery request queue
- Task package CRUD (with image upload)
- Coupon CRUD
- Withdrawal account management
- Wallet balance editing per user
- Platform settings: withdrawal min/fee, home banner image, claim-page image, social/support links

## Getting started

**Requirements:** PHP 8.3+, Composer, Node 20+, MySQL.

```bash
composer install
npm install
cp .env.example .env   # if starting fresh
```

Configure `.env` — database credentials, and if you need working payments/notifications, the PalPluss keys (`PALPLUSS_*`) and support/link settings (`SUPPORT_*`, `*_URL`).

**First run:** if `storage/installed.lock` doesn't exist, visiting the app redirects to `/setup` — a standalone installation wizard that checks PHP requirements, tests the DB connection, runs migrations, and creates the first admin account. This page is intentionally plain Blade/vanilla JS (it runs before the app is configured, so it can't depend on the Vite build).

Once installed, run the full dev stack (PHP server + Vite HMR + queue worker + log tailing, all together):

```bash
composer run dev
```

Or individually:

```bash
php artisan serve       # backend
npm run dev              # Vite dev server (HMR)
```

**Production build:**

```bash
npm run build
```

## Project structure

- `routes/web.php` — all routes; every controller action either renders an Inertia page or redirects with a flash message
- `resources/js/Pages/{Auth,Dashboard,Admin}/*.vue` — one Vue page per route, mirroring the controller structure
- `resources/js/layouts/{AppLayout,AdminLayout}.vue` — the two shells (warm stone/amber theme for user pages, gray/blue theme for admin — deliberately different systems, scoped independently in `resources/css/app.css`)
- `resources/js/components/` — shared pieces: `Icon.vue` (inlines SVGs from `public/icons/hugeicons`), `PageHeader`, `Pagination`, `BottomNav`
- `resources/js/components/ui/` — shadcn-vue primitives (Button, Card, Dialog, Table, Tabs, Sonner, etc.)
- `resources/js/composables/useFlashToasts.ts` — bridges Laravel session flash messages and Inertia validation errors to Sonner toasts, centrally, for every page
- `app/Http/Middleware/HandleInertiaRequests.php` — shares the authenticated user (with earnings) and flash messages on every request
- `app/Models/` — `User`, `Earning`, `Order`, `Package`, `Transaction`, `Coupon`, `CouponUse`, `WithdrawalAccount`, `PasswordRecoveryRequest`, `Setting`

## Notes for contributors

- Type-check the frontend with `npx vue-tsc --noEmit` before committing.
- `User` has a legacy dual-password field (`passwrd` plaintext, `password` hashed) — login checks the hash first and falls back to plaintext, migrating it to hashed on success. Don't remove the plaintext fallback without a migration plan for existing accounts.
- Decimal-cast model attributes (`Earning.balance`, `Transaction.amount`, `Package.amount`, etc.) serialize to Inertia as **strings**, not numbers — see `resources/js/types/models.ts`.
- `/callback` and `/callback/b2c` are unauthenticated JSON webhook endpoints for PalPluss payment callbacks — CSRF-exempt by design, not part of the Inertia app.
