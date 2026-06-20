<?php
setcookie("name", "Joseph Calleja");
setcookie("Address", "1234 A St.", time()+5);
setcookie("Age", "35", time()+10);

echo "<pre>";
var_dump($_COOKIE);
echo "</pre>";
?>

<?php
echo "<pre>";
var_dump($_SERVER);
echo "</pre>";
?>