<?php
// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer classes (adjust the path as necessary)
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mokti_login";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if 'payment_id' is set in the URL
if (isset($_GET['payment_id'])) {
    $paymentId = intval($_GET['payment_id']);

    // Fetch payment details including created_at
    $stmt = $conn->prepare("SELECT name, card_number, expiry, cvv, created_at FROM payments WHERE id = ?");
    $stmt->bind_param("i", $paymentId);
    $stmt->execute();
    $stmt->bind_result($name, $cardNumber, $expiry, $cvv, $paymentCreatedAt);
    $stmt->fetch();
    $stmt->close();

    if ($name) {
        // Fetch latest order details
        $query = $conn->prepare("SELECT * FROM orders ORDER BY order_id DESC LIMIT 1");
        $query->execute();
        $result = $query->get_result();
        $order = $result->num_rows > 0 ? $result->fetch_assoc() : null;

        // Handle email submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
            $toEmail = $_POST['email'];

            // Create PHPMailer instance
            $mail = new PHPMailer(true);

            try {
                // SMTP settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Use Gmail's SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'dayangnurazmina1@gmail.com'; // Your Gmail address
                $mail->Password = 'tyog xhig pnkn pyen';   // Your Gmail App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Add this to handle SSL issues
                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true,
                    ],
                ];

                // Email settings
                $mail->setFrom('dayangnurazmina1@gmail.com', 'Mokti'); // Sender email and name
                $mail->addAddress($toEmail); // Recipient email
                $mail->isHTML(true);
                $mail->Subject = 'Your Receipt from Mokti';

                // Build email content
                $emailContent = "
                    <h1>Thanks for buying!</h1>
                    <p><strong>Name on Card:</strong> $name</p>
                    <p><strong>Card Number:</strong> **** **** **** " . substr($cardNumber, -4) . "</p>
                    <p><strong>Expiry Date:</strong> $expiry</p>
                    <p><strong>Payment Date:</strong> " . date('F j, Y, g:i a', strtotime($paymentCreatedAt)) . "</p>";

                if ($order) {
                    $emailContent .= "
                    <p><strong>Order ID:</strong> " . htmlspecialchars($order['order_id']) . "</p>
                    <p><strong>User ID:</strong> " . htmlspecialchars($order['user_id']) . "</p>
                    <p><strong>Items:</strong> " . htmlspecialchars($order['items']) . "</p>
                    <p><strong>Subtotal:</strong> $" . number_format($order['subtotal'], 2) . "</p>
                    <p><strong>Order Date:</strong> " . date('F j, Y, g:i a', strtotime($order['created_at'])) . "</p>";
                }

                $mail->Body = $emailContent;

                // Send email
                $mail->send();
                echo "<p style='color: green;'>Receipt emailed successfully to $toEmail</p>";
            } catch (Exception $e) {
                echo "<p style='color: red;'>Failed to send email. Error: {$mail->ErrorInfo}</p>";
            }
        }

        // Render receipt page
        echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Receipt</title>
    <style>
        body {
            background: linear-gradient(145deg, #8B0000, #A52A2A);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: 'Arial', sans-serif;
            color: #fff;
        }
        .container {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            width: 85%;
            max-width: 600px;
            text-align: center;
        }
        h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
            color: #FFD700;
        }
        .receipt {
            text-align: left;
            margin: 20px 0;
            font-size: 1.2em;
        }
        .receipt p {
            margin: 5px 0;
        }
        form {
            margin-top: 20px;
        }
        input[type='email'] {
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 10px;
        }
        .buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        button {
            padding: 10px 20px;
            font-size: 1em;
            background-color: #D32F2F;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #B71C1C;
        }
        a button {
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>Thanks for buying!</h1>
        <div class='receipt'>
            <p><strong>Name on Card:</strong> $name</p>
            <p><strong>Card Number:</strong> **** **** **** " . substr($cardNumber, -4) . "</p>
            <p><strong>Expiry Date:</strong> $expiry</p>
            <p><strong>Payment Date:</strong> " . date('F j, Y, g:i a', strtotime($paymentCreatedAt)) . "</p>";

        if ($order) {
            echo "<p><strong>Order ID:</strong> " . htmlspecialchars($order['order_id']) . "</p>";
            echo "<p><strong>User ID:</strong> " . htmlspecialchars($order['user_id']) . "</p>";
            echo "<p><strong>Items:</strong> " . htmlspecialchars($order['items']) . "</p>";
            echo "<p><strong>Subtotal:</strong> $" . number_format($order['subtotal'], 2) . "</p>";
            echo "<p><strong>Order Date:</strong> " . date('F j, Y, g:i a', strtotime($order['created_at'])) . "</p>";
        }

        echo "</div>
        <form method='POST'>
            <input type='email' name='email' placeholder='Enter your email' required>
            <button type='submit'>Email Receipt</button>
        </form>
        <div class='buttons'>
            <button onclick='window.print()'>Print Receipt</button>
            <a href='download_pdf.php?payment_id=$paymentId' target='_blank'>
                <button>Download PDF</button>
            </a>
            <a href='dashboard.html'>
                <button>Go to Dashboard</button>
            </a>
        </div>
    </div>
</body>
</html>";
    } else {
        echo "<p>No payment found.</p>";
    }
} else {
    echo "<h3>Invalid request</h3>";
    echo "<a href='make_payment.html'>Make a Payment</a>";
}

$conn->close();
?>
