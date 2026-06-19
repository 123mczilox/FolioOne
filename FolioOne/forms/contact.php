<?php
// Set error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form data and sanitize
    $name = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
    $email = isset($_POST['email']) ? trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)) : '';
    $subject = isset($_POST['subject']) ? trim(strip_tags($_POST['subject'])) : '';
    $message = isset($_POST['message']) ? trim(strip_tags($_POST['message'])) : '';

    // Validate required fields
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        echo 'All fields are required';
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo 'Invalid email format';
        exit;
    }

    // Email configuration
    $to = 'wanjohizidane14@gmail.com';
    $email_subject = "Portfolio Contact: " . $subject;

    // Create email headers
    $headers = "From: " . $email . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    // Create email body
    $email_body = "
    <html>
    <head>
        <title>New Contact Form Submission</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #FFB800; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f9f9f9; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #555; }
            .value { background: white; padding: 10px; border-radius: 4px; border: 1px solid #ddd; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>New Contact Form Submission</h2>
                <p>You have received a new message from your portfolio website</p>
            </div>
            <div class='content'>
                <div class='field'>
                    <div class='label'>Name:</div>
                    <div class='value'>" . htmlspecialchars($name) . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Email:</div>
                    <div class='value'>" . htmlspecialchars($email) . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Subject:</div>
                    <div class='value'>" . htmlspecialchars($subject) . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Message:</div>
                    <div class='value'>" . nl2br(htmlspecialchars($message)) . "</div>
                </div>
                <hr>
                <p><small>This message was sent from your portfolio contact form on " . date('Y-m-d H:i:s') . "</small></p>
            </div>
        </div>
    </body>
    </html>
    ";

    // Send email
    try {
        // Check if running on localhost/development
        $is_localhost = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);
        
        if ($is_localhost) {
            // Development mode: Save to file instead of sending
            $logs_dir = __DIR__ . '/logs';
            if (!is_dir($logs_dir)) {
                mkdir($logs_dir, 0755, true);
            }
            
            $log_file = $logs_dir . '/submissions_' . date('Y-m-d') . '.txt';
            $log_entry = "=== " . date('Y-m-d H:i:s') . " ===\n";
            $log_entry .= "Name: $name\n";
            $log_entry .= "Email: $email\n";
            $log_entry .= "Subject: $subject\n";
            $log_entry .= "Message:\n$message\n";
            $log_entry .= "---\n\n";
            
            file_put_contents($log_file, $log_entry, FILE_APPEND);
            echo 'OK'; // Same response for smooth user experience
        } else {
            // Production mode: Send actual email
            if (mail($to, $email_subject, $email_body, $headers)) {
                echo 'OK';
            } else {
                echo 'Failed to send message. Please try again later.';
            }
        }
    } catch (Exception $e) {
        echo 'An error occurred: ' . $e->getMessage();
    }

} else {
    // If not POST request
    echo 'Method not allowed';
}
?>
