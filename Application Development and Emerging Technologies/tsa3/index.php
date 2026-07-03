<?php
session_start();

if (isset($_SESSION['username'])) {
    header('Location: activityA/home.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TSA3 - Landing Page</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Finlandica+Headline:wght@400;700&display=swap"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="style.css" rel="stylesheet">
</head>

<body>

    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="card main-card p-5 text-center">
            <h1 class="mb-1">Technical Summative 3</h1>
            <p class="subtitle mb-1">Web Forms and Database</p>
            <p class="mb-4">by Tristan Sam R. Carabbacan</p>

            <div class="d-grid gap-3">
                <a href="activityA/a_landingpage.php" class="btn btn-outline-dark">Activity A</a>
                <a href="activityb/b_landingpage.php" class="btn btn-outline-dark">Activity B</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>