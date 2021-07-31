<?php

include("../components/header.php");
include("../components/nav.php");

$params = explode("/", $_SERVER["REQUEST_URI"]);
$blogId = end($params);

$foundBlog = false;
$unauthorizedBlog = false;

$userId = "";
$title = "";
$subTitle = "";
$body = "";
$tags = "";

if ($blogId) {
    include("../database.php");
    $qid = $blogId;
    $sql = "SELECT id, userId, title, subTitle, body, tags FROM Blogs where id=? LIMIT 1";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('i', $qid);
        $stmt->execute();
        $stmt->bind_result($id, $userId, $title, $subTitle, $body, $tags);
        $success = $stmt->fetch();

        if ($success) {
            $foundBlog = true;
            if ($userId !== $_SESSION["id"]) {
                $unauthorizedBlog = true;
            }
        } else {
            $foundBlog = false;
        }
    }

    $conn->close();
}
?>

<div class="container blog-container">
    <?php if ($unauthorizedBlog) : ?>
        <h1>Slow down there, champ. This isn't your blog! You've gotta get outta here</h1>
    <?php elseif ($foundBlog) : ?>
        <?php
        $blogTitle = "Editing blog";
        include("form.php");
        ?>
    <?php else : ?>
        <h1>Sorry, friend. The blog you're looking for no longer (or maybe never did..!) exist. Please move along</h1>
    <?php endif; ?>
</div>

<?php include("../components/footer.php"); ?>

<?php
extract($_POST);
if (isset($_POST["title"]) && isset($_POST["body"]) && isset($blogId)) {

    $conn = getContext();

    $userId = $_SESSION["id"];
    $title = $_POST["title"];
    $subTitle = $_POST["subtitle"];
    $body = $_POST["body"];
    $tags = $_POST["tags"];

    if (isset($_POST['submit'])) {

        $query = "UPDATE Blogs SET title=?, subTitle=?, body=?, tags=? WHERE id=?";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            trigger_error($conn->error, E_USER_ERROR);
            return;
        }

        $stmt->bind_param("ssssi", $title, $subTitle, $body, $tags, $blogId);

        $stmt->execute();
        
        header("location: /dashboard");
    }

    $conn->close();
}
?>