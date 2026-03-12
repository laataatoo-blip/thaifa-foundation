<?php

if (!class_exists('DatabaseManagement')) {
    include_once(__DIR__ . '/DatabaseManagement.class.php');
}
if (!class_exists('FoundationSchema')) {
    include_once(__DIR__ . '/FoundationSchema.class.php');
}

class DonationManagement
{
    private $db;

    public static function statusMap()
    {
        return [
            'pending' => 'รอตรวจสอบ',
            'paid' => 'ชำระแล้ว',
            'verified' => 'ยืนยันแล้ว',
            'rejected' => 'ไม่ผ่านการตรวจสอบ',
            'cancelled' => 'ยกเลิก',
        ];
    }

    public function __construct()
    {
        $this->db = new DatabaseManagement();
        FoundationSchema::ensure($this->db);
    }

    public function getDb() { return $this->db; }

    public function getCampaigns($activeOnly = true)
    {
        $sql = "SELECT * FROM donation_campaigns";
        if ($activeOnly) $sql .= " WHERE is_active = 1";
        $sql .= " ORDER BY sort_order ASC, id ASC";
        return $this->db->selectAll($sql);
    }

    public function createDonation($data)
    {
        $name = trim((string)($data['donor_name'] ?? ''));
        $amount = (float)($data['amount'] ?? 0);
        if ($name === '') throw new Exception('กรุณากรอกชื่อผู้บริจาค');
        if ($amount <= 0) throw new Exception('จำนวนเงินต้องมากกว่า 0');

        $payload = [
            'donor_name' => $name,
            'donor_email' => trim((string)($data['donor_email'] ?? '')),
            'donor_phone' => trim((string)($data['donor_phone'] ?? '')),
            'amount' => $amount,
            'donation_type' => trim((string)($data['donation_type'] ?? 'once')),
            'campaign_id' => (int)($data['campaign_id'] ?? 0) ?: null,
            'payment_method' => trim((string)($data['payment_method'] ?? 'transfer')),
            'payment_ref' => trim((string)($data['payment_ref'] ?? '')),
            'slip_image' => trim((string)($data['slip_image'] ?? '')),
            'status' => 'pending',
            'note' => trim((string)($data['note'] ?? '')),
        ];

        $r = $this->db->insert('donations', $payload);
        return (int)($r['lastInsertID'] ?? 0);
    }

    public function listDonations($status = '')
    {
        $sql = "SELECT d.*, c.name AS campaign_name
                FROM donations d
                LEFT JOIN donation_campaigns c ON c.id = d.campaign_id
                WHERE 1=1";
        $params = [];
        if ($status !== '') {
            $sql .= " AND d.status = :status";
            $params[':status'] = $status;
        }
        $sql .= " ORDER BY d.id DESC";
        return $this->db->selectAll($sql, $params);
    }

    public function getDonationById($id)
    {
        return $this->db->selectOne(
            "SELECT d.*, c.name AS campaign_name
             FROM donations d
             LEFT JOIN donation_campaigns c ON c.id = d.campaign_id
             WHERE d.id = :id LIMIT 1",
            [':id' => (int)$id]
        );
    }

    public function updateDonationStatus($id, $status, $note = '')
    {
        $status = trim((string)$status);
        if (!isset(self::statusMap()[$status])) {
            throw new Exception('สถานะไม่ถูกต้อง');
        }
        $params = [
            ':id' => (int)$id,
            ':status' => $status,
            ':note' => trim((string)$note),
        ];
        $sql = "UPDATE donations SET status = :status, note = :note";
        if (in_array($status, ['paid', 'verified'], true)) {
            $sql .= ", paid_at = IFNULL(paid_at, NOW())";
        }
        $sql .= " WHERE id = :id";
        $this->db->query($sql, $params);
    }

    public function donationStats()
    {
        $all = $this->db->selectOne("SELECT COUNT(*) c, COALESCE(SUM(amount),0) amt FROM donations");
        $paid = $this->db->selectOne("SELECT COUNT(*) c, COALESCE(SUM(amount),0) amt FROM donations WHERE status IN ('paid','verified')");
        $pending = $this->db->selectOne("SELECT COUNT(*) c FROM donations WHERE status = 'pending'");

        return [
            'total_count' => (int)($all['c'] ?? 0),
            'total_amount' => (float)($all['amt'] ?? 0),
            'paid_count' => (int)($paid['c'] ?? 0),
            'paid_amount' => (float)($paid['amt'] ?? 0),
            'pending_count' => (int)($pending['c'] ?? 0),
        ];
    }
}
