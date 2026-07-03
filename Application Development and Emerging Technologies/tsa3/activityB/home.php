<?php
session_start();

if (!isset($_SESSION['activityB_username'])) {
    header('Location: login_form.php');
    exit;
}

$user = null;
$error_message = '';
$success_message = '';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $connection = new mysqli('localhost', 'root', '', 'tsa3');
    $connection->set_charset('utf8mb4');

    $statement = $connection->prepare(
        'SELECT id, firstname, middlename, lastname, username, password, dob, email, contact_number
        FROM registrations
        WHERE username = ?
        LIMIT 1'
    );
    $statement->bind_param('s', $_SESSION['activityB_username']);
    $statement->execute();
    $result = $statement->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        session_destroy();
        header('Location: login_form.php');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $reenter_password = $_POST['reenter_password'] ?? '';

        if ($current_password !== $user['password']) {
            $error_message = 'Current password is not the same with the old password';
        } elseif ($new_password !== $reenter_password) {
            $error_message = 'New password and Re-Enter new password should be the same.';
        } else {
            $update_statement = $connection->prepare(
                'UPDATE registrations SET password = ? WHERE id = ?'
            );
            $update_statement->bind_param('si', $new_password, $user['id']);
            $update_statement->execute();

            $user['password'] = $new_password;
            $success_message = 'Password reset successfully.';
        }
    }
} catch (mysqli_sql_exception $exception) {
    $error_message = 'Database error: ' . $exception->getMessage();
}

$full_name = '';
$birthday = '';

if ($user) {
    $full_name = trim($user['firstname'] . ' ' . $user['middlename'] . ' ' . $user['lastname']);
    $birthday = date('F j Y', strtotime($user['dob']));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity B | Home</title>

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
            <div class="d-flex justify-content-between align-items-start mb-4">
                <h1>User Information Form</h1>
                <a href="logout.php">Log-out</a>
            </div>

            <?php if ($user): ?>
            <div class="mb-4">
                <p class="mb-1">
                    <strong>Welcome</strong> <?php echo htmlspecialchars($full_name); ?>
                </p>
                <p class="mb-1">
                    <strong>Birthday:</strong> <?php echo htmlspecialchars($birthday); ?>
                </p>
                <p class="mb-1"><strong>Contact Details</strong></p>
                <p class="mb-1 ms-4">
                    <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?>
                </p>
                <p class="mb-0 ms-4">
                    <strong>Contact:</strong> <?php echo htmlspecialchars($user['contact_number']); ?>
                </p>
            </div>
            <?php endif; ?>

            <hr class="my-4">

            <h5 class="mb-3">RESET PASSWORD</h5>

            <form method="POST">
                <div class="row align-items-center mb-2">
                    <label class="col-sm-5 col-form-label">Enter Current Password:</label>
                    <div class="col-sm-7">
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                </div>

                <div class="row align-items-center mb-2">
                    <label class="col-sm-5 col-form-label">Enter New Password:</label>
                    <div class="col-sm-7">
                        <input type="password" name="new_password" class="form-control" required>
                    </div>
                </div>

                <div class="row align-items-center mb-4">
                    <label class="col-sm-5 col-form-label">Re-Enter New Password:</label>
                    <div class="col-sm-7">
                        <input type="password" name="reenter_password" class="form-control" required>
                    </div>
                </div>

                <div class="d-grid col-sm-9 mx-auto">
                    <button type="submit" class="btn btn-outline-dark">Reset Password</button>
                </div>
            </form>

            <?php if ($error_message != ''): ?>
            <div class="alert alert-danger mt-4 mb-0" role="alert">
                <strong>ERROR:</strong> <?php echo htmlspecialchars($error_message); ?>
            </div>
            <?php elseif ($success_message != ''): ?>
            <div class="alert alert-success mt-4 mb-0" role="alert">
                <strong>SUCCESS:</strong> <?php echo htmlspecialchars($success_message); ?>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>