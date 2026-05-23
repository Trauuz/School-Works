<?php require("../component/header.php"); ?>

<div class="directory-container">
    <h1>Resume</h1>
    <div class="resume-grid">
        <div class="resume-image">
            <div class="image-box">
                <img src="https://s.yimg.com/ny/api/res/1.2/arQPnZ9RMRWhQske6bdPNA--/YXBwaWQ9aGlnaGxhbmRlcjt3PTY0MDtoPTM2MA--/https://media.zenfs.com/en/moneywise_ecomm_711/f10edf68b5f1fd231f5ab006e1afd5b1"
                    alt="Profile Picture">
            </div>
        </div>

        <div class="resume-right">
            <div class="resume-detail">
                <?php
                    if (isset($_GET['page'])) {
                        $file = __DIR__ . '/../component/' . $_GET['page'] . '.php';
                        if (file_exists($file)) {
                            include $file;
                        } else {
                            echo '<p>Section not available.</p>';
                        }
                    } else {
                        include __DIR__ . '/../component/objective.php';
                    }
                ?>
            </div>

            <div class="resume-buttons">
                <a class="button-link" href="?page=objective">Objective</a>
                <a class="button-link" href="?page=personal">Personal</a>
                <a class="button-link" href="?page=education">Education</a>
                <a class="button-link" href="?page=skills">Skills</a>
                <a class="button-link" href="?page=affiliation">Affiliation</a>
                <a class="button-link" href="?page=experience">Experience</a>
            </div>
        </div>
    </div>

    <center>
        <a href="../index.php" class="button-link">Return</a>
    </center>
</div>

<?php require("../component/footer.php"); ?>