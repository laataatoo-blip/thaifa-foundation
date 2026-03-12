<?php

if (!class_exists('DatabaseManagement')) {
    include_once(__DIR__ . '/DatabaseManagement.class.php');
}
if (!class_exists('MemberSchema')) {
    include_once(__DIR__ . '/MemberSchema.class.php');
}

class ShopSchema
{
    public static function ensure(DatabaseManagement $db)
    {
        MemberSchema::ensure($db);

        $db->query("CREATE TABLE IF NOT EXISTS shop_categories (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(150) NOT NULL,
            sort_order INT NOT NULL DEFAULT 0,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $db->query("CREATE TABLE IF NOT EXISTS shop_products (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            category_id INT UNSIGNED NULL,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NULL,
            description TEXT NULL,
            price DECIMAL(10,2) NOT NULL DEFAULT 0,
            stock_qty INT NOT NULL DEFAULT 0,
            cover_image VARCHAR(500) NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT fk_shop_products_category FOREIGN KEY (category_id) REFERENCES shop_categories(id)
                ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $db->query("CREATE TABLE IF NOT EXISTS shop_product_images (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            product_id INT UNSIGNED NOT NULL,
            image_url VARCHAR(500) NOT NULL,
            sort_order INT NOT NULL DEFAULT 1,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_shop_product_images_product FOREIGN KEY (product_id) REFERENCES shop_products(id)
                ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $db->query("CREATE TABLE IF NOT EXISTS shop_stock_movements (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            product_id INT UNSIGNED NOT NULL,
            movement_type VARCHAR(20) NOT NULL,
            qty_change INT NOT NULL,
            qty_before INT NOT NULL,
            qty_after INT NOT NULL,
            note VARCHAR(255) NULL,
            created_by_admin INT NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_shop_stock_product FOREIGN KEY (product_id) REFERENCES shop_products(id)
                ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $db->query("CREATE TABLE IF NOT EXISTS shop_orders (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            order_no VARCHAR(40) NOT NULL UNIQUE,
            member_id INT UNSIGNED NULL,
            customer_name VARCHAR(150) NOT NULL,
            customer_phone VARCHAR(30) NOT NULL,
            customer_address TEXT NOT NULL,
            note TEXT NULL,
            total_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
            status VARCHAR(40) NOT NULL DEFAULT 'pending',
            payment_status VARCHAR(40) NOT NULL DEFAULT 'pending',
            tracking_code VARCHAR(80) NULL,
            last_status_at DATETIME NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_shop_orders_member (member_id),
            CONSTRAINT fk_shop_orders_member FOREIGN KEY (member_id) REFERENCES foundation_members(id)
                ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $hasMemberIdColumn = $db->selectOne("SELECT COUNT(*) AS c
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'shop_orders'
              AND COLUMN_NAME = 'member_id'");
        if ((int)($hasMemberIdColumn['c'] ?? 0) === 0) {
            $db->query("ALTER TABLE shop_orders ADD COLUMN member_id INT UNSIGNED NULL AFTER order_no");
            $db->query("ALTER TABLE shop_orders ADD INDEX idx_shop_orders_member (member_id)");
        }

        $hasMemberFk = $db->selectOne("SELECT COUNT(*) AS c
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'shop_orders'
              AND COLUMN_NAME = 'member_id'
              AND CONSTRAINT_NAME = 'fk_shop_orders_member'");
        if ((int)($hasMemberFk['c'] ?? 0) === 0) {
            $db->query("ALTER TABLE shop_orders
                ADD CONSTRAINT fk_shop_orders_member
                FOREIGN KEY (member_id) REFERENCES foundation_members(id)
                ON DELETE SET NULL ON UPDATE CASCADE");
        }

        $db->query("CREATE TABLE IF NOT EXISTS shop_order_items (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            order_id INT UNSIGNED NOT NULL,
            product_id INT UNSIGNED NULL,
            product_name VARCHAR(255) NOT NULL,
            product_price DECIMAL(10,2) NOT NULL,
            qty INT NOT NULL DEFAULT 1,
            line_total DECIMAL(10,2) NOT NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_shop_order_items_order FOREIGN KEY (order_id) REFERENCES shop_orders(id)
                ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT fk_shop_order_items_product FOREIGN KEY (product_id) REFERENCES shop_products(id)
                ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $db->query("CREATE TABLE IF NOT EXISTS shop_order_status_logs (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            order_id INT UNSIGNED NOT NULL,
            status VARCHAR(40) NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT NULL,
            location VARCHAR(255) NULL,
            created_by_admin INT NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_shop_order_logs_order FOREIGN KEY (order_id) REFERENCES shop_orders(id)
                ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $hasCategories = $db->selectOne("SELECT COUNT(*) AS c FROM shop_categories");
        if ((int)($hasCategories['c'] ?? 0) === 0) {
            $db->query("INSERT INTO shop_categories(name, sort_order) VALUES
                ('ของที่ระลึก', 1),
                ('เสื้อผ้า', 2),
                ('หนังสือ', 3)");
        }

        $hasProducts = $db->selectOne("SELECT COUNT(*) AS c FROM shop_products");
        if ((int)($hasProducts['c'] ?? 0) === 0) {
            $db->query("INSERT INTO shop_products(category_id, name, slug, description, price, stock_qty, cover_image, is_active) VALUES
                (1, 'กระเป๋าผ้า THAIFA', 'thaifa-bag', 'กระเป๋าผ้าพิมพ์โลโก้ THAIFA รายได้สนับสนุนงานมูลนิธิ', 199.00, 100, 'https://images.unsplash.com/photo-1597481499750-3e6b22637e12?w=800', 1),
                (2, 'เสื้อยืด THAIFA', 'thaifa-shirt', 'เสื้อยืดผ้าเนื้อนุ่ม ใส่สบาย พร้อมโลโก้มูลนิธิ', 350.00, 80, 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=800', 1),
                (3, 'หนังสือแรงบันดาลใจ', 'thaifa-book', 'หนังสือสร้างแรงบันดาลใจ รายได้เข้าสมทบทุนการศึกษา', 490.00, 60, 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=800', 1)");
        }
    }
}
