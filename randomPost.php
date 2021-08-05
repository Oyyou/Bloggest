<?php

session_start();

include("database.php");
$conn = getConnection();

$sql = "SELECT * FROM Blogs";
$result = $conn->query($sql);
$count = $result->num_rows;
$rand = rand(0, $count - 1);

$result->data_seek($rand);
$datarow = $result->fetch_array();
$obj = $datarow;

$conn->close();

header("location: /post/".$obj[0]);
exit;
?>
