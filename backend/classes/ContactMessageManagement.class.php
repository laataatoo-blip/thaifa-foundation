<?php

if (!class_exists('DatabaseManagement')) {
    include_once(__DIR__ . '/DatabaseManagement.class.php');
}
if (!class_exists('FoundationSchema')) {
    include_once(__DIR__ . '/FoundationSchema.class.php');
}

class ContactMessageManagement
{
    private $db;

    public static function statusMap()
    {
        return [
            'new' => 'ใหม่',
            'in_progress' => 'กำลังดำเนินการ',
            'replied' => 'ตอบแล้ว',
            'closed' => 'ปิดงาน',
            'spam' => 'สแปม',
        ];
    }

    public function __construct()
    {
        $this->db = new DatabaseManagement();
        FoundationSchema::ensure($this->db);
    }

    public function createMessage($data)
    {
        $fullName = trim((string)($data['full_name'] ?? ''));
        $email = trim((string)($data['email'] ?? ''));
        $subject = trim((string)($data['subject'] ?? ''));
        $message = trim((string)($data['message'] ?? ''));

        if ($fullName === '') {
            throw new Exception('กรุณากรอกชื่อ-นามสกุล');
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('กรุณากรอกอีเมลให้ถูกต้อง');
        }
        if ($subject === '') {
            throw new Exception('กรุณากรอกหัวข้อ');
        }
        if ($message === '') {
            throw new Exception('กรุณากรอกข้อความ');
        }

        $payload = [
            'full_name' => mb_substr($fullName, 0, 190),
            'email' => mb_substr($email, 0, 190),
            'subject' => mb_substr($subject, 0, 220),
            'message' => $message,
            'status' => 'new',
        ];

        $r = $this->db->insert('contact_messages', $payload);
        return (int)($r['lastInsertID'] ?? 0);
    }

    public function listMessages($status = '')
    {
        $sql = "SELECT * FROM contact_messages WHERE 1=1";
        $params = [];
        $status = trim((string)$status);
        if ($status !== '') {
            $sql .= " AND status = :status";
            $params[':status'] = $status;
        }
        $sql .= " ORDER BY id DESC";
        return $this->db->selectAll($sql, $params);
    }

    public function getById($id)
    {
        return $this->db->selectOne(
            "SELECT * FROM contact_messages WHERE id = :id LIMIT 1",
            [':id' => (int)$id]
        );
    }

    public function updateStatus($id, $status, $adminNote = '')
    {
        $status = trim((string)$status);
        if (!isset(self::statusMap()[$status])) {
            throw new Exception('สถานะไม่ถูกต้อง');
        }

        $params = [
            ':id' => (int)$id,
            ':status' => $status,
            ':admin_note' => trim((string)$adminNote),
        ];

        $sql = "UPDATE contact_messages
                SET status = :status,
                    admin_note = :admin_note";

        if ($status === 'replied') {
            $sql .= ", replied_at = IFNULL(replied_at, NOW())";
        }

        $sql .= " WHERE id = :id";
        $this->db->query($sql, $params);
    }

    public function stats()
    {
        $all = $this->db->selectOne("SELECT COUNT(*) AS c FROM contact_messages");
        $new = $this->db->selectOne("SELECT COUNT(*) AS c FROM contact_messages WHERE status = 'new'");
        $inProgress = $this->db->selectOne("SELECT COUNT(*) AS c FROM contact_messages WHERE status = 'in_progress'");
        $replied = $this->db->selectOne("SELECT COUNT(*) AS c FROM contact_messages WHERE status = 'replied'");

        return [
            'total' => (int)($all['c'] ?? 0),
            'new' => (int)($new['c'] ?? 0),
            'in_progress' => (int)($inProgress['c'] ?? 0),
            'replied' => (int)($replied['c'] ?? 0),
        ];
    }
}

