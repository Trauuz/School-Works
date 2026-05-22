<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Person Directory</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Finlandica+Headline:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
</head>

<body>
    <?php
         $people = [ 
            ["name" => "Togi", "images" => "https://yt3.googleusercontent.com/aGAuhlVoHXZjEESd0NmSYXuwX-P1G9L5xTeJpY-j_swVTf8hOQlDTExh0hOr7LR8cCkBRvoO8SM=s160-c-k-c0x00ffffff-no-rj", "age" => 23, "birthday" => "2002-07-20", "contact" => "@togitogi"],
            ["name" => "Trainwreckstv", "images" => "https://staticg.sportskeeda.com/editor/2022/04/c18e4-16510496915192-1920.jpg", "age" => 34, "birthday" => "1990-12-20", "contact" => "@trainwreckstv"],
            ["name" => "Roshtein", "images" => "https://pbs.twimg.com/profile_images/1917924822497067008/vLtbOtZ9_400x400.jpg", "age" => 35, "birthday" => "1990-01-01", "contact" => "@roshtein"],
            ["name" => "xQc", "images" => "https://www.dexerto.com/cdn-image/wp-content/uploads/2024/03/21/xQc-streamer.jpg?width=768&quality=60&format=auto", "age" => 30, "birthday" => "1995-11-12", "contact" => "@xqc"],
            ["name" => "Adin Ross", "images" => "https://ygo-assets-entities-emea.yougov.net/5fa239f4-c165-11f0-a525-47ffc67233ff.jpg?zcw=710&zch=710&zct=47&zcl=374&ph=264", "age" => 25, "birthday" => "2000-10-11", "contact" => "@adinross"],
            ["name" => "ClassyBeef", "images" => "https://www.gambleboost.com/wp-content/uploads/2022/06/ClassyBeef-2.jpg", "age" => 29, "birthday" => "1996-01-01", "contact" => "@classybeef"],
            ["name" => "DeuceAce", "images" => "https://win.gg/wp-content/uploads/2025/11/DeuceAce_net_worth.jpg.webp", "age" => 28, "birthday" => "1997-01-01", "contact" => "@deuceacetv"],
            ["name" => "Sliker", "images" => "https://static.wikia.nocookie.net/youtube/images/2/20/Sliker2.png/revision/latest?cb=20221113101005", "age" => 31, "birthday" => "1994-04-03", "contact" => "@itsliker"],
            ["name" => "The Felt Life", "images" => "https://yt3.googleusercontent.com/O25kQh_eoEEOQJFRWLohg69M7t75Lo8PfBVldxmSbvNkdDJ2Rq3UpRCR_rrIc6fXBWK0qM2G=s160-c-k-c0x00ffffff-no-rj", "age" => 31, "birthday" => "1994-04-03", "contact" => "@thefeltlife"],
            ["name" => "Vegas Matt", "images" => "https://d3k81ch9hvuctc.cloudfront.net/company/WFHDhL/images/744593a9-7bd5-4128-b822-f89be492fca1.jpeg", "age" => 56, "birthday" => "1969-10-04", "contact" => "@vegasmatt"]
         ];

        usort($people, function($a, $b) {
             return strcmp($a['name'], $b['name']);
         });
    ?>

    <div class="directory-container">
        <h1>Person Directory</h1>

        <table class="directory-table">
            <thead>
                <tr class="table-header">
                    <th>No.</th>
                    <th>Name</th>
                    <th>Image</th>
                    <th>Age</th>
                    <th>Birthday</th>
                    <th>Social Media</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                foreach ($people as $person) {
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . $person['name'] . "</td>";
                    echo "<td><img src='" . $person['images'] . "' alt='" . $person['name'] . "' class='table-img'></td>";
                    echo "<td>" . $person['age'] . "</td>";
                    echo "<td>" . $person['birthday'] . "</td>";
                    echo "<td><code>" . $person['contact'] . "</code></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

    </div>
    <center>
        <a href="index.php" class="button-link">Return</a>
    </center>
</body>

</html>