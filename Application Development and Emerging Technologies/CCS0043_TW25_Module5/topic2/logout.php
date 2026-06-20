<?php
session_start();

unset($_SESSION['fullname']);
unset($_SESSION['islogged']);

session_destroy();
header('Location: login.php');
exit();

?>