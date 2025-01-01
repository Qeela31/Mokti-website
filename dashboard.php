<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: verymainpage.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p>You are now in the member dashboard.</p>
        <div class="buttons">
            <a href="edit_profile.php"><button>Edit Profile</button></a>
            <a href="browse_product.php"><button>Browse Product</button></a>
            <a href="verymainpage.html"><button>Log Out</button></a>
        </div>
    </div>
</body>
</html>
