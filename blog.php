<?php
$foundBlog = false;

$title = "";
$subTitle = "";
$body = "";
$tags = "";
$comments = [];

print_r($_GET);

if (isset($_GET['id'])) {
    include("database.php");
    $qid = $_GET['id'];
    $sql = "SELECT * FROM Blogs B INNER JOIN Comments C on B.Id = C.BlogId Where B.Id = ?";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
    /*
    $sql = "SELECT id, title, subTitle, body, tags FROM Blogs where id=? LIMIT 1";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('i', $qid);
        $stmt->execute();
        $stmt->bind_result($id, $title, $subTitle, $body, $tags);
        $success = $stmt->fetch();

        if ($success) {
            $foundBlog = true;
        } else {
            $foundBlog = false;
        }
        $conn->close();
        include("database.php");

        $sqlComments = "SELECT id, userId, blogId, value, timePosted from Comments where blogId=?";
        $stmtComments = $conn->prepare($sqlComments);
        if ($stmtComments) {
            print_r("Hello");
            $stmtComments->bind_param('i', $id);
            $stmtComments->execute();
            $stmtComments->bind_result($cid, $blogUserId, $blogId, $value, $timePosted);

            print_r($cid);
            print_r($blogUserId);
            print_r($blogId);
            print_r($value);
        }
    }*/
}

if (!$foundBlog) {
}
?>

<?php include("components/header.php"); ?>
<?php include("components/nav.php"); ?>
<div class="container">
    <div class="blog-container">
        <?php if ($foundBlog) : ?>
            <h1><?= $title ?></h1>
            <h2><?= $subTitle ?></h2>
            <div class="blog-body-container">
                <?php
                $dom = new DOMDocument();

                $dom->preserveWhiteSpace = false;
                $dom->loadHTML($body);
                $dom->formatOutput = true;

                print $dom->saveXML($dom->documentElement);
                ?>
            </div>
            <p><?= $tags ?></p>
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