# Student Result Management System

A small college presentation project built with **PHP, HTML/CSS/JavaScript and Supabase PostgreSQL**.

## Scope

### Student
- Login with roll number and password
- Search result by roll number
- View subject-wise marks
- Automatic total, percentage, grade and pass/fail status
- Logout

### Admin
- Login
- Dashboard
- Add/view/edit/delete students
- Add/view/update/delete results
- Logout

No teacher portal, attendance, fees, notifications, PDF generation, or advanced analytics are included.

## Stack

- PHP
- HTML5 / CSS3 / JavaScript
- Supabase PostgreSQL
- PDO PostgreSQL driver
- PHP Sessions

## Setup

1. Create a Supabase project.
2. Open **SQL Editor** in Supabase and run `database/schema.sql`.
3. Make sure PHP has the `pdo_pgsql` extension enabled.
4. Set these environment variables for PHP/Apache:

```text
DB_HOST=your-supabase-db-host
DB_PORT=5432
DB_NAME=postgres
DB_USER=postgres
DB_PASSWORD=your-database-password
```

For Supabase, use the PostgreSQL connection details from the project's **Connect** panel. Keep the database password private and never commit it.

5. Run the project using XAMPP Apache or PHP's local server.

```bash
php -S localhost:8000
```

6. Open `http://localhost:8000/`.

## Demo credentials

The SQL schema includes demo records for presentation/testing:

- Admin: `admin` / `admin123`
- Student: roll `2024001` / password `student123`

Change demo credentials before using the project outside a classroom/demo environment.
