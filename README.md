# Company Transaction Management API

## Description

This project provides a set of APIs to manage the financial transactions of a company. It's built using Laravel and features robust authentication and role-based authorization.

### Features

- **Authentication**: Utilizes Laravel Sanctum for secure token-based authentication.
- **Authorization**: Implements role-based authorization using `spatie/laravel-permission`.

## Prerequisites

- PHP >= 7.4
- MySQL (or database of your choice)
- Composer

## Installation Steps

### 1. Clone the Project

```bash
git clone https://github.com/yehia-khalil/transactions-manager.git
```

### 2. Install Dependencies
Navigate to the project folder and install the required PHP packages:

```bash
cd transactions-manager
composer install
```

### 3. Generate Application Key

```bash
php artisan key:generate
```

### 4. Set Up Environment Variables

Copy `.env.example` to `.env` and update the database credentials:

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Migrate and Seed Database

Run the migration and seed the database:

```bash
php artisan migrate --seed
```

This will create the required database tables and seed in two roles: `admin` and `user`. It will also create 10 users and an admin account with the following credentials:

- **Email**: `admin@admin.com`
- **Password**: `password`

### 6. Additional Seeding (Optional)

To seed in categories, subcategories, transactions for the past 3 years and upcoming 2 years with 3 transactions per year, and their payments, run:

```bash
php artisan db:seed --class=TransactionSeeder
```

## Usage

Once the setup is complete, you can start the development server by running:

```bash
php artisan serve
```

The API will be available at `http://127.0.0.1:8000/`.

---

### Generating Monthly Financial Reports

To generate a monthly report for financial performance over a specified date range, you can run the following Artisan command:

```bash
php artisan report:generate {start_date} {end_date}
```

Replace `{start_date}` and `{end_date}` with the desired date range in YYYY-MM-DD format. For example:

```bash
php artisan php artisan report:generate 2020-01-01 2025-01-01
```

This command will run a job to query the transactions, aggregate the data, and generate an Excel report. The report will then be downloaded to your computer.

---


## API Documentation

You can find the postman collection .json file inside the project

---
