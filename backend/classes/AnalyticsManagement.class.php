<?php

if (!class_exists('DatabaseManagement')) {
    include_once(__DIR__ . '/DatabaseManagement.class.php');
}
if (!class_exists('FoundationSchema')) {
    include_once(__DIR__ . '/FoundationSchema.class.php');
}

class AnalyticsManagement
{
    private $db;

    public function __construct()
    {
        $this->db = new DatabaseManagement();
        FoundationSchema::ensure($this->db);
    }

    public function trackCurrentRequest()
    {
        if (php_sapi_name() === 'cli') return;

        $path = parse_url((string)($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
        $path = $this->normalizePath((string)$path);
        if ($path === '') return;
        if (preg_match('#(^|/)admin/#i', $path)) return;
        if (preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf)$/i', $path)) return;

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $sessionKey = session_id() ?: substr(sha1(uniqid((string)mt_rand(), true)), 0, 24);
        $ip = $this->getClientIp();
        $ipHash = hash('sha256', $ip . '|thaifa-analytics');
        $ref = $this->normalizeReferrer((string)($_SERVER['HTTP_REFERER'] ?? ''));
        $ua = substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);

        $dedupeKey = 'view_' . md5($path);
        $now = time();
        $last = (int)($_SESSION[$dedupeKey] ?? 0);
        if ($last > 0 && ($now - $last) < 900) {
            return;
        }
        $_SESSION[$dedupeKey] = $now;

        $this->db->query(
            "INSERT INTO analytics_pageviews(view_date, page_path, referrer, user_agent, ip_hash, session_key)
             VALUES (CURDATE(), :page_path, :referrer, :user_agent, :ip_hash, :session_key)",
            [
                ':page_path' => $path,
                ':referrer' => $ref,
                ':user_agent' => $ua,
                ':ip_hash' => $ipHash,
                ':session_key' => $sessionKey,
            ]
        );
    }

    private function envValue($key, $default = '')
    {
        $v = getenv($key);
        if ($v !== false && $v !== null && $v !== '') return (string)$v;
        if (isset($_ENV[$key]) && $_ENV[$key] !== '') return (string)$_ENV[$key];
        if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') return (string)$_SERVER[$key];
        return (string)$default;
    }

    private function canonicalSiteHost()
    {
        $raw = trim($this->envValue('SITE_URL', $this->envValue('APP_URL', $this->envValue('PUBLIC_SITE_URL', ''))));
        if ($raw !== '') {
            $h = parse_url($raw, PHP_URL_HOST);
            if (is_string($h) && $h !== '') {
                return strtolower($h);
            }
        }
        $host = trim((string)($_SERVER['HTTP_HOST'] ?? ''));
        if ($host !== '') {
            return strtolower(preg_replace('/:\d+$/', '', $host));
        }
        return '';
    }

    private function normalizeReferrer($rawRef)
    {
        $rawRef = trim((string)$rawRef);
        if ($rawRef === '') return '';

        $parts = @parse_url($rawRef);
        if (!is_array($parts)) {
            return mb_substr($rawRef, 0, 255, 'UTF-8');
        }

        $host = strtolower((string)($parts['host'] ?? ''));
        $path = trim((string)($parts['path'] ?? ''));
        $query = trim((string)($parts['query'] ?? ''));

        if ($path === '') {
            $path = '/';
        }

        $canonicalHost = $this->canonicalSiteHost();
        $currentHost = strtolower((string)preg_replace('/:\d+$/', '', (string)($_SERVER['HTTP_HOST'] ?? '')));
        $isInternal = $host !== '' && (
            $host === $canonicalHost ||
            $host === $currentHost ||
            $host === 'localhost' ||
            $host === '127.0.0.1'
        );

        if ($isInternal) {
            return mb_substr($this->normalizePath($path) . ($query !== '' ? '?' . $query : ''), 0, 255, 'UTF-8');
        }

        if ($host !== '') {
            return mb_substr($host . $path, 0, 255, 'UTF-8');
        }

        return mb_substr($rawRef, 0, 255, 'UTF-8');
    }

    private function normalizePageOrRefForReport($value)
    {
        $value = trim((string)$value);
        if ($value === '') {
            return '';
        }

        if (preg_match('#^https?://#i', $value)) {
            return $this->normalizeReferrer($value);
        }

        $hostLike = preg_match('/^[a-z0-9.-]+\.[a-z]{2,}(\/|$)/i', $value) === 1;
        if ($hostLike) {
            return mb_substr($value, 0, 255, 'UTF-8');
        }

        if ($value[0] !== '/') {
            return '/' . ltrim($value, '/');
        }

        return $value;
    }

    private function aggregateTopPages(array $rows)
    {
        $bucket = [];
        foreach ($rows as $r) {
            $key = $this->normalizePageOrRefForReport((string)($r['page_path'] ?? ''));
            if ($key === '') {
                continue;
            }
            if (!isset($bucket[$key])) {
                $bucket[$key] = [
                    'page_path' => $key,
                    'views' => 0,
                    'visitors' => 0,
                ];
            }
            $bucket[$key]['views'] += (int)($r['views'] ?? 0);
            $bucket[$key]['visitors'] += (int)($r['visitors'] ?? 0);
        }

        usort($bucket, function ($a, $b) {
            return ((int)$b['views']) <=> ((int)$a['views']);
        });

        return array_slice(array_values($bucket), 0, 12);
    }

    private function aggregateTopReferrers(array $rows)
    {
        $bucket = [];
        foreach ($rows as $r) {
            $key = $this->normalizePageOrRefForReport((string)($r['referrer'] ?? ''));
            if ($key === '') {
                continue;
            }
            if (!isset($bucket[$key])) {
                $bucket[$key] = [
                    'referrer' => $key,
                    'views' => 0,
                ];
            }
            $bucket[$key]['views'] += (int)($r['views'] ?? 0);
        }

        usort($bucket, function ($a, $b) {
            return ((int)$b['views']) <=> ((int)$a['views']);
        });

        return array_slice(array_values($bucket), 0, 8);
    }

    private function getClientIp()
    {
        $candidates = [];

        $headers = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_REAL_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_CLIENT_IP',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $header) {
            $raw = trim((string)($_SERVER[$header] ?? ''));
            if ($raw === '') {
                continue;
            }
            foreach (explode(',', $raw) as $part) {
                $ip = trim($part);
                if ($ip !== '') {
                    $candidates[] = $ip;
                }
            }
        }

        foreach ($candidates as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }

        foreach ($candidates as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }

        return '0.0.0.0';
    }

    private function normalizePath($path)
    {
        $path = trim((string)$path);
        if ($path === '') {
            return '';
        }

        $path = preg_replace('#/+#', '/', $path);

        if (preg_match('#^(.+?\.php)(?:/.*)?$#i', $path, $m)) {
            $path = $m[1];
        }

        if ($path[0] !== '/') {
            $path = '/' . $path;
        }

        return $path;
    }

    public function overview($days = 7)
    {
        $days = max(1, (int)$days);
        $summary = $this->db->selectOne(
            "SELECT COUNT(*) AS views,
                    COUNT(DISTINCT ip_hash) AS visitors,
                    COUNT(DISTINCT session_key) AS sessions
             FROM analytics_pageviews
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)"
        );

        $daily = $this->db->selectAll(
            "SELECT view_date, COUNT(*) AS views, COUNT(DISTINCT ip_hash) AS visitors
             FROM analytics_pageviews
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
             GROUP BY view_date
             ORDER BY view_date ASC"
        );

        $topPagesRaw = $this->db->selectAll(
            "SELECT page_path, COUNT(*) AS views, COUNT(DISTINCT ip_hash) AS visitors
             FROM analytics_pageviews
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
             GROUP BY page_path
             ORDER BY views DESC
             LIMIT 300"
        );

        $topRefRaw = $this->db->selectAll(
            "SELECT referrer, COUNT(*) AS views
             FROM analytics_pageviews
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
               AND referrer <> ''
             GROUP BY referrer
             ORDER BY views DESC
             LIMIT 300"
        );

        $topPages = $this->aggregateTopPages($topPagesRaw);
        $topRef = $this->aggregateTopReferrers($topRefRaw);

        return [
            'views' => (int)($summary['views'] ?? 0),
            'visitors' => (int)($summary['visitors'] ?? 0),
            'sessions' => (int)($summary['sessions'] ?? 0),
            'daily' => $daily,
            'top_pages' => $topPages,
            'top_referrers' => $topRef,
        ];
    }
}
