<?php
$conn = new mysqli("localhost", "root", "", "mokti_login");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get and sanitize user inputs to prevent XSS
$username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
$phone = htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8');
$address = htmlspecialchars($_POST['address'], ENT_QUOTES, 'UTF-8');
$password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');

// Prepare the SQL query using placeholders for SQL injection prevention
$sql = "INSERT INTO admins (username, email, phone, address, password, role) 
        VALUES (?, ?, ?, ?, ?, ?)";

// Prepare the statement
$stmt = $conn->prepare($sql);

// Check if the statement was prepared successfully
if ($stmt === false) {
    die("Error preparing the SQL query: " . $conn->error);
}

// Bind the sanitized inputs to the placeholders
// "ssssss" means string parameters (for username, email, phone, address, and role)
// "s" means string parameter (for password)
$stmt->bind_param("ssssss", $username, $email, $phone, $address, $password, $role);

// Set the role (since it's fixed as 'admin')
$role = 'admin';

// Execute the statement
if ($stmt->execute()) {
    header("Location: login_admin.html");
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
