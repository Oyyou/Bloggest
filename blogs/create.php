<?php

use function PHPSTORM_META\type;

$uploadPath = $_SERVER['DOCUMENT_ROOT'] . "/uploads/";

include("../components/header.php");
include("../components/nav.php");
$blogTitle = "New blog";
include("form.php");
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

                    $uploadOk = 1;
                    if ($image_size !== false) {

                        //$addedBlogComponent = addBlogComponent($conn, $blogId, $key, "image", $newFileName);

                        //if (!$addedBlogComponent) {
                        //    echo $addedBlogComponent->error;
                        //}

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

        foreach ($_POST["components"] as $key => $component) {
            $compObj = json_decode($component);
            var_dump($compObj);

            $addedBlogComponent = addBlogComponent($conn, $blogId, $compObj->uuid, $key, $compObj->type, str_replace(array("\n", "\r"), '', nl2br(htmlspecialchars($compObj->value))));

            if (!$addedBlogComponent) {
                echo $addedBlogComponent->error;
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