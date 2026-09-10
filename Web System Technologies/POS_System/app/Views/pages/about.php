<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About | Basic POS System</title>
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
</head>

<body>
    <header>
        <h1>Basic POS System</h1>
        <nav>
            <a href="<?= site_url('/') ?>">Home</a>
            <a href="<?= site_url('customers') ?>">Customer Accounts</a>
            <a href="<?= site_url('users') ?>">User Accounts</a>
            <a href="<?= site_url('about') ?>">About</a>
        </nav>
    </header>
    <main>
        <h2>About</h2>
        <p>
            This basic POS website was created with CodeIgniter 4. It demonstrates
            routes, controllers, views, static PHP arrays, and page navigation.
        </p>
        <p>
            Customer and user records currently use temporary arrays. A database
            can be added in a future version.
        </p>
    </main>
</body>

</html>