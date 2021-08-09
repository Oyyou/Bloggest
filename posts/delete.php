<?php

$uploadPath = $_SERVER['DOCUMENT_ROOT'] . "/uploads/";

include("../components/header.php");
include("../components/nav.php");

$params = explode("/", $_SERVER["REQUEST_URI"]);
$blogId = end($params);

$foundBlog = false;
$unauthorizedBlog = false;

$userId = "";
$title = "";
$shortDescription = "";
$tags = "";
$componentList = array();

if ($blogId) {
    include("../database.php");
    include("../php-functions.php");
    $conn = getConnection();
    $qid = $blogId;
    $blog = getBlogById($conn, $blogId);

    if ($blog) {
        $userId = $blog->userId;
        $title = $blog->title;
        $shortDescription = $blog->shortDescription;
        $tags = $blog->tags;
        $foundBlog = true;

        if ($userId !== $_SESSION["id"]) {
            $unauthorizedBlog = true;
        }
    }

    $conn->close();
}
?>

<div class="container blog-container">
    <?php if ($unauthorizedBlog) : ?>
        <h2>Slow down there, champ. This isn't your blog! You've gotta get outta here</h2>
    <?php elseif ($foundBlog) : ?>
        <form method="post" id="submit" class="form-deleting">
            <h2>Deleting post: <?= $title ?></h2>
            <div>
                <p>Once you delete this post it'll be gone forever. Are you sure?</p>
                <input id="submit" name="submit" type="submit" value="Yes">
            </div>
        </form>
    <?php else : ?>
        <h2>Sorry, friend. The blog you're looking for no longer (or maybe never did..!) exist. Please move along</h2>
    <?php endif; ?>
</div>

<?php include("../components/footer.php"); ?>

<?php
extract($_POST);
if (isset($_POST["submit"])) {

    $conn = getConnection();
    deleteBlogById($conn, $blogId);
    deletePostComponentsByBlogId($conn, $blogId);
    deletePostComponentItemsByBlogId($conn, $blogId);
    $conn->close();

?>
    <script type="text/javascript">
        window.location = "/dashboard";
    </script>
<?php
}
?>