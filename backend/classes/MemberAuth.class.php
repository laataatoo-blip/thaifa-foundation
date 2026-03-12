<?php

if (!class_exists('DatabaseManagement')) {
    include_once(__DIR__ . '/DatabaseManagement.class.php');
}
if (!class_exists('MemberSchema')) {
    include_once(__DIR__ . '/MemberSchema.class.php');
}

class MemberAuth
{
    const SESSION_KEY = 'thaifa_member_id';

    private $dbm;
    private $pdo;

    public function __construct()
    {
        $this->dbm = new DatabaseManagement();
        MemberSchema::ensure($this->dbm);
        $this->pdo = $this->dbm->db();
        $this->ensureSession();
    }

    private function ensureSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function isLoggedIn()
    {
        return $this->currentMember() !== null;
    }

    public function currentMember()
    {
        $this->ensureSession();
        $id = (int)($_SESSION[self::SESSION_KEY] ?? 0);
        if ($id <= 0) {
            return null;
        }

        $stmt = $this->pdo->prepare(
            "SELECT id, email, first_name, last_name, phone, line_id, address, role_key, status, last_login_at, created_at
             FROM foundation_members
             WHERE id = :id AND status = 'active'
             LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        $member = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$member) {
            unset($_SESSION[self::SESSION_KEY]);
            return null;
        }

        return $member;
    }

    public function login($identity, $password)
    {
        $identity = trim((string)$identity);
        $password = (string)$password;

        if ($identity === '' || $password === '') {
            throw new Exception('กรุณากรอกข้อมูลเข้าสู่ระบบให้ครบ');
        }

        $stmt = $this->pdo->prepare(
            "SELECT *
             FROM foundation_members
             WHERE (email = :identity OR phone = :identity)
               AND status = 'active'
             LIMIT 1"
        );
        $stmt->execute([':identity' => $identity]);
        $member = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$member || !password_verify($password, (string)$member['password_hash'])) {
            throw new Exception('อีเมล/เบอร์โทร หรือรหัสผ่านไม่ถูกต้อง');
        }

        $_SESSION[self::SESSION_KEY] = (int)$member['id'];
        $this->pdo->prepare("UPDATE foundation_members SET last_login_at = NOW() WHERE id = :id")
            ->execute([':id' => (int)$member['id']]);

        return $this->currentMember();
    }

    public function register($data)
    {
        $email = strtolower(trim((string)($data['email'] ?? '')));
        $password = (string)($data['password'] ?? '');
        $confirmPassword = (string)($data['confirm_password'] ?? '');
        $firstName = trim((string)($data['first_name'] ?? ''));
        $lastName = trim((string)($data['last_name'] ?? ''));
        $phone = preg_replace('/[^0-9+]/', '', (string)($data['phone'] ?? ''));
        $lineId = trim((string)($data['line_id'] ?? ''));
        $address = trim((string)($data['address'] ?? ''));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('รูปแบบอีเมลไม่ถูกต้อง');
        }
        if ($firstName === '' || $lastName === '' || $phone === '') {
            throw new Exception('กรุณากรอกชื่อ นามสกุล และเบอร์โทรให้ครบ');
        }
        if (strlen($password) < 8) {
            throw new Exception('รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร');
        }
        if ($password !== $confirmPassword) {
            throw new Exception('ยืนยันรหัสผ่านไม่ตรงกัน');
        }

        $check = $this->pdo->prepare(
            "SELECT id FROM foundation_members WHERE email = :email LIMIT 1"
        );
        $check->execute([':email' => $email]);
        if ($check->fetch(PDO::FETCH_ASSOC)) {
            throw new Exception('อีเมลนี้ถูกใช้งานแล้ว');
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO foundation_members
             (email, password_hash, first_name, last_name, phone, line_id, address, role_key, status)
             VALUES
             (:email, :password_hash, :first_name, :last_name, :phone, :line_id, :address, 'member', 'active')"
        );
        $stmt->execute([
            ':email' => $email,
            ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':phone' => $phone,
            ':line_id' => $lineId !== '' ? $lineId : null,
            ':address' => $address !== '' ? $address : null,
        ]);

        $_SESSION[self::SESSION_KEY] = (int)$this->pdo->lastInsertId();
        return $this->currentMember();
    }

    public function logout()
    {
        $this->ensureSession();
        unset($_SESSION[self::SESSION_KEY]);
    }

    public function requestPasswordReset($identity)
    {
        $identity = trim((string)$identity);
        if ($identity === '') {
            throw new Exception('กรุณากรอกอีเมลหรือเบอร์โทรที่ใช้สมัคร');
        }

        $stmt = $this->pdo->prepare(
            "SELECT id, email, first_name, last_name
             FROM foundation_members
             WHERE (email = :identity OR phone = :identity)
               AND status = 'active'
             LIMIT 1"
        );
        $stmt->execute([':identity' => $identity]);
        $member = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$member) {
            return [
                'sent' => true,
                'token' => null,
                'reset_link' => null,
            ];
        }

        $token = bin2hex(random_bytes(24));
        $tokenHash = password_hash($token, PASSWORD_DEFAULT);

        $this->pdo->prepare(
            "UPDATE foundation_member_password_resets
             SET used_at = NOW()
             WHERE member_id = :member_id AND used_at IS NULL"
        )->execute([':member_id' => (int)$member['id']]);

        $this->pdo->prepare(
            "INSERT INTO foundation_member_password_resets (member_id, token_hash, expires_at)
             VALUES (:member_id, :token_hash, DATE_ADD(NOW(), INTERVAL 60 MINUTE))"
        )->execute([
            ':member_id' => (int)$member['id'],
            ':token_hash' => $tokenHash,
        ]);

        return [
            'sent' => true,
            'token' => $token,
            'reset_link' => 'reset-password.php?token=' . urlencode($token),
            'member' => $member,
        ];
    }

    public function validateResetToken($token)
    {
        return $this->findResetRowByToken($token) !== null;
    }

    public function resetPasswordByToken($token, $newPassword, $confirmPassword)
    {
        $token = trim((string)$token);
        $newPassword = (string)$newPassword;
        $confirmPassword = (string)$confirmPassword;

        if (strlen($newPassword) < 8) {
            throw new Exception('รหัสผ่านใหม่ต้องมีอย่างน้อย 8 ตัวอักษร');
        }
        if ($newPassword !== $confirmPassword) {
            throw new Exception('ยืนยันรหัสผ่านใหม่ไม่ตรงกัน');
        }

        $reset = $this->findResetRowByToken($token);
        if (!$reset) {
            throw new Exception('ลิงก์รีเซ็ตรหัสผ่านไม่ถูกต้องหรือหมดอายุแล้ว');
        }

        $this->pdo->prepare(
            "UPDATE foundation_members
             SET password_hash = :password_hash, updated_at = NOW()
             WHERE id = :member_id"
        )->execute([
            ':password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
            ':member_id' => (int)$reset['member_id'],
        ]);

        $this->pdo->prepare(
            "UPDATE foundation_member_password_resets
             SET used_at = NOW()
             WHERE id = :id"
        )->execute([':id' => (int)$reset['id']]);

        $_SESSION[self::SESSION_KEY] = (int)$reset['member_id'];

        return $this->currentMember();
    }

    private function findResetRowByToken($token)
    {
        $token = trim((string)$token);
        if ($token === '') {
            return null;
        }

        $rows = $this->pdo->query(
            "SELECT r.*, m.status AS member_status
             FROM foundation_member_password_resets r
             INNER JOIN foundation_members m ON m.id = r.member_id
             WHERE r.used_at IS NULL
               AND r.expires_at > NOW()
               AND m.status = 'active'
             ORDER BY r.id DESC
             LIMIT 100"
        )->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            if (password_verify($token, (string)$row['token_hash'])) {
                return $row;
            }
        }

        return null;
    }
}
