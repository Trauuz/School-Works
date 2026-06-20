<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <title>Document</title>
</head>

<body>
    <div class="container p-5 vh-100">
        <div class="row d-flex justify-content-center mb-3">
            <div class="col col-md-4">
                <div class="card shadow-lg">
                    <div class="card-body">
                        <!-- <form action="<?= $_SERVER['PHP_SELF']; ?>" method="get"> -->
                        <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post">
                            <div class="mb-3">
                                <label for="fullname" class="form-label">Full Name</label>
                                <input type="text" name="fullname" id="fullname" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" name="address" id="address" class="form-control">
                            </div>
                            <div class="mb-3">
                                <button type="submit" name="submit" id="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col col-md-6">
                <h3>Data From $_GET</h3>
                <?php
                if(isset($_GET['submit'])):
                ?>
                <p>FULL NAME: <?= $_GET['fullname'] ?></p>
                <p>ADDRESS: <?= $_GET['address'] ?></p>
                <?php
                endif;
                ?>
            </div>
            <div class="col col-md-6">
                <h3>Data From $_POST</h3>
                <?php
                if(isset($_POST['submit'])):
                ?>
                <p>FULL NAME: <?= $_POST['fullname'] ?></p>
                <p>ADDRESS: <?= $_POST['address'] ?></p>
                <?php
                endif;
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?php
                if(isset($_REQUEST['submit'])):
                ?>
                <p>FULL NAME: <?= $_REQUEST['fullname'] ?></p>
                <p>ADDRESS: <?= $_REQUEST['address'] ?></p>
                <?php
                endif;
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>
</body>

</html>