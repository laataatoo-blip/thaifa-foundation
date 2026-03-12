<?php

if (!class_exists('DatabaseManagement')) {
    include_once(__DIR__ . '/DatabaseManagement.class.php');
}

class AdminSecurityManagement
{
    /** @var DatabaseManagement */
    private $db;

    public function __construct()
    {
        $this->db = new DatabaseManagement();
        $this->ensureSchema();
    }

    public function db()
    {
        return $this->db;
    }

    private function ensureSchema()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS admin_access_logs (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            admin_id INT NULL,
            username VARCHAR(120) NULL,
            action_key VARCHAR(40) NOT NULL,
            session_id VARCHAR(128) NULL,
            ip_address VARCHAR(64) NULL,
            user_agent VARCHAR(700) NULL,
            device_hash CHAR(64) NULL,
            request_uri VARCHAR(255) NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            KEY idx_admin_action_created (admin_id, action_key, created_at),
            KEY idx_action_created (action_key, created_at),
            KEY idx_ip_created (ip_address, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS admin_active_sessions (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            session_id VARCHAR(128) NOT NULL,
            admin_id INT NOT NULL,
            username VARCHAR(120) NULL,
            ip_address VARCHAR(64) NULL,
            user_agent VARCHAR(700) NULL,
            device_hash CHAR(64) NULL,
            first_seen DATETIME NOT NULL,
            last_seen DATETIME NOT NULL,
            last_uri VARCHAR(255) NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            UNIQUE KEY uniq_session_id (session_id),
            KEY idx_admin_last_seen (admin_id, last_seen),
            KEY idx_active_last_seen (is_active, last_seen)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    private function clientIp()
    {
        $keys = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_REAL_IP',
            'HTTP_X_FORWARDED_FOR',
            'REMOTE_ADDR',
        ];
        foreach ($keys as $k) {
            $v = trim((string)($_SERVER[$k] ?? ''));
            if ($v === '') {
                continue;
            }
            if ($k === 'HTTP_X_FORWARDED_FOR' && strpos($v, ',') !== false) {
                $parts = explode(',', $v);
                $v = trim((string)($parts[0] ?? ''));
            }
            if ($v !== '') {
                return mb_substr($v, 0, 64, 'UTF-8');
            }
        }
        return '';
    }

    private function userAgent()
    {
        return mb_substr(trim((string)($_SERVER['HTTP_USER_AGENT'] ?? '')), 0, 700, 'UTF-8');
    }

    private function requestUri()
    {
        return mb_substr(trim((string)($_SERVER['REQUEST_URI'] ?? '')), 0, 255, 'UTF-8');
    }

    private function deviceHash()
    {
        $ua = (string)($_SERVER['HTTP_USER_AGENT'] ?? '');
        $lang = (string)($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '');
        return hash('sha256', $ua . '|' . $lang);
    }

    private function sessionId()
    {
        $sid = session_id();
        if ($sid === '' && isset($_COOKIE[session_name()])) {
            $sid = (string)$_COOKIE[session_name()];
        }
        return mb_substr((string)$sid, 0, 128, 'UTF-8');
    }

    private function logAction($actionKey, $adminId = null, $username = '')
    {
        $this->db->query(
            "INSERT INTO admin_access_logs
            (admin_id, username, action_key, session_id, ip_address, user_agent, device_hash, request_uri)
            VALUES
            (:admin_id, :username, :action_key, :session_id, :ip_address, :user_agent, :device_hash, :request_uri)",
            [
                ':admin_id' => $adminId ? (int)$adminId : null,
                ':username' => $username !== '' ? $username : null,
                ':action_key' => trim((string)$actionKey),
                ':session_id' => $this->sessionId() ?: null,
                ':ip_address' => $this->clientIp() ?: null,
                ':user_agent' => $this->userAgent() ?: null,
                ':device_hash' => $this->deviceHash(),
                ':request_uri' => $this->requestUri() ?: null,
            ]
        );
    }

    public function recordLoginSuccess(array $adminRow)
    {
        $adminId = (int)($adminRow['AdminID'] ?? 0);
        $username = trim((string)($adminRow['Username'] ?? ''));
        $this->logAction('login_success', $adminId, $username);
        $this->upsertActiveSession($adminId, $username);
    }

    public function recordLoginFailed($username = '')
    {
        $this->logAction('login_failed', null, trim((string)$username));
    }

    public function touchCurrentAdminSession()
    {
        if (!isset($_SESSION['AdminLogin']) || !is_array($_SESSION['AdminLogin'])) {
            return;
        }
        $adminId = (int)($_SESSION['AdminLogin']['AdminID'] ?? 0);
        if ($adminId <= 0) {
            return;
        }
        $username = trim((string)($_SESSION['AdminLogin']['Username'] ?? ''));
        $this->upsertActiveSession($adminId, $username);
    }

    public function isCurrentSessionAllowed()
    {
        $sid = $this->sessionId();
        if ($sid === '') {
            return true;
        }
        $row = $this->db->selectOne(
            "SELECT is_active FROM admin_active_sessions WHERE session_id = :session_id LIMIT 1",
            [':session_id' => $sid]
        );
        if (!$row) {
            return true;
        }
        return (int)($row['is_active'] ?? 0) === 1;
    }

    public function recordLogout($adminId = null, $username = '')
    {
        $sid = $this->sessionId();
        if ($sid !== '') {
            $this->db->query(
                "UPDATE admin_active_sessions SET is_active = 0, last_seen = NOW() WHERE session_id = :session_id",
                [':session_id' => $sid]
            );
        }
        $this->logAction('logout', $adminId ? (int)$adminId : null, trim((string)$username));
    }

    private function upsertActiveSession($adminId, $username = '')
    {
        $sid = $this->sessionId();
        if ($sid === '') {
            return;
        }

        $this->db->query(
            "INSERT INTO admin_active_sessions
            (session_id, admin_id, username, ip_address, user_agent, device_hash, first_seen, last_seen, last_uri, is_active)
            VALUES
            (:session_id, :admin_id, :username, :ip_address, :user_agent, :device_hash, NOW(), NOW(), :last_uri, 1)
            ON DUPLICATE KEY UPDATE
                admin_id = VALUES(admin_id),
                username = VALUES(username),
                ip_address = VALUES(ip_address),
                user_agent = VALUES(user_agent),
                device_hash = VALUES(device_hash),
                last_seen = NOW(),
                last_uri = VALUES(last_uri),
                is_active = 1",
            [
                ':session_id' => $sid,
                ':admin_id' => (int)$adminId,
                ':username' => $username !== '' ? $username : null,
                ':ip_address' => $this->clientIp() ?: null,
                ':user_agent' => $this->userAgent() ?: null,
                ':device_hash' => $this->deviceHash(),
                ':last_uri' => $this->requestUri() ?: null,
            ]
        );
    }

    public function revokeSessionById($sessionId)
    {
        $sessionId = trim((string)$sessionId);
        if ($sessionId === '') {
            return;
        }
        $this->db->query(
            "UPDATE admin_active_sessions SET is_active = 0 WHERE session_id = :session_id",
            [':session_id' => $sessionId]
        );
    }

    public function getAdminAccounts()
    {
        return $this->db->selectAll(
            "SELECT AdminID, Name, Username, isActive
             FROM admin
             ORDER BY isActive DESC, AdminID ASC"
        );
    }

    public function getAccessLogs($limit = 200)
    {
        $limit = max(20, (int)$limit);
        return $this->db->selectAll(
            "SELECT l.*,
                    COALESCE(a.Name, l.username, '-') AS admin_name,
                    a.Username AS admin_username
             FROM admin_access_logs l
             LEFT JOIN admin a ON a.AdminID = l.admin_id
             ORDER BY l.id DESC
             LIMIT {$limit}"
        );
    }

    public function getActiveSessions($activeWithinMinutes = 120, $includeInactive = false)
    {
        $mins = max(1, (int)$activeWithinMinutes);
        $where = $includeInactive ? "1=1" : "s.is_active = 1";
        return $this->db->selectAll(
            "SELECT s.*,
                    COALESCE(a.Name, s.username, '-') AS admin_name,
                    a.Username AS admin_username,
                    TIMESTAMPDIFF(MINUTE, s.last_seen, NOW()) AS mins_since_last_seen
             FROM admin_active_sessions s
             LEFT JOIN admin a ON a.AdminID = s.admin_id
             WHERE {$where}
               AND s.last_seen >= DATE_SUB(NOW(), INTERVAL {$mins} MINUTE)
             ORDER BY s.last_seen DESC"
        );
    }

    public function getSecuritySummary()
    {
        $summary = [
            'admin_total' => 0,
            'admin_active' => 0,
            'active_sessions' => 0,
            'failed_24h' => 0,
            'success_24h' => 0,
        ];

        $row = $this->db->selectOne(
            "SELECT COUNT(*) AS total_admin,
                    SUM(CASE WHEN isActive='Y' THEN 1 ELSE 0 END) AS active_admin
             FROM admin"
        );
        if ($row) {
            $summary['admin_total'] = (int)($row['total_admin'] ?? 0);
            $summary['admin_active'] = (int)($row['active_admin'] ?? 0);
        }

        $row = $this->db->selectOne(
            "SELECT COUNT(*) AS total_sessions
             FROM admin_active_sessions
             WHERE is_active = 1
               AND last_seen >= DATE_SUB(NOW(), INTERVAL 120 MINUTE)"
        );
        if ($row) {
            $summary['active_sessions'] = (int)($row['total_sessions'] ?? 0);
        }

        $row = $this->db->selectOne(
            "SELECT
                SUM(CASE WHEN action_key='login_failed' THEN 1 ELSE 0 END) AS failed_login,
                SUM(CASE WHEN action_key='login_success' THEN 1 ELSE 0 END) AS success_login
             FROM admin_access_logs
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)"
        );
        if ($row) {
            $summary['failed_24h'] = (int)($row['failed_login'] ?? 0);
            $summary['success_24h'] = (int)($row['success_login'] ?? 0);
        }

        return $summary;
    }
}

