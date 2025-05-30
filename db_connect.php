<?php
$conn = new mysqli("localhost", "root", "", "tyongkoy_furnitures");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>