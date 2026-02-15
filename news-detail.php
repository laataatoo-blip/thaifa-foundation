<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดข่าว - มูลนิธิตัวแทนประกันชีวิตและที่ปรึกษาการเงิน (THAIFA Foundation)</title>
    <meta name="description" content="รายละเอียดข่าวและกิจกรรมของมูลนิธิตัวแทนประกันชีวิตและที่ปรึกษาการเงิน">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&family=Mali:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-blue: #233882;
            --primary-red: #e83b3b;
            --light-blue: #bde7ff;
            --white: #ffffff;
            --gray-light: #f5f5f5;
            --gray-medium: #e0e0e0;
            --gray-dark: #666666;
            --text-dark: #333333;
        }

        body {
            font-family: 'Mali', sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            background-color: var(--white);
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Prompt', sans-serif;
        }

        /* Navigation */
        nav {
            background-color: var(--primary-blue);
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-family: 'Prompt', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--white);
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .nav-links a {
            color: var(--white);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: var(--light-blue);
        }

        /* Mobile Menu */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: var(--white);
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Breadcrumb */
        .breadcrumb {
            background-color: var(--gray-light);
            padding: 1rem 2rem;
        }

        .breadcrumb-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            gap: 0.5rem;
            align-items: center;
            font-size: 0.9rem;
        }

        .breadcrumb a {
            color: var(--primary-blue);
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .breadcrumb span {
            color: var(--gray-dark);
        }

        /* Main Content */
        .news-detail-container {
            max-width: 1200px;
            margin: 3rem auto;
            padding: 0 2rem;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 2rem;
            transition: color 0.3s;
        }

        .back-button:hover {
            color: var(--primary-red);
        }

        /* Article Header */
        .article-header {
            margin-bottom: 2rem;
        }

        .article-category {
            display: inline-block;
            background-color: var(--primary-red);
            color: var(--white);
            padding: 0.3rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }

        .article-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-blue);
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .article-meta {
            display: flex;
            gap: 2rem;
            color: var(--gray-dark);
            font-size: 0.95rem;
            flex-wrap: wrap;
        }

        .article-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Featured Image */
        .featured-image {
            width: 100%;
            height: 500px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        /* Article Content */
        .article-content {
            margin-bottom: 3rem;
        }

        .article-content p {
            margin-bottom: 1.5rem;
            font-size: 1.05rem;
            line-height: 1.8;
            text-align: justify;
        }

        .article-content h2 {
            font-size: 1.8rem;
            color: var(--primary-blue);
            margin-top: 2rem;
            margin-bottom: 1rem;
        }

        .article-content h3 {
            font-size: 1.4rem;
            color: var(--primary-blue);
            margin-top: 1.5rem;
            margin-bottom: 0.8rem;
        }

        .article-content ul, .article-content ol {
            margin-bottom: 1.5rem;
            margin-left: 2rem;
        }

        .article-content li {
            margin-bottom: 0.5rem;
            font-size: 1.05rem;
        }

        /* Photo Gallery */
        .photo-gallery {
            margin: 3rem 0;
        }

        .gallery-title {
            font-size: 2rem;
            font-weight: 600;
            color: var(--primary-blue);
            margin-bottom: 1.5rem;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            cursor: pointer;
            aspect-ratio: 4/3;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .gallery-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .gallery-item:hover img {
            transform: scale(1.05);
        }

        .gallery-item-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
            color: var(--white);
            padding: 1rem;
            transform: translateY(100%);
            transition: transform 0.3s;
        }

        .gallery-item:hover .gallery-item-overlay {
            transform: translateY(0);
        }

        .gallery-item-caption {
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Lightbox */
        .lightbox {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.95);
            z-index: 1000;
            overflow: auto;
        }

        .lightbox.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .lightbox-content {
            position: relative;
            max-width: 90%;
            max-height: 90%;
            margin: auto;
        }

        .lightbox-image {
            width: 100%;
            height: auto;
            max-height: 80vh;
            object-fit: contain;
            border-radius: 8px;
        }

        .lightbox-caption {
            background-color: rgba(255,255,255,0.95);
            color: var(--text-dark);
            padding: 1rem 2rem;
            text-align: center;
            margin-top: 1rem;
            border-radius: 8px;
        }

        .lightbox-caption h3 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: var(--primary-blue);
        }

        .lightbox-caption p {
            font-size: 0.95rem;
            color: var(--gray-dark);
        }

        .lightbox-close {
            position: fixed;
            top: 2rem;
            right: 2rem;
            font-size: 2.5rem;
            color: var(--white);
            background: none;
            border: none;
            cursor: pointer;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s;
            z-index: 1001;
        }

        .lightbox-close:hover {
            background-color: rgba(255,255,255,0.2);
        }

        .lightbox-nav {
            position: fixed;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(255,255,255,0.1);
            color: var(--white);
            border: none;
            font-size: 2rem;
            padding: 1rem;
            cursor: pointer;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s;
            z-index: 1001;
        }

        .lightbox-nav:hover {
            background-color: rgba(255,255,255,0.2);
        }

        .lightbox-prev {
            left: 2rem;
        }

        .lightbox-next {
            right: 2rem;
        }

        .lightbox-counter {
            position: fixed;
            top: 2rem;
            left: 50%;
            transform: translateX(-50%);
            background-color: rgba(255,255,255,0.9);
            color: var(--text-dark);
            padding: 0.5rem 1.5rem;
            border-radius: 20px;
            font-weight: 500;
            z-index: 1001;
        }

        /* Tags */
        .article-tags {
            margin: 2rem 0;
            padding: 1.5rem;
            background-color: var(--gray-light);
            border-radius: 8px;
        }

        .tags-title {
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--primary-blue);
        }

        .tag-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .tag {
            background-color: var(--white);
            color: var(--primary-blue);
            padding: 0.4rem 1rem;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.9rem;
            border: 1px solid var(--primary-blue);
            transition: all 0.3s;
        }

        .tag:hover {
            background-color: var(--primary-blue);
            color: var(--white);
        }

        /* Share Buttons */
        .share-section {
            margin: 2rem 0;
            padding: 1.5rem;
            background-color: var(--gray-light);
            border-radius: 8px;
        }

        .share-title {
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--primary-blue);
        }

        .share-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .share-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.2rem;
            border-radius: 25px;
            text-decoration: none;
            color: var(--white);
            font-weight: 500;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .share-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .share-facebook { background-color: #1877f2; }
        .share-line { background-color: #00b900; }
        .share-twitter { background-color: #1da1f2; }
        .share-copy { background-color: var(--gray-dark); }

        /* Related News */
        .related-news {
            margin: 3rem 0;
        }

        .related-news-title {
            font-size: 2rem;
            font-weight: 600;
            color: var(--primary-blue);
            margin-bottom: 2rem;
        }

        .related-news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }

        .related-news-card {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            text-decoration: none;
            color: inherit;
        }

        .related-news-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }

        .related-news-image {
            width: 100%;
            aspect-ratio: 16/9;
            object-fit: cover;
        }

        .related-news-content {
            padding: 1.5rem;
        }

        .related-news-title-text {
            font-family: 'Prompt', sans-serif;
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-blue);
            margin-bottom: 0.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .related-news-date {
            color: var(--gray-dark);
            font-size: 0.9rem;
        }

        /* Footer */
        footer {
            background-color: var(--primary-blue);
            color: var(--white);
            padding: 3rem 2rem 1.5rem;
            margin-top: 4rem;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            color: var(--light-blue);
        }

        .footer-section p,
        .footer-section a {
            color: var(--white);
            text-decoration: none;
            line-height: 1.8;
        }

        .footer-section a:hover {
            color: var(--light-blue);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.2);
            font-size: 0.9rem;
        }

        .motto {
            font-style: italic;
            color: var(--light-blue);
            margin-bottom: 0.5rem;
        }

        /* Floating Contact Button */
        .floating-contact {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background-color: var(--primary-red);
            color: var(--white);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            z-index: 999;
            text-decoration: none;
        }

        .floating-contact:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 16px rgba(0,0,0,0.4);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .mobile-menu-btn {
                display: block;
            }

            .article-title {
                font-size: 1.8rem;
            }

            .featured-image {
                height: 300px;
            }

            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 1rem;
            }

            .lightbox-close,
            .lightbox-nav {
                width: 40px;
                height: 40px;
                font-size: 1.5rem;
            }

            .lightbox-prev {
                left: 0.5rem;
            }

            .lightbox-next {
                right: 0.5rem;
            }

            .lightbox-counter {
                top: 0.5rem;
                font-size: 0.85rem;
                padding: 0.4rem 1rem;
            }

            .related-news-grid {
                grid-template-columns: 1fr;
            }

            .floating-contact {
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
                bottom: 1.5rem;
                right: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .article-title {
                font-size: 1.5rem;
            }

            .gallery-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <a href="index.php" class="logo">THAIFA</a>
            <button class="mobile-menu-btn">☰</button>
            <ul class="nav-links">
                <li><a href="index.php">หน้าแรก</a></li>
                <li><a href="about.php">เกี่ยวกับเรา</a></li>
                <li><a href="calendar.php">กิจกรรม</a></li>
                <li><a href="stories.php">เรื่องราว</a></li>
                <li><a href="shop.php">ร้านค้า</a></li>
                <li><a href="donate.php">บริจาค</a></li>
                <li><a href="volunteer.php">อาสาสมัคร</a></li>
                <li><a href="contact.php">ติดต่อเรา</a></li>
            </ul>
        </div>
    </nav>

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <div class="breadcrumb-container">
            <a href="index.php">หน้าแรก</a>
            <span>›</span>
            <a href="calendar.php">กิจกรรม</a>
            <span>›</span>
            <span>โครงการมอบทุนการศึกษา ประจำปี 2569</span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="news-detail-container">
        <!-- Back Button -->
        <a href="calendar.php" class="back-button">
            ← กลับไปหน้ากิจกรรม
        </a>

        <!-- Article Header -->
        <div class="article-header">
            <span class="article-category">โครงการการศึกษา</span>
            <h1 class="article-title">โครงการมอบทุนการศึกษา "หนึ่งความรักจากฉัน หมื่นพันความฝันของเธอ" ประจำปี 2569</h1>
            <div class="article-meta">
                <div class="article-meta-item">
                    <span>📅</span>
                    <span>15 กุมภาพันธ์ 2569</span>
                </div>
                <div class="article-meta-item">
                    <span>📍</span>
                    <span>โรงเรียนบ้านห้วยทราย จ.เชียงราย</span>
                </div>
                <div class="article-meta-item">
                    <span>👁️</span>
                    <span>2,547 ครั้ง</span>
                </div>
            </div>
        </div>

        <!-- Featured Image -->
        <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=1200&h=600&fit=crop" alt="โครงการมอบทุนการศึกษา" class="featured-image">

        <!-- Article Content -->
        <article class="article-content">
            <p>
                มูลนิธิตัวแทนประกันชีวิตและที่ปรึกษาการเงิน (THAIFA Foundation) ได้จัดโครงการมอบทุนการศึกษา "หนึ่งความรักจากฉัน หมื่นพันความฝันของเธอ" ประจำปี 2569 ขึ้น เพื่อสนับสนุนการศึกษาแก่เด็กและเยาวชนที่ขาดแคลนทุนทรัพย์ในพื้นที่ห่างไกล โดยในปีนี้ได้มอบทุนการศึกษาให้แก่นักเรียนในพื้นที่จังหวัดเชียงราย จำนวน 120 ทุน
            </p>

            <p>
                โครงการนี้ได้รับการสนับสนุนจากสมาชิกมูลนิธิ THAIFA ทั่วประเทศ พร้อมด้วยพาร์ทเนอร์จากภาคเอกชน ที่มีจิตศรัทธาร่วมกันสร้างโอกาสทางการศึกษาให้แก่เด็กๆ เพื่อให้พวกเขามีโอกาสพัฒนาศักยภาพและสร้างอนาคตที่ดีให้กับตนเองและสังคม
            </p>

            <h2>วัตถุประสงค์ของโครงการ</h2>
            <ul>
                <li>สนับสนุนทุนการศึกษาแก่เด็กและเยาวชนที่ขาดแคลนทุนทรัพย์</li>
                <li>ส่งเสริมการเข้าถึงโอกาสทางการศึกษาที่เท่าเทียม</li>
                <li>สร้างแรงบันดาลใจให้เด็กๆ มีความมุ่งมั่นในการเรียน</li>
                <li>สนับสนุนอุปกรณ์การเรียนและเครื่องแบบนักเรียน</li>
                <li>เสริมสร้างศักยภาพของเด็กและเยาวชนในชุมชนห่างไกล</li>
            </ul>

            <h2>กิจกรรมในวันมอบทุน</h2>
            <p>
                ในวันที่ 15 กุมภาพันธ์ 2569 คณะกรรมการและสมาชิกมูลนิธิ THAIFA ได้เดินทางไปมอบทุนการศึกษาและพบปะกับน้องๆ นักเรียนที่โรงเรียนบ้านห้วยทราย จังหวัดเชียงราย โดยมีกิจกรรมต่างๆ ดังนี้
            </p>

            <ol>
                <li><strong>พิธีมอบทุนการศึกษา</strong> - มอบเงินทุนการศึกษาให้แก่นักเรียน 120 ทุน พร้อมอุปกรณ์การเรียนครบชุด</li>
                <li><strong>การบรรยายพิเศษ</strong> - วิทยากรจากมูลนิธิให้กำลังใจและแรงบันดาลใจแก่น้องๆ</li>
                <li><strong>กิจกรรมเสริมทักษะ</strong> - จัดกิจกรรมเวิร์คช็อปการวางแผนการเงินเบื้องต้นสำหรับเด็ก</li>
                <li><strong>รับประทานอาหารกลางวัน</strong> - พบปะสนทนากับน้องๆ และผู้ปกครอง</li>
                <li><strong>มอบของที่ระลึก</strong> - แจกของที่ระลึกและของเล่นเสริมพัฒนาการ</li>
            </ol>

            <h2>ความประทับใจจากผู้รับทุน</h2>
            <p>
                <em>"หนูดีใจมากๆ ค่ะ ที่ได้รับทุนการศึกษา หนูจะตั้งใจเรียนให้ดีที่สุด และเมื่อโตขึ้นหนูอยากเป็นครูเพื่อสอนน้องๆ ในหมู่บ้านเหมือนที่พี่ๆ ช่วยเหลือหนูในวันนี้ค่ะ"</em> - น้องกานต์ชนก นักเรียนชั้นประถมศึกษาปีที่ 5
            </p>

            <p>
                <em>"ผมรู้สึกขอบคุณมูลนิธิ THAIFA มากครับ ที่ให้โอกาสผมได้เรียนหนังสือต่อ ทุนนี้จะช่วยให้ผมไม่เป็นภาระของพ่อแม่มากเกินไป และผมจะนำความรู้ที่ได้มาพัฒนาหมู่บ้านของเราให้ดีขึ้นครับ"</em> - น้องธนพล นักเรียนชั้นมัธยมศึกษาปีที่ 3
            </p>

            <h2>ผลกระทบของโครงการ</h2>
            <p>
                โครงการมอบทุนการศึกษาของมูลนิธิ THAIFA ดำเนินมาอย่างต่อเนื่องตั้งแต่ปี 2563 จนถึงปัจจุบัน โดยได้มอบทุนการศึกษาไปแล้วกว่า 1,500 ทุน ในพื้นที่ทั่วประเทศ ส่งผลให้เด็กๆ ได้มีโอกาสเรียนหนังสือต่อ และพัฒนาตนเองอย่างเต็มศักยภาพ
            </p>

            <p>
                นอกจากนี้ มูลนิธิยังติดตามผลการเรียนและให้คำปรึกษาแก่นักเรียนที่ได้รับทุนอย่างต่อเนื่อง เพื่อให้แน่ใจว่าทุนการศึกษาเหล่านี้สามารถสร้างความเปลี่ยนแปลงที่ยั่งยืนให้กับชีวิตของเด็กๆ ได้จริง
            </p>
        </article>

        <!-- Photo Gallery -->
        <section class="photo-gallery">
            <h2 class="gallery-title">📷 ภาพบรรยากาศกิจกรรม</h2>
            <div class="gallery-grid">
                <div class="gallery-item" onclick="openLightbox(0)">
                    <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=600&h=400&fit=crop" alt="พิธีมอบทุนการศึกษา">
                    <div class="gallery-item-overlay">
                        <div class="gallery-item-caption">พิธีมอบทุนการศึกษาแก่นักเรียน</div>
                    </div>
                </div>
                <div class="gallery-item" onclick="openLightbox(1)">
                    <img src="https://images.unsplash.com/photo-1497486751825-1233686d5d80?w=600&h=400&fit=crop" alt="นักเรียนรับทุน">
                    <div class="gallery-item-overlay">
                        <div class="gallery-item-caption">น้องๆ นักเรียนที่ได้รับทุนการศึกษา</div>
                    </div>
                </div>
                <div class="gallery-item" onclick="openLightbox(2)">
                    <img src="https://images.unsplash.com/photo-1509062522246-3755977927d7?w=600&h=400&fit=crop" alt="กิจกรรมเสริมทักษะ">
                    <div class="gallery-item-overlay">
                        <div class="gallery-item-caption">กิจกรรมเวิร์คช็อปการวางแผนการเงิน</div>
                    </div>
                </div>
                <div class="gallery-item" onclick="openLightbox(3)">
                    <img src="https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?w=600&h=400&fit=crop" alt="มอบอุปกรณ์การเรียน">
                    <div class="gallery-item-overlay">
                        <div class="gallery-item-caption">มอบอุปกรณ์การเรียนให้น้องๆ</div>
                    </div>
                </div>
                <div class="gallery-item" onclick="openLightbox(4)">
                    <img src="https://images.unsplash.com/photo-1544776193-352d25ca82cd?w=600&h=400&fit=crop" alt="รอยยิ้มของเด็กๆ">
                    <div class="gallery-item-overlay">
                        <div class="gallery-item-caption">รอยยิ้มแห่งความสุขของน้องๆ</div>
                    </div>
                </div>
                <div class="gallery-item" onclick="openLightbox(5)">
                    <img src="https://images.unsplash.com/photo-1503676382389-4809596d5290?w=600&h=400&fit=crop" alt="ภาพหมู่">
                    <div class="gallery-item-overlay">
                        <div class="gallery-item-caption">ภาพหมู่คณะกรรมการและนักเรียน</div>
                    </div>
                </div>
                <div class="gallery-item" onclick="openLightbox(6)">
                    <img src="https://images.unsplash.com/photo-1517164850305-99a3e65bb47e?w=600&h=400&fit=crop" alt="การบรรยายพิเศษ">
                    <div class="gallery-item-overlay">
                        <div class="gallery-item-caption">การบรรยายพิเศษให้กำลังใจ</div>
                    </div>
                </div>
                <div class="gallery-item" onclick="openLightbox(7)">
                    <img src="https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=600&h=400&fit=crop" alt="รับประทานอาหาร">
                    <div class="gallery-item-overlay">
                        <div class="gallery-item-caption">รับประทานอาหารกลางวันร่วมกัน</div>
                    </div>
                </div>
                <div class="gallery-item" onclick="openLightbox(8)">
                    <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=600&h=400&fit=crop" alt="กิจกรรมกลุ่ม">
                    <div class="gallery-item-overlay">
                        <div class="gallery-item-caption">กิจกรรมกลุ่มเสริมทักษะ</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tags -->
        <div class="article-tags">
            <div class="tags-title">🏷️ หมวดหมู่</div>
            <div class="tag-list">
                <a href="#" class="tag">ทุนการศึกษา</a>
                <a href="#" class="tag">การศึกษา</a>
                <a href="#" class="tag">เด็กและเยาวชน</a>
                <a href="#" class="tag">เชียงราย</a>
                <a href="#" class="tag">CSR</a>
                <a href="#" class="tag">2569</a>
            </div>
        </div>

        <!-- Share Section -->
        <div class="share-section">
            <div class="share-title">📤 แชร์ข่าวนี้</div>
            <div class="share-buttons">
                <a href="#" class="share-btn share-facebook">
                    📘 Facebook
                </a>
                <a href="#" class="share-btn share-line">
                    💚 Line
                </a>
                <a href="#" class="share-btn share-twitter">
                    🐦 Twitter
                </a>
                <button class="share-btn share-copy" onclick="copyLink()">
                    📋 คัดลอกลิงก์
                </button>
            </div>
        </div>

        <!-- Related News -->
        <section class="related-news">
            <h2 class="related-news-title">ข่าวที่เกี่ยวข้อง</h2>
            <div class="related-news-grid">
                <a href="#" class="related-news-card">
                    <img src="https://images.unsplash.com/photo-1509062522246-3755977927d7?w=600&h=400&fit=crop" alt="ข่าวที่เกี่ยวข้อง" class="related-news-image">
                    <div class="related-news-content">
                        <h3 class="related-news-title-text">โครงการมอบอุปกรณ์การเรียนแก่โรงเรียนในพื้นที่ห่างไกล</h3>
                        <p class="related-news-date">10 กุมภาพันธ์ 2569</p>
                    </div>
                </a>
                <a href="#" class="related-news-card">
                    <img src="https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?w=600&h=400&fit=crop" alt="ข่าวที่เกี่ยวข้อง" class="related-news-image">
                    <div class="related-news-content">
                        <h3 class="related-news-title-text">โครงการปรับปรุงห้องสมุดโรงเรียน จ.แม่ฮ่องสอน</h3>
                        <p class="related-news-date">5 กุมภาพันธ์ 2569</p>
                    </div>
                </a>
                <a href="#" class="related-news-card">
                    <img src="https://images.unsplash.com/photo-1544776193-352d25ca82cd?w=600&h=400&fit=crop" alt="ข่าวที่เกี่ยวข้อง" class="related-news-image">
                    <div class="related-news-content">
                        <h3 class="related-news-title-text">ค่ายวิทยาศาสตร์น้อยสำหรับเด็กนักเรียนชนบท</h3>
                        <p class="related-news-date">28 มกราคม 2569</p>
                    </div>
                </a>
            </div>
        </section>
    </div>

    <!-- Lightbox -->
    <div id="lightbox" class="lightbox">
        <button class="lightbox-close" onclick="closeLightbox()">&times;</button>
        <div class="lightbox-counter" id="lightboxCounter"></div>
        <button class="lightbox-nav lightbox-prev" onclick="changeImage(-1)">❮</button>
        <button class="lightbox-nav lightbox-next" onclick="changeImage(1)">❯</button>
        <div class="lightbox-content">
            <img id="lightboxImage" class="lightbox-image" src="" alt="">
            <div class="lightbox-caption">
                <h3 id="lightboxTitle"></h3>
                <p id="lightboxDescription"></p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>มูลนิธิ THAIFA</h3>
                    <p>มูลนิธิตัวแทนประกันชีวิตและที่ปรึกษาการเงิน</p>
                    <p class="motto">"จากสิ่งที่เราได้รับ กลับคืนสู่สังคม"</p>
                </div>
                <div class="footer-section">
                    <h3>เมนู</h3>
                    <p><a href="about.php">เกี่ยวกับเรา</a></p>
                    <p><a href="calendar.php">กิจกรรม</a></p>
                    <p><a href="stories.php">เรื่องราว</a></p>
                    <p><a href="contact.php">ติดต่อเรา</a></p>
                </div>
                <div class="footer-section">
                    <h3>ติดต่อเรา</h3>
                    <p>📘 Facebook: มูลนิธิตัวแทนประกันชีวิตและที่ปรึกษาการเงิน</p>
                    <p>💚 Line: @thaifa</p>
                    <p>🎵 TikTok: thaifafoundation</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2569 THAIFA Foundation. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Floating Contact Button -->
    <a href="contact.php" class="floating-contact" title="ติดต่อเรา">
        💬
    </a>

    <script>
        // Gallery images data
        const galleryImages = [
            {
                src: 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=1200&h=800&fit=crop',
                title: 'พิธีมอบทุนการศึกษาแก่นักเรียน',
                description: 'คณะกรรมการมูลนิธิ THAIFA มอบทุนการศึกษาให้แก่นักเรียนในพื้นที่จังหวัดเชียงราย'
            },
            {
                src: 'https://images.unsplash.com/photo-1497486751825-1233686d5d80?w=1200&h=800&fit=crop',
                title: 'น้องๆ นักเรียนที่ได้รับทุนการศึกษา',
                description: 'นักเรียนทั้ง 120 คนยิ้มแย้มแจ่มใสหลังจากได้รับทุนการศึกษาและอุปกรณ์การเรียน'
            },
            {
                src: 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=1200&h=800&fit=crop',
                title: 'กิจกรรมเวิร์คช็อปการวางแผนการเงิน',
                description: 'วิทยากรจากมูลนิธิให้ความรู้เรื่องการวางแผนการเงินเบื้องต้นแก่นักเรียน'
            },
            {
                src: 'https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?w=1200&h=800&fit=crop',
                title: 'มอบอุปกรณ์การเรียนให้น้องๆ',
                description: 'การมอบชุดอุปกรณ์การเรียนที่จำเป็นสำหรับนักเรียนทุกคน'
            },
            {
                src: 'https://images.unsplash.com/photo-1544776193-352d25ca82cd?w=1200&h=800&fit=crop',
                title: 'รอยยิ้มแห่งความสุขของน้องๆ',
                description: 'น้องๆ มีความสุขและมีกำลังใจในการเรียนต่อหลังได้รับทุนการศึกษา'
            },
            {
                src: 'https://images.unsplash.com/photo-1503676382389-4809596d5290?w=1200&h=800&fit=crop',
                title: 'ภาพหมู่คณะกรรมการและนักเรียน',
                description: 'คณะกรรมการ สมาชิก และนักเรียนถ่ายภาพร่วมกันเป็นที่ระลึก'
            },
            {
                src: 'https://images.unsplash.com/photo-1517164850305-99a3e65bb47e?w=1200&h=800&fit=crop',
                title: 'การบรรยายพิเศษให้กำลังใจ',
                description: 'วิทยากรบรรยายให้กำลังใจและแรงบันดาลใจแก่นักเรียน'
            },
            {
                src: 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=1200&h=800&fit=crop',
                title: 'รับประทานอาหารกลางวันร่วมกัน',
                description: 'คณะกรรมการและน้องๆ รับประทานอาหารกลางวันและพูดคุยกันอย่างอบอุ่น'
            },
            {
                src: 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1200&h=800&fit=crop',
                title: 'กิจกรรมกลุ่มเสริมทักษะ',
                description: 'นักเรียนร่วมกิจกรรมกลุ่มเพื่อพัฒนาทักษะการทำงานร่วมกัน'
            }
        ];

        let currentImageIndex = 0;

        // Open lightbox
        function openLightbox(index) {
            currentImageIndex = index;
            const lightbox = document.getElementById('lightbox');
            const lightboxImage = document.getElementById('lightboxImage');
            const lightboxTitle = document.getElementById('lightboxTitle');
            const lightboxDescription = document.getElementById('lightboxDescription');
            const lightboxCounter = document.getElementById('lightboxCounter');

            lightboxImage.src = galleryImages[index].src;
            lightboxTitle.textContent = galleryImages[index].title;
            lightboxDescription.textContent = galleryImages[index].description;
            lightboxCounter.textContent = `${index + 1} / ${galleryImages.length}`;

            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        // Close lightbox
        function closeLightbox() {
            const lightbox = document.getElementById('lightbox');
            lightbox.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Change image
        function changeImage(direction) {
            currentImageIndex += direction;

            if (currentImageIndex < 0) {
                currentImageIndex = galleryImages.length - 1;
            } else if (currentImageIndex >= galleryImages.length) {
                currentImageIndex = 0;
            }

            const lightboxImage = document.getElementById('lightboxImage');
            const lightboxTitle = document.getElementById('lightboxTitle');
            const lightboxDescription = document.getElementById('lightboxDescription');
            const lightboxCounter = document.getElementById('lightboxCounter');

            lightboxImage.src = galleryImages[currentImageIndex].src;
            lightboxTitle.textContent = galleryImages[currentImageIndex].title;
            lightboxDescription.textContent = galleryImages[currentImageIndex].description;
            lightboxCounter.textContent = `${currentImageIndex + 1} / ${galleryImages.length}`;
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            const lightbox = document.getElementById('lightbox');
            if (lightbox.classList.contains('active')) {
                if (e.key === 'ArrowLeft') {
                    changeImage(-1);
                } else if (e.key === 'ArrowRight') {
                    changeImage(1);
                } else if (e.key === 'Escape') {
                    closeLightbox();
                }
            }
        });

        // Close lightbox when clicking outside image
        document.getElementById('lightbox').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLightbox();
            }
        });

        // Copy link function
        function copyLink() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(function() {
                alert('คัดลอกลิงก์เรียบร้อยแล้ว!');
            }, function() {
                alert('ไม่สามารถคัดลอกลิงก์ได้');
            });
        }

        // Mobile menu toggle (simple version)
        document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
            const navLinks = document.querySelector('.nav-links');
            if (navLinks.style.display === 'flex') {
                navLinks.style.display = 'none';
            } else {
                navLinks.style.display = 'flex';
                navLinks.style.flexDirection = 'column';
                navLinks.style.position = 'absolute';
                navLinks.style.top = '100%';
                navLinks.style.left = '0';
                navLinks.style.right = '0';
                navLinks.style.backgroundColor = 'var(--primary-blue)';
                navLinks.style.padding = '1rem';
            }
        });
    </script>
</body>
</html>
