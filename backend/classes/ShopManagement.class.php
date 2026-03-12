<?php

if (!class_exists('DatabaseManagement')) {
    include_once(__DIR__ . '/DatabaseManagement.class.php');
}
if (!class_exists('ShopSchema')) {
    include_once(__DIR__ . '/ShopSchema.class.php');
}

class ShopManagement
{
    private $db;

    private static $statusLabels = [
        'pending' => 'รอชำระ/รอตรวจสอบ',
        'confirmed' => 'ยืนยันคำสั่งซื้อ',
        'packing' => 'กำลังแพ็กสินค้า',
        'shipping' => 'ส่งเข้าระบบขนส่ง',
        'out_for_delivery' => 'กำลังนำส่ง',
        'delivered' => 'จัดส่งสำเร็จ',
        'cancelled' => 'ยกเลิกคำสั่งซื้อ'
    ];

    public function __construct()
    {
        $this->db = new DatabaseManagement();
        ShopSchema::ensure($this->db);
    }

    public function getDb()
    {
        return $this->db;
    }

    public static function statusMap()
    {
        return self::$statusLabels;
    }

    public static function statusLabel($status)
    {
        return self::$statusLabels[$status] ?? $status;
    }

    public static function progressStep($status)
    {
        $steps = [
            'pending' => 1,
            'confirmed' => 2,
            'packing' => 3,
            'shipping' => 4,
            'out_for_delivery' => 5,
            'delivered' => 6,
            'cancelled' => 0,
        ];
        return $steps[$status] ?? 1;
    }

    public static function statusClass($status)
    {
        switch ($status) {
            case 'delivered': return 'success';
            case 'cancelled': return 'danger';
            case 'out_for_delivery':
            case 'shipping': return 'info';
            case 'packing':
            case 'confirmed': return 'primary';
            default: return 'secondary';
        }
    }

    public function getCategories($activeOnly = true)
    {
        $sql = "SELECT id, name FROM shop_categories";
        if ($activeOnly) {
            $sql .= " WHERE is_active = 1";
        }
        $sql .= " ORDER BY sort_order ASC, id ASC";
        return $this->db->selectAll($sql);
    }

    public function getProducts($activeOnly = true)
    {
        $sql = "SELECT p.*, c.name AS category_name
                FROM shop_products p
                LEFT JOIN shop_categories c ON c.id = p.category_id";
        if ($activeOnly) {
            $sql .= " WHERE p.is_active = 1";
        }
        $sql .= " ORDER BY p.id DESC";
        return $this->db->selectAll($sql);
    }

    public function getProductById($id)
    {
        return $this->db->selectOne(
            "SELECT p.*, c.name AS category_name FROM shop_products p
             LEFT JOIN shop_categories c ON c.id = p.category_id
             WHERE p.id = :id LIMIT 1",
            [':id' => (int)$id]
        );
    }

    public function upsertProduct($id, $data)
    {
        $params = [
            'category_id' => (int)($data['category_id'] ?? 0) ?: null,
            'name' => trim((string)($data['name'] ?? '')),
            'slug' => trim((string)($data['slug'] ?? '')),
            'description' => trim((string)($data['description'] ?? '')),
            'price' => (float)($data['price'] ?? 0),
            'stock_qty' => (int)($data['stock_qty'] ?? 0),
            'cover_image' => trim((string)($data['cover_image'] ?? '')),
            'is_active' => !empty($data['is_active']) ? 1 : 0,
        ];

        if ($params['slug'] === '') {
            $params['slug'] = $this->slugify($params['name']);
        }

        if ($id > 0) {
            $this->db->query(
                "UPDATE shop_products SET
                    category_id = :category_id,
                    name = :name,
                    slug = :slug,
                    description = :description,
                    price = :price,
                    stock_qty = :stock_qty,
                    cover_image = :cover_image,
                    is_active = :is_active
                 WHERE id = :id",
                [
                    ':category_id' => $params['category_id'],
                    ':name' => $params['name'],
                    ':slug' => $params['slug'],
                    ':description' => $params['description'],
                    ':price' => $params['price'],
                    ':stock_qty' => $params['stock_qty'],
                    ':cover_image' => $params['cover_image'],
                    ':is_active' => $params['is_active'],
                    ':id' => $id,
                ]
            );
            return $id;
        }

        $r = $this->db->insert('shop_products', $params);
        return (int)($r['lastInsertID'] ?? 0);
    }

    public function adjustProductStock($productId, $mode, $qty, $adminId = null, $note = '')
    {
        $productId = (int)$productId;
        $qty = (int)$qty;
        $mode = trim((string)$mode);

        if ($productId <= 0) {
            throw new Exception('ไม่พบสินค้า');
        }
        if ($qty < 0) {
            throw new Exception('จำนวนต้องมากกว่าหรือเท่ากับ 0');
        }
        if (!in_array($mode, ['add', 'subtract', 'set'], true)) {
            throw new Exception('รูปแบบการปรับสต็อกไม่ถูกต้อง');
        }

        $product = $this->getProductById($productId);
        if (!$product) {
            throw new Exception('ไม่พบสินค้า');
        }

        $before = (int)($product['stock_qty'] ?? 0);
        $after = $before;
        $change = 0;

        if ($mode === 'add') {
            $after = $before + $qty;
            $change = $qty;
        } elseif ($mode === 'subtract') {
            $after = max(0, $before - $qty);
            $change = -$qty;
        } else {
            $after = $qty;
            $change = $after - $before;
        }

        $this->db->query(
            "UPDATE shop_products SET stock_qty = :stock_qty, updated_at = NOW() WHERE id = :id",
            [':stock_qty' => $after, ':id' => $productId]
        );

        $this->db->query(
            "INSERT INTO shop_stock_movements
            (product_id, movement_type, qty_change, qty_before, qty_after, note, created_by_admin)
            VALUES
            (:product_id, :movement_type, :qty_change, :qty_before, :qty_after, :note, :created_by_admin)",
            [
                ':product_id' => $productId,
                ':movement_type' => $mode,
                ':qty_change' => $change,
                ':qty_before' => $before,
                ':qty_after' => $after,
                ':note' => trim((string)$note),
                ':created_by_admin' => $adminId,
            ]
        );
    }

    public function getStockMovements($productId = 0, $limit = 50)
    {
        $limit = max(1, (int)$limit);
        if ((int)$productId > 0) {
            return $this->db->selectAll(
                "SELECT m.*, p.name AS product_name
                 FROM shop_stock_movements m
                 INNER JOIN shop_products p ON p.id = m.product_id
                 WHERE m.product_id = :product_id
                 ORDER BY m.id DESC
                 LIMIT {$limit}",
                [':product_id' => (int)$productId]
            );
        }

        return $this->db->selectAll(
            "SELECT m.*, p.name AS product_name
             FROM shop_stock_movements m
             INNER JOIN shop_products p ON p.id = m.product_id
             ORDER BY m.id DESC
             LIMIT {$limit}"
        );
    }

    public function addToCart($productId, $qty = 1)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $productId = (int)$productId;
        $qty = max(1, (int)$qty);

        if (!isset($_SESSION['shop_cart']) || !is_array($_SESSION['shop_cart'])) {
            $_SESSION['shop_cart'] = [];
        }

        if (!isset($_SESSION['shop_cart'][$productId])) {
            $_SESSION['shop_cart'][$productId] = 0;
        }
        $_SESSION['shop_cart'][$productId] += $qty;
    }

    public function updateCartQty($productId, $qty)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $productId = (int)$productId;
        $qty = (int)$qty;

        if (!isset($_SESSION['shop_cart']) || !is_array($_SESSION['shop_cart'])) {
            $_SESSION['shop_cart'] = [];
        }

        if ($qty <= 0) {
            unset($_SESSION['shop_cart'][$productId]);
            return;
        }
        $_SESSION['shop_cart'][$productId] = $qty;
    }

    public function getCartItems()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $cart = $_SESSION['shop_cart'] ?? [];
        if (empty($cart) || !is_array($cart)) {
            return [];
        }

        $ids = array_map('intval', array_keys($cart));
        $in = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->db()->prepare("SELECT * FROM shop_products WHERE id IN ($in)");
        $stmt->execute($ids);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($products as $p) {
            $pid = (int)$p['id'];
            $qty = (int)($cart[$pid] ?? 0);
            if ($qty <= 0) continue;
            $price = (float)$p['price'];
            $result[] = [
                'product_id' => $pid,
                'name' => $p['name'],
                'price' => $price,
                'qty' => $qty,
                'line_total' => $price * $qty,
                'cover_image' => $p['cover_image'],
                'stock_qty' => (int)$p['stock_qty'],
                'is_active' => (int)$p['is_active'],
            ];
        }

        usort($result, function ($a, $b) {
            return $a['product_id'] <=> $b['product_id'];
        });

        return $result;
    }

    public function cartCount()
    {
        $items = $this->getCartItems();
        $sum = 0;
        foreach ($items as $i) {
            $sum += (int)$i['qty'];
        }
        return $sum;
    }

    public function cartSubtotal()
    {
        $items = $this->getCartItems();
        $sum = 0.0;
        foreach ($items as $i) {
            $sum += (float)$i['line_total'];
        }
        return $sum;
    }

    public function createOrder($customer, $selectedProductIds = [])
    {
        $items = $this->getCartItems();
        $selectedIds = [];
        if (is_array($selectedProductIds) && !empty($selectedProductIds)) {
            foreach ($selectedProductIds as $sid) {
                $sid = (int)$sid;
                if ($sid > 0) {
                    $selectedIds[$sid] = true;
                }
            }
            if (!empty($selectedIds)) {
                $items = array_values(array_filter($items, function ($i) use ($selectedIds) {
                    return isset($selectedIds[(int)$i['product_id']]);
                }));
            }
        }

        if (empty($items)) {
            throw new Exception('ไม่พบสินค้าในตะกร้า');
        }

        $name = trim((string)($customer['customer_name'] ?? ''));
        $phone = trim((string)($customer['customer_phone'] ?? ''));
        $address = trim((string)($customer['customer_address'] ?? ''));
        $memberId = (int)($customer['member_id'] ?? 0);
        if ($memberId <= 0) {
            throw new Exception('กรุณาเข้าสู่ระบบก่อนสั่งซื้อสินค้า');
        }

        if ($name === '' || $phone === '' || $address === '') {
            throw new Exception('กรุณากรอกชื่อ เบอร์โทร และที่อยู่ให้ครบ');
        }

        $db = $this->db->db();
        $db->beginTransaction();

        try {
            $orderNo = $this->generateOrderNo();
            $total = 0.0;
            foreach ($items as $i) {
                $total += (float)$i['line_total'];
            }

            $stmt = $db->prepare("INSERT INTO shop_orders
                (order_no, member_id, customer_name, customer_phone, customer_address, note, total_amount, status, payment_status, last_status_at)
                VALUES
                (:order_no, :member_id, :customer_name, :customer_phone, :customer_address, :note, :total_amount, 'pending', 'pending', NOW())");
            $stmt->execute([
                ':order_no' => $orderNo,
                ':member_id' => $memberId,
                ':customer_name' => $name,
                ':customer_phone' => $phone,
                ':customer_address' => $address,
                ':note' => trim((string)($customer['note'] ?? '')),
                ':total_amount' => $total,
            ]);
            $orderId = (int)$db->lastInsertId();

            $itemStmt = $db->prepare("INSERT INTO shop_order_items
                (order_id, product_id, product_name, product_price, qty, line_total)
                VALUES (:order_id, :product_id, :product_name, :product_price, :qty, :line_total)");

            $stockStmt = $db->prepare("UPDATE shop_products
                SET stock_qty = GREATEST(stock_qty - :qty, 0)
                WHERE id = :id");

            $movementStmt = $db->prepare("INSERT INTO shop_stock_movements
                (product_id, movement_type, qty_change, qty_before, qty_after, note, created_by_admin)
                VALUES (:product_id, 'subtract', :qty_change, :qty_before, :qty_after, :note, NULL)");

            foreach ($items as $i) {
                $itemStmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => (int)$i['product_id'],
                    ':product_name' => $i['name'],
                    ':product_price' => $i['price'],
                    ':qty' => (int)$i['qty'],
                    ':line_total' => $i['line_total'],
                ]);

                $stockStmt->execute([
                    ':qty' => (int)$i['qty'],
                    ':id' => (int)$i['product_id'],
                ]);

                $beforeQty = (int)($i['stock_qty'] ?? 0);
                $afterQty = max(0, $beforeQty - (int)$i['qty']);
                $movementStmt->execute([
                    ':product_id' => (int)$i['product_id'],
                    ':qty_change' => -((int)$i['qty']),
                    ':qty_before' => $beforeQty,
                    ':qty_after' => $afterQty,
                    ':note' => 'ตัดสต็อกจากคำสั่งซื้อ ' . $orderNo,
                ]);
            }

            $this->insertOrderLog($orderId, 'pending', 'คำสั่งซื้อถูกสร้างแล้ว', 'ร้านค้ารับคำสั่งซื้อเรียบร้อย', null, null, $db);

            $db->commit();
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            if (empty($selectedIds)) {
                $_SESSION['shop_cart'] = [];
            } else {
                foreach (array_keys($selectedIds) as $pid) {
                    unset($_SESSION['shop_cart'][(int)$pid]);
                }
            }

            return [
                'order_id' => $orderId,
                'order_no' => $orderNo,
            ];
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function listOrders()
    {
        return $this->db->selectAll(
            "SELECT o.*, (SELECT COUNT(*) FROM shop_order_items i WHERE i.order_id = o.id) AS item_count
             FROM shop_orders o
             ORDER BY o.id DESC"
        );
    }

    public function getOrderByNo($orderNo)
    {
        $order = $this->db->selectOne(
            "SELECT * FROM shop_orders WHERE order_no = :order_no LIMIT 1",
            [':order_no' => trim((string)$orderNo)]
        );
        if (!$order) {
            return null;
        }
        $orderId = (int)$order['id'];
        $items = $this->db->selectAll(
            "SELECT * FROM shop_order_items WHERE order_id = :order_id ORDER BY id ASC",
            [':order_id' => $orderId]
        );
        $logs = $this->db->selectAll(
            "SELECT * FROM shop_order_status_logs WHERE order_id = :order_id ORDER BY created_at DESC, id DESC",
            [':order_id' => $orderId]
        );

        return [
            'order' => $order,
            'items' => $items,
            'logs' => $logs,
        ];
    }

    public function getOrderById($id)
    {
        $id = (int)$id;
        $order = $this->db->selectOne("SELECT * FROM shop_orders WHERE id = :id LIMIT 1", [':id' => $id]);
        if (!$order) return null;

        return [
            'order' => $order,
            'items' => $this->db->selectAll("SELECT * FROM shop_order_items WHERE order_id = :order_id ORDER BY id ASC", [':order_id' => $id]),
            'logs' => $this->db->selectAll("SELECT * FROM shop_order_status_logs WHERE order_id = :order_id ORDER BY created_at DESC, id DESC", [':order_id' => $id]),
        ];
    }

    public function updateOrderStatus($orderId, $status, $description = '', $location = '', $adminId = null)
    {
        $orderId = (int)$orderId;
        if (!isset(self::$statusLabels[$status])) {
            throw new Exception('สถานะไม่ถูกต้อง');
        }

        $title = self::statusLabel($status);
        $this->db->query(
            "UPDATE shop_orders
             SET status = :status,
                 last_status_at = NOW(),
                 updated_at = NOW()
             WHERE id = :id",
            [
                ':status' => $status,
                ':id' => $orderId,
            ]
        );

        $this->insertOrderLog($orderId, $status, $title, $description, $location, $adminId);
    }

    public function insertOrderLog($orderId, $status, $title, $description = '', $location = '', $adminId = null, $pdo = null)
    {
        $conn = $pdo ?: $this->db->db();
        $stmt = $conn->prepare(
            "INSERT INTO shop_order_status_logs
            (order_id, status, title, description, location, created_by_admin)
            VALUES
            (:order_id, :status, :title, :description, :location, :created_by_admin)"
        );
        $stmt->execute([
            ':order_id' => (int)$orderId,
            ':status' => $status,
            ':title' => trim((string)$title),
            ':description' => trim((string)$description),
            ':location' => trim((string)$location),
            ':created_by_admin' => $adminId,
        ]);
    }

    private function generateOrderNo()
    {
        do {
            $orderNo = 'TF' . date('ymd') . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
            $exists = $this->db->selectOne(
                "SELECT id FROM shop_orders WHERE order_no = :order_no LIMIT 1",
                [':order_no' => $orderNo]
            );
        } while (!empty($exists));

        return $orderNo;
    }

    private function slugify($text)
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
        $text = trim($text, '-');
        return $text ?: ('product-' . time());
    }
}
