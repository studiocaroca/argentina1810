<?php
// Shared between the blog list and the single-post page.
$monthNames = [
    'es' => ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'],
    'en' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    'it' => ['gennaio', 'febbraio', 'marzo', 'aprile', 'maggio', 'giugno', 'luglio', 'agosto', 'settembre', 'ottobre', 'novembre', 'dicembre'],
];

function formatBlogDate($dateStr, $lang, $monthNames) {
    $ts = strtotime($dateStr);
    if (!$ts) return '';
    $day = (int) date('j', $ts);
    $month = $monthNames[$lang][(int) date('n', $ts) - 1] ?? '';
    $year = date('Y', $ts);
    if ($lang === 'en') return "$month $day, $year";
    if ($lang === 'it') return "$day $month $year";
    return "$day de $month de $year";
}
