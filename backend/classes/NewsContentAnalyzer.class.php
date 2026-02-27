<?php

class NewsContentAnalyzer
{
    private static function isGenericTitle($text)
    {
        $text = trim((string)$text);
        if ($text === '') return true;

        if (preg_match('/^มูลนิธิตัวแทนประกันชีวิตและที่ปรึกษาการเงิน$/u', $text)) return true;
        if (preg_match('/^thaifa foundation$/iu', $text)) return true;
        if (preg_match('/^mูลนิธิ|^สมาคม/u', $text) && mb_strlen($text, 'UTF-8') < 45) return true;

        return false;
    }

    private static function pickTitleFromDetail($detail)
    {
        $detail = trim((string)$detail);
        if ($detail === '') return '';

        $preferred = [
            'ร่วม',
            'มอบ',
            'ประชุม',
            'อบรม',
            'สัมมนา',
            'ทุน',
            'บริจาค',
            'โครงการ',
            'งาน',
            'เชิญ'
        ];

        $lines = preg_split('/\R/u', $detail);
        $best = '';
        foreach ($lines as $line) {
            $line = trim((string)$line);
            if ($line === '') continue;
            if (preg_match('/^ที่มาโพสต์ Facebook/u', $line)) continue;
            if (preg_match('/^https?:\/\//i', $line)) continue;
            if (preg_match('/^วันที่|^เวลา/u', $line)) continue;

            $clean = preg_replace('/\s+/u', ' ', $line ?? '');
            $clean = trim((string)$clean, " \t\n\r\0\x0B.-|");
            if ($clean === '') continue;

            if (self::isGenericTitle($clean)) {
                if ($best === '') $best = $clean;
                continue;
            }

            foreach ($preferred as $kw) {
                if (mb_strpos($clean, $kw) !== false) {
                    return $clean;
                }
            }

            if ($best === '' || mb_strlen($clean, 'UTF-8') > mb_strlen($best, 'UTF-8')) {
                $best = $clean;
            }
        }

        return $best;
    }

    public static function normalizeNewsTitle($title, $detail = '')
    {
        $title = trim((string)$title);
        $detail = trim((string)$detail);

        if ($title !== '') {
            $parts = array_values(array_filter(array_map('trim', explode('|', $title)), function ($v) {
                return $v !== '';
            }));
            if (!empty($parts)) {
                $pick = $parts[0];
                if (preg_match('/ยอดดู|ความรู้สึก|ครั้ง/u', $pick) && isset($parts[1])) {
                    $pick = $parts[1];
                }
                $title = $pick;
            }
        }

        if (($title === '' || self::isGenericTitle($title)) && $detail !== '') {
            $fromDetail = self::pickTitleFromDetail($detail);
            if ($fromDetail !== '') {
                $title = $fromDetail;
            }
        }

        $title = preg_replace('/\s+/u', ' ', $title ?? '');
        $title = trim((string)$title, " \t\n\r\0\x0B.-|");
        if (self::isGenericTitle($title) && $detail !== '') {
            $fromDetail = self::pickTitleFromDetail($detail);
            if ($fromDetail !== '' && !self::isGenericTitle($fromDetail)) {
                $title = $fromDetail;
            }
        }
        if (mb_strlen($title, 'UTF-8') > 255) {
            $title = mb_substr($title, 0, 252, 'UTF-8') . '...';
        }

        return $title;
    }

    public static function normalizeNewsCategory($category)
    {
        $category = trim((string)$category);
        $category = preg_replace('/\s+/u', ' ', $category ?? '');
        $lower = mb_strtolower($category, 'UTF-8');

        $map = [
            'general' => 'ทั่วไป',
            'facebook' => 'ประชาสัมพันธ์',
            'event' => 'กิจกรรม',
            'activity' => 'กิจกรรม',
            'donation' => 'บริจาค',
            'education' => 'ทุนการศึกษา',
            'meeting' => 'ประชุม',
            'training' => 'อบรม/สัมมนา',
            'seminar' => 'อบรม/สัมมนา',
            'pr' => 'ประชาสัมพันธ์'
        ];

        if (isset($map[$lower])) {
            $category = $map[$lower];
        }

        if ($category === '' || strpos($category, 'à') !== false || preg_match('/^\?+$/', $category)) {
            $category = 'ทั่วไป';
        }

        if (mb_strlen($category, 'UTF-8') > 100) {
            $category = mb_substr($category, 0, 100, 'UTF-8');
        }

        return $category;
    }

    public static function detectCategory($title, $detail = '', $sourceUrl = '', $existingCategory = '')
    {
        $existingCategory = self::normalizeNewsCategory($existingCategory);
        if ($existingCategory !== '' && $existingCategory !== 'ทั่วไป') {
            return $existingCategory;
        }

        $hay = mb_strtolower(trim((string)$title . ' ' . (string)$detail . ' ' . (string)$sourceUrl), 'UTF-8');

        $rules = [
            'ทุนการศึกษา' => ['ทุน', 'การศึกษา', 'นักเรียน', 'นักศึกษา', 'มอบทุน'],
            'บริจาค' => ['บริจาค', 'เงินช่วย', 'ส่งมอบ', 'อุปกรณ์ทางการแพทย์', 'ช่วยเหลือ', 'มอบเครื่อง'],
            'อบรม/สัมมนา' => ['สัมมนา', 'อบรม', 'workshop', 'training', 'บรรยาย'],
            'ประชุม' => ['ประชุม', 'คณะกรรมการ', 'agm', 'วาระ'],
            'กิจกรรม' => ['กิจกรรม', 'ร่วมงาน', 'งานประจำปี', 'โครงการ', 'จิตอาสา', 'ทำบุญ'],
            'ประชาสัมพันธ์' => ['ประกาศ', 'แจ้ง', 'ประชาสัมพันธ์', 'เชิญชวน', 'เปิดรับ', 'facebook.com']
        ];

        foreach ($rules as $cat => $keywords) {
            foreach ($keywords as $kw) {
                if ($kw !== '' && mb_strpos($hay, mb_strtolower($kw, 'UTF-8')) !== false) {
                    return $cat;
                }
            }
        }

        return 'ทั่วไป';
    }

    public static function simplifyDetail($detail)
    {
        $detail = str_replace("\r", "\n", (string)$detail);

        $sourceBlock = '';
        if (preg_match('/\n*ที่มาโพสต์ Facebook:\s*\n?(.+)$/us', $detail, $m)) {
            $url = trim((string)($m[1] ?? ''));
            if ($url !== '') {
                $sourceBlock = "ที่มาโพสต์ Facebook:\n" . $url;
            }
            $detail = preg_replace('/\n*ที่มาโพสต์ Facebook:\s*\n?.+$/us', '', $detail);
        }

        $lines = preg_split('/\R/u', $detail);
        $clean = [];
        $seen = [];
        foreach ($lines as $line) {
            $line = trim((string)$line);
            if ($line === '') continue;
            if (preg_match('/^ยอดดู|ความรู้สึก|ความคิดเห็น|แชร์/u', $line)) continue;
            $line = preg_replace('/#[\p{L}\p{N}_]+/u', '', $line);
            $line = preg_replace('/\s+/u', ' ', $line ?? '');
            $line = trim((string)$line, " \t\n\r\0\x0B");
            if ($line === '' || isset($seen[$line])) continue;
            $seen[$line] = true;
            $clean[] = $line;
            if (count($clean) >= 10) break;
        }

        $out = trim(implode("\n", $clean));
        if ($sourceBlock !== '') {
            $out .= ($out !== '' ? "\n\n" : '') . $sourceBlock;
        }

        return $out;
    }
}
