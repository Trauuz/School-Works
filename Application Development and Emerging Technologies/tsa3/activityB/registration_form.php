<?php
$form_data = [
    'firstname' => '',
    'middlename' => '',
    'lastname' => '',
    'username' => '',
    'dob' => '',
    'email' => '',
    'contact_number' => ''
];
$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($form_data as $field => $value) {
        $form_data[$field] = trim($_POST[$field] ?? '');
    }

    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['c_password'] ?? '';

    if ($password !== $confirm_password) {
        $error_message = 'Password and Confirm Password do not match.';
    } else {
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
                'INSERT INTO registrations
                    (firstname, middlename, lastname, username, password, dob, email, contact_number)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $statement->bind_param(
                'ssssssss',
                $form_data['firstname'],
                $form_data['middlename'],
                $form_data['lastname'],
                $form_data['username'],
                $password,
                $form_data['dob'],
                $form_data['email'],
                $form_data['contact_number']
            );
            $statement->execute();

            $success_message = 'Registration saved to the database successfully.';
            foreach ($form_data as $field => $value) {
                $form_data[$field] = '';
            }
        } catch (mysqli_sql_exception $exception) {
            $error_message = 'Database error: ' . $exception->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity B | Registration Module</title>

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
                <h1>Registration Form</h1>
            </div>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" name="firstname" class="form-control"
                        value="<?php echo htmlspecialchars($form_data['firstname']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Middle Name</label>
                    <input type="text" name="middlename" class="form-control"
                        value="<?php echo htmlspecialchars($form_data['middlename']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="lastname" class="form-control"
                        value="<?php echo htmlspecialchars($form_data['lastname']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control"
                        value="<?php echo htmlspecialchars($form_data['username']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="c_password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Birthday</label>
                    <input type="date" name="dob" class="form-control"
                        value="<?php echo htmlspecialchars($form_data['dob']); ?>" required>
                </div>


                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                        value="<?php echo htmlspecialchars($form_data['email']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Contact Number</label>
                    <input type="text" name="contact_number" class="form-control"
                        value="<?php echo htmlspecialchars($form_data['contact_number']); ?>" required>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-dark">Submit</button>
                </div>

            </form>
            <hr class="my-4">

            <?php if ($error_message != ''): ?>

            <div class="alert alert-danger mb-4" role="alert">
                <strong>ERROR:</strong> <?php echo htmlspecialchars($error_message); ?>
            </div>

            <?php elseif ($success_message != ''): ?>

            <div class="alert alert-success mb-4" role="alert">
                <strong>SUCCESS:</strong> <?php echo htmlspecialchars($success_message); ?>
            </div>

            <?php endif; ?>

            <a href="b_landingpage.php" class="btn btn-outline-dark">Return to Activity B Landing Page</a>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
