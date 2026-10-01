<?php
// Contact form endpoint, shared by both /cliente/contacto.php (Free
// Explorer, B2C) and /partners/contacto.php (Partner Agency, B2B). A
// hidden `form_type` field ('explorer' | 'partner') picks the email
// subject/greeting; everything else is read as a flexible set of optional
// fields so one endpoint can serve both forms without duplicating the
// mail-sending logic.
//
// This endpoint only ever returns JSON — a PHP warning/notice printed
// to the response body (e.g. mail() failing locally, or on a host with
// display_errors on) would otherwise get prepended as raw HTML in front
// of the JSON, breaking any consumer that parses the body.
ini_set('display_errors', '0');

header('Content-Type: application/json; charset=utf-8');

function fail($message, $code = 400) {
    http_response_code($code);
    echo json_encode(['success' => false, 'error' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    fail('Método no permitido.', 405);
}

// Honeypot — a real visitor never sees this field (hidden off-screen in
// CSS and skipped in tab order, see .hp-field in site.css), but a simple
// bot that blindly fills every input trips it. Pretend success without
// actually sending anything.
if (!empty($_POST['empresa'])) {
    echo json_encode(['success' => true]);
    exit;
}

// Strips newlines from anything that ends up in a mail header — without
// this, a value like "x@x.com\nBcc: spamlist@..." could inject extra
// headers into the email (a classic mail-header-injection attack).
function clean_header_value($value) {
    return str_replace(["\r", "\n"], '', $value);
}

// Reads a field as trimmed text, or joins an array (checkbox groups like
// travel_vibe[]) into a comma-separated string.
function read_field($key) {
    $value = $_POST[$key] ?? '';
    if (is_array($value)) {
        return implode(', ', array_map('trim', array_filter($value, function ($v) { return trim($v) !== ''; })));
    }
    return trim($value);
}

$formType = trim($_POST['form_type'] ?? 'explorer') === 'partner' ? 'partner' : 'explorer';

$nombre = read_field('nombre');
$email = read_field('email');
$mensaje = read_field('message');

if ($nombre === '' || $email === '' || $mensaje === '') {
    fail('Completá todos los campos obligatorios.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fail('El email no es válido.');
}

// TODO(cliente): reemplazar por la casilla real de Argentina 1810 antes
// de publicar — este placeholder no está monitoreado.
$to = 'hola@argentina1810.com';

if ($formType === 'partner') {
    $subjectText = 'Nueva agencia socia — Argentina 1810 (Partner Agency)';
    $fields = [
        'Agencia' => read_field('agencia'),
        'País' => read_field('pais'),
        'Nombre de contacto' => $nombre,
        'Cargo' => read_field('cargo'),
        'Email' => $email,
        'Teléfono / WhatsApp' => read_field('telefono'),
        'Mercado' => read_field('mercado'),
    ];
} else {
    $subjectText = 'Nuevo viajero — Argentina 1810 (Free Explorer)';
    $fields = [
        'Nombre' => $nombre,
        'País de origen' => read_field('pais'),
        'Email' => $email,
        'Teléfono / WhatsApp' => read_field('telefono'),
        'Fechas aproximadas' => read_field('fechas'),
        'Número de viajeros' => read_field('viajeros'),
        'Travel Vibe' => read_field('travel_vibe'),
    ];
}

$subject = '=?UTF-8?B?' . base64_encode($subjectText) . '?=';

$body = '';
foreach ($fields as $label => $value) {
    if ($value !== '') $body .= "{$label}: {$value}\n";
}
$body .= "\nMensaje:\n{$mensaje}\n";

// The From address has to belong to the sending server's own domain, or
// most mail servers will flag or reject it as spoofed — it can't just be
// the destination address. Reply-To is the visitor's real address, so
// hitting "reply" goes straight to them.
$fromDomain = clean_header_value($_SERVER['SERVER_NAME'] ?? 'argentina1810.com');
$headers = [
    'From: Argentina 1810 <no-reply@' . $fromDomain . '>',
    'Reply-To: ' . clean_header_value($nombre) . ' <' . clean_header_value($email) . '>',
    'Content-Type: text/plain; charset=UTF-8',
];

// @-suppressed for the same reason as ini_set() above — a connection
// failure here (e.g. no local mail server) must not leak a warning into
// the JSON response; $sent below already handles the failure case.
$sent = @mail($to, $subject, $body, implode("\r\n", $headers));

if (!$sent) {
    fail('No se pudo enviar el mensaje. Probá de nuevo en un rato.', 500);
}

echo json_encode(['success' => true]);
