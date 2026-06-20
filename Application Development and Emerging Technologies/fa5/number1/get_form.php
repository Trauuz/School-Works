<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POST Global Variable</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Finlandica+Headline:wght@400;700&display=swap"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
</head>

<body>

    <div class="container py-5">
        <div class="row justify-content-center">

            <div class="col-lg-8">
                <div class="card main-card p-5">

                    <div class="text-center mb-4">
                        <h1>$_GET</h1>
                        <p class="subtitle">Personal Information Form</p>
                    </div>

                    <form method="GET" action="">

                        <div class="mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" name="firstname" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Middle Name</label>
                            <input type="text" name="middlename" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="lastname" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="dob" class="form-control" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Address</label>
                            <textarea name="address" rows="3" class="form-control" required></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-dark">
                                Submit
                            </button>
                        </div>

                    </form>

                    <?php if(isset($_POST['firstname'])): ?>
                    <hr class="my-4">

                    <h3 class="mb-3 text-center">Data From $_POST</h3>

                    <div class="card p-4 bg-light">
                        <p>
                            <strong>FULL NAME:</strong>
                            <?php
                                echo $_POST['firstname'] . " " .
                                    $_POST['middlename'] . " " .
                                    $_POST['lastname'];
                                ?>
                        </p>

                        <p>
                            <strong>DATE OF BIRTH:</strong>
                            <?php echo $_POST['dob']; ?>
                        </p>

                        <p class="mb-0">
                            <strong>ADDRESS:</strong>
                            <?php echo $_POST['address']; ?>
                        </p>
                    </div>
                    <?php endif; ?>

                    <hr class="my-4">

                    <a href="index.php" class="btn btn-outline-dark">
                        Return to Super Global Variable
                    </a>

                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>