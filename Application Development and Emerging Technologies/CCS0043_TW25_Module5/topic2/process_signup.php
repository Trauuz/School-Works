<?php
session_start();

if(isset($_POST['submit'])) {
    // Get data from form
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];
    $fullname = $_POST['fullname'];

    // Check if all has content
    if(empty($email) || empty($password) || empty($confirmpassword) || empty($fullname)) {
        $_SESSION['error'] = "Missing field...";
        header('Location: signup.php');
        exit();
    } else {
        // Check if email is correct format
        $emailpattern = "/^[a-zA-Z]+[a-zA-Z0-9_\.]*@[a-zA-Z]+\.[a-zA-Z]{2,8}$/";
        if(!preg_match($emailpattern, $email)) {
            $_SESSION['error'] = "Email is invalid...";
            header('Location: signup.php');
            exit();
        }

        // Check if password is correct format
        if(strlen($password) < 8 ) {
            $_SESSION['error'] = "Password must be at least 8 characters long.";
            header('Location: signup.php');
            exit();
        } else {
            if($password !== $confirmpassword) {
                $_SESSION['error'] = "Passwords do not match.";
                header('Location: signup.php');
                exit();
            }
        }

        // Check if email is correct format
        $namepattern = "/^[a-zA-Z]+[a-zA-Z\s\.\']*$/";
        if(!preg_match($namepattern, $fullname)) {
            $_SESSION['error'] = "Illegal character detected in name.";
            header('Location: signup.php');
            exit();
        }
    }
}

?>