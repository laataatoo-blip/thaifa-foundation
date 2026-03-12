<?php
if (!class_exists('AnalyticsManagement')) {
    include_once(__DIR__ . '/../classes/AnalyticsManagement.class.php');
}
try {
    $analyticsTracker = new AnalyticsManagement();
    $analyticsTracker->trackCurrentRequest();
} catch (Throwable $e) {
    // ignore tracking failure
}
