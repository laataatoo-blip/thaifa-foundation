<?php

if (!class_exists('DatabaseManagement')) {
    include_once(__DIR__ . '/DatabaseManagement.class.php');
}
if (!class_exists('FoundationSchema')) {
    include_once(__DIR__ . '/FoundationSchema.class.php');
}

class TeamManagement
{
    private $db;

    public static function groupLabels()
    {
        return [
            'advisors' => 'ที่ปรึกษา',
            'executives' => 'กรรมการบริหาร',
            'committee' => 'คณะกรรมการ',
        ];
    }

    public function __construct()
    {
        $this->db = new DatabaseManagement();
        FoundationSchema::ensure($this->db);
    }

    public static function getTemplateMembers()
    {
        $configPath = __DIR__ . '/../config/team_template.php';
        if (is_file($configPath)) {
            $template = include $configPath;
            if (is_array($template)) {
                return $template;
            }
        }
        return [
            'advisors' => [],
            'executives' => [],
            'committee' => [],
        ];
    }

    public function listMembers($groupKey = '', $activeOnly = false)
    {
        $sql = "SELECT * FROM foundation_team_members WHERE 1=1";
        $params = [];
        if ($groupKey !== '') {
            $sql .= " AND group_key = :group_key";
            $params[':group_key'] = $groupKey;
        }
        if ($activeOnly) {
            $sql .= " AND is_active = 1";
        }
        $sql .= " ORDER BY group_key ASC, sort_order ASC, id ASC";
        return $this->db->selectAll($sql, $params);
    }

    public function membersGrouped($activeOnly = true)
    {
        $rows = $this->listMembers('', $activeOnly);
        $g = ['advisors' => [], 'executives' => [], 'committee' => []];
        foreach ($rows as $r) {
            $key = (string)($r['group_key'] ?? 'committee');
            if (!isset($g[$key])) $g[$key] = [];
            $g[$key][] = $r;
        }
        return $g;
    }

    public function getMemberById($id)
    {
        return $this->db->selectOne("SELECT * FROM foundation_team_members WHERE id = :id LIMIT 1", [':id' => (int)$id]);
    }

    public function saveMember($id, $data)
    {
        $name = trim((string)($data['member_name'] ?? ''));
        $group = trim((string)($data['group_key'] ?? 'committee'));
        if ($name === '') throw new Exception('กรุณากรอกชื่อบุคลากร');
        if (!isset(self::groupLabels()[$group])) throw new Exception('หมวดบุคลากรไม่ถูกต้อง');

        $payload = [
            'group_key' => $group,
            'member_name' => $name,
            'member_title' => trim((string)($data['member_title'] ?? '')),
            'member_bio' => trim((string)($data['member_bio'] ?? '')),
            'photo_url' => trim((string)($data['photo_url'] ?? '')),
            'sort_order' => (int)($data['sort_order'] ?? 0),
            'is_active' => !empty($data['is_active']) ? 1 : 0,
        ];

        if ((int)$id > 0) {
            $this->db->query(
                "UPDATE foundation_team_members SET
                    group_key = :group_key,
                    member_name = :member_name,
                    member_title = :member_title,
                    member_bio = :member_bio,
                    photo_url = :photo_url,
                    sort_order = :sort_order,
                    is_active = :is_active
                 WHERE id = :id",
                [
                    ':group_key' => $payload['group_key'],
                    ':member_name' => $payload['member_name'],
                    ':member_title' => $payload['member_title'],
                    ':member_bio' => $payload['member_bio'],
                    ':photo_url' => $payload['photo_url'],
                    ':sort_order' => $payload['sort_order'],
                    ':is_active' => $payload['is_active'],
                    ':id' => (int)$id,
                ]
            );
            return (int)$id;
        }

        $r = $this->db->insert('foundation_team_members', $payload);
        return (int)($r['lastInsertID'] ?? 0);
    }

    public function deleteMember($id)
    {
        $this->db->query("DELETE FROM foundation_team_members WHERE id = :id", [':id' => (int)$id]);
    }

    public function applyTemplateMembers($replaceAll = true)
    {
        $template = self::getTemplateMembers();
        $validGroups = self::groupLabels();

        if ($replaceAll) {
            $this->db->query("DELETE FROM foundation_team_members");
        }

        $inserted = 0;
        foreach ($template as $groupKey => $members) {
            if (!isset($validGroups[$groupKey]) || !is_array($members)) {
                continue;
            }

            $sortOrder = 1;
            foreach ($members as $m) {
                $name = trim((string)($m['member_name'] ?? ''));
                if ($name === '') {
                    continue;
                }
                $this->db->insert('foundation_team_members', [
                    'group_key' => $groupKey,
                    'member_name' => $name,
                    'member_title' => trim((string)($m['member_title'] ?? '')),
                    'member_bio' => trim((string)($m['member_bio'] ?? '')),
                    'photo_url' => trim((string)($m['photo_url'] ?? '')),
                    'sort_order' => (int)($m['sort_order'] ?? $sortOrder),
                    'is_active' => isset($m['is_active']) ? (int)!empty($m['is_active']) : 1,
                ]);
                $sortOrder++;
                $inserted++;
            }
        }

        return $inserted;
    }
}
