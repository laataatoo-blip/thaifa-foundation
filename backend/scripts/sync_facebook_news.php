<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Bangkok');

require_once __DIR__ . '/../classes/DatabaseManagement.class.php';
require_once __DIR__ . '/../classes/FacebookNewsSync.class.php';

try {
    $DB = new DatabaseManagement();
    $sync = new FacebookNewsSync($DB);
    $result = $sync->sync(20);

    echo sprintf(
        "[%s] Facebook sync done | created=%d updated=%d skipped=%d total=%d\n",
        date('Y-m-d H:i:s'),
        (int)$result['created'],
        (int)$result['updated'],
        (int)$result['skipped'],
        (int)$result['total']
    );
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, sprintf("[%s] Facebook sync failed: %s\n", date('Y-m-d H:i:s'), $e->getMessage()));
    exit(1);
}
