<?php require '../component/header.php'; ?>

<div class="container">
    <div class="story-container">
        <div class="story-row">
            <div class="story-column">
                <?php include '../component/story/story1.php'; ?>
            </div>
            <div class="story-column">
                <?php include '../component/story/story2.php'; ?>
            </div>
            <div class="story-column">
                <?php include '../component/story/story3.php'; ?>
            </div>
            <div class="story-column">
                <?php include '../component/story/story4.php'; ?>
            </div>
            <div class="story-column">
                <?php include '../component/story/story5.php'; ?>
            </div>
        </div>

        <div class="button-footer">
            <a href="../index.php" class="button-link">
                Return to Home
            </a>
        </div>
    </div>
</div>

<?php require '../component/footer.php'; ?>