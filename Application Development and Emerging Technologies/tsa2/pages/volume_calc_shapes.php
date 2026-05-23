<?php require("../component/header.php"); ?>

<?php
    function cubeVolume($side) {
        return $side ** 3;
    }

    function rectangularPrismVolume($length, $width, $height) {
        return $length * $width * $height;
    }

    function cylinderVolume($radius, $height) {
        return pi() * ($radius ** 2) * $height;
    }

    function coneVolume($radius, $height) {
        return (1 / 3) * pi() * ($radius ** 2) * $height;
    }

    function sphereVolume($radius) {
        return (4 / 3) * pi() * ($radius ** 3);
    }

    $cubeSide = 5;
    $prismLength = 8;
    $prismWidth = 4;
    $prismHeight = 3;
    $cylinderRadius = 3;
    $cylinderHeight = 7;
    $coneRadius = 2.5;
    $coneHeight = 6;
    $sphereRadius = 4;

    $volumes = [
        [
            'shape' => 'Cube',
            'value' => "s = $cubeSide",
            'formula' => 'V = s<sup>3</sup>',
            'answer' => cubeVolume($cubeSide)
        ],
        [
            'shape' => 'Right Rectangular Prism',
            'value' => "l = $prismLength, w = $prismWidth, h = $prismHeight",
            'formula' => 'V = l × w × h',
            'answer' => rectangularPrismVolume($prismLength, $prismWidth, $prismHeight)
        ],
        [
            'shape' => 'Cylinder',
            'value' => "r = $cylinderRadius, h = $cylinderHeight",
            'formula' => 'V = π r<sup>2</sup> h',
            'answer' => round(cylinderVolume($cylinderRadius, $cylinderHeight), 2)
        ],
        [
            'shape' => 'Cone',
            'value' => "r = $coneRadius, h = $coneHeight",
            'formula' => 'V = (1/3) π r<sup>2</sup> h',
            'answer' => round(coneVolume($coneRadius, $coneHeight), 2)
        ],
        [
            'shape' => 'Sphere',
            'value' => "r = $sphereRadius",
            'formula' => 'V = (4/3) π r<sup>3</sup>',
            'answer' => round(sphereVolume($sphereRadius), 2)
        ]
    ];
?>

<div class="directory-container">
    <h1>Volume of Shapes</h1>

    <table class="directory-table">
        <thead>
            <tr class="table-header">
                <th colspan="4">Volume of Shapes</th>
            </tr>
            <tr>
                <th>Shape</th>
                <th>Values</th>
                <th>Formula</th>
                <th>Answer</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($volumes as $item): ?>
            <tr>
                <td style="text-align: left; font-weight: 600;"><?php echo $item['shape']; ?></td>
                <td style="text-align: left;"><?php echo $item['value']; ?></td>
                <td><?php echo $item['formula']; ?></td>
                <td><?php echo $item['answer']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="button-card">
        <a href="../index.php" class="button-link">Back to home</a>
    </div>
</div>

<?php require("../component/footer.php"); ?>