<?php
$conn = new mysqli("localhost", "root", "", "tyongkoy_furnitures");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Hash the password for security
$hashed_password = password_hash("112233", PASSWORD_BCRYPT);

// Insert admin account (or update if it exists)
$sql = "INSERT INTO admins (username, password) 
        VALUES ('admin1', '$hashed_password') 
        ON DUPLICATE KEY UPDATE password='$hashed_password'";

if ($conn->query($sql) === TRUE) {
    echo "Admin account inserted/updated successfully!";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
