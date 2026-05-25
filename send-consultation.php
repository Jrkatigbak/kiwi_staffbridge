<?php
header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$name = trim($_POST['full_name'] ?? '');
$company = trim($_POST['company_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$employees = trim($_POST['employees'] ?? '');
$service = trim($_POST['service_needed'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '') {
    echo json_encode(['success' => false, 'message' => 'Name and email are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

try {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'inquiry@staffbridgeph.com';
    $mail->Password = 'zcpwhezttydpxfsk';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('inquiry@staffbridgeph.com', 'Staffbridge Website');
    $mail->addAddress('inquiry@staffbridgeph.com', 'Staffbridge Admin');
    $mail->addReplyTo($email, $name);

    $mail->isHTML(true);
    $mail->Subject = 'Consultation Request from ' . $name;

    $mail->Body = '
        <h2>New Consultation Request</h2>
        <p><strong>Name:</strong> ' . htmlspecialchars($name) . '</p>
        <p><strong>Company:</strong> ' . htmlspecialchars($company) . '</p>
        <p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>
        <p><strong>Employees:</strong> ' . htmlspecialchars($employees) . '</p>
        <p><strong>Service Needed:</strong> ' . htmlspecialchars($service) . '</p>
        <p><strong>Message:</strong><br>' . nl2br(htmlspecialchars($message)) . '</p>
    ';

    $mail->AltBody =
        "New Consultation Request\n\n" .
        "Name: {$name}\n" .
        "Company: {$company}\n" .
        "Email: {$email}\n" .
        "Employees: {$employees}\n" .
        "Service Needed: {$service}\n\n" .
        "Message:\n{$message}";

    $mail->send();

    echo json_encode([
        'success' => true,
        'message' => 'Your consultation request has been sent.'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Mailer Error: ' . $mail->ErrorInfo
    ]);
}