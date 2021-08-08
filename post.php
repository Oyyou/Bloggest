<?php
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

$pageTitle = null;
$pageDescription = null;
$pageAuthor = null;
$pageType = "article";
$additionalHeaders = array();

$foundBlog = false;
$foundComponents = false;
$foundAuthor = false;
$blog = null;
$author = null;

$params = explode("/", $_SERVER["REQUEST_URI"]);
$blogId = end($params);
$components = array();

if ($blogId) {
    include("database.php");
    $conn = getConnection();
    $blog = getBlogById($conn, $blogId);

    if ($blog) {
        $foundBlog = true;
        $components = getBlogComponents($conn, $blogId);
        $author = getUserById($conn, $blog->userId);

        $pageTitle = $blog->title;
        $pageDescription = $blog->shortDescription;

        if ($components) {
            $foundComponents = true;
        }

        if ($author) {
            $foundAuthor = true;
            $pageAuthor = $author->Username;
        }

        array_push($additionalHeaders, [
            'property' => "article:author",
            'content' => $actual_link . '/user/' . $pageAuthor,
        ], [
            'property' => "article:tag",
            'content' => "",
        ], [
            'property' => "article:published_time",
            'content' => "",
        ], [
            'property' => "article:modified_time ",
            'content' => "",
        ]);
    }

    $conn->close();
}
?>

<?php include("components/header.php"); ?>
<?php include("components/nav.php"); ?>
<div class="container">
    <div class="blog-container">
        <?php if ($foundBlog) : ?>
            <div class="blog-header-container">
                <h1><?= $blog->title ?></h1>
            </div>
            <div class="blog-author-container">
                <div class="blog-author-name">
                    <?php if ($foundAuthor) : ?>
                        <p class="blog-author"><a href="/user/<?= $author->Username ?>"><?= $author->Username ?></a></p>
                    <?php else : ?>
                        <p class="blog-author">Unknown author</p>
                    <?php endif; ?>
                </div>
                <div class="share-container">
                    <button id="share-button">
                        <svg id="share-light" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-share" viewBox="0 0 16 16">
                            <path d="M13.5 1a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zM11 2.5a2.5 2.5 0 1 1 .603 1.628l-6.718 3.12a2.499 2.499 0 0 1 0 1.504l6.718 3.12a2.5 2.5 0 1 1-.488.876l-6.718-3.12a2.5 2.5 0 1 1 0-3.256l6.718-3.12A2.5 2.5 0 0 1 11 2.5zm-8.5 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm11 5.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3z" />
                        </svg>
                        <svg id="share-dark" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-share-fill" viewBox="0 0 16 16">
                            <path d="M11 2.5a2.5 2.5 0 1 1 .603 1.628l-6.718 3.12a2.499 2.499 0 0 1 0 1.504l6.718 3.12a2.5 2.5 0 1 1-.488.876l-6.718-3.12a2.5 2.5 0 1 1 0-3.256l6.718-3.12A2.5 2.5 0 0 1 11 2.5z" />
                        </svg>
                    </button>
                    <div id="share-dropdown" class="share-dropdown" style="display: none;">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-twitter" viewBox="0 0 16 16">
                                <path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z" />
                            </svg>
                            <p>Twitter</p>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
                                <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z" />
                            </svg>
                            <p>Facebook</p>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-link" viewBox="0 0 16 16">
                                <path d="M6.354 5.5H4a3 3 0 0 0 0 6h3a3 3 0 0 0 2.83-4H9c-.086 0-.17.01-.25.031A2 2 0 0 1 7 10.5H4a2 2 0 1 1 0-4h1.535c.218-.376.495-.714.82-1z" />
                                <path d="M9 5.5a3 3 0 0 0-2.83 4h1.098A2 2 0 0 1 9 6.5h3a2 2 0 1 1 0 4h-1.535a4.02 4.02 0 0 1-.82 1H12a3 3 0 1 0 0-6H9z" />
                            </svg>
                            <p>Copy link</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="blog-body-container">
                <?php while ($component = $components->fetch_assoc()) {
                    switch ($component["type"]) {
                        case "image";
                            getImageComponentContainer($component);
                            break;
                        case "textarea";
                            getTextComponentContainer($component);
                            break;
                    }
                }
                ?>
                <?php
                /*$dom = new DOMDocument();

                $dom->preserveWhiteSpace = false;
                $dom->loadHTML($blog->body);
                $dom->formatOutput = true;

                print $dom->saveXML($dom->documentElement);*/
                ?>
            </div>
            <?= getTagsContainer($blog->tags) ?>
            <div class="blog-comments-container">
                <div class="add-comment-container">

                </div>
                <div class="comments-container">

                </div>
            </div>
        <?php else : ?>
            <h1>Sorry, friend. The post you're looking for no longer (or maybe never did..!) exist. Please move along</h1>
        <?php endif; ?>
    </div>
</div>

<?php include("components/footer.php"); ?>

<?php
function getImageComponentContainer($component)
{
?>
    <div class="component-container image-container">
        <img src="/uploads/<?= $component["content"] ?>">
    </div>

<?php
}
function getTextComponentContainer($component)
{
?>
    <div class="component-container text-container">
        <p><?= $component["content"] ?></p>
    </div>

<?php
}

function getTagsContainer($tags)
{
    if (!$tags) return;

    $newTags = array_map('trim', explode(",", $tags));
?>

    <div class="tags-container">
        <?php foreach ($newTags as $tag) : ?>
            <span><?= $tag ?></span>
        <?php endforeach; ?>
    </div>

<?php
}
?>

<script>
    const getComponentContainer = (component) => {

    }

    $('#share-button').click(function(event) {
        event.stopPropagation();
        toggleShareDropdown();
    });

    function toggleShareDropdown() {
        let element = document.getElementById("share-dropdown");

        if (element.style.display === "none") {
            element.style.display = "block";
        } else {
            element.style.display = "none";
        }
    }

    $(window).click(function() {
        let element = document.getElementById("share-dropdown");
        if (element.style.display === "block") {
            element.style.display = "none";
        }
    });
</script>