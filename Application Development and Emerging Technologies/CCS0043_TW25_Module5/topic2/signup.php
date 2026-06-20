<?php
session_start();

require('fragments/header.php');
?>
            <div class="col col-md-4">
                <div class="card shadow-lg">
                    <div class="card-body">
                        <h3>User Sign Up</h3>
                        <?php
                        if(isset($_SESSION['error'])):
                        ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['error']; ?>
                        </div>
                        <?php
                            unset($_SESSION['error']);
                        endif;
                        ?>
                        <form action="process_signup.php" method="post" novalidate>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="confirmpassword" class="form-label">Confirm Password</label>
                                <input type="password" name="confirmpassword" id="confirmpassword" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="fullname" class="form-label">Full Name</label>
                                <input type="text" name="fullname" id="fullname" class="form-control">
                            </div>
                            <div class="col-12 d-flex justify-content-between mb-3">
                                <button type="submit" name="submit" id="submit" class="btn btn-primary">Sign Up</button>
                                <a href="login.php" class="btn btn-warning">Back to Log In</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
<?php
require('fragments/footer.php');
?>