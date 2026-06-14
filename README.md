<img width="2560" height="1600" alt="Screenshot 2026-04-26 215751" src="https://github.com/user-attachments/assets/0f6be489-1a67-40d3-baa1-fa96077048c1" />
# chriss Starter Kit

chriss Starter Kit is a Laravel starter project built for students and developers who want a clean foundation for small to medium web applications. It includes custom authentication, public pages, a protected dashboard, profile pages, notification routes, and reusable Blade layouts styled with Bootstrap and custom CSS.

The goal is simple: give you a working Laravel base that is easy to read, extend, and redesign.

## What Is Included

- Public home page and about page
- Custom login, logout, and password reset flow
- Protected dashboard route under `/dashboard`
- Responsive dashboard layout with header, sidebar, stat cards, summary tiles, chart area, and recent activity panel
- Profile page with editable account information
- Notification routes and basic notification views
- Audit trail model, migration, middleware, observer, and service structure
- Responsive Blade layouts using Bootstrap 5, Bootstrap Icons, and custom CSS
- MySQL-ready Laravel migrations for users, cache, jobs, and audit trails
- Friendly error pages for common HTTP errors

## Tech Stack

- PHP 8.2+
- Laravel 12
- Blade templates
- Bootstrap 5
- Bootstrap Icons
- JavaScript
- MySQL
- Chart.js on the dashboard
- SweetAlert2 for system alerts

## Main Routes

```text
/              Home page
/home          Home page
/about         About page
/login         Login page
/forgot-password
/dashboard     Protected dashboard
/dashboard/profile
/dashboard/notifications
```

## Requirements

- PHP 8.2 or newer
- Composer
- MySQL or another database supported by Laravel

## Installation

```bash
composer install
```

Create your local environment file:

```bash
cp .env.example .env
```

Then update the database values in `.env`:

```env
APP_NAME="chriss Starter Kit"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

Then run:

```bash
php artisan key:generate
php artisan migrate
php artisan serve
```

Open:

```text
http://127.0.0.1:8000
```

## Start Your Own Git Repository

If you downloaded this starter kit and want to use it as your own project, remove the existing Git history first, then initialize a fresh repository:

```bash
rm -rf .git
git init
git add .
git commit -m "Initial project setup"
```

On Windows PowerShell:

```powershell
Remove-Item -Recurse -Force .git
git init
git add .
git commit -m "Initial project setup"
```

After that, create your own GitHub repository and connect it:

```bash
git remote add origin https://github.com/your-username/your-repository-name.git
git branch -M main
git push -u origin main
```

## Testing

```bash
php artisan test
```

## Dashboard Notes

The dashboard contains a mix of real and placeholder data:

- `Total Users` comes from the users table.
- `Active Sessions`, `New Messages`, `Pending Tasks`, summary tiles, and chart values are placeholder/demo values.
- These placeholders are meant to be replaced with real business data as you build your application.

## Authentication Notes

Authentication is custom-built with local controllers:

- `LoginController`
- `PasswordResetController`

This project is not using Laravel Breeze or Jetstream.

## Known Notes

- Use `.env.example` as the template for local setup. Never commit real production secrets in `.env`.
- The visible pages are powered by Blade, Bootstrap, and public CSS/JS files.
- The dashboard chart and several dashboard counters are demo placeholders.
- If old styles remain in the browser, run `php artisan view:clear` and hard refresh with `Ctrl + F5`.

## Suggested Next Steps

- Replace dashboard placeholder data with real queries.
- Add role and permission logic if your application needs admins, staff, or users.
- Add CRUD modules for your project domain.
- Add tests for authentication, profile updates, and dashboard access.
- Add tests for any new CRUD modules you build.

## License

This project uses the MIT license. See [LICENSE](LICENSE) for details.

## Developer
Built and maintained by Hagai Harold Ngobey.

- GitHub: <https://github.com/harryhagai>
- Email: hngobey@gmail.com

<img width="2560" height="1600" alt="Screenshot 2026-04-26 215736" src="https://github.com/user-attachments/assets/3449e40c-ff4e-4088-8ccb-7d622d0ce897" />
<img width="2560" height="1600" alt="Screenshot 2026-04-26 215701" src="https://github.com/user-attachments/assets/b52c2a49-a8a2-40f0-a96d-5484ceb3bf71" />
<img width="2560" height="1600" alt="Screenshot 2026-04-26 215634" src="https://github.com/user-attachments/assets/d9f6d76a-3d0a-49e5-b83a-852235b4574e" />
<img width="2560" height="1600" alt="Screenshot 2026-04-26 215832" src="https://github.com/user-attachments/assets/a590967b-1145-421d-bb47-61747cf72337" />
<img width="2560" height="1600" alt="Screenshot 2026-04-26 215815" src="https://github.com/user-attachments/assets/a61352e4-efd8-4a00-81d1-6469bbe993af" />
<img width="2560" height="1600" alt="Screenshot 2026-04-26 215806" src="https://github.com/user-attachments/assets/3d637a28-05a6-4b56-872c-e9f05a5b1776" />

