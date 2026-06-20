<?php
session_start();

require('fragments/header.php');
?>
            <div class="col col-md-4">
                <div class="card shadow-lg">
                    <div class="card-body">
                        <h3>User Log In</h3>
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
                        <form action="check_user.php" method="post">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control">
                            </div>
                            <div class="col-12 d-flex justify-content-between mb-3">
                                <button type="submit" name="submit" id="submit" class="btn btn-primary">Log In</button>
                                <a href="signup.php" class="btn btn-secondary">Sign Up</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
<?php
require('fragments/footer.php');
?>