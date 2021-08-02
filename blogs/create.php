<?php
include("../components/header.php");
include("../components/nav.php");
$blogTitle = "New blog";
include("form.php");
include("../components/footer.php");

extract($_POST);
if (isset($_POST['submit']) && isset($_POST["title"]) && isset($_POST["body"])) {

    include("../database.php");

    $userId = $_SESSION["id"];
    $title = $_POST["title"];
    $body = $_POST["body"];
    $tags = $_POST["tags"];

    $conn = getConnection();
    if (isset($_POST['submit'])) {

        $stmt = $conn->prepare("INSERT INTO Blogs (userId, title, body, tags) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $userId, $title, $body, $tags);

        $stmt->execute();

        $blogId = $conn->insert_id;

        foreach ($_FILES["images"]["tmp_name"] as $key => $tmp_name) {

            $file_name = $_FILES["images"]["name"][$key];
            $file_tmp = $_FILES["images"]["tmp_name"][$key];
            $image_size = getimagesize($_FILES["images"]["tmp_name"][$key]);
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);

            $uploadOk = 1;
            if ($image_size !== false) {
                echo "File is an image - " . $image_size["mime"] . ".";

                $contents = addslashes(file_get_contents($file_tmp));

                $addedBlogComponent = addBlogComponent($conn, $blogId, "Image", $contents);

                if(!$addedBlogComponent) {
                    echo $addedBlogComponent->error;
                }

                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }
        }

        //header("location: /dashboard");
    }
    $conn->close();
    $_POST = array();
}
