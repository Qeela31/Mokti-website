<?php
require('fpdf/fpdf.php');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mokti_login";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if 'payment_id' is set in the URL
if (isset($_GET['payment_id'])) {
    $paymentId = intval($_GET['payment_id']);

    // Fetch payment details
    $stmt = $conn->prepare("SELECT name, card_number, expiry, created_at FROM payments WHERE id = ?");
    $stmt->bind_param("i", $paymentId);
    $stmt->execute();
    $stmt->bind_result($name, $cardNumber, $expiry, $paymentCreatedAt);
    $stmt->fetch();
    $stmt->close();

    // Fetch order details
    $query = $conn->prepare("SELECT * FROM orders ORDER BY order_id DESC LIMIT 1");
    $query->execute();
    $result = $query->get_result();
    $order = $result->num_rows > 0 ? $result->fetch_assoc() : null;

    // Create PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);

    $pdf->Cell(40, 10, 'Receipt');
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, "Name on Card: $name", 0, 1);
    $pdf->Cell(0, 10, "Card Number: **** **** **** " . substr($cardNumber, -4), 0, 1);
    $pdf->Cell(0, 10, "Expiry Date: $expiry", 0, 1);
    $pdf->Cell(0, 10, "Payment Date: " . date('F j, Y, g:i a', strtotime($paymentCreatedAt)), 0, 1);

    if ($order) {
        $pdf->Cell(0, 10, "Order ID: " . $order['order_id'], 0, 1);
        $pdf->Cell(0, 10, "User ID: " . $order['user_id'], 0, 1);
        $pdf->Cell(0, 10, "Items: " . $order['items'], 0, 1);
        $pdf->Cell(0, 10, "Subtotal: $" . number_format($order['subtotal'], 2), 0, 1);
        $pdf->Cell(0, 10, "Order Date: " . date('F j, Y, g:i a', strtotime($order['created_at'])), 0, 1);
    }

    $pdf->Output();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
