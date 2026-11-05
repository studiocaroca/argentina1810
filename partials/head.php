<?php
// Shared <head> for every Argentina 1810 page. Callers set these before
// including this file:
//   $pageTitle        (required) — used verbatim as <title> and og/twitter title
//   $pageDescription  (required) — meta description + og/twitter description
//   $i18nSrc          (required) — path to this track's translations.json, e.g. /cliente/translations.json
//   $canonicalPath    (optional) — path used for the canonical link, defaults to current request URI
if (!isset($pageTitle)) $pageTitle = 'Argentina 1810';
if (!isset($pageDescription)) $pageDescription = 'This is where Argentina begins.';
if (!isset($i18nSrc)) $i18nSrc = '/cliente/translations.json';
if (!isset($canonicalPath)) $canonicalPath = $_SERVER['REQUEST_URI'] ?? '/';
$canonicalUrl = 'https://www.argentina1810.com' . $canonicalPath;
?><!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php if (!empty($themeColor)): ?>
        <meta name="theme-color" content="<?= htmlspecialchars($themeColor) ?>">
    <?php endif; ?>
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta name="author" content="Argentina 1810">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">

    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta property="og:locale" content="es_AR">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($pageDescription) ?>">

    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="/assets/imgs/favicon.png">
    <?php
        // Cache-busting: the version query string changes whenever the file
        // itself changes, so browsers fetch the new CSS instead of serving a
        // stale cached copy indefinitely (these links had no versioning at
        // all before, which is why edits sometimes didn't show up live).
        $stylesCssVersion = @filemtime(__DIR__ . '/../assets/css/styles.css') ?: time();
        $siteCssVersion = @filemtime(__DIR__ . '/../assets/css/site.css') ?: time();
    ?>
    <link rel="stylesheet" href="/assets/css/styles.css?v=<?= $stylesCssVersion ?>">
    <link rel="stylesheet" href="/assets/css/site.css?v=<?= $siteCssVersion ?>">
</head>
