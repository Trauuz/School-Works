<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Length Conversion</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php
        $input_value = 67; 

        $millimetres = $input_value * 10;
        $decimetre = $input_value * 10;
        $metre = $input_value * 100;
        $kilometre = $input_value * 1000;

        $inches = $input_value * 12;
        $feet = $input_value * 3;
        $yards = $input_value * 22;
        $f_yards = $input_value * 220;
        $c_yards = $input_value * 10;
        $m_yards = $input_value * 1760;
        $m_furlong = $input_value * 8;

        $i_millimetre = $input_value * 0.03937;
        $i_centimetre = $input_value * 0.39370;
        $i_metre = $input_value * 39.37008;
        $f_metre = $input_value * 3.28084;
        $y_metre = $input_value * 1.09361;
        $k_yards = $input_value * 1093.6133;
        $k_miles = $input_value * 0.62137;

        $centimetre_i = $input_value * 2.54;
        $centimetre_f = $input_value * 30.48;
        $centimetre_y = $input_value * 91.44;
        $metre_y = $input_value * 0.9144;
        $metre_m = $input_value * 1609.344;
        $kilometre_m = $input_value * 1.609344;
    ?>

    
    <div class="conversion-card">
        <h1>Metric and Imperial Conversions</h1>
        <table class="conversion-table">
            <tbody>
                <tr class="section-header"><td colspan="6">METRIC CONVERSIONS</td></tr>
                <tr>
                <td><?= $input_value ?> centimetre</td>
                <td>=</td>
                <td><?= $millimetres ?> millimetre</td>
                <td><?= $input_value ?> cm</td>
                <td>=</td>
                <td><?= $millimetres ?> mm</td>
                </tr>

                <tr>
                <td><?= $input_value ?> decimetre</td>
                <td>=</td>
                <td><?= $decimetre ?> centimetre</td>
                <td><?= $input_value ?> dm</td>
                <td>=</td>
                <td><?= $decimetre ?> cm</td>
                </tr>

                <tr>
                <td><?= $input_value ?> metre</td>
                <td>=</td>
                <td><?= $metre ?> centimetre</td>
                <td><?= $input_value ?> m</td>
                <td>=</td>
                <td><?= $metre ?> cm</td>
                </tr>

                <tr>
                <td><?= $input_value ?> kilometre</td>
                <td>=</td>
                <td><?= $kilometre ?> metre</td>
                <td><?= $input_value ?> km</td>
                <td>=</td>
                <td><?= $kilometre ?> m</td>
                </tr>

                <tr class="section-header"><td colspan="6">IMPERIAL CONVERSIONS</td></tr>
                <tr>
                <td><?= $input_value ?> foot</td>
                <td>=</td>
                <td><?= $inches ?> inches</td>
                <td><?= $input_value ?> ft</td>
                <td>=</td>
                <td><?= $inches ?> in</td>
                </tr>

                <tr>
                <td><?= $input_value ?> yard</td>
                <td>=</td>
                <td><?= $feet ?> feet</td>
                <td><?= $input_value ?> yd</td>
                <td>=</td>
                <td><?= $feet ?> ft</td>
                </tr>

                <tr>
                <td><?= $input_value ?> chain</td>
                <td>=</td>
                <td><?= $yards ?> yard</td>
                <td><?= $input_value ?> ch</td>
                <td>=</td>
                <td><?= $yards ?> yd</td>
                </tr>

                <tr>
                <td><?= $input_value ?> furlong</td>
                <td>=</td>
                <td><?= "$f_yards yards (or $c_yards chains)";?> </td>
                <td><?= $input_value ?> fur</td>
                <td>=</td>
                <td><?= "$f_yards yd (or $c_yards ch)";?> </td>
                </tr>
                
                <tr>
                <td><?= $input_value ?> mile</td>
                <td>=</td>
                <td><?= "$m_yards yards (or $m_furlong furlongs)";?> </td>
                <td><?= $input_value ?> mi</td>
                <td>=</td>
                <td><?= "$m_yards yd (or $m_furlong fur)";?> </td>
                </tr>

                <tr class="section-header"><td colspan="6">METRIC -> IMPERIAL CONVERSIONS</td></tr>
                <tr>
                <td><?= $input_value ?> millimetre</td>
                <td>=</td>
                <td><?= $i_millimetre ?> inches</td>
                <td><?= $input_value ?> mm</td>
                <td>=</td>
                <td><?= $i_millimetre ?> in</td>
                </tr>

                <tr>
                <td><?= $input_value ?> centimetre</td>
                <td>=</td>
                <td><?= $i_centimetre ?> inches</td>
                <td><?= $input_value ?> cm</td>
                <td>=</td>
                <td><?= $i_centimetre ?> in</td>
                </tr>

                <tr>
                <td><?= $input_value ?> metre</td>
                <td>=</td>
                <td><?= $i_metre ?> inches</td>
                <td><?= $input_value ?> m</td>
                <td>=</td> 
                <td><?= $i_metre ?> yards</td>
                </tr>

                <tr>
                <td><?= $input_value ?> metre</td>
                <td>=</td>
                <td><?= $f_metre ?> feet</td>
                <td><?= $input_value ?> m</td>
                <td>=</td> 
                <td><?= $f_metre ?> ft</td>
                </tr>

                <tr>
                <td><?= $input_value ?> metre</td>
                <td>=</td>
                <td><?= $y_metre ?> yard</td>
                <td><?= $input_value ?> m</td>
                <td>=</td> 
                <td><?= $y_metre ?> yd</td>
                </tr>

                <tr>
                <td><?= $input_value ?> kilometre</td>
                <td>=</td>
                <td><?= $k_yards ?> yard</td>
                <td><?= $input_value ?> km</td>
                <td>=</td> 
                <td><?= $k_yards ?> yd</td>
                </tr>

                <tr>
                <td><?= $input_value ?> kilometre</td>
                <td>=</td>
                <td><?= $k_miles ?> miles</td>
                <td><?= $input_value ?> km</td>
                <td>=</td> 
                <td><?= $k_miles ?> mi</td>
                </tr>
    
                <tr class="section-header"><td colspan="6">IMPERIAL -> METRIC CONVERSIONS</td></tr>
                <tr>
                <td><?= $input_value ?> inch</td>
                <td>=</td>
                <td><?= $centimetre_i ?> centimetres</td>
                <td><?= $input_value ?> in</td>
                <td>=</td>
                <td><?= $centimetre_i ?> cm</td>
                </tr>

                <tr>
                <td><?= $input_value ?> foot</td>
                <td>=</td>
                <td><?= $centimetre_f ?> centimetres</td>
                <td><?= $input_value ?> ft</td>
                <td>=</td>
                <td><?= $centimetre_f ?> cm</td>
                </tr>

                <tr>
                <td><?= $input_value ?> yard</td>
                <td>=</td>
                <td><?= $centimetre_y ?> centimetres</td>
                <td><?= $input_value ?> yd</td>
                <td>=</td>
                <td><?= $centimetre_y ?> cm</td>
                </tr>

                <tr>
                <td><?= $input_value ?> yard</td>
                <td>=</td>
                <td><?= $metre_y ?> metre</td>
                <td><?= $input_value ?> yd</td>
                <td>=</td>
                <td><?= $metre_y ?> m</td>
                </tr>

                <tr>
                <td><?= $input_value ?> mile</td>
                <td>=</td>
                <td><?= $metre_m ?> metres</td>
                <td><?= $input_value ?> mi</td>
                <td>=</td>
                <td><?= $metre_m ?> m</td>
                </tr>

                <tr>
                <td><?= $input_value ?> mile</td>
                <td>=</td>
                <td><?= $kilometre_m ?> kilometres</td>
                <td><?= $input_value ?> mi</td>
                <td>=</td>
                <td><?= $kilometre_m ?> km</td>
                </tr>

                </tbody>
        </table>
        <center>
            <a href="index.php" class="button-link">Return</a>
        </center>
        
    </div>
    
</body>

</html>