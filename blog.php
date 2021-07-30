<?php
$foundBlog = false;
$blog = null;
$author = null;

$params = explode("/", $_SERVER["REQUEST_URI"]);
$blodId = end($params);
//var_dump($blodId);
//var_dump($_GET);
//var_dump($_SERVER);
//var_dump($_REQUEST);

if ($blodId) {
    include("database.php");
    $blog = getBlogById($conn, $blodId);

    if ($blog) {
        $foundBlog = true;
        $author = getUserById($conn, $blog->userId);

        if (!$author) {
            $foundBlog = false;
        }

        //var_dump($blog);
        //var_dump($author);
    }

    $conn->close();
}
?>

<?php include("components/header.php"); ?>
<?php include("components/nav.php"); ?>
<div class="container">
    <div class="blog-container">
        <?php if ($foundBlog) : ?>
            <h1><?= $blog->title ?></h1>
            <h2><?= $blog->subTitle ?></h2>
            <p class="blog-author"><?= $author->Username ?></p>
            <div class="blog-body-container">
                <?php
                $dom = new DOMDocument();

                $dom->preserveWhiteSpace = false;
                $dom->loadHTML($blog->body);
                $dom->formatOutput = true;

                print $dom->saveXML($dom->documentElement);
                ?>
            </div>
            <p><?= $blog->tags ?></p>
    </div>
    <div class="blog-comments-container">
        <div class="add-comment-container"></div>
        <div class="comments-container">

        </div>
    </div>
<?php else : ?>
    <h1>Sorry, friend. The blog you're looking for no longer (or maybe never did..!) exist. Please move along</h1>
<?php endif; ?>
</div>

<?php include("components/footer.php"); ?>