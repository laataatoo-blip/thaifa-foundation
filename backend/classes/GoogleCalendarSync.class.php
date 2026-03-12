<?php

if (!class_exists('CalendarManagement')) {
    include_once(__DIR__ . '/CalendarManagement.class.php');
}

class GoogleCalendarSync
{
    private const DEFAULT_PUBLIC_CALENDAR_URL = 'https://calendar.google.com/calendar/u/0?cid=ODU4MWUwZmMzMWM4NWJjNmE3N2IxYjE0NGM5ZDJjMTljMGJkOTA1ZWI1ZDJlN2E4YzcwMmI2ZTMwMDljY2RhOEBncm91cC5jYWxlbmRhci5nb29nbGUuY29t';
    private $calendar;

    public function __construct(CalendarManagement $calendar)
    {
        $this->calendar = $calendar;
    }

    public function syncFromEnv($adminId = null)
    {
        $icsUrl = self::resolveConfiguredIcsUrl();
        if ($icsUrl === '') {
            throw new Exception('ยังไม่ได้ตั้งค่า Google Calendar สำหรับซิงก์');
        }
        return $this->syncFromIcsUrl($icsUrl, $adminId);
    }

    public static function resolveConfiguredIcsUrl()
    {
        $icsUrl = trim((string)(getenv('GOOGLE_CALENDAR_ICS_URL') ?: ''));
        if ($icsUrl !== '') {
            // Allow admins to paste either a direct .ics URL or a normal Google Calendar URL.
            if (preg_match('#^https?://calendar\.google\.com/#i', $icsUrl) && stripos($icsUrl, '.ics') === false) {
                $fromIcsEnv = self::buildIcsUrlFromGoogleUrl($icsUrl);
                if ($fromIcsEnv !== '') {
                    return $fromIcsEnv;
                }
            }
            return $icsUrl;
        }

        $publicUrl = trim((string)(getenv('GOOGLE_CALENDAR_PUBLIC_URL') ?: ''));
        if ($publicUrl === '') {
            $publicUrl = self::DEFAULT_PUBLIC_CALENDAR_URL;
        }

        $fromPublic = self::buildIcsUrlFromGoogleUrl($publicUrl);
        if ($fromPublic !== '') {
            return $fromPublic;
        }

        return '';
    }

    public static function resolveConfiguredIcsCandidates()
    {
        $icsUrl = self::resolveConfiguredIcsUrl();
        if ($icsUrl === '') {
            return [];
        }

        $candidates = [$icsUrl];
        foreach (self::buildIcsUrlCandidatesFromGoogleUrl($icsUrl) as $u) {
            if (!in_array($u, $candidates, true)) {
                $candidates[] = $u;
            }
        }
        return $candidates;
    }

    public static function buildIcsUrlFromGoogleUrl($googleUrl)
    {
        $googleUrl = trim((string)$googleUrl);
        if ($googleUrl === '') {
            return '';
        }

        if (preg_match('#^https?://calendar\.google\.com/calendar/ical/.+\.ics$#i', $googleUrl)) {
            return $googleUrl;
        }

        $parts = @parse_url($googleUrl);
        if (!is_array($parts)) {
            return '';
        }

        $query = [];
        if (!empty($parts['query'])) {
            parse_str($parts['query'], $query);
        }

        $cid = trim((string)($query['cid'] ?? $query['src'] ?? ''));
        if ($cid === '' && !empty($parts['path'])) {
            if (preg_match('#/calendar/embed#', (string)$parts['path']) && !empty($query['src'])) {
                $cid = trim((string)$query['src']);
            }
        }
        if ($cid === '') {
            return '';
        }

        if (strpos($cid, '@') === false && preg_match('/^[A-Za-z0-9+\/_=.-]+$/', $cid)) {
            $decoded = base64_decode(strtr($cid, '-_', '+/'), true);
            if (is_string($decoded) && strpos($decoded, '@') !== false) {
                $cid = $decoded;
            }
        }

        if (strpos($cid, '@') === false) {
            return '';
        }

        return 'https://calendar.google.com/calendar/ical/' . rawurlencode($cid) . '/public/basic.ics';
    }

    public static function buildIcsUrlCandidatesFromGoogleUrl($googleUrl)
    {
        $googleUrl = trim((string)$googleUrl);
        if ($googleUrl === '') {
            return [];
        }

        if (preg_match('#^https?://calendar\.google\.com/calendar/ical/.+\.ics$#i', $googleUrl)) {
            return [$googleUrl];
        }

        $parts = @parse_url($googleUrl);
        if (!is_array($parts)) {
            return [];
        }

        $query = [];
        if (!empty($parts['query'])) {
            parse_str($parts['query'], $query);
        }

        $cid = trim((string)($query['cid'] ?? $query['src'] ?? ''));
        if ($cid === '' && !empty($parts['path']) && preg_match('#/calendar/embed#', (string)$parts['path']) && !empty($query['src'])) {
            $cid = trim((string)$query['src']);
        }
        if ($cid === '') {
            return [];
        }

        if (strpos($cid, '@') === false && preg_match('/^[A-Za-z0-9+\/_=.-]+$/', $cid)) {
            $decoded = base64_decode(strtr($cid, '-_', '+/'), true);
            if (is_string($decoded) && strpos($decoded, '@') !== false) {
                $cid = $decoded;
            }
        }

        if (strpos($cid, '@') === false) {
            return [];
        }

        $cidEnc = rawurlencode($cid);
        return [
            'https://calendar.google.com/calendar/ical/' . $cidEnc . '/public/basic.ics',
            'https://calendar.google.com/calendar/ical/' . $cidEnc . '/public/full.ics',
        ];
    }

    public function syncFromIcsUrl($icsUrl, $adminId = null)
    {
        $icsUrl = trim((string)$icsUrl);
        $icsCandidates = [$icsUrl];
        if ($icsUrl !== '' && preg_match('#^https?://calendar\.google\.com/#i', $icsUrl)) {
            $icsCandidates = self::buildIcsUrlCandidatesFromGoogleUrl($icsUrl);
            if (empty($icsCandidates)) {
                $converted = self::buildIcsUrlFromGoogleUrl($icsUrl);
                if ($converted !== '') {
                    $icsCandidates = [$converted];
                } else {
                    $icsCandidates = [$icsUrl];
                }
            }
        }

        $events = $this->fetchAndParseIcsCandidates($icsCandidates);
        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($events as $event) {
            $uid = trim((string)($event['uid'] ?? ''));
            if ($uid === '') {
                $uid = sha1(($event['title'] ?? '') . '|' . ($event['start_at'] ?? ''));
            }

            $title = trim((string)($event['title'] ?? ''));
            $startAt = trim((string)($event['start_at'] ?? ''));
            if ($title === '' || $startAt === '') {
                $skipped++;
                continue;
            }

            $description = trim((string)($event['description'] ?? ''));
            $summary = $description !== '' ? mb_substr($description, 0, 220, 'UTF-8') : '';

            $payload = [
                'type_id' => null,
                'title' => $title,
                'summary' => $summary,
                'description' => $description,
                'location' => trim((string)($event['location'] ?? '')),
                'start_at' => $startAt,
                'end_at' => !empty($event['end_at']) ? $event['end_at'] : null,
                'is_all_day' => !empty($event['is_all_day']) ? 1 : 0,
                'is_visible' => 1,
            ];

            $existing = $this->calendar->findBySource('google_ics', $uid);
            $id = $this->calendar->saveSyncedEvent('google_ics', $uid, $payload, $adminId);
            if (!empty($existing['id'])) {
                $updated++;
            } elseif ($id > 0) {
                $created++;
            } else {
                $skipped++;
            }
        }

        return [
            'total' => count($events),
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
        ];
    }

    private function fetchAndParseIcsCandidates(array $icsCandidates)
    {
        $lastException = null;
        $used = [];
        foreach ($icsCandidates as $candidate) {
            $candidate = trim((string)$candidate);
            if ($candidate === '' || in_array($candidate, $used, true)) {
                continue;
            }
            $used[] = $candidate;

            try {
                return $this->fetchAndParseIcs($candidate);
            } catch (Throwable $e) {
                $lastException = $e;
            }
        }

        if ($lastException instanceof Throwable) {
            throw $lastException;
        }
        throw new Exception('ไม่พบลิงก์ ICS ที่พร้อมใช้งานสำหรับซิงก์');
    }

    private function fetchAndParseIcs($icsUrl)
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 25,
                'header' => "User-Agent: THAIFA-CalendarSync/1.0\r\n",
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
            ],
        ]);
        $raw = @file_get_contents($icsUrl, false, $context);
        if ($raw === false || trim($raw) === '') {
            $status = '';
            if (!empty($http_response_header[0])) {
                $status = ' (' . trim((string)$http_response_header[0]) . ')';
            }
            throw new Exception('ไม่สามารถดึงข้อมูลจาก Google Calendar ICS URL ได้' . $status);
        }

        if (stripos((string)$raw, 'BEGIN:VCALENDAR') === false) {
            throw new Exception('ลิงก์ Google Calendar นี้ยังไม่เปิดให้อ่านแบบ ICS กรุณาเปิด Public calendar หรือใช้ Secret address in iCal format');
        }

        $raw = str_replace("\r\n", "\n", (string)$raw);
        $lines = explode("\n", $raw);
        $unfolded = [];
        foreach ($lines as $line) {
            if ($line === '') {
                $unfolded[] = '';
                continue;
            }
            $firstChar = substr($line, 0, 1);
            if (($firstChar === ' ' || $firstChar === "\t") && !empty($unfolded)) {
                $unfolded[count($unfolded) - 1] .= substr($line, 1);
            } else {
                $unfolded[] = $line;
            }
        }

        $events = [];
        $current = null;
        foreach ($unfolded as $line) {
            $line = trim($line);
            if ($line === 'BEGIN:VEVENT') {
                $current = [];
                continue;
            }
            if ($line === 'END:VEVENT') {
                if (is_array($current)) {
                    $mapped = $this->mapRawEvent($current);
                    if (!empty($mapped)) {
                        $events[] = $mapped;
                    }
                }
                $current = null;
                continue;
            }
            if (!is_array($current) || strpos($line, ':') === false) {
                continue;
            }

            [$namePart, $value] = explode(':', $line, 2);
            $namePart = trim((string)$namePart);
            $value = trim((string)$value);

            $nameSeg = explode(';', $namePart);
            $prop = strtoupper((string)array_shift($nameSeg));
            $params = [];
            foreach ($nameSeg as $p) {
                if (strpos($p, '=') !== false) {
                    [$k, $v] = explode('=', $p, 2);
                    $params[strtoupper(trim((string)$k))] = trim((string)$v);
                }
            }
            $current[$prop] = ['value' => $value, 'params' => $params];
        }

        return $events;
    }

    private function mapRawEvent(array $raw)
    {
        $uid = (string)($raw['UID']['value'] ?? '');
        $title = (string)($raw['SUMMARY']['value'] ?? '');
        $description = (string)($raw['DESCRIPTION']['value'] ?? '');
        $location = (string)($raw['LOCATION']['value'] ?? '');

        $start = $this->parseIcsDate(
            (string)($raw['DTSTART']['value'] ?? ''),
            $raw['DTSTART']['params'] ?? []
        );
        if (empty($start['datetime'])) {
            return [];
        }
        $end = $this->parseIcsDate(
            (string)($raw['DTEND']['value'] ?? ''),
            $raw['DTEND']['params'] ?? []
        );

        return [
            'uid' => $uid,
            'title' => html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'description' => html_entity_decode(str_replace('\\n', "\n", $description), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'location' => html_entity_decode($location, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'start_at' => $start['datetime'],
            'end_at' => $end['datetime'] ?? null,
            'is_all_day' => !empty($start['is_all_day']) ? 1 : 0,
        ];
    }

    private function parseIcsDate($value, array $params = [])
    {
        $value = trim((string)$value);
        if ($value === '') {
            return ['datetime' => null, 'is_all_day' => 0];
        }

        $isAllDay = strtoupper((string)($params['VALUE'] ?? '')) === 'DATE' || preg_match('/^\d{8}$/', $value);
        $tz = trim((string)($params['TZID'] ?? ''));

        try {
            if ($isAllDay) {
                $dt = DateTime::createFromFormat('Ymd', substr($value, 0, 8), new DateTimeZone('Asia/Bangkok'));
                return ['datetime' => $dt ? $dt->format('Y-m-d 00:00:00') : null, 'is_all_day' => 1];
            }

            if (preg_match('/^\d{8}T\d{6}Z$/', $value)) {
                $dt = DateTime::createFromFormat('Ymd\THis\Z', $value, new DateTimeZone('UTC'));
                if ($dt) {
                    $dt->setTimezone(new DateTimeZone('Asia/Bangkok'));
                    return ['datetime' => $dt->format('Y-m-d H:i:s'), 'is_all_day' => 0];
                }
            }

            if (preg_match('/^\d{8}T\d{6}$/', $value)) {
                $zone = $tz !== '' ? new DateTimeZone($tz) : new DateTimeZone('Asia/Bangkok');
                $dt = DateTime::createFromFormat('Ymd\THis', $value, $zone);
                if ($dt) {
                    $dt->setTimezone(new DateTimeZone('Asia/Bangkok'));
                    return ['datetime' => $dt->format('Y-m-d H:i:s'), 'is_all_day' => 0];
                }
            }

            if (preg_match('/^\d{8}T\d{4}$/', $value)) {
                $zone = $tz !== '' ? new DateTimeZone($tz) : new DateTimeZone('Asia/Bangkok');
                $dt = DateTime::createFromFormat('Ymd\THi', $value, $zone);
                if ($dt) {
                    $dt->setTimezone(new DateTimeZone('Asia/Bangkok'));
                    return ['datetime' => $dt->format('Y-m-d H:i:s'), 'is_all_day' => 0];
                }
            }
        } catch (Throwable $e) {
            return ['datetime' => null, 'is_all_day' => 0];
        }

        return ['datetime' => null, 'is_all_day' => 0];
    }
}
