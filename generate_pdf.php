<?php
require('fpdf/fpdf.php');

// Database connection
$conn = new mysqli("localhost", "root", "", "mokti_login");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch latest order
$query = $conn->prepare("SELECT * FROM orders ORDER BY order_id DESC LIMIT 1");
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $order = $result->fetch_assoc();

    // Create PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);

    $pdf->Cell(0, 10, 'Receipt', 0, 1, 'C');
    $pdf->Ln(10);

    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Order ID: ' . $order['order_id'], 0, 1);
    $pdf->Cell(0, 10, 'User ID: ' . $order['user_id'], 0, 1);
    $pdf->Cell(0, 10, 'Items: ' . $order['items'], 0, 1);
    $pdf->Cell(0, 10, 'Subtotal: $' . number_format($order['subtotal'], 2), 0, 1);

    $pdf->Output('D', 'receipt.pdf'); // Force download as 'receipt.pdf'
} else {
    echo "No receipt found.";
}

$conn->close();
?>
