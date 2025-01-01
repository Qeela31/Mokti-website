<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Collect POST data and sanitize inputs
    $name = htmlspecialchars(trim($_POST['name']));
    $cardNumber = htmlspecialchars(trim($_POST['cardNumber']));
    $expiry = htmlspecialchars(trim($_POST['expiry']));
    $cvv = htmlspecialchars(trim($_POST['cvv']));

    // Validate input fields
    $errors = [];

    if (empty($name)) {
        $errors[] = "Name on card is required.";
    }

    if (!preg_match("/^\d{16}$/", $cardNumber)) {
        $errors[] = "Card number must be 16 digits.";
    }

    if (!preg_match("/^(0[1-9]|1[0-2])\/\d{2}$/", $expiry)) {
        $errors[] = "Expiry date must be in MM/YY format.";
    }

    if (!preg_match("/^\d{3}$/", $cvv)) {
        $errors[] = "CVV must be 3 digits.";
    }

    // Check for validation errors
    if (!empty($errors)) {
        echo "<h3>Payment Failed</h3><ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul><a href='javascript:history.back()'>Go Back</a>";
        exit;
    }

    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "mokti_login";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert data into the `payments` table
    $stmt = $conn->prepare("INSERT INTO payments (name, card_number, expiry, cvv) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $cardNumber, $expiry, $cvv);
    
    if ($stmt->execute()) {
        // Get the last inserted ID
        $paymentId = $stmt->insert_id;
    
        // Redirect to receipt page with payment ID
        header("Location:receipt.php?payment_id=$paymentId");
        exit;
    }
    if ($stmt->execute()) {
        echo "<h3>Payment Successful!</h3>";
        echo "<p>Thank you for your payment, $name.</p>";
        echo "<div style='margin-bottom: 10px;'><a href='browse_product.html' style='display: inline-block; padding: 10px; background-color: #e74c3c; color: white; text-decoration: none; border-radius: 5px;'>Back to Products</a></div>";
        echo "<div><a href='receipt.html' style='display: inline-block; padding: 10px; background-color:rgb(250, 38, 38); color: white; text-decoration: none; border-radius: 5px;'>View Receipt</a></div>";
    } else {
        echo "<h3>Payment Failed</h3>";
        echo "<p>Failed to store payment information. Please try again.</p>";
        echo "<a href='javascript:history.back()'>Go Back</a>";
    }

    $stmt->close();
    $conn->close();
} else {
    // Redirect to the form page if accessed without POST data
    header("Location: make_payment.html");
    exit;
}

?>