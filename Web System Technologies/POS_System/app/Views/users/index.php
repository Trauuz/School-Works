<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Accounts | Basic POS System</title>
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
        <h2>User Accounts</h2>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= esc($user['username']) ?></td>
                    <td><?= esc($user['full_name']) ?></td>
                    <td><?= esc($user['role']) ?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </main>
</body>

</html>