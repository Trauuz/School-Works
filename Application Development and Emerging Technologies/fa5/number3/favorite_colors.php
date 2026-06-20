<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $_SESSION["color1"] = $_POST["color1"];
    $_SESSION["color2"] = $_POST["color2"];
    $_SESSION["color3"] = $_POST["color3"];
    $_SESSION["color4"] = $_POST["color4"];
    $_SESSION["color5"] = $_POST["color5"];

    header("Location: ./result_colors.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Session</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
</head>

<body>

    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="card main-card p-5">

            <h1 class="text-center mb-3">PHP Session</h1>

            <p class="subtitle text-center mb-4">Enter Your 5 Favorite Colors</p>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Favorite Color 1</label>
                    <input type="text" name="color1" class="form-control" placeholder="Red" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Favorite Color 2</label>
                    <input type="text" name="color2" class="form-control" placeholder="Yellow" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Favorite Color 3</label>
                    <input type="text" name="color3" class="form-control" placeholder="Orange" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Favorite Color 4</label>
                    <input type="text" name="color4" class="form-control" placeholder="Violet" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Favorite Color 5</label>
                    <input type="text" name="color5" class="form-control" placeholder="Blue" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-dark">
                        Send Colors
                    </button>
                </div>
            </form>

            <hr>

            <a href="../index.php" class="btn btn-outline-dark">Return to Home</a>
        </div>
    </div>

</body>

</html>
``