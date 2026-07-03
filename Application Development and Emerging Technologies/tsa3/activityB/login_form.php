<?php
session_start();

if (isset($_SESSION['activityB_username'])) {
    header('Location: home.php');
    exit;
}

$saved_username = $_COOKIE['username'] ?? '';
$saved_password = $_COOKIE['password'] ?? '';
$login_error = '';

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

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $connection = new mysqli('localhost', 'root', '');
        $connection->set_charset('utf8mb4');
        $connection->query('CREATE DATABASE IF NOT EXISTS tsa3');
        $connection->select_db('tsa3');
        $connection->query(
            'CREATE TABLE IF NOT EXISTS registrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                firstname VARCHAR(100) NOT NULL,
                middlename VARCHAR(100) NOT NULL,
                lastname VARCHAR(100) NOT NULL,
                username VARCHAR(100) NOT NULL,
                password VARCHAR(255) NOT NULL,
                dob DATE NOT NULL,
                email VARCHAR(150) NOT NULL,
                contact_number VARCHAR(30) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )'
        );
        $statement = $connection->prepare(
            'SELECT firstname, middlename, lastname, username
            FROM registrations
            WHERE username = ? AND password = ?
            LIMIT 1'
        );
        $statement->bind_param('ss', $username, $password);
        $statement->execute();
        $result = $statement->get_result();

        if ($user = $result->fetch_assoc()) {
            $_SESSION['activityB_username'] = $user['username'];
            $_SESSION['activityB_fullname'] = trim(
                $user['firstname'] . ' ' . $user['middlename'] . ' ' . $user['lastname']
            );

            header('Location: home.php');
            exit;
        }

        $login_error = 'Invalid username or password.';
    } catch (mysqli_sql_exception $exception) {
        $login_error = 'Database error: ' . $exception->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity B | Login Module</title>

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

                <?php if ($login_error != ''): ?>

                <div class="alert alert-danger mb-4" role="alert">
                    <strong>ERROR:</strong> <?php echo htmlspecialchars($login_error); ?>
                </div>

                <?php endif; ?>

                <a href="b_landingpage.php" class="btn btn-outline-dark w-100 text-center">Return to Activity B Landing
                    Page</a>
            </form>
        </div>

    </div>

</body>

</html>
