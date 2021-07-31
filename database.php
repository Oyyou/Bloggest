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

function getBlogById(mysqli $conn, int $id)
{
    $stmt = $conn->prepare("SELECT id, userId, title, subTitle, body, tags FROM Blogs where id=? LIMIT 1");

    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $value = $result->fetch_object();

    return $value;
}
