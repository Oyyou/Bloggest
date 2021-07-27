<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "molfdb";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("MySQL connection failed: " . $conn->connect_error);
    }
?>