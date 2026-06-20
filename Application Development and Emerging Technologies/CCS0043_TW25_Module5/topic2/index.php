<?php
session_start();

if(!isset($_SESSION['islogged'])) {
    header('Location: login.php');
    exit();
}

require('fragments/header.php');
?>
            <div class="col col md-12">
                <h1>Welcome back, <?= $_SESSION['fullname']; ?>!</h1>
                <p>This is our webpage....</p>
            </div>
            <div class="col col-md-12">
                <a href="logout.php" class="btn btn-warning">Log Out</a>
            </div>
<?php
require('fragments/footer.php');
?>