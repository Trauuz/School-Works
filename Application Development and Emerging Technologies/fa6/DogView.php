<?php
include "db_connection.php";

$sql = "SELECT * FROM dogs ORDER BY id ASC";
$result = mysqli_query($connection, $sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dog Records</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Finlandica+Headline:wght@400;700&display=swap"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>

<body>
    <div class="container py-5">
        <div class="card main-card table-card p-4 p-md-5 mx-auto">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div>
                    <h1 class="mb-1">Dog Records</h1>
                    <p class="subtitle mb-0">All registered dog information.</p>
                </div>

                <div class="d-flex gap-2">
                    <a href="DogRegister.php" class="btn btn-dark">Add Dog</a>
                    <a href="index.php" class="btn btn-outline-dark">Back</a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0 dog-table">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Breed</th>
                            <th>Age</th>
                            <th>Address</th>
                            <th>Color</th>
                            <th>Height</th>
                            <th>Weight</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row["id"]; ?></td>
                            <td><?php echo htmlspecialchars($row["name"]); ?></td>
                            <td><?php echo htmlspecialchars($row["breed"]); ?></td>
                            <td><?php echo htmlspecialchars($row["age"]); ?></td>
                            <td><?php echo htmlspecialchars($row["address"]); ?></td>
                            <td><?php echo htmlspecialchars($row["color"]); ?></td>
                            <td><?php echo htmlspecialchars($row["height"]); ?></td>
                            <td><?php echo htmlspecialchars($row["weight"]); ?></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">No dog records found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>