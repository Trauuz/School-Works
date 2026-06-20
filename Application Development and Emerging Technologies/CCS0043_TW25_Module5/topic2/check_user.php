<?php
session_start();

if(isset($_POST['submit'])) {
    if(trim($_POST['username']) === 'admin' && trim($_POST['password']) === 'password'){
        $_SESSION['fullname'] = "Joseph";
        $_SESSION['islogged'] = true;
        header('Location: index.php');
        exit();
    } else {
        $_SESSION['error'] = "Username or password is incorrect.";
        header('Location: login.php');
        exit();
    }
}
?>