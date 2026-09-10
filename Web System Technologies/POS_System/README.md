# Basic Point-of-Sale System

This project is the first version of a basic Point-of-Sale (POS) website created with CodeIgniter 4. It demonstrates routing, controllers, views, navigation, and passing static PHP array data from controllers to views.

The project contains four pages:

- A landing page
- An About page
- A Customer Accounts page
- A User Accounts page

Customer and user records are stored in static PHP arrays. This version does not use a database.

## Project Links

- GitHub repository: **[Add your GitHub repository URL here]**
- Hosted application: **[Add your live application URL here]**

Before submitting, replace both placeholders with working links. Open the hosted link and confirm that its pages and data match the code in this repository.

## Features

- Four working CodeIgniter routes
- Separate controllers for pages, customers, and users
- At least five sample customer records
- At least five sample user or staff records
- PHP `foreach` loops for displaying account records
- Navigation links between all four pages
- Simple responsive styling
- No database dependency

## Application Routes

| Page | Route | Description |
|---|---|---|
| Home | `/` | Displays the POS landing page |
| About | `/about` | Describes the purpose of the application |
| Customer Accounts | `/customers` | Lists customer names, email addresses, and phone numbers |
| User Accounts | `/users` | Lists usernames, full names, and roles |

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

## Verifying the Application

After starting the application, check each page:

```text
http://localhost/POS_System/public/
http://localhost/POS_System/public/about
http://localhost/POS_System/public/customers
http://localhost/POS_System/public/users
```

Confirm that:

- Every route opens without an error.
- The navigation links work on every page.
- The Customer Accounts page displays at least five records.
- The User Accounts page displays at least five records.
- The hosted version displays the same pages and records as this repository.

If you are using `php spark serve`, use `http://localhost:8080` instead of `http://localhost/POS_System/public` in the addresses above.

## Project Structure

```text
POS_System/
├── app/
│   ├── Config/
│   │   └── Routes.php
│   ├── Controllers/
│   │   ├── Customers.php
│   │   ├── Pages.php
│   │   └── Users.php
│   └── Views/
│       ├── customers/
│       │   └── index.php
│       ├── pages/
│       │   ├── about.php
│       │   └── home.php
│       └── users/
│           └── index.php
├── public/
│   └── css/
│       └── style.css
├── writable/
├── .env
├── composer.json
├── README.md
└── spark
```

## How the Project Works

Routes in `app/Config/Routes.php` connect each URL to a controller method. The controllers prepare the page data and send it to the appropriate view. The customer and user views use PHP `foreach` loops to display each record as a table row.

Because the records are static arrays, changes made through the browser are not stored permanently. Database support is outside the scope of this version.

## Database

No database or database export is included because this activity specifically uses static PHP arrays as temporary data sources.

## Author

- Name: **[Add your name here]**
- Section: **[Add your section here]**
