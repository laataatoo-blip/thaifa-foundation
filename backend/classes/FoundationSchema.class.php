<?php

if (!class_exists('DatabaseManagement')) {
    include_once(__DIR__ . '/DatabaseManagement.class.php');
}

class FoundationSchema
{
    public static function ensure(DatabaseManagement $db)
    {
        $db->query("CREATE TABLE IF NOT EXISTS donation_campaigns (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(180) NOT NULL,
            description TEXT NULL,
            sort_order INT NOT NULL DEFAULT 0,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $db->query("CREATE TABLE IF NOT EXISTS donations (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            donor_name VARCHAR(180) NOT NULL,
            donor_email VARCHAR(190) NULL,
            donor_phone VARCHAR(40) NULL,
            amount DECIMAL(12,2) NOT NULL,
            donation_type VARCHAR(20) NOT NULL DEFAULT 'once',
            campaign_id INT UNSIGNED NULL,
            payment_method VARCHAR(40) NOT NULL DEFAULT 'transfer',
            payment_ref VARCHAR(120) NULL,
            slip_image VARCHAR(500) NULL,
            status VARCHAR(30) NOT NULL DEFAULT 'pending',
            note TEXT NULL,
            paid_at DATETIME NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT fk_donations_campaign FOREIGN KEY (campaign_id) REFERENCES donation_campaigns(id)
                ON DELETE SET NULL ON UPDATE CASCADE,
            INDEX idx_donations_status (status),
            INDEX idx_donations_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $db->query("CREATE TABLE IF NOT EXISTS foundation_team_members (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            group_key VARCHAR(30) NOT NULL,
            member_name VARCHAR(190) NOT NULL,
            member_title VARCHAR(190) NULL,
            member_bio TEXT NULL,
            photo_url VARCHAR(500) NULL,
            sort_order INT NOT NULL DEFAULT 0,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_team_group (group_key),
            INDEX idx_team_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $db->query("CREATE TABLE IF NOT EXISTS contact_messages (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(190) NOT NULL,
            email VARCHAR(190) NOT NULL,
            subject VARCHAR(220) NOT NULL,
            message TEXT NOT NULL,
            status VARCHAR(30) NOT NULL DEFAULT 'new',
            admin_note TEXT NULL,
            replied_at DATETIME NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_contact_status (status),
            INDEX idx_contact_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $db->query("CREATE TABLE IF NOT EXISTS analytics_pageviews (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            view_date DATE NOT NULL,
            page_path VARCHAR(255) NOT NULL,
            referrer VARCHAR(255) NULL,
            user_agent VARCHAR(255) NULL,
            ip_hash VARCHAR(64) NOT NULL,
            session_key VARCHAR(100) NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_analytics_date (view_date),
            INDEX idx_analytics_page (page_path),
            INDEX idx_analytics_ip (ip_hash)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $campaignCount = $db->selectOne("SELECT COUNT(*) AS c FROM donation_campaigns");
        if ((int)($campaignCount['c'] ?? 0) === 0) {
            $db->query("INSERT INTO donation_campaigns(name, description, sort_order, is_active) VALUES
                ('ทุนการศึกษา', 'สนับสนุนทุนการศึกษาแก่เด็กและเยาวชนที่ขาดแคลน', 1, 1),
                ('อุปกรณ์ทางการแพทย์', 'จัดหาอุปกรณ์ทางการแพทย์ให้โรงพยาบาลของรัฐ', 2, 1),
                ('กิจกรรมชุมชน', 'สนับสนุนกิจกรรมเพื่อผู้ด้อยโอกาสและชุมชน', 3, 1)");
        }

        $teamCount = $db->selectOne("SELECT COUNT(*) AS c FROM foundation_team_members");
        if ((int)($teamCount['c'] ?? 0) === 0) {
            $db->query("INSERT INTO foundation_team_members(group_key, member_name, member_title, member_bio, sort_order, is_active) VALUES
                ('advisors', 'ชื่อที่ปรึกษา', 'ที่ปรึกษามูลนิธิ', 'รายละเอียดเกี่ยวกับที่ปรึกษา บทบาทและความรับผิดชอบ', 1, 1),
                ('executives', 'ชื่อกรรมการบริหาร', 'ประธานกรรมการ', 'บทบาทและความรับผิดชอบ', 1, 1),
                ('committee', 'ชื่อกรรมการ', 'กรรมการ', 'บทบาทและความรับผิดชอบ', 1, 1)");
        }
    }
}
