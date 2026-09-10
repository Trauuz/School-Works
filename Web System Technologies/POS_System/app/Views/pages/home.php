<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | Basic POS System</title>
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
        <h2>Welcome to the POS System</h2>
        <p>
            This is the first version of our Point-of-Sale system.
            Use the navigation menu to view customers, users, and information
            about the application.
        </p>
    </main>
</body>

</html>