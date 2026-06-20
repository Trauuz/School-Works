<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Cookie</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Finlandica+Headline:wght@400;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
</head>

<body>
    <?php
    if (!isset($_COOKIE['firstname'])) {
        setcookie("firstname", "Tristan", time() + 10);
    }

    if (!isset($_COOKIE['middlename'])) {
        setcookie("middlename", "Sam", time() + 20);
    }

    if (!isset($_COOKIE['lastname'])) {
        setcookie("lastname", "Carabbacan", time() + 30);
    }
?>
    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="card main-card p-5">

            <h1 class="text-center mb-3">PHP Cookie</h1>
            <p class="subtitle text-center mb-4">
                Expiring Cookie Values
            </p>

            <div class="card p-3 bg-light">

                <p>
                    <strong>First Name (10 sec):</strong>
                    <?php echo $_COOKIE['firstname'] ?? "Expired"; ?>
                </p>

                <p>
                    <strong>Middle Name (20 sec):</strong>
                    <?php echo $_COOKIE['middlename'] ?? "Expired"; ?>
                </p>

                <p>
                    <strong>Last Name (30 sec):</strong>
                    <?php echo $_COOKIE['lastname'] ?? "Expired"; ?>
                </p>

            </div>

            <hr>

            <a href="../index.php" class="btn btn-outline-dark">
                Return to Home
            </a>

        </div>
    </div>

</body>

</html>