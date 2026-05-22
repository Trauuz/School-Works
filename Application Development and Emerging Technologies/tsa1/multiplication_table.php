<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TSA1 Landing Page</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Finlandica+Headline:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="multiplication-table">
            <center><h1>Multiplication Table</h1></center>
            <table>
            <?php
                for ($row = 0; $row <= 10; $row++) {
                    echo "<tr>";

                    for ($col = 0; $col <= 10; $col++) {
                        $value = $row * $col;

                        if (($row + $col) % 2 == 0) {
                            $color = "even";
                        } else {
                            $color = "odd";
                        }

                        echo "<td class=' $color'>" . $value . "</td>";
                    }
                    echo "</tr>";
                }
            ?>
            </table>
            <center><a href="index.php" class="button-link">Return</a></center>
        </div>

    </div>
</body>
</html>