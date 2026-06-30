# 🇰🇪 Kenyan SMM Panel

A complete Social Media Marketing (SMM) panel built with Laravel 11 and Vue.js, designed for the Kenyan market with M-Pesa integration and KES pricing.

## Features

- **User Management**: Registration, login, roles (Admin, Reseller, User)
- **Wallet System**: Add funds via M-Pesa STK Push, cards, crypto, or bank transfer
- **Service Catalog**: Categorized social media services (Instagram, TikTok, YouTube, X/Twitter, Facebook, Telegram)
- **Order Management**: Place, track, and manage orders
- **Admin Dashboard**: Manage services, orders, users, and view reports
- **Reseller API**: JSON API for automated ordering
- **Support Tickets**: Built-in customer support system
- **M-Pesa Integration**: Seamless STK Push for instant wallet funding in KES

## Tech Stack

- **Backend**: Laravel 11, PHP 8.4, SQLite
- **Frontend**: Vue.js 3, Vite, Tailwind CSS
- **Payments**: M-Pesa Daraja API (configurable)
- **Authentication**: Laravel Sanctum

## Getting Started

```bash
# Install dependencies
composer install
npm install

# Run migrations and seed data
php artisan migrate --seed

# Build frontend assets
npm run build

# Start the development server
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## Default Credentials

- **Admin**: admin@smmpanel.co.ke / admin123
- **Register** a new user account via the web UI

## API Endpoints

### Public
- `POST /api/register` - Register new user
- `POST /api/login` - Login
- `GET /api/services` - List services
- `GET /api/services/{id}` - Get service details
- `GET /api/categories` - List categories

### Protected (auth:sanctum)
- `POST /api/logout` - Logout
- `GET /api/user` - Get current user
- `PUT /api/user` - Update profile
- `GET /api/balance` - Get wallet balance
- `POST /api/orders` - Place order
- `GET /api/orders` - List user orders
- `POST /api/deposit` - Deposit funds
- `GET /api/transactions` - Transaction history
- `POST /api/tickets` - Create support ticket
- `GET /api/tickets` - List support tickets

### Reseller API
- `GET /api/reseller/services` - List services
- `POST /api/reseller/orders` - Place order
- `GET /api/reseller/orders/{id}/status` - Check order status
- `GET /api/reseller/balance` - Get balance

### Admin (auth:sanctum + admin)
- `GET /api/admin/dashboard` - Admin dashboard stats
- `GET /api/admin/services` - Manage services
- `POST /api/admin/services` - Create service
- `PUT /api/admin/services/{id}` - Update service
- `DELETE /api/admin/services/{id}` - Delete service
- `POST /api/admin/services/bulk-import` - Bulk import services
- `GET /api/admin/orders` - Manage all orders
- `PUT /api/admin/orders/{id}/status` - Update order status
- `GET /api/admin/users` - Manage users
- `PUT /api/admin/users/{id}/balance` - Update user balance
- `PUT /api/admin/users/{id}/role` - Update user role
- `GET /api/admin/reports/orders` - Order reports
- `GET /api/admin/reports/revenue` - Revenue reports
- `GET /api/admin/reports/users` - User reports

## Configuration

### M-Pesa
Set the following in your `.env` file:

```env
MPESA_CONSUMER_KEY=your_consumer_key
MPESA_CONSUMER_SECRET=your_consumer_secret
MPESA_SHORTCODE=174379
MPESA_PASSKEY=your_passkey
MPESA_ENVIRONMENT=sandbox
```

## Project Structure

```
app/
├── Http/Controllers/
│   ├── Admin/
│   │   ├── AdminController.php
│   │   ├── CategoryController.php
│   │   └── ServiceController.php
│   ├── AuthController.php
│   ├── OrderController.php
│   ├── PaymentController.php
│   ├── ReportController.php
│   ├── ResellerController.php
│   ├── ServiceController.php
│   ├── TicketController.php
│   └── UserController.php
├── Middleware/
│   └── AdminMiddleware.php
├── Models/
│   ├── Category.php
│   ├── Order.php
│   ├── Service.php
│   ├── SupportTicket.php
│   ├── Transaction.php
│   └── User.php
└── Services/
    └── MpesaService.php

database/migrations/
├── 0001_01_01_000000_create_users_table.php
├── 2024_01_01_000001_create_categories_table.php
├── 2024_01_01_000002_create_services_table.php
├── 2024_01_01_000003_create_orders_table.php
├── 2024_01_01_000004_create_transactions_table.php
├── 2024_01_01_000005_create_support_tickets_table.php
└── 2024_06_30_000001_add_smm_fields_to_users_table.php

resources/js/
├── App.vue
├── app.js
├── router.js
├── pages/
│   ├── Home.vue
│   ├── Login.vue
│   ├── Register.vue
│   ├── Dashboard.vue
│   ├── Services.vue
│   ├── Orders.vue
│   ├── Deposit.vue
│   ├── Tickets.vue
│   └── admin/
│       ├── Dashboard.vue
│       ├── Services.vue
│       ├── Orders.vue
│       └── Users.vue
└── components/
```

## License

MIT License
