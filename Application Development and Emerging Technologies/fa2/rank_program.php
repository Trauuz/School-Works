<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Ranking Program</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php
        $name ="Tristan Sam R. Carabbacan";
        $picture = "profile.jpg";
        $grade = 67;
        $rank = "";

        if ($grade > 100 || $grade < 0) {
            $rank = "Invalid Grade";
        }elseif ($grade >= 93 && $grade <= 100) {
            $rank = "A+";
        } elseif ($grade >= 90 && $grade < 93) {
            $rank = "A";
        } elseif ($grade >= 87 && $grade < 90) {
            $rank = "B+";
        } elseif ($grade >= 83 && $grade < 87) {
            $rank = "B";
        } elseif ($grade >= 80 && $grade < 83) {
            $rank = "-B";
        } elseif ($grade >= 77 && $grade < 80) {
            $rank = "C+";
        } elseif ($grade >= 73 && $grade < 77) {
            $rank = "C-";
        } elseif ($grade >= 67 && $grade < 73) {
            $rank = "D+";
        } elseif ($grade >= 63 && $grade < 67) {
            $rank = "D";
        } elseif ($grade >= 60 && $grade < 63) {
            $rank = "D-";
        } else{
            $rank = "F";
        }
    ?>
    <div class="container">
        <div class="rank-card">
        <div class="name-container">
            <?php echo "Name: " . $name; ?>
        </div>
        
        <div class="picture-container">
            <img class="profile-picture" src="<?php echo $picture; ?>" alt="Profile Picture">
        </div>
        
        <div class="grade-container">
            <?php echo "Grade: " . $grade; ?>
        </div>
        
        <div class="rank-container">
            <?php echo "Rank: " . $rank; ?>
        </div>

        <div class="button-container">
            <a href="index.php" class="button-link">Return</a>
        </div>
    </div>
    </div>

</body>

</html>