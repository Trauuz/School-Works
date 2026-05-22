<?php 
    $names = array(
    "harry potter",
    "hermione granger",
    "ron weasley",
    "albus dumbledore",
    "severus snape",
    "rubeus hagrid",
    "draco malfoy",
    "ginny weasley",
    "neville longbottom",
    "luna lovegood",
    "sirius black",
    "remus lupin",
    "minerva mcgonagall",
    "lord voldemort",
    "bellatrix lestrange",
    "dobby",
    "cedric diggory",
    "cho chang",
    "fleur delacour",
    "james potter"
);

    function processNames($names) {

        $length = strlen($names);
        
        $uppercaseFirst = ucfirst($names);
        
        $vowels = array('a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U');
        $vowels_replaced = str_replace($vowels, '@', $names);

        $aPos = strpos($names, 'a');
        if ($aPos !== false) {
            $position_of_a = $aPos + 1;
        } else {
            $pos_upper = strpos($names, 'A');
            $position_of_a = ($pos_upper !== false) ? ($pos_upper + 1) : "Not found";
        }

        $reversed_name = strrev($names);

        return array(
            "original" => $names,
            "length" => $length,
            "uppercaseFirst" => $uppercaseFirst,
            "vowels_replaced" => $vowels_replaced,
            "position_of_a" => $position_of_a,
            "reversed_name" => $reversed_name
        );
    }
?>

<?php require('../component/header.php'); ?>

<div class="container">
    <div class="string-container">
        <table class="string-table">
            <thead>
                <tr>
                    <th colspan="6" class="main-header">List of names</th>
                </tr>
                <tr>
                    <th>Name</th>
                    <th>Number of characters</th>
                    <th>Uppercase first character</th>
                    <th>Replace vowels with @</th>
                    <th>Check position of character "a"</th>
                    <th>Reverse name</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    foreach ($names as $name) {
                        $data = processNames($name);
                        echo "<tr>";
                        echo "<td>" . $data['original'] . "</td>";
                        echo "<td>" . $data['length'] . "</td>";
                        echo "<td>" . $data['uppercaseFirst'] . "</td>";
                        echo "<td>" . $data['vowels_replaced'] . "</td>";
                        echo "<td>" . $data['position_of_a'] . "</td>";
                        echo "<td>" . $data['reversed_name'] . "</td>";
                        echo "</tr>";
                    }
                    ?>
            </tbody>
        </table>

        <div class="button-footer">
            <a href="../index.php" class="button-link">
                Return to Home
            </a>
        </div>
    </div>
</div>


<?php require('../component/footer.php'); ?>