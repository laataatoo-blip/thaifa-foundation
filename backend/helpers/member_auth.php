<?php

if (!class_exists('MemberAuth')) {
    include_once(__DIR__ . '/../classes/MemberAuth.class.php');
}

function thaifaMemberAuth()
{
    static $instance = null;
    if ($instance === null) {
        $instance = new MemberAuth();
    }
    return $instance;
}

function thaifaMember()
{
    return thaifaMemberAuth()->currentMember();
}

function thaifaMemberId()
{
    $member = thaifaMember();
    return (int)($member['id'] ?? 0);
}

function thaifaMemberDisplayName($fallback = '')
{
    $member = thaifaMember();
    if (!$member) {
        return $fallback;
    }
    return trim((string)($member['first_name'] ?? '') . ' ' . (string)($member['last_name'] ?? ''));
}

function thaifaRequireMember($nextUrl = '')
{
    $member = thaifaMember();
    if ($member) {
        return $member;
    }

    if ($nextUrl === '') {
        $nextUrl = $_SERVER['REQUEST_URI'] ?? 'index.php';
    }

    header('Location: login.php?next=' . urlencode($nextUrl));
    exit;
}
