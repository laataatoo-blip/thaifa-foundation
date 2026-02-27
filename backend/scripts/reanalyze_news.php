<?php
date_default_timezone_set('Asia/Bangkok');

include_once(__DIR__ . '/../classes/DatabaseManagement.class.php');
include_once(__DIR__ . '/../classes/NewsContentAnalyzer.class.php');

$DB = new DatabaseManagement();

$rows = $DB->selectAll("SELECT id, title, detail, category, source_post_url FROM news ORDER BY id ASC");
$updated = 0;

foreach ($rows as $row) {
    $id = (int)($row['id'] ?? 0);
    if ($id <= 0) continue;

    $oldTitle = (string)($row['title'] ?? '');
    $oldDetail = (string)($row['detail'] ?? '');
    $oldCategory = (string)($row['category'] ?? '');
    $sourceUrl = (string)($row['source_post_url'] ?? '');

    $newTitle = NewsContentAnalyzer::normalizeNewsTitle($oldTitle, $oldDetail);
    $newDetail = NewsContentAnalyzer::simplifyDetail($oldDetail);
    $newCategory = NewsContentAnalyzer::detectCategory($newTitle, $newDetail, $sourceUrl, $oldCategory);

    if ($newTitle !== $oldTitle || $newDetail !== $oldDetail || $newCategory !== $oldCategory) {
        $DB->query(
            "UPDATE news
             SET title = :title, detail = :detail, category = :category
             WHERE id = :id
             LIMIT 1",
            [
                ':title' => $newTitle,
                ':detail' => $newDetail,
                ':category' => $newCategory,
                ':id' => $id
            ]
        );
        $updated++;
    }
}

echo '[' . date('Y-m-d H:i:s') . '] Reanalyze done. total=' . count($rows) . ' updated=' . $updated . PHP_EOL;

