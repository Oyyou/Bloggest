<?php

session_start();

include("database.php");

$sql = "SELECT * FROM Blogs";
$result = $conn->query($sql);
$count = $result->num_rows;
$rand = rand(0, $count - 1);

$result->data_seek($rand);
$datarow = $result->fetch_array();
$obj = $datarow;
print $obj;
header("location: /blog.php?id=".$obj[0]);
exit;
?>
