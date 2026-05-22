<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arithmetic with Array (With Functions)</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Finlandica+Headline:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
</head>

<body>
    <?php
        function calculate_operations($param1, $param2, $param3) {
            $sum        = $param1 + $param2 + $param3;
            $difference = $param1 - $param2 - $param3;
            $product    = $param1 * $param2 * $param3;
            $quotient   = ($param2 != 0 && $param3 != 0) ? ($param1 / $param2) / $param3 : 0;

            return [
                "Addition" => $sum,
                "Subtraction" => $difference,
                "Multiplication" => $product,
                "Division" => $quotient
            ];
        }

        $p1 = 23;
        $p2 = 34;
        $p3 = 35;

        $results = calculate_operations($p1, $p2, $p3);
    ?>

    <div class="directory-container">
        <h1>Arithmetic with User Defined Function</h1>

        <table class="directory-table">
            <thead>
                <tr class="table-header">
                    <th colspan="2">Parameters: <?php echo "$p1, $p2, $p3"; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($results as $operation => $value) {
                    echo "<tr>";
                    echo "<td style='text-align: left; font-weight: 600;'>" . $operation . "</td>";
                    echo "<td>" . $value . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <a href="index.php" class="button-link">Return</a>
    </div>
</body>

</html>