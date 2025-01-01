<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "mokti_login");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'];

    $delete = $conn->prepare("DELETE FROM cart WHERE id = ?");
    $delete->bind_param("i", $id);
    $delete->execute();

    echo "Product removed from cart!";
}
$conn->close();
?>
