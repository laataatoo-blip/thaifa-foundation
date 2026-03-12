<?php
include_once(__DIR__ . '/backend/helpers/member_auth.php');

$auth = thaifaMemberAuth();
$auth->logout();

$next = trim((string)($_GET['next'] ?? 'index.php'));
if ($next === '' || preg_match('/^https?:\/\//i', $next) || strpos($next, '//') !== false) {
    $next = 'index.php';
}

header('Location: ' . ltrim($next, '/'));
exit;
