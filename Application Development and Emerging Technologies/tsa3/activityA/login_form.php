<?php
session_start();

if (isset($_SESSION['username'])) {
    header('Location: home.php');
    exit;
}

function getLoginCredentials()
{
    static $static_username = 'admin';
    static $static_password = 'pass123';

    return [$static_username, $static_password];
}

$saved_username = $_COOKIE['username'] ?? '';
$saved_password = $_COOKIE['password'] ?? '';
$login_error = '';
[$static_username, $static_password] = getLoginCredentials();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (isset($_POST['remember_me'])) {
        setcookie('username', $username, time() + (86400 * 30), '/');
        setcookie('password', $password, time() + (86400 * 30), '/');

        $saved_username = $username;
        $saved_password = $password;
    } else {
        setcookie('username', '', time() - 3600, '/');
        setcookie('password', '', time() - 3600, '/');

        $saved_username = '';
        $saved_password = '';
    }

    if ($username === $static_username && $password === $static_password) {
        $_SESSION['username'] = $username;
        header('Location: home.php');
        exit;
    }

    $login_error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity A | Landing Page</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Finlandica+Headline:wght@400;700&display=swap"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="../style.css" rel="stylesheet">
</head>

<body>

    <div class="container py-5 d-flex justify-content-center">
        <div class="card main-card p-5 w-100">
            <div class="text-center mb-4">
                <h1>Login Form</h1>
            </div>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control"
                        value="<?php echo htmlspecialchars($saved_username); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control"
                        value="<?php echo htmlspecialchars($saved_password); ?>">
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="remember_me" id="remember_me" class="form-check-input"
                        <?php echo ($saved_username !== '' || $saved_password !== '') ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="remember_me">Remember me</label>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-dark">Login</button>
                </div>

                <hr class="my-4">

                <div class="card p-4 bg-light mb-4">
                    <p>
                        <strong>Default User:</strong>
                        <?php echo htmlspecialchars($static_username); ?>
                    </p>
                    <p>
                        <strong>Default Password:</strong>
                        <?php echo htmlspecialchars($static_password); ?>
                    </p>
                </div>

                <?php if ($login_error != ''): ?>

                <div class="alert alert-danger mb-4" role="alert">
                    <strong>ERROR:</strong> <?php echo htmlspecialchars($login_error); ?>
                </div>

                <?php endif; ?>

                <a href="a_landingpage.php" class="btn btn-outline-dark w-100 text-center">Return to Activity A Landing
                    Page</a>
            </form>
        </div>

    </div>

</body>

</html>