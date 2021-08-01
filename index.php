<?php
include("database.php");

$conn = getConnection();

$blogs = getBlogs($conn, 5);
$conn->close();
?>

<?php include("components/header.php"); ?>
<?php include("components/nav.php"); ?>
<div class="container home-container">
    <h1>Welcome to Bloggest!</h1>
    <h2>The biggest* blogger site in existence</h2>
    <p>Here at <b>Bloggest</b> we strive to make it as easy as possible for our users to share their opinion</p>
    <?php if ($blogs->num_rows) : ?>
        <div class="recent-blogs-container">
            <h3>Recent blogs</h3>
            <div class="recent-blogs-list">
                <?php while ($blog = $blogs->fetch_assoc()) {
                    getBlogHTML($blog);
                } ?>
            </div>
        </div>
    <?php endif; ?>
    <p class="small-print">* That is a lie</p>
</div>
<?php include("components/footer.php"); ?>

<?php

function getBlogHTML($blog)
{
    $conn = getConnection();
    $author = getUserbyId($conn, $blog["userId"]);
    $conn->close();

?>
    <div class="blog-container-preview">
        <h4><?php print $blog["title"] ?> - <?= $author->Username ?></h4>
        <p><?php print $blog["body"] ?></p>
    </div>
<?php
}

?>