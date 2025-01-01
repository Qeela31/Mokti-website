<?php
require('fpdf/fpdf.php'); // Include FPDF library

// Database connection
$conn = new mysqli("localhost", "root", "", "mokti_login");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all orders (you can modify the query if you want to filter by date)
$query = "SELECT order_id, user_id, items, subtotal, order_date FROM orders ORDER BY order_date DESC";
$result = $conn->query($query);

// Create a new FPDF instance
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Title of the PDF
$pdf->Cell(200, 10, 'Transaction Summary', 0, 1, 'C');

// Table headers
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(30, 10, 'Order ID', 1);
$pdf->Cell(30, 10, 'User ID', 1);
$pdf->Cell(50, 10, 'Items', 1);
$pdf->Cell(30, 10, 'Subtotal', 1);
$pdf->Cell(50, 10, 'Order Date', 1);
$pdf->Ln();

// Table content
$pdf->SetFont('Arial', '', 12);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(30, 10, $row['order_id'], 1);
        $pdf->Cell(30, 10, $row['user_id'], 1);
        $pdf->Cell(50, 10, $row['items'], 1);
        $pdf->Cell(30, 10, "$" . number_format($row['subtotal'], 2), 1);
        $pdf->Cell(50, 10, $row['order_date'], 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(200, 10, 'No records found', 0, 1, 'C');
}

// Output the PDF
$pdf->Output('D', 'transaction_summary.pdf');

// Close the database connection
$conn->close();
?>
