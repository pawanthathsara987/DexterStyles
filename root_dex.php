<?php

$conn =  mysqli_connect('localhost','root','','dexterstyles');

if ($conn ->connect_error) {
    die("Connection  failed: " . $conn->connect_error);
}

// Select database
$conn->select_db("dexterstyles");

?>
