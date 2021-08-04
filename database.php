<?php
function getConnection()
{
    $servernameDb = "localhost";
    $usernameDb = "root";
    $passwordDb = "";
    $dbnameDb = "molfdb";

    $conn = new mysqli($servernameDb, $usernameDb, $passwordDb, $dbnameDb);
    if ($conn->connect_error) {
        die("MySQL connection failed: " . $conn->connect_error);
    }

    return $conn;
}

function getUserbyName(mysqli $conn, string $username)
{
    $stmt = $conn->prepare("SELECT * FROM Users Where Username=? limit 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $value = $result->fetch_object();

    return $value;
}

function getUserbyId(mysqli $conn, string $id)
{
    $stmt = $conn->prepare("SELECT Id, Username FROM Users Where Id=? limit 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $value = $result->fetch_object();

    return $value;
}

function getUserbyEmail(mysqli $conn, string $email)
{
    $stmt = $conn->prepare("SELECT Email FROM Users Where Email=? limit 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $value = $result->fetch_object();

    return $value;
}

function addNewUser(mysqli $conn, string $username, string $email, string $hasedPassword)
{
    $stmt = $conn->prepare("INSERT INTO Users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hasedPassword);

    if ($stmt->execute()) {
        return true;
    }

    return false;
    // $stmt->error;    
}

function addBlogComponent(mysqli $conn, int $blogId, string $uuid, int $outputOrder, string $type, string $content): mysqli_stmt
{
    $prepared = $stmt = $conn->prepare("INSERT INTO Components (blogId, uuid, outputOrder, type, content) VALUES (?, ?, ?, ?, ?)");
    if (!$prepared) {
        echo $conn->error;
    }
    $stmt->bind_param("isiss", $blogId, $uuid, $outputOrder, $type, $content);

    $stmt->execute();

    return $stmt;
}

function getBlogComponents(mysqli $conn, int $blogId)
{
    $result = $conn->query("SELECT id, uuid, blogId, outputOrder, type, content FROM Components WHERE blogId=$blogId");

    return $result;
}

function getBlogComponentByIds(mysqli $conn, int $blogId, string $uuid)
{
    $stmt = $conn->prepare("SELECT id, uuid, blogId, outputOrder, type, content FROM Components WHERE blogId=? AND uuid=? LIMIT 1");

    $stmt->bind_param('is', $blogId, $uuid);
    $stmt->execute();
    $result = $stmt->get_result();
    $value = $result->fetch_object();

    return $value;
}

function updateBlogComponent(mysqli $conn, int $id, int $outputOrder, string $content) {
    
    $stmt = $conn->prepare("UPDATE components SET outputOrder=?, content=? WHERE id=?");
    $stmt->bind_param("isi", $outputOrder, $content, $id);
    $stmt->execute();
}

function deleteBlogComponent(mysqli $conn, int $id) {
    
    $stmt = $conn->prepare("DELETE FROM components WHERE Id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
}

function getBlogById(mysqli $conn, int $id)
{
    $stmt = $conn->prepare("SELECT id, userId, title, shortDescription, tags FROM Blogs where id=? LIMIT 1");

    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $value = $result->fetch_object();

    return $value;
}

function getBlogs(mysqli $conn, int $amount)
{
    $result = $conn->query("SELECT id, userId, title, shortDescription, tags FROM Blogs LIMIT $amount");

    return $result;
}
