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
$componentList = array();

if ($blogId) {
    include("../database.php");
    $conn = getConnection();
    $qid = $blogId;
    $blog = getBlogById($conn, $blogId);

    if ($blog) {
        $userId = $blog->userId;
        $title = $blog->title;
        $subTitle = $blog->subTitle;
        $body = $blog->body;
        $tags = $blog->tags;
        $foundBlog = true;

        $components = getBlogComponents($conn, $blogId);

        if ($components->num_rows > 0) {
            while ($component = $components->fetch_assoc()) {
                if ($component) {
                    $component["content"] = "data:image/jpg;charset=utf8;base64," . base64_encode($component["content"]);
                    array_push($componentList, $component);
                }
            }
        }

        if ($userId !== $_SESSION["id"]) {
            $unauthorizedBlog = true;
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

    $conn = getConnection();

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

        //header("location: /dashboard");
    }

    $conn->close();

?>
    <script type="text/javascript">
        window.location = "/dashboard";
    </script>
<?php
}
?>