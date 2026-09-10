## Features
- Four working CodeIgniter routes
- Separate controllers for pages, customers, and users
- At least five sample customer records
- At least five sample user or staff records
- PHP `foreach` loops for displaying account records
- Navigation links between all four pages
- Simple responsive styling
- No database dependency

## How the Project Works
Routes in `app/Config/Routes.php` connect each URL to a controller method. The controllers prepare the page data and send it to the appropriate view. The customer and user views use PHP `foreach` loops to display each record as a table row.

Because the records are static arrays, changes made through the browser are not stored permanently. Database support is outside the scope of this version.

## Requirements
Install the following before setting up the project:

- PHP 8.1 or later
- Composer
- PHP `intl` extension
- PHP `mbstring` extension
- XAMPP with Apache, or another compatible local web server

## Installation
### 1. Clone the repository

```bash
git clone YOUR_GITHUB_REPOSITORY_URL
cd POS_System
```

Replace `YOUR_GITHUB_REPOSITORY_URL` with the repository link shown in the Project Links section.

Alternatively, download the repository as a ZIP file and extract it into:

```text
C:\xampp\htdocs\POS_System
```

### 2. Install the PHP dependencies

Run this command from the project directory:

```bash
composer install
```

Composer installs the PHP packages required by the project.

### 3. Configure the environment

Create a file named `.env` in the project root and add:

```ini
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost/POS_System/public/'
```

The development environment displays useful error information while working locally. The base URL tells CodeIgniter where the application is located.

If you use CodeIgniter's development server instead of Apache, change the base URL to:

```ini
app.baseURL = 'http://localhost:8080/'
```

## Running with XAMPP

1. Place the project in `C:\xampp\htdocs\POS_System`.
2. Open the XAMPP Control Panel.
3. Start Apache.
4. Open the following address in a browser:

```text
http://localhost/POS_System/public/
```

## Running with CodeIgniter's Development Server

From the project directory, run:

```bash
php spark serve
```

Then open:

```text
http://localhost:8080/
```

Keep the terminal open while using the application. Press `Ctrl+C` to stop the server.
