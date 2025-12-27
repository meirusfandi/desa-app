# Desa App

Desa App is a web-based village administration system designed to streamline public service requests (Surat) and manage internal village data. It provides dedicated dashboards for Residents (Warga), Secretaries, Village Heads (Kepala Desa), and Administrators.

## Features

- **Role-Based Access Control**:
  - **Admin**: Manage master data, users, and roles.
  - **Sekretaris**: Verify and process incoming surat requests.
  - **Kepala Desa**: Digitally sign approved surat requests.
  - **Warga**: Submit surat requests and track their status.
- **Surat Management**: Digital submission, processing, and generation of surat documents (Word/PDF).
- **Mazer Admin Dashboard**: Modern, responsive UI based on the Mazer template.
- **Dark Mode Support**: Fully integrated dark mode theme.

## Requirements

Ensure your environment meets the following requirements:

- **PHP**: ^8.2
- **Composer**
- **Node.js** & **NPM**
- **Database**: SQLite (default), MySQL, or PostgreSQL

## Installation

Follow these steps to set up the project locally:

1.  **Clone the Repository**
    ```bash
    git clone https://github.com/yourusername/desa-app.git
    cd desa-app
    ```

2.  **Install PHP Dependencies**
    ```bash
    composer install
    ```

3.  **Install JavaScript Dependencies**
    ```bash
    npm install
    ```

4.  **Environment Setup**
    Copy the example environment file and configure it:
    ```bash
    cp .env.example .env
    ```

5.  **Generate Application Key**
    ```bash
    php artisan key:generate
    ```

6.  **Database Setup**
    Ensure your database is configured in `.env`. By default, it uses SQLite.
    Run migrations and seed the database with default roles and users:
    ```bash
    php artisan migrate --seed
    ```

7.  **Build Assets**
    ```bash
    npm run build
    ```

8.  **Run the Application**
    Start the local development server:
    ```bash
    php artisan serve
    ```
    The app will be available at `http://localhost:8000`.

## Usage

### Default Credentials

The application comes with pre-seeded accounts for testing:

| Role | Email | Password |
| :--- | :--- | :--- |
| **Admin** | `admin@desa.id` | `admin123` |
| **Sekretaris** | `sekretaris@desa.id` | `password123` |
| **Kepala Desa** | `kepala@desa.id` | `password123` |

> **Note**: For the **Warga** role, you can register a new account via the registration page.

## Project Structure

- `app/Models`: Eloquent models (User, SuratRequest, SuratType, etc.).
- `app/Http/Controllers`: Logic for Warga, Admin, Sekretaris, and Kepala Desa.
- `resources/views`: Blade templates using the Mazer theme.
  - `layouts`: Main layout files (`admin.blade.php`, `warga.blade.php`).
  - `warga`, `admin`, `sekretaris`, `kepala_desa`: Role-specific views.

## Contributing

Contributions are welcome! Please follow these steps:

1.  **Fork** the repository.
2.  **Clone** your fork locally.
3.  Create a new **Branch** for your feature or fix:
    ```bash
    git checkout -b feature/amazing-feature
    ```
4.  **Commit** your changes.
5.  **Push** to your branch.
6.  Open a **Pull Request**.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
