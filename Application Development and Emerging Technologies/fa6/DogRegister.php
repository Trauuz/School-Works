<?php
include "db_connection.php";

$message = "";
$messageType = "";

$name = "";
$breed = "";
$age = "";
$address = "";
$color = "";
$height = "";
$weight = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($connection, $_POST["name"]);
    $breed = mysqli_real_escape_string($connection, $_POST["breed"]);
    $age = $_POST["age"];
    $address = mysqli_real_escape_string($connection, $_POST["address"]);
    $color = mysqli_real_escape_string($connection, $_POST["color"]);
    $height = $_POST["height"];
    $weight = $_POST["weight"];

    if ($name == "" || $breed == "" || $age == "" || $address == "" || $color == "" || $height == "" || $weight == "") {
        $message = "Please complete all fields.";
        $messageType = "danger";
    } else {
        $sql = "INSERT INTO dogs (name, breed, age, address, color, height, weight)
                VALUES ('$name', '$breed', '$age', '$address', '$color', '$height', '$weight')";

        if (mysqli_query($connection, $sql)) {
            $message = "Dog information saved successfully.";
            $messageType = "success";

            $name = "";
            $breed = "";
            $age = "";
            $address = "";
            $color = "";
            $height = "";
            $weight = "";
        } else {
            $message = "Error: " . mysqli_error($connection);
            $messageType = "danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dog Registration</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Finlandica+Headline:wght@400;700&display=swap"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>

<body>
    <div class="container py-5">
        <div class="card main-card p-4 p-md-5 mx-auto">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="mb-1">Dog Registration</h1>
                    <p class="subtitle mb-0">Enter dog information below.</p>
                </div>
                <a href="index.php" class="btn btn-outline-dark">Back</a>
            </div>

            <?php if ($message !== ""): ?>
            <div class="alert alert-<?php echo $messageType; ?>" role="alert">
                <?php echo htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="DogRegister.php" class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name"
                        value="<?php echo htmlspecialchars($name); ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="breed" class="form-label">Breed</label>
                    <input type="text" class="form-control" id="breed" name="breed"
                        value="<?php echo htmlspecialchars($breed); ?>" required>
                </div>

                <div class="col-md-4">
                    <label for="age" class="form-label">Age</label>
                    <input type="number" class="form-control" id="age" name="age" min="0"
                        value="<?php echo htmlspecialchars($age); ?>" required>
                </div>

                <div class="col-md-4">
                    <label for="height" class="form-label">Height</label>
                    <input type="number" class="form-control" id="height" name="height" min="0.01" step="0.01"
                        value="<?php echo htmlspecialchars($height); ?>" required>
                </div>

                <div class="col-md-4">
                    <label for="weight" class="form-label">Weight</label>
                    <input type="number" class="form-control" id="weight" name="weight" min="0.01" step="0.01"
                        value="<?php echo htmlspecialchars($weight); ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="color" class="form-label">Color</label>
                    <input type="text" class="form-control" id="color" name="color"
                        value="<?php echo htmlspecialchars($color); ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="address" name="address"
                        value="<?php echo htmlspecialchars($address); ?>" required>
                </div>

                <div class="col-12 d-flex gap-2 justify-content-end mt-4">
                    <a href="DogView.php" class="btn btn-outline-dark">Dog List</a>
                    <button type="submit" class="btn btn-dark">Save Dog</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>