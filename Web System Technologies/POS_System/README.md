# Basic Point-of-Sale System

This project is the first version of a basic Point-of-Sale (POS) website created with CodeIgniter 4. It demonstrates routing, controllers, views, navigation, and passing static PHP array data from controllers to views.

The project contains four pages:

- A landing page
- An About page
- A Customer Accounts page
- A User Accounts page

Customer and user records are stored in static PHP arrays. This version does not use a database.

## Features

- Four working CodeIgniter routes
- Separate controllers for pages, customers, and users
- At least five sample customer records
- At least five sample user or staff records
- PHP `foreach` loops for displaying account records
- Navigation links between all four pages
- Simple responsive styling
- No database dependency

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

## How the Project Works

Routes in `app/Config/Routes.php` connect each URL to a controller method. The controllers prepare the page data and send it to the appropriate view. The customer and user views use PHP `foreach` loops to display each record as a table row.

Because the records are static arrays, changes made through the browser are not stored permanently. Database support is outside the scope of this version.