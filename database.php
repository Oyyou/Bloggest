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

function addPostComponentItem(mysqli $conn, int $blogId, ?int $componentId, string $uuid, int $outputOrder, string $type, string $content, bool $isRequired = false)
{
    $prepared = $stmt = $conn->prepare("INSERT INTO ComponentItems (blogId, componentId, uuid, outputOrder, type, content, isRequired) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (!$prepared) {
        echo $conn->error;
    }
    $stmt->bind_param("iisissi", $blogId, $componentId, $uuid, $outputOrder, $type, $content, $isRequired);

    $stmt->execute();
    echo $stmt->error;
    return $stmt;
}

function getPostComponentItems(mysqli $conn, int $componentId)
{
    $result = $conn->query("SELECT id, uuid, componentId, outputOrder, type, content, isRequired FROM ComponentItems WHERE componentId=$componentId ORDER BY outputOrder");

    return $result;
}

function getPostMainComponent(mysqli $conn, $componentId)
{
    $stmt = $conn->prepare("SELECT id, uuid, componentId, outputOrder, type, content, isRequired FROM ComponentItems WHERE id=? and type ='component' ORDER BY outputOrder limit 1");
    $stmt->bind_param("i", $componentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $value = $result->fetch_object();

    return $value;
}

function getPostMainComponentByUUID(mysqli $conn, $uuid)
{
    $stmt = $conn->prepare("SELECT id, uuid, componentId, outputOrder, type, content, isRequired FROM ComponentItems WHERE uuid=? and type ='component' ORDER BY outputOrder limit 1");
    $stmt->bind_param("s", $uuid);
    $stmt->execute();
    $result = $stmt->get_result();
    $value = $result->fetch_object();

    return $value;
}

function getPostComponentItemsByBlogId(mysqli $conn, int $blogId)
{
    $result = $conn->query("SELECT id, uuid, blogId, componentId, outputOrder, type, content, isRequired FROM ComponentItems WHERE blogId=$blogId ORDER BY blogId, componentId, outputOrder");

    return $result;
}

function getBlogComponents(mysqli $conn, int $blogId)
{
    $result = $conn->query("SELECT id, uuid, blogId, outputOrder, type, content FROM Components WHERE blogId=$blogId ORDER BY outputOrder");

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

function updateBlogComponent(mysqli $conn, int $id, int $outputOrder, string $content)
{

    $stmt = $conn->prepare("UPDATE components SET outputOrder=?, content=? WHERE id=?");
    $stmt->bind_param("isi", $outputOrder, $content, $id);
    $stmt->execute();
}

function deleteBlogComponent(mysqli $conn, int $id)
{
    $stmt = $conn->prepare("DELETE FROM components WHERE Id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

function deletePostComponentsByBlogId(mysqli $conn, int $blogId)
{
    $stmt = $conn->prepare("DELETE FROM components WHERE BlogId=?");
    $stmt->bind_param("i", $blogId);
    $stmt->execute();
}

function deletePostComponentItemsByBlogId(mysqli $conn, int $blogId)
{
    $stmt = $conn->prepare("DELETE FROM componentItems WHERE BlogId=?");
    $stmt->bind_param("i", $blogId);
    $stmt->execute();
}

function deleteBlogById(mysqli $conn, int $id)
{
    $stmt = $conn->prepare("DELETE FROM blogs WHERE Id=?");
    $stmt->bind_param("i", $id);
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
