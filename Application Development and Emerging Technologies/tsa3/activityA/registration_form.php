<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Module</title>

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
                    <label class="form-label">First Name</label>
                    <input type="text" name="firstname" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Middle Name</label>
                    <input type="text" name="middlename" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="lastname" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
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
                    <input type="date" name="dob" class="form-control" required>
                </div>


                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Contact Number</label>
                    <input type="number" name="contact_number" class="form-control" required>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-dark">Submit</button>
                </div>

            </form>
            <hr class="my-4">

            <?php if (
                isset($_POST['password'], $_POST['c_password']) &&
                $_POST['password'] != $_POST['c_password']
            ): ?>

            <div class="alert alert-danger mb-4" role="alert">
                <strong>ERROR:</strong> Password and Confirm Password do not match.
            </div>

            <?php elseif (isset($_POST['firstname'])): ?>

            <div class="card p-4 bg-light mb-4">
                <p>
                    <strong>FULL NAME:</strong>
                    <?php
                        echo $_POST['firstname'] . " " .
                            $_POST['middlename'] . " " .
                            $_POST['lastname'];
                    ?>
                </p>
                <p>
                    <strong>USERNAME:</strong>
                    <?php echo $_POST['username']; ?>
                </p>
                <p>
                    <strong>PASSWORD:</strong>
                    <?php echo $_POST['password']; ?>
                </p>
                <p>
                    <strong>DATE OF BIRTH:</strong>
                    <?php echo $_POST['dob']; ?>
                </p>
                <p>
                    <strong>EMAIL:</strong>
                    <?php echo $_POST['email']; ?>
                </p>
                <p>
                    <strong>CONTACT NUMBER:</strong>
                    <?php echo $_POST['contact_number']; ?>
                </p>
            </div>

            <?php endif; ?>

            <a href="a_landingpage.php" class="btn btn-outline-dark">Return to Activity A Landing Page</a>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>