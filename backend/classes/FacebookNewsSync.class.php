<?php
if (!class_exists('NewsContentAnalyzer')) {
    include_once(__DIR__ . '/NewsContentAnalyzer.class.php');
}

class FacebookNewsSync
{
    private $DB;
    private $pageId;
    private $accessToken;
    private $graphVersion;
    private $defaultAdminId;

    public function __construct($DB)
    {
        $this->DB = $DB;
        $this->pageId = trim((string)(getenv('FB_PAGE_ID') ?: ''));
        $this->accessToken = trim((string)(getenv('FB_PAGE_ACCESS_TOKEN') ?: ''));
        $this->graphVersion = trim((string)(getenv('FB_GRAPH_VERSION') ?: 'v21.0'));
        $this->defaultAdminId = (int)(getenv('FB_DEFAULT_ADMIN_ID') ?: 1);
    }

    public function validateConfig()
    {
        if ($this->pageId === '' || $this->accessToken === '') {
            throw new RuntimeException(
                'ยังไม่ได้ตั้งค่า FB_PAGE_ID หรือ FB_PAGE_ACCESS_TOKEN ใน environment'
            );
        }
    }

    public function sync($limit = 10)
    {
        $this->validateConfig();
        $this->ensureSchema();

        $limit = max(1, min(50, (int)$limit));

        $fields = implode(',', [
            'id',
            'message',
            'created_time',
            'permalink_url',
            'full_picture',
            'attachments{media_type,media,subattachments}'
        ]);

        $url = sprintf(
            'https://graph.facebook.com/%s/%s/posts?fields=%s&limit=%d&access_token=%s',
            rawurlencode($this->graphVersion),
            rawurlencode($this->pageId),
            rawurlencode($fields),
            $limit,
            rawurlencode($this->accessToken)
        );

        $payload = $this->httpGetJson($url);
        $posts = $payload['data'] ?? [];

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($posts as $post) {
            $fbPostId = trim((string)($post['id'] ?? ''));
            $createdAt = trim((string)($post['created_time'] ?? ''));
            if ($fbPostId === '' || $createdAt === '') {
                $skipped++;
                continue;
            }

            $message = trim((string)($post['message'] ?? ''));
            $permalink = trim((string)($post['permalink_url'] ?? ''));
            $postedDateTime = date('Y-m-d H:i:s', strtotime($createdAt));
            $postedDate = substr($postedDateTime, 0, 10);

            $title = $this->buildTitle($message, $postedDateTime);
            $detail = $this->buildDetail($message, $permalink, $fbPostId);
            $category = NewsContentAnalyzer::detectCategory($title, $detail, $permalink, 'ประชาสัมพันธ์');
            $images = $this->extractImages($post);

            $existing = $this->DB->selectOne(
                "SELECT id FROM news WHERE fb_post_id = :fb_post_id LIMIT 1",
                [':fb_post_id' => $fbPostId]
            );

            if ($existing && isset($existing['id'])) {
                $newsId = (int)$existing['id'];
                $this->DB->query(
                    "UPDATE news
                     SET title = :title,
                         detail = :detail,
                         category = :category,
                         posted_date = :posted_date,
                         source_published_at = :source_published_at,
                         source_post_url = :source_post_url,
                         is_visible = 1
                     WHERE id = :id
                     LIMIT 1",
                    [
                        ':title' => $title,
                        ':detail' => $detail,
                        ':category' => $category,
                        ':posted_date' => $postedDate,
                        ':source_published_at' => $postedDateTime,
                        ':source_post_url' => $permalink,
                        ':id' => $newsId
                    ]
                );
                $updated++;
            } else {
                $this->DB->query(
                    "INSERT INTO news
                        (title, detail, category, posted_date, admin_id, is_visible, source_published_at, source_post_url, fb_post_id)
                     VALUES
                        (:title, :detail, :category, :posted_date, :admin_id, 1, :source_published_at, :source_post_url, :fb_post_id)",
                    [
                        ':title' => $title,
                        ':detail' => $detail,
                        ':category' => $category,
                        ':posted_date' => $postedDate,
                        ':admin_id' => $this->defaultAdminId,
                        ':source_published_at' => $postedDateTime,
                        ':source_post_url' => $permalink,
                        ':fb_post_id' => $fbPostId
                    ]
                );
                $idRow = $this->DB->selectOne("SELECT LAST_INSERT_ID() AS id");
                $newsId = (int)($idRow['id'] ?? 0);
                if ($newsId <= 0) {
                    $skipped++;
                    continue;
                }
                $created++;
            }

            $this->syncNewsImages($newsId, $images);
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'total' => count($posts)
        ];
    }

    private function buildTitle($message, $postedDateTime)
    {
        $msg = trim((string)$message);
        $fallback = 'อัปเดตข่าวจาก Facebook วันที่ ' . date('d/m/Y', strtotime($postedDateTime));
        $line = $msg !== '' ? (preg_split('/\R/u', $msg)[0] ?? $msg) : $fallback;
        $line = NewsContentAnalyzer::normalizeNewsTitle($line, $msg);
        return $line !== '' ? $line : $fallback;
    }

    private function buildDetail($message, $permalink, $fbPostId)
    {
        $msg = trim((string)$message);
        $out = $msg !== '' ? NewsContentAnalyzer::simplifyDetail($msg) : 'โพสต์จากเพจ Facebook THAIFAFD';
        $out .= "\n\nที่มาโพสต์ Facebook:\n";
        if ($permalink !== '') {
            $out .= $permalink . "\n";
        }
        $out .= 'https://www.facebook.com/' . $fbPostId;
        return $out;
    }

    private function extractImages($post)
    {
        $images = [];
        $seen = [];

        if (!empty($post['full_picture'])) {
            $img = trim((string)$post['full_picture']);
            if ($img !== '') {
                $images[] = $img;
                $seen[$img] = true;
            }
        }

        $attachments = $post['attachments']['data'] ?? [];
        foreach ($attachments as $att) {
            $items = [];
            $items[] = $att;
            $subs = $att['subattachments']['data'] ?? [];
            foreach ($subs as $sub) {
                $items[] = $sub;
            }

            foreach ($items as $item) {
                $src = trim((string)($item['media']['image']['src'] ?? ''));
                if ($src !== '' && !isset($seen[$src])) {
                    $images[] = $src;
                    $seen[$src] = true;
                }
            }
        }

        return array_values($images);
    }

    private function syncNewsImages($newsId, $images)
    {
        $this->DB->query("DELETE FROM news_images WHERE news_id = :news_id", [':news_id' => (int)$newsId]);

        if (empty($images)) {
            return;
        }

        $sort = 1;
        foreach ($images as $img) {
            $this->DB->query(
                "INSERT INTO news_images (news_id, image_url, alt_text, sort_order)
                 VALUES (:news_id, :image_url, :alt_text, :sort_order)",
                [
                    ':news_id' => (int)$newsId,
                    ':image_url' => (string)$img,
                    ':alt_text' => 'ภาพประกอบข่าวจาก Facebook',
                    ':sort_order' => $sort++
                ]
            );
        }
    }

    private function httpGetJson($url)
    {
        $ctx = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: THAIFA-News-Sync/1.0\r\n",
                'timeout' => 30
            ]
        ]);

        $json = @file_get_contents($url, false, $ctx);
        if ($json === false || $json === '') {
            throw new RuntimeException('ไม่สามารถเรียก Facebook Graph API ได้');
        }

        $data = json_decode($json, true);
        if (!is_array($data)) {
            throw new RuntimeException('รูปแบบข้อมูลจาก Facebook ไม่ถูกต้อง');
        }

        if (isset($data['error'])) {
            $msg = (string)($data['error']['message'] ?? 'Facebook API error');
            throw new RuntimeException('Facebook API: ' . $msg);
        }

        return $data;
    }

    private function ensureSchema()
    {
        $this->ensureColumn(
            'category',
            "ALTER TABLE news ADD COLUMN category VARCHAR(100) NOT NULL DEFAULT 'General' AFTER detail"
        );

        $this->ensureColumn(
            'source_published_at',
            "ALTER TABLE news ADD COLUMN source_published_at DATETIME NULL AFTER posted_date"
        );
        $this->ensureColumn(
            'source_post_url',
            "ALTER TABLE news ADD COLUMN source_post_url VARCHAR(1024) NULL AFTER source_published_at"
        );
        $this->ensureColumn(
            'fb_post_id',
            "ALTER TABLE news ADD COLUMN fb_post_id VARCHAR(128) NULL AFTER source_post_url"
        );

        $idx = $this->DB->selectOne(
            "SELECT COUNT(*) AS c
             FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = 'news'
               AND INDEX_NAME = 'uniq_fb_post_id'"
        );
        if ((int)($idx['c'] ?? 0) === 0) {
            $this->DB->query("CREATE UNIQUE INDEX uniq_fb_post_id ON news (fb_post_id)");
        }
    }

    private function ensureColumn($columnName, $alterSql)
    {
        $row = $this->DB->selectOne(
            "SELECT COUNT(*) AS c
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = 'news'
               AND COLUMN_NAME = :column_name",
            [':column_name' => $columnName]
        );
        if ((int)($row['c'] ?? 0) === 0) {
            $this->DB->query($alterSql);
        }
    }
}
