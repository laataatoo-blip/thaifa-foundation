<?php

if (!class_exists('DatabaseManagement')) {
    include_once(__DIR__ . '/DatabaseManagement.class.php');
}

class CalendarSchema
{
    private static function columnExists(DatabaseManagement $db, $table, $column)
    {
        $row = $db->selectOne(
            "SELECT COUNT(*) AS c
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = :table_name
               AND COLUMN_NAME = :column_name",
            [
                ':table_name' => (string)$table,
                ':column_name' => (string)$column,
            ]
        );
        return (int)($row['c'] ?? 0) > 0;
    }

    private static function indexExists(DatabaseManagement $db, $table, $index)
    {
        $row = $db->selectOne(
            "SELECT COUNT(*) AS c
             FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = :table_name
               AND INDEX_NAME = :index_name",
            [
                ':table_name' => (string)$table,
                ':index_name' => (string)$index,
            ]
        );
        return (int)($row['c'] ?? 0) > 0;
    }

    public static function ensure(DatabaseManagement $db)
    {
        $db->query("CREATE TABLE IF NOT EXISTS calendar_event_types (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(120) NOT NULL,
            slug VARCHAR(120) NOT NULL,
            color_hex VARCHAR(20) NOT NULL DEFAULT '#233882',
            sort_order INT NOT NULL DEFAULT 0,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $db->query("CREATE TABLE IF NOT EXISTS calendar_events (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            type_id INT UNSIGNED NULL,
            title VARCHAR(255) NOT NULL,
            summary VARCHAR(500) NULL,
            description TEXT NULL,
            location VARCHAR(255) NULL,
            start_at DATETIME NOT NULL,
            end_at DATETIME NULL,
            is_all_day TINYINT(1) NOT NULL DEFAULT 0,
            is_visible TINYINT(1) NOT NULL DEFAULT 1,
            created_by_admin INT NULL,
            updated_by_admin INT NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT fk_calendar_events_type FOREIGN KEY (type_id) REFERENCES calendar_event_types(id)
                ON DELETE SET NULL ON UPDATE CASCADE,
            INDEX idx_calendar_events_start_at (start_at),
            INDEX idx_calendar_events_visible (is_visible)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        if (!self::columnExists($db, 'calendar_events', 'source_type')) {
            $db->query("ALTER TABLE calendar_events ADD COLUMN source_type VARCHAR(32) NULL AFTER is_visible");
        }
        if (!self::columnExists($db, 'calendar_events', 'source_event_id')) {
            $db->query("ALTER TABLE calendar_events ADD COLUMN source_event_id VARCHAR(255) NULL AFTER source_type");
        }
        if (!self::columnExists($db, 'calendar_events', 'last_synced_at')) {
            $db->query("ALTER TABLE calendar_events ADD COLUMN last_synced_at DATETIME NULL AFTER source_event_id");
        }
        if (!self::indexExists($db, 'calendar_events', 'idx_calendar_source')) {
            $db->query("ALTER TABLE calendar_events ADD INDEX idx_calendar_source (source_type, source_event_id)");
        }

        $hasTypes = $db->selectOne("SELECT COUNT(*) AS c FROM calendar_event_types");
        if ((int)($hasTypes['c'] ?? 0) === 0) {
            $db->query("INSERT INTO calendar_event_types(name, slug, color_hex, sort_order, is_active) VALUES
                ('มอบทุนการศึกษา', 'scholarship', '#233882', 1, 1),
                ('กิจกรรมชุมชน', 'community', '#ef4444', 2, 1),
                ('ประชุม', 'meeting', '#3b82f6', 3, 1),
                ('ระดมทุน', 'fundraising', '#22c55e', 4, 1)");
        }

        $hasEvents = $db->selectOne("SELECT COUNT(*) AS c FROM calendar_events");
        if ((int)($hasEvents['c'] ?? 0) === 0) {
            $db->query("INSERT INTO calendar_events(type_id, title, summary, description, location, start_at, end_at, is_all_day, is_visible)
                SELECT t.id,
                       'ประชุมคณะกรรมการมูลนิธิ',
                       'ประชุมติดตามโครงการและแผนงานประจำเดือน',
                       'ประชุมคณะกรรมการเพื่อติดตามความคืบหน้าโครงการต่างๆ ของมูลนิธิ',
                       'สำนักงานใหญ่ THAIFA Foundation',
                       DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 7 DAY), '%Y-%m-%d 10:00:00'),
                       DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 7 DAY), '%Y-%m-%d 12:00:00'),
                       0,
                       1
                FROM calendar_event_types t WHERE t.slug = 'meeting' LIMIT 1");

            $db->query("INSERT INTO calendar_events(type_id, title, summary, description, location, start_at, end_at, is_all_day, is_visible)
                SELECT t.id,
                       'มอบทุนการศึกษาเยาวชน',
                       'มอบทุนการศึกษาให้เยาวชนที่ขาดแคลนในพื้นที่',
                       'กิจกรรมมอบทุนการศึกษา พร้อมอุปกรณ์การเรียนให้แก่นักเรียนผู้ด้อยโอกาส',
                       'โรงเรียนเครือข่ายภาคกลาง',
                       DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 14 DAY), '%Y-%m-%d 09:00:00'),
                       DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 14 DAY), '%Y-%m-%d 16:00:00'),
                       0,
                       1
                FROM calendar_event_types t WHERE t.slug = 'scholarship' LIMIT 1");
        }
    }
}
