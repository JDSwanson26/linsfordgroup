<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed.']);
    exit;
}

$to = 'info@linsfordgroup.co.za';

function clean_field($value) {
    $value = trim((string) ($value ?? ''));
    // Strip anything that could be used to inject extra mail headers.
    return str_replace(["\r", "\n"], '', $value);
}

$name = clean_field($_POST['name'] ?? '');
$email = clean_field($_POST['email'] ?? '');
$phone = clean_field($_POST['phone'] ?? '');
$subject = clean_field($_POST['subject'] ?? 'Website enquiry');
$message = trim((string) ($_POST['message'] ?? ''));
$honeypot = clean_field($_POST['website'] ?? '');

// Bots that fill in the hidden honeypot field get a fake success with no email sent.
if ($honeypot !== '') {
    echo json_encode(['success' => true]);
    exit;
}

if ($name === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'error' => 'Please provide your name, a valid email address, and a message.']);
    exit;
}

$body = "Name: $name\n";
if ($phone !== '') {
    $body .= "Phone: $phone\n";
}
$body .= "Email: $email\n\n$message";

$headers = [
    'From: Linsford Group Website <noreply@linsfordgroup.co.za>',
    'Reply-To: ' . $email,
    'X-Mailer: PHP/' . phpversion(),
];

$sent = mail($to, $subject, $body, implode("\r\n", $headers));

if ($sent) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Could not send your message. Please try again or call us directly.']);
}
