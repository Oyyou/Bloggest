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

        //$components = getBlogComponents($conn, $blogId);
        $components = getPostComponentItemsByBlogId($conn, $blogId);

        if ($components) {
            if ($components->num_rows > 0) {
                while ($component = $components->fetch_assoc()) {
                    if ($component) {
                        switch ($component["type"]) {
                            case "image":
                                //$component["content"] = $uploadPath . $component["content"];
                                break;
                        }
                        if (isset($component["componentId"])) {
                            $parentComponent = getPostMainComponent($conn, $component["componentId"]);
                            $component['parentUUID'] = $parentComponent->uuid;
                        } else {
                            $component['parentUUID'] = null;
                        }

                        array_push($componentList, $component);
                    }
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

<main class="container">
    <?php if ($unauthorizedBlog) : ?>
        <h2>Slow down there, champ. This isn't your blog! You've gotta get outta here</h2>
    <?php elseif ($foundBlog) : ?>
        <?php
        $blogTitle = "Editing post";
        include("form.php");
        ?>
    <?php else : ?>
        <h2>Sorry, friend. The blog you're looking for no longer (or maybe never did..!) exist. Please move along</h2>
    <?php endif; ?>
</main>

<?php include("../components/footer.php"); ?>

<?php
extract($_POST);
if (isset($_POST["title"]) && isset($_POST["shortDescription"]) && isset($blogId)) {

    $conn = getConnection();

    $userId = $_SESSION["id"];
    $title = $_POST["title"];
    $shortDescription = $_POST["shortDescription"];
    $tags = $_POST["tags"];

    if (isset($_POST['submit'])) {

        $allImagesSet = true;

        if (isset($_FILES["images"])) {
            foreach ($_FILES["images"]["tmp_name"] as $key => $tmp_name) {

                $image = $_FILES["images"]["tmp_name"][$key];

                if (empty($image)) {
                    $allImagesSet = false;
                } else {
                    $image_size = getimagesize($image);

                    if ($image_size === false) {
                        $allImagesSet = false;
                    }
                }
            }
        }

        $query = "UPDATE Blogs SET title=?, shortDescription=?, modifiedTime=?, tags=? WHERE id=?";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            trigger_error($conn->error, E_USER_ERROR);
            return;
        }

        $date = date('Y-m-d H:i:s');
        $stmt->bind_param("ssssi", $title, $shortDescription, $date, $tags, $blogId);

        $stmt->execute();
        $newImageNames = array();

        if ($allImagesSet) {

            if (isset($_FILES["images"])) {
                foreach ($_FILES["images"]["tmp_name"] as $key => $tmp_name) {

                    $file_name = $_FILES["images"]["name"][$key];
                    $file_tmp = $_FILES["images"]["tmp_name"][$key];
                    $image_size = getimagesize($_FILES["images"]["tmp_name"][$key]);
                    $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                    $newFileName = generateRandomString() . "." . $ext;

                    $target_file = $uploadPath . $newFileName;

                    move_uploaded_file($file_tmp, $target_file);

                    $newImageNames[$file_name] = $newFileName;

                    $uploadOk = 1;
                    if ($image_size !== false) {
                        $uploadOk = 1;
                    } else {
                        echo "File is not an image.";
                        $uploadOk = 0;
                    }
                }
            }
        } else {
            echo "Not all images have a value..!";
        }

        $groupedComponentItems = isset($_POST["componentItems"]) ? groupBy($_POST["componentItems"], function ($e) {
            return $e->uuid;
        }) : array();

        $usedUUIDs = array();

        //foreach ($groupedComponentItems as $groupKey => $group) {
        foreach ($_POST["componentItems"] as $key => $component) {
            $componentObj = json_decode($component);

            $dbComponent = getBlogComponentByIds($conn, $blogId, $componentObj->uuid);

            // If the component already exists in the db
            if ($dbComponent) {
                // Update existing component
                updateBlogComponent($conn, $dbComponent->id, $key, $componentObj->value);
            } else {

                $value = $componentObj->value;

                if (isset($newImageNames[$value])) {
                    $value = $newImageNames[$value];
                }

                $value = str_replace(array("\n", "\r"), '', nl2br(htmlspecialchars($value)));

                // Add new component
                addBlogComponent($conn, $blogId, $componentObj->uuid, $key, $componentObj->type, $value);
                //addPostComponentItem($conn, $blogId, $parentId, $componentObj->uuid, $i, $componentObj->type, $value);
            }

            array_push($usedUUIDs, $componentObj->uuid);
        }

        $dbComponents = getBlogComponents($conn, $blogId);
        foreach ($dbComponents as $component) {
            if (!in_array($component["uuid"], $usedUUIDs)) {
                deleteBlogComponent($conn, $component["id"]);
            }
        }

        // Now we need to delete any components from the db that aren't in the '$_POST'

        //header("location: /dashboard");
    }

    $conn->close();

?>
    <script type="text/javascript">
        //window.location = "/post/" + <?= $blogId ?>;
    </script>
<?php
}
?>