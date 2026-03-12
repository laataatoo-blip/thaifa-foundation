<?php

if (!class_exists('DatabaseManagement')) {
    include_once(__DIR__ . '/DatabaseManagement.class.php');
}
if (!class_exists('CalendarSchema')) {
    include_once(__DIR__ . '/CalendarSchema.class.php');
}

class CalendarManagement
{
    private $db;

    public function __construct()
    {
        $this->db = new DatabaseManagement();
        CalendarSchema::ensure($this->db);
    }

    public function getDb()
    {
        return $this->db;
    }

    public function getTypes($activeOnly = true)
    {
        $sql = "SELECT * FROM calendar_event_types";
        if ($activeOnly) {
            $sql .= " WHERE is_active = 1";
        }
        $sql .= " ORDER BY sort_order ASC, id ASC";
        return $this->db->selectAll($sql);
    }

    public function saveType($id, $data)
    {
        $payload = [
            'name' => trim((string)($data['name'] ?? '')),
            'slug' => trim((string)($data['slug'] ?? '')),
            'color_hex' => trim((string)($data['color_hex'] ?? '#233882')),
            'sort_order' => (int)($data['sort_order'] ?? 0),
            'is_active' => !empty($data['is_active']) ? 1 : 0,
        ];

        if ($payload['name'] === '') {
            throw new Exception('กรุณากรอกชื่อประเภทกิจกรรม');
        }

        if ($payload['slug'] === '') {
            $payload['slug'] = $this->slugify($payload['name']);
        }

        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $payload['color_hex'])) {
            $payload['color_hex'] = '#233882';
        }

        if ((int)$id > 0) {
            $this->db->query(
                "UPDATE calendar_event_types SET
                    name = :name,
                    slug = :slug,
                    color_hex = :color_hex,
                    sort_order = :sort_order,
                    is_active = :is_active
                 WHERE id = :id",
                [
                    ':name' => $payload['name'],
                    ':slug' => $payload['slug'],
                    ':color_hex' => $payload['color_hex'],
                    ':sort_order' => $payload['sort_order'],
                    ':is_active' => $payload['is_active'],
                    ':id' => (int)$id,
                ]
            );
            return (int)$id;
        }

        $r = $this->db->insert('calendar_event_types', $payload);
        return (int)($r['lastInsertID'] ?? 0);
    }

    public function getEvents($visibleOnly = false, $month = '', $year = 0)
    {
        $sql = "SELECT e.*, t.name AS type_name, t.color_hex, t.slug AS type_slug
                FROM calendar_events e
                LEFT JOIN calendar_event_types t ON t.id = e.type_id
                WHERE 1=1";
        $params = [];

        if ($visibleOnly) {
            $sql .= " AND e.is_visible = 1";
        }

        $month = (int)$month;
        $year = (int)$year;
        if ($month >= 1 && $month <= 12 && $year >= 2000) {
            $sql .= " AND MONTH(e.start_at) = :month AND YEAR(e.start_at) = :year";
            $params[':month'] = $month;
            $params[':year'] = $year;
        }

        $sql .= " ORDER BY e.start_at ASC, e.id ASC";
        return $this->db->selectAll($sql, $params);
    }

    public function getEventById($id)
    {
        return $this->db->selectOne(
            "SELECT e.*, t.name AS type_name, t.color_hex, t.slug AS type_slug
             FROM calendar_events e
             LEFT JOIN calendar_event_types t ON t.id = e.type_id
             WHERE e.id = :id
             LIMIT 1",
            [':id' => (int)$id]
        );
    }

    public function findBySource($sourceType, $sourceEventId)
    {
        $sourceType = trim((string)$sourceType);
        $sourceEventId = trim((string)$sourceEventId);
        if ($sourceType === '' || $sourceEventId === '') {
            return null;
        }
        return $this->db->selectOne(
            "SELECT * FROM calendar_events
             WHERE source_type = :source_type
               AND source_event_id = :source_event_id
             LIMIT 1",
            [
                ':source_type' => $sourceType,
                ':source_event_id' => $sourceEventId,
            ]
        );
    }

    public function getUpcomingEvents($limit = 10)
    {
        $limit = max(1, (int)$limit);
        return $this->db->selectAll(
            "SELECT e.*, t.name AS type_name, t.color_hex
             FROM calendar_events e
             LEFT JOIN calendar_event_types t ON t.id = e.type_id
             WHERE e.is_visible = 1
               AND e.start_at >= NOW()
             ORDER BY e.start_at ASC
             LIMIT {$limit}"
        );
    }

    public function saveEvent($id, $data, $adminId = null)
    {
        $title = trim((string)($data['title'] ?? ''));
        if ($title === '') {
            throw new Exception('กรุณากรอกชื่อกิจกรรม');
        }

        $startAt = trim((string)($data['start_at'] ?? ''));
        if ($startAt === '') {
            throw new Exception('กรุณาเลือกวันเวลาเริ่มต้นกิจกรรม');
        }

        $endAt = trim((string)($data['end_at'] ?? ''));
        $isAllDay = !empty($data['is_all_day']) ? 1 : 0;
        $startDate = date('Y-m-d H:i:s', strtotime($startAt));
        $endDate = null;

        if ($endAt !== '') {
            $endDate = date('Y-m-d H:i:s', strtotime($endAt));
            if ($endDate < $startDate) {
                throw new Exception('วันเวลาสิ้นสุดต้องไม่ก่อนวันเวลาเริ่มต้น');
            }
        }

        $payload = [
            'type_id' => (int)($data['type_id'] ?? 0) ?: null,
            'title' => $title,
            'summary' => trim((string)($data['summary'] ?? '')),
            'description' => trim((string)($data['description'] ?? '')),
            'location' => trim((string)($data['location'] ?? '')),
            'start_at' => $startDate,
            'end_at' => $endDate,
            'is_all_day' => $isAllDay,
            'is_visible' => !empty($data['is_visible']) ? 1 : 0,
        ];

        if ((int)$id > 0) {
            $payload['updated_by_admin'] = $adminId;
            $this->db->query(
                "UPDATE calendar_events SET
                    type_id = :type_id,
                    title = :title,
                    summary = :summary,
                    description = :description,
                    location = :location,
                    start_at = :start_at,
                    end_at = :end_at,
                    is_all_day = :is_all_day,
                    is_visible = :is_visible,
                    updated_by_admin = :updated_by_admin
                WHERE id = :id",
                [
                    ':type_id' => $payload['type_id'],
                    ':title' => $payload['title'],
                    ':summary' => $payload['summary'],
                    ':description' => $payload['description'],
                    ':location' => $payload['location'],
                    ':start_at' => $payload['start_at'],
                    ':end_at' => $payload['end_at'],
                    ':is_all_day' => $payload['is_all_day'],
                    ':is_visible' => $payload['is_visible'],
                    ':updated_by_admin' => $payload['updated_by_admin'],
                    ':id' => (int)$id,
                ]
            );
            return (int)$id;
        }

        $payload['created_by_admin'] = $adminId;
        $payload['updated_by_admin'] = $adminId;
        $r = $this->db->insert('calendar_events', $payload);
        return (int)($r['lastInsertID'] ?? 0);
    }

    public function updateEventDateRange($id, $startAt, $endAt = null, $allDay = 0, $adminId = null)
    {
        $id = (int)$id;
        if ($id <= 0) {
            throw new Exception('ไม่พบรหัสกิจกรรม');
        }

        $startDate = date('Y-m-d H:i:s', strtotime((string)$startAt));
        if ($startDate === false || $startDate === '1970-01-01 00:00:00') {
            throw new Exception('วันเวลาเริ่มต้นไม่ถูกต้อง');
        }

        $endDate = null;
        if (!empty($endAt)) {
            $endDate = date('Y-m-d H:i:s', strtotime((string)$endAt));
            if ($endDate < $startDate) {
                throw new Exception('วันเวลาสิ้นสุดต้องไม่ก่อนวันเวลาเริ่มต้น');
            }
        }

        $this->db->query(
            "UPDATE calendar_events
             SET start_at = :start_at,
                 end_at = :end_at,
                 is_all_day = :is_all_day,
                 updated_by_admin = :updated_by_admin
             WHERE id = :id",
            [
                ':start_at' => $startDate,
                ':end_at' => $endDate,
                ':is_all_day' => !empty($allDay) ? 1 : 0,
                ':updated_by_admin' => $adminId,
                ':id' => $id,
            ]
        );
    }

    public function saveSyncedEvent($sourceType, $sourceEventId, array $payload, $adminId = null)
    {
        $sourceType = trim((string)$sourceType);
        $sourceEventId = trim((string)$sourceEventId);
        if ($sourceType === '' || $sourceEventId === '') {
            throw new Exception('ข้อมูลอ้างอิงจากแหล่งภายนอกไม่ครบ');
        }

        $now = date('Y-m-d H:i:s');
        $payload['source_type'] = $sourceType;
        $payload['source_event_id'] = $sourceEventId;
        $payload['last_synced_at'] = $now;

        $existing = $this->findBySource($sourceType, $sourceEventId);
        if (!empty($existing['id'])) {
            $payload['updated_by_admin'] = $adminId;
            $this->db->query(
                "UPDATE calendar_events
                 SET type_id = :type_id,
                     title = :title,
                     summary = :summary,
                     description = :description,
                     location = :location,
                     start_at = :start_at,
                     end_at = :end_at,
                     is_all_day = :is_all_day,
                     is_visible = :is_visible,
                     updated_by_admin = :updated_by_admin,
                     last_synced_at = :last_synced_at
                 WHERE id = :id",
                [
                    ':type_id' => $payload['type_id'] ?? null,
                    ':title' => $payload['title'] ?? '',
                    ':summary' => $payload['summary'] ?? '',
                    ':description' => $payload['description'] ?? '',
                    ':location' => $payload['location'] ?? '',
                    ':start_at' => $payload['start_at'] ?? null,
                    ':end_at' => $payload['end_at'] ?? null,
                    ':is_all_day' => !empty($payload['is_all_day']) ? 1 : 0,
                    ':is_visible' => !empty($payload['is_visible']) ? 1 : 0,
                    ':updated_by_admin' => $adminId,
                    ':last_synced_at' => $now,
                    ':id' => (int)$existing['id'],
                ]
            );
            return (int)$existing['id'];
        }

        $payload['created_by_admin'] = $adminId;
        $payload['updated_by_admin'] = $adminId;
        $r = $this->db->insert('calendar_events', $payload);
        return (int)($r['lastInsertID'] ?? 0);
    }

    public function deleteEvent($id)
    {
        $this->db->query("DELETE FROM calendar_events WHERE id = :id", [':id' => (int)$id]);
    }

    public function toggleVisible($id, $visible)
    {
        $this->db->query(
            "UPDATE calendar_events SET is_visible = :is_visible WHERE id = :id",
            [':is_visible' => (int)$visible, ':id' => (int)$id]
        );
    }

    public function eventsForJson()
    {
        $rows = $this->db->selectAll(
            "SELECT e.id, e.title, e.summary, e.description, e.location, e.start_at, e.end_at,
                    e.is_all_day, t.name AS type_name, t.color_hex
             FROM calendar_events e
             LEFT JOIN calendar_event_types t ON t.id = e.type_id
             WHERE e.is_visible = 1
             ORDER BY e.start_at ASC, e.id ASC"
        );

        $result = [];
        foreach ($rows as $r) {
            $result[] = [
                'id' => (int)$r['id'],
                'title' => (string)$r['title'],
                'summary' => (string)($r['summary'] ?? ''),
                'description' => (string)($r['description'] ?? ''),
                'location' => (string)($r['location'] ?? ''),
                'start_at' => (string)$r['start_at'],
                'end_at' => (string)($r['end_at'] ?? ''),
                'is_all_day' => (int)($r['is_all_day'] ?? 0),
                'type_name' => (string)($r['type_name'] ?? 'กิจกรรมทั่วไป'),
                'color_hex' => (string)($r['color_hex'] ?? '#233882'),
            ];
        }
        return $result;
    }

    public function eventsForAdminJson()
    {
        $rows = $this->db->selectAll(
            "SELECT e.id, e.type_id, e.title, e.summary, e.description, e.location, e.start_at, e.end_at,
                    e.is_all_day, e.is_visible, e.source_type, e.source_event_id, e.last_synced_at,
                    t.name AS type_name, t.color_hex
             FROM calendar_events e
             LEFT JOIN calendar_event_types t ON t.id = e.type_id
             ORDER BY e.start_at ASC, e.id ASC"
        );

        $result = [];
        foreach ($rows as $r) {
            $result[] = [
                'id' => (int)$r['id'],
                'type_id' => !empty($r['type_id']) ? (int)$r['type_id'] : null,
                'title' => (string)$r['title'],
                'summary' => (string)($r['summary'] ?? ''),
                'description' => (string)($r['description'] ?? ''),
                'location' => (string)($r['location'] ?? ''),
                'start_at' => (string)$r['start_at'],
                'end_at' => (string)($r['end_at'] ?? ''),
                'is_all_day' => (int)($r['is_all_day'] ?? 0),
                'is_visible' => (int)($r['is_visible'] ?? 0),
                'source_type' => (string)($r['source_type'] ?? ''),
                'source_event_id' => (string)($r['source_event_id'] ?? ''),
                'last_synced_at' => (string)($r['last_synced_at'] ?? ''),
                'type_name' => (string)($r['type_name'] ?? 'กิจกรรมทั่วไป'),
                'color_hex' => (string)($r['color_hex'] ?? '#233882'),
            ];
        }
        return $result;
    }

    private function slugify($text)
    {
        $text = strtolower(trim((string)$text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        $text = trim((string)$text, '-');
        return $text !== '' ? $text : ('type-' . time());
    }
}
