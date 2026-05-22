<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Combination Program</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    
    <div class="container">
        <div class="conversion-card">
        <h1>Two-Digit Decimal Combinations</h1>
        
        <p>
            <?php
                for ($i = 0; $i <= 9; $i++) {
                    for ($j = 0; $j <= 9; $j++) {
                        echo sprintf("%02d", $i * 10 + $j) . ", ";
                    }
                }
            ?>
        </p>
        
        <center>
            <a href="index.php" class="button-link">Return</a>
        </center>
    </div>
    </div>
    
</body>

</html>