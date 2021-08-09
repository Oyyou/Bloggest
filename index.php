<?php
include("database.php");

$conn = getConnection();

$blogs = getBlogs($conn, 5);
$conn->close();
?>

<?php include("components/header.php"); ?>
<?php include("components/nav.php"); ?>
<div class="container home-container">
    <h2>Welcome to Bloggest!</h2>
    <p>For blogging</p>
    <?php if ($blogs->num_rows) : ?>
        <div class="recent-blogs-container">
            <h3>Recent posts</h3>
            <div class="recent-blogs-list">
                <?php while ($blog = $blogs->fetch_assoc()) {
                    getBlogHTML($blog);
                } ?>
            </div>
        </div>
    <?php endif; ?>
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
        <h4><?php print $blog["title"] ?></h4>
        <p><?php print $blog["shortDescription"] ?></p>
        <p><a href="/user/<?= $author->Username ?>"><?= $author->Username ?></a></p>
    </div>
<?php
}

?>