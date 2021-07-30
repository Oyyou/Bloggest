<?php
include("../components/header.php");
include("../components/nav.php");
$blogTitle = "New blog";
include("form.php");
include("../components/footer.php");

extract($_POST);
if (isset($_POST["title"]) && isset($_POST["body"])) {

    include("../database.php");

    $userId = $_SESSION["id"];
    $title = $_POST["title"];
    $subtitle = $_POST["subtitle"];
    $body = $_POST["body"];
    $tags = $_POST["tags"];

    if (isset($_POST['submit'])) {

        $stmt = $conn->prepare("INSERT INTO Blogs (userId, title, subtitle, body, tags) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $userId, $title, $subtitle, $body, $tags);

        $stmt->execute();

        header("location: /dashboard");
    }
    $conn->close();
    $_POST = array();
}
