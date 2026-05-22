<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TSA1 Landing Page</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Finlandica+Headline:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <link href="style.css" rel="stylesheet">
</head>

<body>

    <?php

    $f_name = "Tristan Sam";
    $m_name = "Roxas";
    $l_name = "Carabbacan";

    $full_name = "$f_name $m_name $l_name";

    $gender = "Male";
    $birthday = "2006-07-06";
    $current_school = "Far Eastern University - Institute of Technology";
    $program = "Bachelor of Science in Information Technology";
    $expected_graduation = "June 2028";
    $previous_school = "Sample Senior High School";

?>

    <form action="" method="post" onsubmit="return submitForm()">

        <div class="section-title">
            Student Information
        </div>

        <div class="row">
            <div class="form-group">
                <label>Full Legal Name</label>
                <input type="text" name="full_name" value="<?= $full_name; ?>">
            </div>

        </div>

        <div class="row">
            <div class="form-group">
                <label>Birthday</label>
                <input type="date" name="birthday" value="<?= $birthday; ?>">

            </div>

            <div class="form-group">
                <label>Gender</label>
                <select name="gender">
                    <option value="Male" <?= ($gender == "Male") ? "selected" : ""; ?>>
                        Male
                    </option>
                    <option value="Female" <?= ($gender == "Female") ? "selected" : ""; ?>>
                        Female
                    </option>
                </select>
            </div>
        </div>

        <div class="section-title">
            Educational Attainment
        </div>
        <div class="row">
            <div class="form-group">
                <label>Current School</label>
                <input type="text" name="current_school" value="<?= $current_school; ?>">
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <label>Program / Course</label>
                <input type="text" name="program" value="<?= $program; ?>">
            </div>

            <div class="form-group">
                <label>Expected Graduation Year</label>
                <input type="text" name="expected_graduation" value="<?= $expected_graduation; ?>">
            </div>
        </div>

        <button type="submit" class="btn">
            Register Student
        </button>

    </form>

    <script>
    function submitForm() {

        alert("<?= $full_name ?> submitted form successfully!");

        window.location.href = "index.php";

        return false;
    }
    </script>

</body>

</html>