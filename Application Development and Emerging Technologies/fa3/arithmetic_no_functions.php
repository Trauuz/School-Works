<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arithmetic No Functions</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Finlandica+Headline:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
</head>

<body>
    <?php
        $arr = [23, 34, 35, 30, 25, 29, 28, 31, 31, 56];

        $sum        = $arr[0];
        $difference = $arr[0];
        $product    = $arr[0];
        $quotient   = $arr[0];

    for ($i = 1; $i < 10; $i++) {
        $sum        += $arr[$i];
        $difference -= $arr[$i];
        $product    *= $arr[$i];
        $quotient   /= $arr[$i];
    }

    $array_list_string = "";
    for ($i = 0; $i < 10; $i++) {
        $array_list_string .= $arr[$i];
        if ($i < 9) {
            $array_list_string .= ", ";
        }
    }
    ?>
    <div class="directory-container">
        <h1>Arithmetic with Array</h1>

        <table class="directory-table">
            <thead>
                <tr class="table-header">
                    <th colspan="2">Array list: <?php echo $array_list_string; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: left; font-weight: 600;">Addition</td>
                    <td><?php echo $sum; ?></td>
                </tr>
                <tr>
                    <td style="text-align: left; font-weight: 600;">Subtraction</td>
                    <td><?php echo $difference; ?></td>
                </tr>
                <tr>
                    <td style="text-align: left; font-weight: 600;">Multiplication</td>
                    <td><?php echo $product; ?></td>
                </tr>
                <tr>
                    <td style="text-align: left; font-weight: 600;">Division</td>
                    <td><?php echo $quotient; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <center>
        <a href="index.php" class="button-link">Return</a>
    </center>
</body>

</html>