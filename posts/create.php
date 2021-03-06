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

        $i = 0;

        foreach ($groupedComponentItems as $groupKey => $group) {

            $uuid = $group['key'];
            $components = $group['value'];
            $mainComponents = array_usearch($components, function ($obj) {
                return $obj->type == "component";
            });

            // If we for some reason don't have a main component, we leave this group
            if (count($mainComponents) > 0) {

                $mainComponent = $mainComponents[0];

                $parentId = null;

                if (property_exists($mainComponent, "parentUUID")) {
                    $parentId = getPostMainComponentByUUID($conn, $mainComponent->parentUUID)->id;
                }
                $i++;
                // Add the main component to the db first to get the id
                addPostComponentItem($conn, $blogId, $parentId, $uuid, $i, $mainComponent->type, $mainComponent->value);
            }

            $componentId = $conn->insert_id;

            foreach ($components as $componentsKey => $component) {
                $i++;

                // Don't do anything with the main component
                if ($component->type === "component") {
                    continue;
                }

                $parentId = null;
                //var_dump($component);

                if (property_exists($component, "parentUUID")) {
                    $parentId = getPostMainComponentByUUID($conn, $component->parentUUID)->id;
                }

                $value = str_replace(array("\n", "\r"), '', nl2br(htmlspecialchars($component->value)));

                // Add the secondary component to the db
                addPostComponentItem($conn, $blogId, $parentId, $uuid, $i, $component->type, $value, $component->isRequired);
            }
        }

        $conn->close();
        $_POST = array();
        exit;

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