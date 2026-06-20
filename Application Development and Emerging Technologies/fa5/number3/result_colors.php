<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Result Colors</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
</head>

<body>

    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="card main-card p-5">

            <h1 class="text-center mb-4">My Favorite Colors</h1>

            <div class="card p-4 bg-light">
                <h4 style="color: <?php echo $_SESSION['color1']; ?>">
                    My Favorite Color 1:
                    <?php echo $_SESSION['color1']; ?>
                </h4>

                <h4 style="color: <?php echo $_SESSION['color2']; ?>">
                    My Favorite Color 2:
                    <?php echo $_SESSION['color2']; ?>
                </h4>

                <h4 style="color: <?php echo $_SESSION['color3']; ?>">
                    My Favorite Color 3:
                    <?php echo $_SESSION['color3']; ?>
                </h4>

                <h4 style="color: <?php echo $_SESSION['color4']; ?>">
                    My Favorite Color 4:
                    <?php echo $_SESSION['color4']; ?>
                </h4>

                <h4 style="color: <?php echo $_SESSION['color5']; ?>">
                    My Favorite Color 5:
                    <?php echo $_SESSION['color5']; ?>
                </h4>

            </div>

            <hr>

            <a href="favorite_colors.php" class="btn btn-outline-dark">Back</a>

        </div>
    </div>

</body>

</html>