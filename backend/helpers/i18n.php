<?php

if (!function_exists('thaifa_lang')) {
    function thaifa_lang()
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $allowed = ['th', 'en'];

        if (isset($_GET['lang'])) {
            $requested = strtolower(trim((string)$_GET['lang']));
            if (in_array($requested, $allowed, true)) {
                $_SESSION['thaifa_lang'] = $requested;
                // Avoid "Cannot modify header information" when output already started
                if (!headers_sent()) {
                    setcookie('thaifa_lang', $requested, [
                        'expires' => time() + (86400 * 180),
                        'path' => '/',
                        'httponly' => false,
                        'samesite' => 'Lax',
                    ]);
                }
                // Keep current request in sync even if cookie header could not be sent
                $_COOKIE['thaifa_lang'] = $requested;
            }
        }

        if (!empty($_SESSION['thaifa_lang']) && in_array($_SESSION['thaifa_lang'], $allowed, true)) {
            return $_SESSION['thaifa_lang'];
        }

        if (!empty($_COOKIE['thaifa_lang']) && in_array($_COOKIE['thaifa_lang'], $allowed, true)) {
            $_SESSION['thaifa_lang'] = $_COOKIE['thaifa_lang'];
            return $_COOKIE['thaifa_lang'];
        }

        return 'th';
    }
}

if (!function_exists('thaifa_t')) {
    function thaifa_t($key, $fallback = '')
    {
        $lang = thaifa_lang();
        static $dict = [
            'th' => [
                'login' => 'เข้าสู่ระบบ',
                'register' => 'สมัครสมาชิก',
                'home' => 'หน้าแรก',
                'about' => 'เกี่ยวกับเรา',
                'calendar' => 'ปฏิทิน',
                'shop' => 'ร้านค้า',
                'donate' => 'การบริจาค',
                'volunteer' => 'จิตอาสา',
                'stories' => 'เสียงจากใจ',
                'contact' => 'ติดต่อเรา',
                'lang_th' => 'ไทย',
                'lang_en' => 'EN',
                'calendar_badge' => 'ปฏิทินกิจกรรม',
                'calendar_title' => 'กิจกรรมและโครงการ',
                'calendar_subtitle' => 'ติดตามกิจกรรมและโครงการของมูลนิธิ ร่วมเป็นส่วนหนึ่งในการสร้างสังคมที่ดีขึ้น',
            ],
            'en' => [
                'login' => 'Login',
                'register' => 'Register',
                'home' => 'Home',
                'about' => 'About',
                'calendar' => 'Calendar',
                'shop' => 'Shop',
                'donate' => 'Donations',
                'volunteer' => 'Volunteer',
                'stories' => 'Stories',
                'contact' => 'Contact',
                'lang_th' => 'TH',
                'lang_en' => 'English',
                'calendar_badge' => 'Activity Calendar',
                'calendar_title' => 'Activities & Projects',
                'calendar_subtitle' => 'Follow the foundation activities and projects, and be part of building a better society.',
            ],
        ];

        if (isset($dict[$lang][$key])) {
            return $dict[$lang][$key];
        }
        if (isset($dict['th'][$key])) {
            return $dict['th'][$key];
        }
        return $fallback !== '' ? $fallback : $key;
    }
}

if (!function_exists('thaifa_lang_url')) {
    function thaifa_lang_url($lang)
    {
        $lang = strtolower(trim((string)$lang));
        if (!in_array($lang, ['th', 'en'], true)) {
            $lang = 'th';
        }

        $uri = (string)($_SERVER['REQUEST_URI'] ?? '');
        if ($uri === '') {
            return '?lang=' . $lang;
        }

        $parts = parse_url($uri);
        $path = (string)($parts['path'] ?? '');
        $query = [];
        if (!empty($parts['query'])) {
            parse_str((string)$parts['query'], $query);
        }
        $query['lang'] = $lang;
        $qs = http_build_query($query);

        return $path . ($qs !== '' ? ('?' . $qs) : '');
    }
}

if (!function_exists('thaifa_i18n_replace_map')) {
    function thaifa_i18n_replace_map()
    {
        $map = [
            // Global nav/common
            'ติดต่อเรา' => 'Contact Us',
            'หน้าแรก' => 'Home',
            'เกี่ยวกับเรา' => 'About Us',
            'ปฏิทิน' => 'Calendar',
            'ร้านค้า' => 'Shop',
            'การบริจาค' => 'Donations',
            'จิตอาสา' => 'Volunteer',
            'เสียงจากใจ' => 'Stories',
            'เข้าสู่ระบบ' => 'Login',
            'สมัครสมาชิก' => 'Register',
            'ออกจากระบบ' => 'Logout',
            'สวัสดี' => 'Hello',

            // Home/news
            'ข่าวสารและกิจกรรม' => 'News & Activities',
            'ข่าวสารล่าสุด' => 'Latest News',
            'ติดตามกิจกรรมและข่าวสารของมูลนิธิ THAIFA' => 'Follow THAIFA Foundation activities and updates.',
            'อ่านต่อ' => 'Read More',
            'ดูข่าวทั้งหมด' => 'View All News',
            'ย่อข่าวทั้งหมด' => 'Show Less',
            'ไม่พบข่าวที่ต้องการ' => 'News Not Found',
            'ข่าวอาจถูกปิดการแสดงผลหรือไม่มีอยู่ในระบบ' => 'This news may be hidden or unavailable.',
            'กลับไปหน้าข่าวสาร' => 'Back to News',

            // Calendar
            'ปฏิทินกิจกรรม' => 'Activity Calendar',
            'กิจกรรมและโครงการ' => 'Activities & Projects',
            'ติดตามกิจกรรมและโครงการของมูลนิธิ ร่วมเป็นส่วนหนึ่งในการสร้างสังคมที่ดีขึ้น' => 'Follow our foundation activities and projects, and be part of creating a better society.',
            'วันนี้' => 'Today',
            'เดือน' => 'Month',
            'สัปดาห์' => 'Week',
            'กำหนดการ' => 'Agenda',
            'ประเภทกิจกรรม' => 'Event Categories',
            'กิจกรรมที่กำลังจะมาถึง' => 'Upcoming Events',
            'ยังไม่มีกิจกรรมที่กำลังจะมาถึง' => 'No upcoming events yet.',
            'กิจกรรมทั่วไป' => 'General Event',
            'ซิงก์จาก Google Calendar' => 'Sync from Google Calendar',
            'พร้อมซิงก์ Google Calendar แล้ว' => 'Google Calendar sync is ready.',
            'จัดการปฏิทินกิจกรรม' => 'Manage Calendar Events',

            // Shop
            'ร้านค้ามูลนิธิ' => 'Foundation Shop',
            'สินค้าและของที่ระลึก' => 'Products & Souvenirs',
            'ช้อปเพื่อส่งต่อโอกาส รายได้สมทบกองทุนการกุศลของมูลนิธิ' => 'Shop to support opportunities. Proceeds go to the foundation fund.',
            'ค้นหาสินค้าในร้านมูลนิธิ' => 'Search products in foundation shop',
            'ทุกหมวดหมู่' => 'All Categories',
            'ใหม่ล่าสุด' => 'Newest',
            'ยอดนิยม' => 'Popular',
            'ราคาน้อย-มาก' => 'Price: Low to High',
            'ราคามาก-น้อย' => 'Price: High to Low',
            'เพิ่มลงตะกร้า' => 'Add to Cart',
            'คงเหลือ' => 'Stock',
            'ขายแล้ว' => 'Sold',
            'จัดส่งทั่วประเทศ' => 'Nationwide Delivery',
            'ส่งฟรีทางไปรษณีย์หรือขนส่ง' => 'Free shipping by post or courier',
            'รายได้เพื่อการกุศล' => 'Charity Revenue',
            '100% เข้ากองทุนมูลนิธิ' => '100% to Foundation Fund',
            'สินค้าคุณภาพ' => 'Quality Products',
            'ผลิตด้วยมาตรฐานสูง' => 'Produced with high standards',
            'การจัดสรรเงินกองทุน' => 'Fund Allocation',
            'ทุนการศึกษาและการสงเคราะห์' => 'Education & Welfare Support',
            'สนับสนุนการศึกษาและช่วยเหลือผู้ด้อยโอกาส' => 'Supporting education and helping underserved groups',
            'ค่าใช้จ่ายบริหาร' => 'Administrative Expenses',
            'ค่าขนส่ง ค่าใช้จ่ายในการประชุม และค่าเดินทาง' => 'Shipping, meeting costs, and travel expenses',

            // Cart / order
            'ตะกร้าสินค้า' => 'Shopping Cart',
            'เลือกซื้อสินค้าเพิ่ม' => 'Continue Shopping',
            'ตะกร้าสินค้าว่างเปล่า' => 'Your cart is empty',
            'เริ่มช้อปปิ้งเพื่อสนับสนุนมูลนิธิกันเถอะ' => 'Start shopping to support the foundation.',
            'กลับไปร้านค้า' => 'Back to Shop',
            'ไปตะกร้า' => 'Go to Cart',
            'ติดตามสถานะสินค้า' => 'Track Order Status',
            'หมายเลขคำสั่งซื้อ' => 'Order Number',
            'เบอร์โทร (ถ้ามี)' => 'Phone (optional)',
            'ดูสถานะ' => 'Check Status',
            'สถานะล่าสุด' => 'Latest Status',
            'สินค้าในคำสั่งซื้อ' => 'Order Items',
            'ยอดรวม' => 'Grand Total',

            // Auth
            'สำหรับสมาชิกมูลนิธิ' => 'For foundation members',
            'อีเมล หรือ เบอร์โทร' => 'Email or Phone',
            'รหัสผ่าน' => 'Password',
            'ลืมรหัสผ่าน?' => 'Forgot password?',
            'ยังไม่มีบัญชี?' => "Don't have an account?",
            'มีบัญชีแล้ว?' => 'Already have an account?',
            'สร้างบัญชีเพื่อสั่งซื้อสินค้าและติดตามสถานะคำสั่งซื้อ' => 'Create an account to place orders and track status.',
            'ยืนยันรหัสผ่าน' => 'Confirm Password',
            'ลืมรหัสผ่าน' => 'Forgot Password',
            'กรอกอีเมลหรือเบอร์โทรที่เคยสมัครไว้' => 'Enter your registered email or phone.',
            'กลับไปหน้าเข้าสู่ระบบ' => 'Back to Login',
            'ตั้งรหัสผ่านใหม่' => 'Reset Password',
            'ลิงก์รีเซ็ตรหัสผ่านไม่ถูกต้องหรือหมดอายุแล้ว' => 'Reset link is invalid or expired.',
            'ขอลิงก์ใหม่' => 'Request New Link',
            'รหัสผ่านใหม่' => 'New Password',
            'ยืนยันรหัสผ่านใหม่' => 'Confirm New Password',
            'บันทึกรหัสผ่านใหม่' => 'Save New Password',

            // Contact
            'เรายินดีรับฟังและให้บริการทุกท่าน' => 'We are here to listen and support you.',
            'ส่งข้อความถึงเรา' => 'Send Us a Message',
            'กรอกแบบฟอร์มด้านล่าง เราจะตอบกลับโดยเร็วที่สุด' => 'Fill in the form below. We will reply as soon as possible.',
            'ชื่อ-นามสกุล' => 'Full Name',
            'ส่งข้อความ' => 'Send Message',
            'เราจะตอบกลับภายใน 24 ชั่วโมงในวันทำการ' => 'We will respond within 24 business hours.',
            'ที่อยู่สำนักงาน' => 'Office Address',
            'เวลาทำการ' => 'Business Hours',
            'จันทร์ - ศุกร์: 09:00 - 18:00 น.' => 'Mon - Fri: 09:00 - 18:00',
            'เสาร์: 09:00 - 16:00 น.' => 'Sat: 09:00 - 16:00',
            'อาทิตย์และวันหยุดนักขัตฤกษ์: ปิด' => 'Sun & Public Holidays: Closed',

            // Volunteer / Stories
            'ร่วมเป็นจิตอาสา' => 'Join as a Volunteer',
            'เป็นจิตอาสา' => 'Be a Volunteer',
            'สมัครเป็นจิตอาสา' => 'Apply as Volunteer',
            'เสียงจากจิตอาสา' => 'Voices from Volunteers',
            'เสียงจากผู้สมัครจิตอาสา' => 'Voices from Volunteer Applicants',
            'เรื่องราวจากผู้รับ' => 'Stories from Beneficiaries',

            // More global/page titles
            'เกี่ยวกับเรา - THAIFA Foundation' => 'About - THAIFA Foundation',
            'THAIFA Foundation - มูลนิธิตัวแทนประกันชีวิตและที่ปรึกษาการเงิน' => 'THAIFA Foundation - The Insurance and Financial Advisors Foundation',
            'ปฏิทินกิจกรรม - THAIFA Foundation' => 'Activity Calendar - THAIFA Foundation',
            'ร้านค้าตระกล้า - THAIFA' => 'Foundation Shop - THAIFA',
            'การบริจาค - THAIFA Foundation' => 'Donations - THAIFA Foundation',
            'ติดต่อเรา - THAIFA Foundation' => 'Contact - THAIFA Foundation',
            'จิตอาสา - THAIFA Foundation' => 'Volunteer - THAIFA Foundation',
            'ตะกร้าสินค้า - THAIFA' => 'Shopping Cart - THAIFA',
            'เข้าสู่ระบบ - THAIFA Foundation' => 'Login - THAIFA Foundation',
            'สมัครสมาชิก - THAIFA Foundation' => 'Register - THAIFA Foundation',
            'ลืมรหัสผ่าน - THAIFA Foundation' => 'Forgot Password - THAIFA Foundation',
            'ตั้งรหัสผ่านใหม่ - THAIFA Foundation' => 'Reset Password - THAIFA Foundation',
            'ไม่พบข่าว - THAIFA Foundation' => 'News Not Found - THAIFA Foundation',
            'ข่าวที่เกี่ยวข้อง' => 'Related News',
            'ภาพประกอบข่าว' => 'News Gallery',
            'เผยแพร่เมื่อ' => 'Published on',
            'ยังไม่มีข่าวที่เผยแพร่ในขณะนี้' => 'No published news available at this moment.',
            'มูลนิธิตัวแทนประกันชีวิตและที่ปรึกษาการเงิน' => 'The Insurance and Financial Advisors Foundation',

            // About page detailed content
            'พันธกิจเพื่อสังคม' => 'Mission for Society',
            'วัตถุประสงค์หลัก' => 'Core Objectives',
            'ขับเคลื่อนการช่วยเหลืออย่างเป็นระบบ โปร่งใส และเกิดผลลัพธ์จริงต่อผู้รับประโยชน์' => 'Driving structured, transparent support that creates real impact for beneficiaries.',
            'สนับสนุนทุนการศึกษา' => 'Support Educational Scholarships',
            'แก่เด็กและเยาวชนผู้ขาดแคลนทั่วประเทศ' => 'For underprivileged children and youth nationwide',
            'จัดหาอุปกรณ์ทางการแพทย์' => 'Provide Medical Equipment',
            'ให้โรงพยาบาลของรัฐเพื่อเพิ่มโอกาสการรักษา' => 'For public hospitals to improve access to treatment',
            'สนับสนุนผู้ด้อยโอกาส' => 'Support Vulnerable Groups',
            'ผ่านกิจกรรมเพื่อผู้พิการ ผู้ป่วย และชุมชนที่ต้องการการช่วยเหลือ' => 'Through programs for people with disabilities, patients, and communities in need',
            'ส่งเสริมคุณธรรมและจิตอาสา' => 'Promote Ethics and Volunteerism',
            'ปลูกฝังค่านิยมการแบ่งปันและการช่วยเหลือในสังคมไทย' => 'Cultivating values of sharing and mutual support in Thai society',
            'ความร่วมมือ' => 'Partnerships',
            'หน่วยงานที่เกี่ยวข้อง' => 'Partner Organizations',
            'มูลนิธิฯ ทำงานร่วมกับหน่วยงานและองค์กรชั้นนำในอุตสาหกรรมประกันภัยไทย' => 'The foundation works closely with leading organizations in Thailand’s insurance industry.',
            'สมาคมประกันชีวิตไทย' => 'Thai Life Assurance Association',
            'สมาคมตัวแทนประกันชีวิตและที่ปรึกษาการเงิน' => 'Life Insurance Agents & Financial Advisors Association',
            'สมาคมประกันวินาศภัยไทย' => 'Thai General Insurance Association',
            'สถาบันประกันภัยไทย' => 'Thailand Insurance Institute',
            'สำนักงานคณะกรรมการกำกับและส่งเสริมการประกอบธุรกิจประกันภัย' => 'Office of Insurance Commission',
            'การทำงานร่วมกันกับหน่วยงานเหล่านี้ช่วยให้มูลนิธิสามารถดำเนินภารกิจเพื่อสังคมได้อย่างมีประสิทธิภาพ และโปร่งใสตามมาตรฐานสากล' => 'Working with these organizations enables the foundation to deliver social missions efficiently and transparently to international standards.',
            'ทีมงาน' => 'Our Team',
            'บุคลากรมูลนิธิ' => 'Foundation Team',
            'คณะกรรมการและทีมงานมูลนิธิที่ทุ่มเทเพื่อสังคม' => 'Dedicated committee members and team driving social impact.',
            'ยังไม่มีข้อมูลที่ปรึกษา' => 'No advisor data yet.',
            'ยังไม่มีข้อมูลกรรมการบริหาร' => 'No executive data yet.',
            'ยังไม่มีข้อมูลคณะกรรมการ' => 'No committee data yet.',
            '"จากสิ่งที่เราได้รับ คืนกลับสู่สังคม"' => '"Giving back to society from what we have received."',
            'เพิ่มเติม' => 'Read More',
            'ซ่อน' => 'Show Less',
            'มอบทุนการศึกษา' => 'Scholarship Support',
            'มูลนิธิตัวแทนประกันชีวิตและที่ปรึกษาการเงิน เดิมชื่อมูลนิธิตัวแทนประกันชีวิตเพื่อการกุศล ก่อตั้งขึ้นเมื่อปี พ.ศ. 2544 สืบเนื่องจากผลสำเร็จในการการจัดงานประชุม APLIC ครั้งที่6 (สภาที่ปรึกษาการเงินแห่งเอเซียแปซิฟิค) โดยซึ่งประเทศไทยเป็นเจ้าภาพจัดขึ้น ซึ่งการสัมมนานี้ยังคงถูกบันทึกให้เป็นการสัมมนาตัวแทนประกันชีวิตที่ยิ่งใหญ่ที่สุดครั้งหนึ่งของเอเชีย ด้วยจำนวนผู้เข้าร่วมสัมมนากว่า 11,000 คน' => 'The Insurance and Financial Advisors Foundation, formerly known as the Life Insurance Agents Charity Foundation, was established in 2001 following the success of the 6th APLIC conference (Asia Pacific Life Insurance Congress) hosted by Thailand, one of the largest life insurance agent gatherings in Asia with over 11,000 participants.',
            'คุณมนตรี แสงอุไรพร นายกสมาคมตัวแทนประกันชีวิต (ซึ่งเป็นสมาชิกในขณะนั้น) ในฐานะประธานจัดงานดังกล่าว มีดำริที่จะก่อตั้งองค์กรการกุศลที่เป็นของตัวแทนประกันชีวิตคนไทย เพื่อฝึกตัวแทนเป็นผู้ให้ แบ่งปัน และมอบสิ่งดี ๆ คืนสู่สังคม ด้วยวิสัยทัศน์ของคุณมนตรี ที่เห็นว่าการเข้าถึงการศึกษาของเยาวชนคือพื้นฐานสำคัญที่สุดในการพัฒนาเยาวชนไทยให้เจริญรุ่งหน้าเทียบกับอารยประเทศ' => 'Mr. Montri Saenguraiporn, then president of the life insurance agents association and chairman of that event, envisioned creating a Thai life-agent-led charity to cultivate giving, sharing, and social contribution. He believed access to education is the key foundation for long-term youth development in Thailand.',

            // Team roles / names
            'ประธานที่ปรึกษา' => 'Chief Advisor',
            'กรรมการบริหารมูลนิธิ' => 'Foundation Executive Board',
            'คณะกรรมการมูลนิธิ' => 'Foundation Committee',
            'ประธานกรรมการ' => 'Chairman',
            'กรรมการบริหาร' => 'Executive Board',
            'คณะกรรมการ' => 'Committee',
            'กรรมการที่ปรึกษาและประธานฝ่ายพัฒนา' => 'Advisory Director and Head of Development',
            'รองประธานฝ่ายบริหารและเหรัญญิก' => 'Vice Chair (Administration) & Treasurer',
            'รองประธานฝ่ายภูมิภาค' => 'Vice Chair (Regional Affairs)',
            'รองประธานฝ่ายหาทุนและฝ่ายกิจกรรม' => 'Vice Chair (Fundraising & Activities)',
            'กรรมการและเลขานุการ' => 'Committee Member & Secretary',
            'คุณมนตรี แสงอุไรพร' => 'Mr. Montri Saenguraiporn',
            'คุณบุษชัย หุตระกูล' => 'Mr. Butsachai Hutrakul',
            'คุณวิทยา นพศิริวงศ์' => 'Mr. Wittaya Nopsiriwong',
            'คุณชัยรัตน์ วิภากรเสฏฐ์' => 'Mr. Chairat Wipakornset',
            'คุณกนกนภา สุวรรณมาลี' => 'Ms. Kanoknapa Suwannamalee',
            'คุณปภิกาญจน์ สรรพศรี' => 'Ms. Paphikan Sapphasri',
            'คุณมฐิติ ครอบนัตกุล' => 'Mr. Mathiti Kropnatkul',
            'คุณวิทยา ชาญพานิชย์' => 'Mr. Wittaya Chanpanich',
            'คุณสุเทพ โลหิตกุล' => 'Mr. Suthep Lohitkul',
            'คุณประสิทธิ์ โรจนาภากุล' => 'Mr. Prasit Rojanaphakul',
            'คุณเทียน นารินทร์ทอง' => 'Mr. Thian Narinthong',
            'คุณภวิกา บุญขวัญจิต' => 'Ms. Pawika Bunkwanchit',
            'คุณนวรินทร์ หงษ์สยามกูร' => 'Ms. Nawarin Hongsayamkul',
            'คุณธีรวัฒน์ เรียรวาวิวัฒน์' => 'Mr. Theerawat Rianwawiwat',
            'คุณปิยวรรณ เหลืองอุทัยศิลป์' => 'Ms. Piyawan Lueanguthaisin',
            'คุณปณัฐฐิกานา วุฒิศิริธาญ์' => 'Ms. Panattikana Wuttisirithan',
            'คุณศศิพัชร เศรษฐอภิโภคุล' => 'Ms. Sasiphat Setthaophokun',
            'คุณจตุพร ฉายสีวรรณ' => 'Mr. Jatuporn Chaisiwan',
            'คุณชัตดาวัลย์ สายมาลัย' => 'Ms. Chatdawan Saimalai',
            'คุณชยากมล บุญสูตร' => 'Ms. Chayakamol Boonsut',
            'คุณณัฐชานันท์ นันพงศ์ภิโกศล' => 'Ms. Natchanan Nanphongphikorn',
            'คุณอนุรัตน์ ประจำ' => 'Mr. Anurat Pracham',
            'คุณประภีรา โอชเติปปนันท์' => 'Ms. Prapheera Ochatippanan',

            // Calendar (labels/dates)
            'รายการ' => 'List',
            'ก่อนหน้า' => 'Previous',
            'ถัดไป' => 'Next',
            'ไม่มีกิจกรรมในวันนี้' => 'No events today.',
            'ยังไม่มีกำหนดการกิจกรรม' => 'No schedule available yet.',
            'ม.ค.' => 'Jan',
            'ก.พ.' => 'Feb',
            'มี.ค.' => 'Mar',
            'เม.ย.' => 'Apr',
            'พ.ค.' => 'May',
            'มิ.ย.' => 'Jun',
            'ก.ค.' => 'Jul',
            'ส.ค.' => 'Aug',
            'ก.ย.' => 'Sep',
            'ต.ค.' => 'Oct',
            'พ.ย.' => 'Nov',
            'ธ.ค.' => 'Dec',
            'มกราคม' => 'January',
            'กุมภาพันธ์' => 'February',
            'มีนาคม' => 'March',
            'เมษายน' => 'April',
            'พฤษภาคม' => 'May',
            'มิถุนายน' => 'June',
            'กรกฎาคม' => 'July',
            'สิงหาคม' => 'August',
            'กันยายน' => 'September',
            'ตุลาคม' => 'October',
            'พฤศจิกายน' => 'November',
            'ธันวาคม' => 'December',

            // Shop/cart extra
            'ไม่พบสินค้าตามเงื่อนไข' => 'No products matched your filters.',
            'ทั่วไป' => 'General',
            'ซื้อสินค้าต่อ' => 'Continue Shopping',
            'ข้อมูลจัดส่ง' => 'Shipping Information',
            'ชื่อผู้รับ' => 'Recipient Name',
            'ที่อยู่จัดส่ง' => 'Shipping Address',
            'หมายเหตุ' => 'Note',
            'ยืนยันสั่งซื้อ' => 'Place Order',
            'ลบรายการที่เลือก' => 'Remove Selected',
            'กรุณาเลือกสินค้าอย่างน้อย 1 รายการ' => 'Please select at least 1 item.',
            'กรุณาเลือกสินค้าที่ต้องการลบ' => 'Please select items to remove.',
            'กรุณากรอกชื่อ เบอร์โทร และที่อยู่ให้ครบ' => 'Please complete recipient name, phone, and address.',
            'กรอกข้อมูลให้ครบก่อนกดยืนยันคำสั่งซื้อ' => 'Please complete all required information before placing the order.',
            'ชิ้น' => ' pcs',
            'ฟรี' => 'Free',
            'ร่วมบริจาค' => 'Donate Now',

            // Donate extra
            'บริจาคออนไลน์' => 'Online Donation',
            'ทำไมต้องบริจาค' => 'Why Donate',
            'ปลอดภัย รวดเร็ว สะดวก' => 'Safe, Fast, Convenient',
            'ปลอดภัย' => 'Secure',
            'ครั้งเดียว' => 'One-time',
            'รายเดือน' => 'Monthly',
            'ดำเนินการบริจาค' => 'Proceed to Donate',
            'ทีมงานจะตรวจสอบและติดต่อกลับ' => 'Our team will verify and contact you.',
            'ยังไม่รองรับการลดหย่อนภาษี' => 'Tax deduction support is not available yet.',
            'ยืนยันการบริจาค' => 'Confirm Donation',
            'ชื่อผู้บริจาค' => 'Donor Name',
            'ประเภทการบริจาค' => 'Donation Type',
            'ระบุจำนวนเงิน' => 'Enter Amount',
            'พร้อมรับใบเสร็จรับเงินผ่านอีเมล' => 'Receive e-receipt by email',
            'ขอบคุณที่ร่วมแบ่งปันความฝันให้เยาวชนไทย' => 'Thank you for sharing hope with Thai youth.',
            'การบริจาคของคุณไม่ว่าจะมากหรือน้อย จะช่วยเปลี่ยนชีวิตของเด็กและเยาวชนไทย' => 'Your donation, no matter the amount, helps transform the lives of Thai children and youth.',
            'ข้อมูลการชำระเงินของคุณได้รับการปกป้องด้วยระบบความปลอดภัยขั้นสูง' => 'Your payment information is protected by advanced security systems.',
            'ความมั่นใจในการบริจาค' => 'Confidence in Your Donation',
            'การบริจาคของคุณจะถูกนำไปใช้อย่างโปร่งใสและมีประสิทธิภาพสูงสุด' => 'Your donations are used transparently and with maximum efficiency.',
            'การบริจาคของคุณจะถูกนำไปใช้ในการช่วยเหลือสังคมในหลายรูปแบบ' => 'Your support is allocated to social impact programs in multiple forms.',
            'จัดหาอุปกรณ์ทางการแพทย์ให้โรงพยาบาลของรัฐ' => 'Provide medical equipment to public hospitals.',
            'มอบความอบอุ่นและโอกาสให้เด็กกำพร้าและด้อยโอกาส' => 'Provide care and opportunities to orphans and underprivileged children.',

            // Contact extra
            'ส่งข้อความเรียบร้อยแล้ว ขอบคุณที่ติดต่อเรา' => 'Message sent successfully. Thank you for contacting us.',
            'คลิกเพื่อส่งอีเมลถึงเรา' => 'Click to email us',
            'ช่องทางติดตามข่าวสาร' => 'Follow Our Channels',
            'ติดต่อสอบถามและติดตามข่าวสาร' => 'Reach us and stay updated.',
            'ดู YouTube' => 'Watch on YouTube',
            'อาคารจูเวลเลอรี่ ห้องเลขที่ 138/32' => 'Jewelry Trade Center, Room 138/32',
            'ชั้นที่ 12 เลขที่ 138' => '12th Floor, No. 138',
            'ถนนนเรศ แขวงสี่พระยา เขตบางรัก' => 'Nares Road, Si Phraya, Bang Rak',
            'กรุงเทพมหานคร 10500' => 'Bangkok 10500',
            'กรอกชื่อ-นามสกุล' => 'Enter your full name',
            'เรื่องที่ต้องการติดต่อ' => 'Subject of your inquiry',
            'เขียนข้อความของคุณที่นี่...' => 'Write your message here...',
            'ติดตามกิจกรรมและข่าวสารของมูลนิธิผ่านช่องทางต่างๆ' => 'Follow foundation activities and updates through our channels.',
            'ช่องทางติดตามข่าวสาร' => 'Follow Our Updates',
            'เยี่ยมชม Facebook' => 'Visit Facebook',
            'เพิ่มเพื่อน LINE' => 'Add us on LINE',

            // Volunteer / stories extra
            'ร่วมงานกับเรา' => 'Work with Us',
            'สนใจเป็นจิตอาสา' => 'Interested in Volunteering?',
            'ทุกคนสามารถเป็นส่วนหนึ่งของการสร้างอนาคตที่ดีกว่า' => 'Everyone can be part of building a better future.',
            'ติดต่อผ่าน LINE' => 'Contact via LINE',
            'ติดต่อทางไลน์' => 'Contact on LINE',
            'คุยรายละเอียดเบื้องต้น' => 'Initial Discussion',
            'รับใบสมัคร' => 'Receive Application Form',
            'ส่งใบสมัครกลับ' => 'Submit Application',
            'ตามกำหนดการ' => 'As Scheduled',
            'กิจกรรมมอบทุน' => 'Scholarship Activities',
            'กิจกรรมระดมทุน' => 'Fundraising Activities',
            'ทั่วประเทศไทย' => 'Nationwide',
            'กรุงเทพและปริมณฑล' => 'Bangkok and Metropolitan Area',
            'ปีละหลายครั้ง' => 'Several times a year',
            'ตามช่วงเวลาโครงการ' => 'Based on project schedule',
            'จดหมายขอบคุณ' => 'Letter of Appreciation',
            'พบปะผู้คนที่มีจิตอาสาเหมือนคุณ' => 'Meet people who share your volunteer spirit.',
            'สร้างคุณค่า' => 'Create Value',
            'เพิ่มประสบการณ์' => 'Gain Experience',
            'ได้รับจดหมายขอบคุณจากมูลนิธิ' => 'Receive a letter of appreciation from the foundation.',
            'ใช้เวลาและความสามารถสร้างคุณค่าให้สังคม' => 'Use your time and skills to create social impact.',
            'เรียนรู้ทักษะใหม่และเพิ่มประสบการณ์' => 'Learn new skills and gain real experience.',
            'พบบุคคลที่มีจิตอาสาเหมือนคุณ' => 'Meet like-minded volunteers.',
            'ที่ร่วมสร้างความเปลี่ยนแปลงที่ดีให้กับสังคม' => 'Who help create positive social change.',
            'ความสุขของผู้ให้' => 'Happiness of Giving',
            'ความในใจของผู้รับ' => 'From the Heart of Beneficiaries',
            'ความตั้งใจและแรงบันดาลใจในการเป็นจิตอาสา' => 'Intention and Inspiration for Volunteering',
            'ทุนการศึกษาไม่ได้เป็นแค่ตัวเงิน แต่เป็นความหวัง โอกาส และกำลังใจ' => 'Scholarships are not just money, but hope, opportunity, and encouragement.',
            'การเป็นจิตอาสาไม่ได้แค่ให้ แต่ยังได้รับกลับมามากมาย' => 'Volunteering is not only about giving, but also receiving meaningful value in return.',
            'ฟังเรื่องราวและความประทับใจจากผู้รับทุนและจิตอาสา' => 'Hear stories and impressions from scholarship recipients and volunteers.',
            'ทุนการศึกษา 7,000 บาท นี้ไม่ได้แค่ช่วยเรื่องค่าใช้จ่าย แต่ยังเป็นแรงจูงใจที่ยิ่งใหญ่ ทำให้ผมรู้ว่ายังมีคนเชื่อในเด็กด้อยโอกาสเหมือนผม วันนี้ผมได้เข้ามหาลัยชั้นนำในอีสานตามความฝัน และในอนาคตผมจะส่งมอบโอกาสนี้ให้เด็กๆ ในฐานะคุณครูที่ดี สอนให้เด็กเป็นคนดีในสังคมตลอดไป' => 'This 7,000 THB scholarship did more than support expenses. It gave me strong motivation and reminded me people still believe in underprivileged students like me. Today I reached my dream university in the Northeast, and I plan to pass this opportunity forward as a dedicated teacher.',
            'ทุนการศึกษา 7,000 บาท/ปี เป็นปัจจัยสำคัญที่ช่วยให้ผมสานฝันได้เต็มที่ ตั้งใจทำประโยชน์ต่อสังคม จนได้รับรางวัลผู้มีความประพฤติดีจากพุทธสมาคมฯ และรางวัลโครงงานวิทยาศาสตร์ระดับประเทศ จบการศึกษาด้วยเกรดเฉลี่ย 3.84 พวกผมสัญญาว่าจะเป็นคนดีของสังคมและเป็นคนที่สังคมต้องการ' => 'The annual 7,000 THB scholarship was a key factor that helped me pursue my goals fully. I committed to doing good for society, earned national recognition, and graduated with a 3.84 GPA. We promise to become responsible people society can rely on.',
            'การได้ร่วมเป็นจิตอาสาสอนหนังสือให้น้องๆ ทำให้ผมได้เห็นรอยยิ้มและความหวังในดวงตาของพวกเขา เป็นประสบการณ์ที่คุ้มค่ามากกว่าที่คาดคิด ทุกครั้งที่เห็นน้องๆ พัฒนาขึ้น รู้สึกภูมิใจและมีความสุขมากครับ' => 'Volunteering as a tutor let me see smiles and hope in the eyes of children. It has been more rewarding than expected. Every time I see their progress, I feel proud and deeply happy.',
            'นอกเหนือจากการสอนในโรงเรียน ฉันยังอยากทำอะไรให้มากกว่านั้น การเป็นจิตอาสากับมูลนิธิจะทำให้ฉันได้เข้าถึงเด็กๆ ที่ต้องการความช่วยเหลือจริงๆ และได้ใช้ประสบการณ์การสอนของฉันให้เกิดประโยชน์สูงสุด' => 'Beyond teaching at school, I wanted to do more. Volunteering with the foundation helps me reach children who truly need support and lets me use my teaching experience to its fullest value.',

            // Auth/messages
            'ยังไม่มีบัญชี' => "Don't have an account",
            'มีบัญชีแล้ว' => 'Already have an account',
            'กรุณายอมรับข้อตกลงก่อนสมัครสมาชิก' => 'Please accept the terms before registering.',
            'ยอมรับเงื่อนไขการใช้งานและนโยบายความเป็นส่วนตัว' => 'Accept Terms of Use and Privacy Policy',
            'สมัครสมาชิกเรียบร้อยแล้ว กรุณาเข้าสู่ระบบ' => 'Registration completed. Please log in.',
            'ตั้งรหัสผ่านใหม่เรียบร้อยแล้ว กรุณาเข้าสู่ระบบ' => 'Password reset successful. Please log in.',
            'กรุณาเข้าสู่ระบบก่อนทำรายการสั่งซื้อ' => 'Please log in before placing an order.',
            'หากพบข้อมูลบัญชีในระบบ เราได้สร้างลิงก์รีเซ็ตรหัสผ่านให้แล้ว' => 'If your account exists, a reset link has been generated.',
            'ลิงก์รีเซ็ต (สำหรับทดสอบระบบ):' => 'Reset Link (for testing):',
            'อีเมลหรือเบอร์โทร' => 'Email or Phone',
            'ขอลิงก์รีเซ็ตรหัสผ่าน' => 'Request Password Reset Link',
            'รหัสผ่าน (อย่างน้อย 8 ตัว)' => 'Password (at least 8 characters)',
            'ที่อยู่ (ไม่บังคับ)' => 'Address (optional)',
            '(ไม่บังคับ)' => '(optional)',

            // Footer long lines
            'มูลนิธิตัวแทนประกันชีวิตและที่ปรึกษาการเงิน มุ่งมั่นสร้างโอกาสและพัฒนาคุณภาพชีวิตของเด็กและเยาวชนไทย' => 'The Insurance and Financial Advisors Foundation is committed to creating opportunities and improving the quality of life for Thai children and youth.',
            'มูลนิธิทำหน้าที่เป็นศูนย์กลางของความร่วมมือระหว่างตัวแทนประกันชีวิตทั่วประเทศ' => 'The foundation serves as a collaboration hub for life insurance agents nationwide.',
            'ภายใต้เจตนารมณ์ที่จะ "คืนกำไรสู่สังคม" หลังจากประสบความสำเร็จจากการจัดงาน' => 'Under the vision of giving back to society after the success of major initiatives.',
            'ผู้เข้าร่วมกิจกรรมทั่วประเทศ' => 'Participants Nationwide',
            'ดำเนินงานอย่างต่อเนื่อง' => 'Years of Continuous Operation',
            'บาทต่อปี สนับสนุนสังคม' => 'THB per year in social support',
            'ตรง 100%' => '100% exact',
            'เต็มจอเสมอ' => 'full-bleed',
            'หนึ่งความรักจากฉัน หมื่นพันความรักฝันของเธอ.png' => 'one-love-many-dreams.png',
            'หนึ่งความรักจากฉัน หมื่นพันความฝันของเธอ' => 'One Love from Me, Thousands of Dreams for You',
            'ร่วมส่งต่อความหวัง' => 'Pass Hope Forward',
            'สร้างอนาคตที่ดีกว่า' => 'Build a Better Future',
            'ให้มีโอกาสทางการศึกษาและอนาคตที่สดใส' => 'To provide educational opportunities and a brighter future',
            'โปร่งใส ตรวจสอบได้' => 'Transparent and Verifiable',
            'เชื่อถือได้ ดำเนินงานต่อเนื่อง' => 'Trustworthy and continuously operated',

            // Category/labels exacts
            'ของที่ระลึก' => 'Souvenir',
            'หนังสือ' => 'Book',
            'เสื้อผ้า' => 'Apparel',
            'ติดตามสินค้า' => 'Track Order',
            'เช่น TF260305ABC123' => 'e.g. TF260305ABC123',
            'เพื่อยืนยันเจ้าของคำสั่งซื้อ' => 'To verify order ownership',
            'เช่น เวลาสะดวกรับสินค้า' => 'e.g. preferred delivery time',

            // Field/context exact snippets
            '>ชื่อ</label>' => '>First Name</label>',
            '>นามสกุล</label>' => '>Last Name</label>',
            '>อีเมล</label>' => '>Email</label>',
            '>เบอร์โทร</label>' => '>Phone</label>',
            'ขอบคุณที่ร่วมเคียงฝันไปด้วยกัน' => 'Thank you for sharing this dream with us.',
            'เสียงจากใจผู้รับ' => 'Stories from Beneficiaries',
            'อาคาร จูเวลเลอรี่ ห้อง 138/32 ชั้น 12' => 'Jewelry Trade Center, Room 138/32, 12th Floor',
            'เลขที่ 138 ถนนนเรศ แขวงสี่พระยา' => 'No. 138 Nares Road, Si Phraya',
            'เขตบางรัก กรุงเทพฯ 10500' => 'Bang Rak, Bangkok 10500',
            'นโยบายความเป็นส่วนตัว' => 'Privacy Policy',
            'ข้อกำหนดการใช้งาน' => 'Terms of Use',
            'รายงานประจำปี' => 'Annual Report',
            'มูลนิธิตัวแทนประกันชีวิตและที่ปรึกษาการเงิน | เลขประจำตัวผู้เสียภาษีอากร: 0993000440226' => 'Insurance and Financial Advisors Foundation | Tax ID: 0993000440226',
            'เดิมชื่อมูลนิธิตัวแทนประกันชีวิตเพื่อการกุศล ก่อตั้งขึ้นเมื่อปี พ.ศ. 2544' => 'Formerly known as the Life Insurance Agents Charity Foundation, established in 2001.',
            'สืบเนื่องจากผลสำเร็จในการการจัดงานประชุม APLIC ครั้งที่6 (สภาที่ปรึกษาการเงินแห่งเอเซียแปซิฟิค) โดยซึ่งประเทศไทยเป็นเจ้าภาพจัดขึ้น ซึ่งการสัมมนานี้ยังคงถูกบันทึกให้เป็นการสัมมนาตัวแทนประกันชีวิตที่ยิ่งใหญ่ที่สุดครั้งหนึ่งของเอเชีย ด้วยจำนวนผู้เข้าร่วมสัมมนากว่า 11,000 คน' => 'This followed the success of the 6th APLIC conference hosted by Thailand, one of Asia’s largest life-agent gatherings with over 11,000 attendees.',
            'นายกสมาคมตัวแทนประกันชีวิต (ซึ่งเป็นสมาชิกในขณะนั้น) ในฐานะประธานจัดงานดังกล่าว มีดำริที่จะก่อตั้งองค์กรการกุศลที่เป็นของตัวแทนประกันชีวิตคนไทย' => 'As association president (and member at that time), he initiated a Thai life-agent-led charitable organization.',
            'เพื่อฝึกตัวแทนเป็นผู้ให้ แบ่งปัน และมอบสิ่งดี ๆ คืนสู่สังคม ด้วยวิสัยทัศน์ของคุณมนตรี ที่เห็นว่าการเข้าถึงการศึกษาของเยาวชนคือพื้นฐานสำคัญที่สุด' => 'To cultivate giving, sharing, and social contribution, grounded in the belief that youth access to education is fundamental.',
            'ในการพัฒนาเยาวชนไทยให้เจริญรุ่งหน้าเทียบกับอารยประเทศ' => 'For advancing Thai youth to thrive at global standards.',
            'const thaiDays = [\'อา\',\'จ\',\'อ\',\'พ\',\'พฤ\',\'ศ\',\'ส\'];' => 'const thaiDays = [\'Sun\',\'Mon\',\'Tue\',\'Wed\',\'Thu\',\'Fri\',\'Sat\'];',
            'Agendaกิจกรรม' => 'Agenda',
            'ทั้งวัน' => 'All day',
            'ประชุมคณะกรรมการมูลนิธิ' => 'Foundation Committee Meeting',
            'ประชุมติดตามโครงการและแผนงานประจำเดือน' => 'Monthly project and plan review meeting',
            'ประชุมคณะกรรมการเพื่อติดตามความคืบหน้าโครงการต่างๆ ของมูลนิธิ' => 'Committee meeting to track foundation project progress.',
            'มอบทุนการศึกษาเยาวชน' => 'Youth Scholarship Award',
            'มอบทุนการศึกษาให้เยาวชนที่ขาดแคลนในพื้นที่' => 'Award scholarships to underprivileged youth in the area.',
            'มอบทุนการศึกษา พร้อมอุปกรณ์การเรียนให้แก่นักเรียนผู้ด้อยโอกาส' => 'Provide scholarships and learning supplies to underserved students.',
            'โรงเรียนเครือข่ายภาคกลาง' => 'Central Region Partner School',
            'ส่งต่อโอกาสทางการศึกษาให้เยาวชนไทย ด้วยหัวใจแห่งความเมตตา' => 'Delivering educational opportunities to Thai youth with compassion.',
            'เลขประจำตัวผู้เสียภาษีอากร:' => 'Tax ID:',
            'หัวข้อ *' => 'Subject *',
            'ข้อความ *' => 'Message *',
            'เพจทางการของมูลนิธิ' => 'Official Foundation Page',
            'รวมวิดีโอจากงาน The Joy of Giving' => 'Compilation videos from The Joy of Giving',
            'สำนักงานมูนิธิ THAIFA' => 'THAIFA Foundation Office',
            'เปิดใน Google Maps' => 'Open in Google Maps',
            'Business Hours: จันทร์-ศุกร์ 9:00-17:00 น.' => 'Business Hours: Mon-Fri 9:00-17:00',
            'Business Hours: จันทร์ - ศุกร์ 9:00-17:00 น.' => 'Business Hours: Mon-Fri 9:00-17:00',
            'ที่ปรึกษา' => 'Advisor',
            'ที่ปรึกษามูลนิธิ' => 'Foundation Advisor',
            'กรรมการ' => 'Committee Member',
            '<p class="text-accent text-xs">กรรมการ</p>' => '<p class="text-accent text-xs">Committee Member</p>',
            '<button class="view-btn px-3 py-1.5 rounded-xl text-sm" data-view="day">วัน</button>' => '<button class="view-btn px-3 py-1.5 rounded-xl text-sm" data-view="day">Day</button>',
            '<span class="text-foreground/90">ประชุม</span>' => '<span class="text-foreground/90">Meeting</span>',
            '<span class="text-foreground/90">ระดมทุน</span>' => '<span class="text-foreground/90">Fundraising</span>',
            '<li>ทุนการศึกษา</li>' => '<li>Scholarship</li>',
            'type_name":"ประชุม"' => 'type_name":"Meeting"',
            'Agendaกิจกรรม' => 'Agenda',
            'Scholarship Activitiesการศึกษา พร้อมอุปกรณ์การเรียนให้แก่นักเรียนผู้ด้อยโอกาส' => 'Scholarship activities with school supplies for underprivileged students.',
            '>อีเมล *</label>' => '>Email *</label>',
            '<h3 class="text-primary mb-3 text-xl">อีเมล</h3>' => '<h3 class="text-primary mb-3 text-xl">Email</h3>',
            '<div class="text-primary">อีเมล</div>' => '<div class="text-primary">Email</div>',
            'อาคารจูเวลเลอรี่ ชั้น 12' => 'Jewelry Trade Center, 12th Floor',
            'เลือกช่องทางที่คุณต้องการติดต่อ' => 'Choose your preferred contact channel',
            'การทำงานร่วมกันกับหน่วยงานเหล่านี้ช่วยให้มูลนิธิสามารถดำเนินภารกิจเพื่อสังคมได้อย่างมีประสิทธิภาพ' => 'Working with these organizations helps the foundation deliver social missions effectively.',
            'และโปร่งใสตามมาตรฐานสากล' => 'And transparently in line with international standards.',
            '<li><a href="#" class="text-white/80 hover:text-accent transition-colors">ทุนการศึกษา</a></li>' => '<li><a href="#" class="text-white/80 hover:text-accent transition-colors">Scholarship</a></li>',
            '>ประชุม</div>' => '>Meeting</div>',
            'Scholarship Activitiesการศึกษา' => 'Scholarship Activities',
            'พร้อมอุปกรณ์การเรียนให้แก่นักเรียนผู้ด้อยโอกาส' => 'with school supplies for underprivileged students',
            'Agendaกิจกรรม\';' => 'Agenda\';',
            'Business Hours: จันทร์-ศุกร์ 9:00-17:00 น.' => 'Business Hours: Mon-Fri 9:00-17:00',
            '>ชื่อ</label>' => '>First Name</label>',
            '>นามสกุล</label>' => '>Last Name</label>',
            '>อีเมล</label>' => '>Email</label>',
            '>เบอร์โทร</label>' => '>Phone</label>',

            // Footer
            'เมนูหลัก' => 'Main Menu',
            'โครงการของเรา' => 'Our Projects',
            'ช่วยเหลือเด็กกำพร้า' => 'Support for Orphans',
            'เครื่องมือแพทย์' => 'Medical Equipment',
            'กิจกรรมชุมชน' => 'Community Activities',
            'สงวนลิขสิทธิ์' => 'All rights reserved.',
            '20+ ปี' => '20+ Years',
            '2 ล้าน+' => '2M+',
            'เวลาทำการ: จันทร์-ศุกร์ 9:00-17:00 น.' => 'Business Hours: Mon-Fri 9:00-17:00',
            'Bookแรงบันดาลใจ' => 'Inspiration Book',
            'Bookสร้างแรงบันดาลใจ รายได้เข้าสมทบทุนการศึกษา' => 'Inspirational book. Proceeds support scholarships.',
            'เสื้อยืด THAIFA' => 'THAIFA T-Shirt',
            'เสื้อยืดผ้าเนื้อนุ่ม ใส่สบาย พร้อมโลโก้มูลนิธิ' => 'Soft, comfortable T-shirt with foundation logo.',
            'กระเป๋าผ้า THAIFA' => 'THAIFA Tote Bag',
            'ไทย' => 'Thailand',
            'เลือกจำนวนเงิน (บาท)' => 'Select amount (THB)',
            'หรือEnter Amount' => 'Or enter amount',
            'เบอร์โทรศัพท์' => 'Phone number',
            'อีเมล' => 'Email',
            'เลือกวัตถุประสงค์Donations' => 'Select donation purpose',
            'ทั้งความสุข ความภูมิใจ และความหมายของชีวิต' => 'Sharing happiness, pride, and meaningful life stories.',
            'มอบเวลาและความสามารถของคุณเพื่อสร้างความเปลี่ยนแปลงที่ดีให้กับสังคม' => 'Offer your time and skills to create positive change in society.',
            'ร่วมเป็นส่วนหนึ่งในการสร้างความเปลี่ยนแปลงที่ดีให้กับสังคม' => 'Be part of creating positive change in society.',
            'เวลาและความสามารถของคุณมีค่ามาก' => 'Your time and skills are valuable.',
            '"จากสิ่งที่เราได้รับ กลับคืนสู่สังคม"' => '"Giving back to society from what we have received"',
            'ก่อตั้งขึ้นเมื่อปี พ.ศ. 2544 โดยกลุ่มตัวแทนประกันชีวิตในประเทศไทย' => 'Founded in 2001 by life insurance agents in Thailand.',
            'APLIC (สมาคมตัวแทนประกันชีวิตแห่งเอเชียแปซิฟิก) ที่มีผู้เข้าร่วมกว่า 11,000 คน' => 'APLIC (Asia Pacific Life Insurance Congress) with over 11,000 participants.',
            'เพื่อช่วยเหลือเยาวชนขาดแคลน Support Educational Scholarships Provide Medical Equipment' => 'To support underserved youth through scholarships and medical equipment programs.',
            'และส่งเสริมกิจกรรมเพื่อสังคมอย่างต่อเนื่องมากว่า 20 ปี' => 'And continuously promote social programs for more than 20 years.',
            'Scholarship Grantเยาวชน' => 'Youth Scholarship Grant',
            'แรงบันดาลใจ' => 'Inspiration',
            'สร้างแรงบันดาลใจ' => 'create inspiration',
            'เข้าสมทบทุนการศึกษา' => 'supports scholarship funds',
            'เลือกจำนวนเงิน (บาท)' => 'Select amount (THB)',
            'หรือEnter Amount' => 'Or enter amount',
            'เบอร์โทรศัพท์' => 'Phone number',
            'เลือกวัตถุประสงค์Donations' => 'Select donation purpose',
            'คุณสมชาย ใจดี' => 'Mr. Somchai Jaidee',
            'กรุงเทพฯ' => 'Bangkok',
            'VolunteerสอนBook 2 ปี' => 'Volunteer Tutor (2 years)',
            'ช่วยสอนน้องๆ กว่า 50 คน ในหลากหลายวิชา' => 'Tutored over 50 children in multiple subjects.',
            'คุณนภา สวยงาม' => 'Ms. Napa Suayngam',
            'Volunteerจัดกิจกรรม 1 ปี' => 'Event Volunteer (1 year)',
            'เชียงใหม่' => 'Chiang Mai',
            '"ได้เป็นส่วนหนึ่งของการสร้างความสุขให้เด็กๆ ผ่านกิจกรรมต่างๆ ทำให้รู้สึกว่าเราก็มีส่วนช่วยสร้างสังคมที่ดีขึ้น การได้เห็นรอยยิ้มของเด็กๆ คือรางวัลที่ดีที่สุด"' => '"Being part of creating happiness for children through activities makes me feel I help build a better society. Seeing children smile is the best reward."',
            'จัดกิจกรรมพัฒนาทักษะให้เด็กๆ มากกว่า 15 ครั้ง' => 'Organized over 15 skill-development activities for children.',
            'เสียงจากผู้รับทุน' => 'Voices from Scholarship Recipients',
            'ที่จะเปลี่ยนชีวิตของเด็กและครอบครัวไปตลอดกาล' => 'Stories that can transform children and families for life.',
            'ยกเลิก' => 'Cancel',
            'ปิด' => 'Close',
            'เปิด' => 'Open',
            "toLocaleString('th-TH'" => "toLocaleString('en-US'",
            'สำนักงานใหญ่ THAIFA Foundation' => 'THAIFA Foundation Headquarters',
            'Scholarship Activitiesการศึกษา' => 'Scholarship Activities',
            'ทั้งวัน' => 'All day',
            '฿' => 'THB ',
            'หรือ' => 'Or ',
            'เพื่อช่วยเหลือเยาวชนขาดแคลน ' => 'To support underserved youth ',
            'The Insurance and Financial Advisors Foundation ขอขอบพระคุณ🙏 พี่สาวของคุณปาน ธนพร' => 'The Insurance and Financial Advisors Foundation sincerely thanks Ms. Pan Thanaporn\'s sister',
            'ที่ถักร้อย สร้อยรักส่งมาให้มูลนิธิ' => 'for donating handcrafted love bracelets to the foundation',
            'เพื่อจัดหารายได้เข้ากองทุนการศึกษาเพื่อพัฒนาเด็กThailandต่อไป' => 'to raise scholarship funds for Thai children.',
            'MeetingFoundation Committee' => 'Foundation Committee Meeting',
            'MeetingติดตามโครงการและแผนงานประจำMonth' => 'Monthly project and plan review meeting',
            'MeetingCommitteeเพื่อติดตามความคืบหน้าโครงการต่างๆ ของมูลนิธิ' => 'Committee meeting to track foundation project progress.',
            'Scholarship Grantให้เยาวชนที่ขาดแคลนในพื้นที่' => 'Award scholarships to underprivileged youth in the area.',
            'กิจกรรมScholarship Grant with school supplies for underprivileged students' => 'Scholarship activities with school supplies for underprivileged students.',
            'Bookcreate inspiration รายได้supports scholarship funds' => 'Inspirational book. Proceeds support scholarships.',
            'VolunteerสอนBook 2 ปี' => 'Volunteer Tutor (2 years)',
            'Volunteerจัดกิจกรรม 1 ปี' => 'Event Volunteer (1 year)',
            'ร่วมจัดFundraising Activitiesเพื่อSupport Educational Scholarshipsและสงเคราะห์เด็กและเยาวชน' => 'Organize fundraising activities to support educational scholarships and youth welfare.',
            'กระเป๋าผ้าพิมพ์โลโก้ THAIFA รายได้สนับสนุนงานมูลนิธิ' => 'THAIFA logo tote bag. Proceeds support foundation programs.',
            'ทุนการศึกษา' => 'Scholarship',
            'เครื่องมือแพทย์' => 'Medical Equipment',
            'ช่วยเหลือฉุกเฉิน' => 'Emergency Relief',
            'โครงการชุมชน' => 'Community Program',
            'ออกบูธในและนอกจังหวัด' => 'Event Booths (In-Province & Nationwide)',
            'ขอขอบพระคุณ🙏 พี่สาวของคุณปาน ธนพร' => 'sincerely thanks Ms. Pan Thanaporn\'s sister',
            'เพื่อจัดหารายได้เข้ากองScholarshipเพื่อพัฒนาเด็กThailandต่อไป' => 'to raise scholarship funds for Thai children.',
            'อุปกรณ์ทางการแพทย์' => 'Medical Supplies',
            'Volunteerจัดกิจกรรม' => 'Event Volunteer',
            '1 ปี' => '1 year',
            'ช่วยออกบูธจำหน่ายสินค้าของมูลนิธิและประชาสัมพันธ์กิจกรรม' => 'Help staff foundation product booths and promote activities.',
            'to the foundation เพื่อจัดหารายได้เข้ากองScholarshipเพื่อพัฒนาเด็กThailandต่อไป' => 'to the foundation to raise scholarship funds for Thai children.',
            'โอนผ่านบัญชีธนาคาร' => 'Bank Transfer',
            'Volunteerจัดกิจกรรม 1 year' => 'Event Volunteer (1 year)',
            'ร่วมงานScholarship Supportและลงพื้นที่โรงเรียนเพื่อพบปะน้องๆ' => 'Join scholarship support activities and visit schools to meet students.',
            'The Insurance and Financial Advisors Foundation sincerely thanks Ms. Pan Thanaporn\'s sister for donating handcrafted love bracelets to the foundation เพื่อจัดหารายได้เข้ากองScholarshipเพื่อพัฒนาเด็กThailandต่อไป' => 'The Insurance and Financial Advisors Foundation sincerely thanks Ms. Pan Thanaporn\'s sister for donating handcrafted love bracelets to raise scholarship funds for Thai children.',
            'สแกน QR' => 'Scan QR',
            'Volunteerจัดกิจกรรม' => 'Event Volunteer',
            'จัดกิจกรรม 1 year' => 'Event activities (1 year)',
            'ร่วมงานมอบทุนการศึกษาและลงพื้นที่โรงเรียนเพื่อพบปะน้องๆ' => 'Join scholarship award activities and visit schools to meet students.',
            'ท่านใดสนใจBook Or  หมอนผ้าห่ม เรียนเชิญที่บูธนะคะ' => 'If you are interested in books or blanket pillows, please visit our booth.',
            'โปร่งใส' => 'Transparent',
            'กิตตินันท์' => 'Kittinan',
            'ส่งใบสมัครทางEmail' => 'Submit Application by Email',
            'มีประสิทธิภาพ' => 'Efficient',
            'นาย Kittinan กุลพิมาย' => 'Mr. Kittinan Kulpimai',
        ];

        $customMapFile = __DIR__ . '/../config/i18n_custom.php';
        if (is_file($customMapFile)) {
            $customMap = include $customMapFile;
            if (is_array($customMap)) {
                $map = array_merge($map, $customMap);
            }
        }

        return $map;
    }
}

if (!function_exists('thaifa_i18n_transform_html')) {
    function thaifa_i18n_transform_html($html)
    {
        if (thaifa_lang() !== 'en') {
            return $html;
        }
        $out = strtr((string)$html, thaifa_i18n_replace_map());
        $cleanupMap = [
            'MeetingFoundation Committee' => 'Foundation Committee Meeting',
            'MeetingติดตามโครงการและแผนงานประจำMonth' => 'Monthly project and plan review meeting',
            'MeetingCommitteeเพื่อติดตามความคืบหน้าโครงการต่างๆ ของมูลนิธิ' => 'Committee meeting to track foundation project progress.',
            'กิจกรรมScholarship Grant with school supplies for underprivileged students' => 'Scholarship activities with school supplies for underprivileged students.',
            'Bookcreate inspiration รายได้supports scholarship funds' => 'Inspirational book. Proceeds support scholarships.',
            'เลือกวัตถุประสงค์Donations' => 'Select donation purpose',
            'VolunteerสอนBook 2 ปี' => 'Volunteer Tutor (2 years)',
            'ร่วมจัดFundraising Activitiesเพื่อSupport Educational Scholarshipsและสงเคราะห์เด็กและเยาวชน' => 'Organize fundraising activities to support educational scholarships and youth welfare.',
            'The Insurance and Financial Advisors Foundation ขอขอบพระคุณ🙏 พี่สาวของคุณปาน ธนพร ที่ถักร้อย สร้อยรักส่งมาให้มูลนิธิ เพื่อจัดหารายได้เข้ากองทุนการศึกษาเพื่อพัฒนาเด็กThailandต่อไป' => 'The Insurance and Financial Advisors Foundation sincerely thanks Ms. Pan Thanaporn\'s sister for donating handcrafted love bracelets to raise scholarship funds for Thai children.',
        ];
        $out = strtr($out, $cleanupMap);
        // Last-resort guard: remove any remaining Thai characters in EN mode.
        $out = preg_replace('/[\x{0E00}-\x{0E7F}]+/u', '', $out);
        return (string)$out;
    }
}

if (!function_exists('thaifa_i18n_buffer_start')) {
    function thaifa_i18n_buffer_start()
    {
        if (thaifa_lang() !== 'en') {
            return;
        }
        if (!defined('THAIFA_I18N_BUFFER_STARTED')) {
            define('THAIFA_I18N_BUFFER_STARTED', true);
            ob_start('thaifa_i18n_transform_html');
        }
    }
}
