<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "molfdb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("MySQL connection failed: " . $conn->connect_error);
}

function getUserbyName(mysqli $conn, string $name)
{
    $sql = "SELECT Username FROM Users Where Username=? limit 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    $value = $result->fetch_object();

    return $value;
}

function getUserbyId(mysqli $conn, string $id)
{
    $stmt = $conn->prepare("SELECT Id, Username, Email FROM Users Where Id=? limit 1");
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

function getUserbyUsername(mysqli $conn, string $username)
{
    $stmt = $conn->prepare("SELECT Username, Title FROM Users Where Username=? limit 1");
    $stmt->bind_param("s", $username);
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

function getBlogById(mysqli $conn, int $id)
{
    $stmt = $conn->prepare("SELECT id, userId, title, subTitle, body, tags FROM Blogs where id=? LIMIT 1");

    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $value = $result->fetch_object();

    return $value;
}
