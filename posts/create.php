<?php

use function PHPSTORM_META\type;

$uploadPath = $_SERVER['DOCUMENT_ROOT'] . "/uploads/";

include("../components/header.php");
include("../components/nav.php");
$blogTitle = "New post";
?>
<main class="container">
    <?php
    include("form.php");
    ?>
</main>
<?php
include("../components/footer.php");

extract($_POST);
if (isset($_POST['submit']) && isset($_POST["title"]) && isset($_POST["shortDescription"])) {

    include("../database.php");
    include("../php-functions.php");

    $userId = $_SESSION["id"];
    $title = $_POST["title"];
    $shortDescription = $_POST["shortDescription"];
    $tags = $_POST["tags"];

    $conn = getConnection();
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

        $stmt = $conn->prepare("INSERT INTO Blogs (userId, title, shortDescription, tags) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $userId, $title, $shortDescription, $tags);

        $stmt->execute();

        $blogId = $conn->insert_id;

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

        foreach ($groupedComponentItems as $groupKey => $group) {

            $uuid = $group['key'];
            $components = $group['value'];
            $mainComponent = array_usearch($components, function ($obj) {
                return $obj->type == "component";
            })[0];

            // If we for some reason don't have a main component, we leave this group
            if (empty($mainComponent)) {
                continue;
            }

            $parentId = null;

            if ($mainComponent->parentUUID) {
                $parentId = getPostMainComponentByUUID($conn, $mainComponent->parentUUID)->id;
            }

            // Add the main component to the db first to get the id
            addPostComponentItem($conn, $blogId, $parentId, $uuid, $groupKey, $mainComponent->type, $mainComponent->value);

            $componentId = $conn->insert_id;

            foreach ($components as $componentsKey => $component) {

                // Don't do anything with the main component
                if ($component->type === "component") {
                    continue;
                }

                $value = str_replace(array("\n", "\r"), '', nl2br(htmlspecialchars($component->value)));

                // Add the secondary component to the db
                addPostComponentItem($conn, $blogId, $componentId, $uuid, $componentsKey, $component->type, $value);
            }
        }

        $conn->close();
        $_POST = array();
        exit;

        if (isset($_POST["component"])) {
            foreach ($_POST["components"] as $key => $component) {

                $compObj = json_decode($component);
                $value = $compObj->value;

                $hasComponentItems = false;
                foreach ($groupedComponentItems as $group) {
                    $uuid = $group['key'];

                    if ($compObj->uuid !== $uuid) {
                        continue;
                    }

                    $hasComponentItems = true;

                    // Create a new component
                    $addedBlogComponent = addBlogComponent($conn, $blogId, $uuid, $key, $compObj->type, $value);

                    $componentId = $conn->insert_id;

                    if (!$addedBlogComponent) {
                        echo $addedBlogComponent->error;
                    }

                    foreach ($group['value'] as $i => $componentItem) {

                        $addedPostComponentItem = addPostComponentItem($conn, $blogId, $componentId, $uuid, $i, $componentItem->type, $componentItem->value);

                        if (!$addedPostComponentItem) {
                            echo $addedBlogComponent->error;
                        }
                    }
                }

                if ($hasComponentItems === false) {

                    if (isset($newImageNames[$value])) {
                        $value = $newImageNames[$value];
                    }

                    $value = str_replace(array("\n", "\r"), '', nl2br(htmlspecialchars($value)));

                    $addedBlogComponent = addBlogComponent($conn, $blogId, $compObj->uuid, $key, $compObj->type, $value);

                    if (!$addedBlogComponent) {
                        echo $addedBlogComponent->error;
                    }
                }
            }
        }

        //header("location: /dashboard");
    }
    $conn->close();
    $_POST = array();

?>
    <script type="text/javascript">
        //window.location = "/dashboard";
    </script>
<?php
}
?>