# MIMS - Microfinance Information Management System

A comprehensive Laravel-based banking and microfinance management system for handling customers, accounts, transactions, and fixed deposits.

## Project Overview

MIMS is a web-based banking application built with Laravel 12 and Livewire that provides:

- Customer management and KYC verification
- Multiple account types with joint account support
- Transaction processing (deposits, withdrawals, transfers)
- Fixed deposit management with automated interest calculations
- Role-based access control and audit logging
- Comprehensive reporting system

## Documentation

- 📋 **[Project Roadmap](ROADMAP.md)** - Development phases and milestones
- 🗄️ **[Database Schema](DATABASE_OUTLINE.md)** - Complete database structure and relationships

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+ & NPM
- SQLite 3.8+ (included with PHP)

## Quick Start

### 1. Clone and Setup

```bash
git clone <repository-url>
cd database-project
cp .env.example .env
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Environment Setup

```bash
# Generate application key
php artisan key:generate

# Create SQLite database file
touch database/database.sqlite
```

### 4. Database Setup

```bash
# Run migrations
php artisan migrate

# Seed database with initial data
php artisan db:seed
```

### 5. Build Assets

```bash
# Development build
npm run dev

# Production build
npm run build
```

### 6. Start Development Server

```bash
# Start Laravel development server
php artisan serve

# Or use the custom dev command (with queue worker, pail, and vite)
composer run dev
```

### 7. Access the Application

- **Laravel App**: http://localhost:8000

## Laravel Basics & Artisan Commands

### Core Artisan Commands

#### Database & Migrations
```bash
# Create a new migration
php artisan make:migration create_table_name

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Reset migrations
php artisan migrate:reset

# Refresh migrations (reset + migrate)
php artisan migrate:refresh

# Seed database
php artisan db:seed

# Create a seeder
php artisan make:seeder TableSeeder
```

#### Models & Factories
```bash
# Create a model
php artisan make:model ModelName

# Create model with migration
php artisan make:model ModelName -m

# Create model with migration, factory, and seeder
php artisan make:model ModelName -mfs

# Create a factory
php artisan make:factory ModelFactory
```

#### Controllers & Middleware
```bash
# Create a controller
php artisan make:controller ControllerName

# Create a resource controller
php artisan make:controller ControllerName --resource

# Create middleware
php artisan make:middleware MiddlewareName
```

#### Livewire Components
```bash
# Create a Livewire component
php artisan make:livewire ComponentName

# Create Livewire component in subdirectory
php artisan make:livewire Auth/LoginForm
```

#### Jobs & Queues
```bash
# Create a job
php artisan make:job JobName

# Process queue jobs
php artisan queue:work

# List failed jobs
php artisan queue:failed

# Retry failed job
php artisan queue:retry job-id
```

#### Cache & Optimization
```bash
# Clear application cache
php artisan cache:clear

# Clear config cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Clear view cache
php artisan view:clear

# Optimize for production
php artisan optimize

# Generate autoload files
composer dump-autoload
```

#### Routes & Views
```bash
# List all routes
php artisan route:list

# Create a request class
php artisan make:request RequestName

# Create a form request
php artisan make:request StoreUserRequest
```

### Project Structure

```
database-project/
├── app/
│   ├── Http/Controllers/     # Application controllers
│   ├── Livewire/            # Livewire components
│   ├── Models/              # Eloquent models
│   └── Providers/           # Service providers
├── bootstrap/               # Application bootstrap files
├── config/                  # Configuration files
├── database/
│   ├── factories/           # Model factories
│   ├── migrations/          # Database migrations
│   └── seeders/            # Database seeders
├── public/                  # Public assets
├── resources/
│   ├── css/                # Stylesheets
│   ├── js/                 # JavaScript files
│   └── views/              # Blade templates
├── routes/                  # Route definitions
├── storage/                 # File storage
└── tests/                   # Application tests
```

## Development Workflow

### 1. Feature Development
```bash
# Create feature branch
git checkout -b feature/customer-management

# Create necessary files
php artisan make:model Customer -mfs
php artisan make:controller CustomerController --resource
php artisan make:livewire Customers/CustomerList

# Run tests
php artisan test

# Commit changes
git add .
git commit -m "Add customer management feature"
```

### 2. Database Changes
```bash
# Create migration for new table
php artisan make:migration create_customers_table

# Create migration to modify existing table
php artisan make:migration add_status_to_customers_table

# Run migration
php artisan migrate
```

### 3. Testing
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/CustomerTest.php

# Run tests with coverage
php artisan test --coverage
```

## Common Tasks

### Create a New Module (e.g., Customer Management)

1. **Model & Migration**:
   ```bash
   php artisan make:model Customer -m
   ```

2. **Controller**:
   ```bash
   php artisan make:controller CustomerController --resource
   ```

3. **Livewire Components**:
   ```bash
   php artisan make:livewire Customers/CustomerList
   php artisan make:livewire Customers/CustomerForm
   ```

4. **Form Requests**:
   ```bash
   php artisan make:request StoreCustomerRequest
   php artisan make:request UpdateCustomerRequest
   ```

5. **Factory & Seeder**:
   ```bash
   php artisan make:factory CustomerFactory
   php artisan make:seeder CustomerSeeder
   ```

### Database Operations

```bash
# Fresh migration (drop all tables and recreate)
php artisan migrate:fresh

# Fresh migration with seeding
php artisan migrate:fresh --seed

# Check migration status
php artisan migrate:status
```

### Troubleshooting

```bash
# Clear all caches
php artisan optimize:clear

# Fix storage permissions
sudo chown -R www-data:www-data storage bootstrap/cache

# Debug routes
php artisan route:list | grep customer

# Check logs
tail -f storage/logs/laravel.log
```

## Production Deployment

### 1. Environment Setup
```bash
# Set production environment
APP_ENV=production
APP_DEBUG=false

# Configure production database
# Set proper cache and session drivers
# Configure mail settings
```

### 2. Optimization
```bash
# Install production dependencies only
composer install --no-dev --optimize-autoloader

# Cache configuration and routes
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build production assets
npm run build
```

### 3. Security
```bash
# Generate secure app key
php artisan key:generate

# Set proper file permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 755 storage bootstrap/cache
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass
6. Submit a pull request

## License

This project is licensed under the MIT License.

## Support

For development questions and issues:
- Check the [ROADMAP.md](ROADMAP.md) for planned features
- Review the [DATABASE_OUTLINE.md](DATABASE_OUTLINE.md) for schema details
- Create an issue for bugs or feature requests