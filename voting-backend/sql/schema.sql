CREATE TABLE IF NOT EXISTS voting_categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(160) NOT NULL UNIQUE,
  name VARCHAR(180) NOT NULL,
  description TEXT NOT NULL,
  group_key VARCHAR(120) NOT NULL,
  group_name VARCHAR(160) NOT NULL,
  group_sort INT NOT NULL DEFAULT 0,
  accent_color VARCHAR(20) NOT NULL DEFAULT '#0f9d58',
  hero_image VARCHAR(255) NULL,
  vote_price INT NOT NULL DEFAULT 100,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS voting_nominees (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NOT NULL,
  slug VARCHAR(160) NOT NULL UNIQUE,
  full_name VARCHAR(180) NOT NULL,
  department VARCHAR(180) NOT NULL,
  level_label VARCHAR(120) NOT NULL,
  bio TEXT NOT NULL,
  photo_url VARCHAR(255) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_voting_nominees_category
    FOREIGN KEY (category_id) REFERENCES voting_categories(id)
    ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS voting_transactions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  reference VARCHAR(120) NOT NULL UNIQUE,
  nominee_id INT UNSIGNED NOT NULL,
  voter_name VARCHAR(180) NOT NULL,
  voter_email VARCHAR(180) NOT NULL,
  votes INT NOT NULL,
  amount INT NOT NULL,
  currency VARCHAR(12) NOT NULL DEFAULT 'NGN',
  payment_provider VARCHAR(40) NOT NULL DEFAULT 'korapay',
  provider_reference VARCHAR(160) NULL,
  status ENUM('pending', 'confirmed', 'failed') NOT NULL DEFAULT 'pending',
  paid_at DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_voting_transactions_nominee_status (nominee_id, status),
  CONSTRAINT fk_voting_transactions_nominee
    FOREIGN KEY (nominee_id) REFERENCES voting_nominees(id)
    ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS voting_settings (
  setting_key VARCHAR(120) PRIMARY KEY,
  setting_value TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS voting_sections (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  section_key VARCHAR(120) NOT NULL UNIQUE,
  section_name VARCHAR(180) NOT NULL,
  description TEXT NULL,
  flyer_image_url VARCHAR(255) NULL,
  folder_name VARCHAR(180) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO voting_categories
  (slug, name, description, group_key, group_name, group_sort, accent_color, hero_image, vote_price, sort_order)
VALUES
  ('best-programmer-of-the-year', 'Best Programmer of the Year', 'Exceptional coding skills and technical problem-solving.', 'tech-digital', 'Tech & Digital', 1, '#0f9d58', '/awards_gala_night_bg_1778965666277.png', 100, 1),
  ('most-innovative-student', 'Most Innovative Student', 'Most creative and forward-thinking tech solutions.', 'tech-digital', 'Tech & Digital', 1, '#0f9d58', '/awards_gala_night_bg_1778965666277.png', 100, 2),
  ('tech-influencer-of-the-year', 'Tech Influencer of the Year', 'Inspiring and leading the tech community online.', 'tech-digital', 'Tech & Digital', 1, '#0f9d58', '/awards_gala_night_bg_1778965666277.png', 100, 3),
  ('best-tech-content-creator', 'Best Tech Content Creator', 'Creating insightful and high-value tech content.', 'tech-digital', 'Tech & Digital', 1, '#0f9d58', '/awards_gala_night_bg_1778965666277.png', 100, 4),
  ('ai-tech-enthusiast-award', 'AI/Tech Enthusiast Award', 'Outstanding learning and implementation of AI/Emerging tech.', 'tech-digital', 'Tech & Digital', 1, '#0f9d58', '/awards_gala_night_bg_1778965666277.png', 100, 5),
  ('most-creative-developer', 'Most Creative Developer', 'Building unique, visually pleasing, and feature-rich software applications.', 'tech-digital', 'Tech & Digital', 1, '#0f9d58', '/awards_gala_night_bg_1778965666277.png', 100, 6),
  ('best-creative-designer', 'Best Creative Designer', 'Excellence in UI/UX and digital product design.', 'tech-digital', 'Tech & Digital', 1, '#0f9d58', '/awards_gala_night_bg_1778965666277.png', 100, 7),
  ('hoc-of-the-year', 'HOC of the Year', 'Best Head of Class for outstanding leadership.', 'leadership-impact', 'Leadership & Impact', 2, '#c69214', '/awards_gala_night_bg_1778965666277.png', 100, 8),
  ('assistant-hoc-of-the-year', 'Assistant HOC of the Year', 'Exceptional assistance and representation for their class.', 'leadership-impact', 'Leadership & Impact', 2, '#c69214', '/awards_gala_night_bg_1778965666277.png', 100, 9),
  ('most-outstanding-leader', 'Most Outstanding Leader', 'Demonstrating unparalleled leadership and community management skills.', 'leadership-impact', 'Leadership & Impact', 2, '#c69214', '/awards_gala_night_bg_1778965666277.png', 100, 10),
  ('best-executive-of-the-year', 'Best Executive of the Year', 'Outstanding performance and commitment as a NACOS executive.', 'leadership-impact', 'Leadership & Impact', 2, '#c69214', '/awards_gala_night_bg_1778965666277.png', 100, 11),
  ('best-team-player', 'Best Team Player', 'Excellence in collaboration and supporting department peers.', 'leadership-impact', 'Leadership & Impact', 2, '#c69214', '/awards_gala_night_bg_1778965666277.png', 100, 12),
  ('social-influencer-of-the-year', 'Social Influencer of the Year', 'Highest social reach and online engagement on campus.', 'social-personality', 'Social & Personality', 3, '#cc4d7c', '/awards_gala_night_bg_1778965666277.png', 100, 13),
  ('social-personality-of-the-year', 'Social Personality of the Year', 'Vibrant, friendly, and highly engaging personalities.', 'social-personality', 'Social & Personality', 3, '#cc4d7c', '/awards_gala_night_bg_1778965666277.png', 100, 14),
  ('most-popular-student', 'Most Popular Student', 'Widely recognized and celebrated student in the department.', 'social-personality', 'Social & Personality', 3, '#cc4d7c', '/awards_gala_night_bg_1778965666277.png', 100, 15),
  ('mr-money-of-the-year', 'Mr. Money of the Year', 'Celebrating outstanding success and hustle in business.', 'social-personality', 'Social & Personality', 3, '#cc4d7c', '/awards_gala_night_bg_1778965666277.png', 100, 16),
  ('fashion-icon-of-the-department', 'Fashion Icon of the Department', 'Consistent, unique, and trend-setting fashion sense.', 'social-personality', 'Social & Personality', 3, '#cc4d7c', '/awards_gala_night_bg_1778965666277.png', 100, 17),
  ('artist-of-the-year', 'Artist of the Year', 'Outstanding musical, visual, or performing creative talent.', 'creative-brands', 'Creative & Brands', 4, '#7757e6', '/awards_gala_night_bg_1778965666277.png', 100, 18),
  ('content-creator-of-the-year', 'Content Creator of the Year', 'Creating engaging and highly-entertaining content for the student community.', 'creative-brands', 'Creative & Brands', 4, '#7757e6', '/awards_gala_night_bg_1778965666277.png', 100, 19),
  ('ceo-of-the-year', 'CEO of the Year', 'Leading the most impressive and professional student-owned brands.', 'creative-brands', 'Creative & Brands', 4, '#7757e6', '/awards_gala_night_bg_1778965666277.png', 100, 20),
  ('tech-entrepreneur-student-founder', 'Tech Entrepreneur/Student Founder', 'Outstanding student-led technology startups or commercial tech solutions.', 'creative-brands', 'Creative & Brands', 4, '#7757e6', '/awards_gala_night_bg_1778965666277.png', 100, 21),
  ('best-brand-of-the-year', 'Best Brand of the Year', 'Highly recognized and trusted brand built by a computer science student.', 'creative-brands', 'Creative & Brands', 4, '#7757e6', '/awards_gala_night_bg_1778965666277.png', 100, 22),
  ('best-male-footballer-of-the-year', 'Best Male Footballer of the Year', 'Most outstanding male player on the pitch.', 'sports', 'Sports', 5, '#1f9a69', '/awards_gala_night_bg_1778965666277.png', 100, 23),
  ('best-female-footballer-of-the-year', 'Best Female Footballer of the Year', 'Most outstanding female player on the pitch.', 'sports', 'Sports', 5, '#1f9a69', '/awards_gala_night_bg_1778965666277.png', 100, 24),
  ('best-football-team-of-the-year', 'Best Football Team of the Year', 'Most dominant and cohesive team within the department.', 'sports', 'Sports', 5, '#1f9a69', '/awards_gala_night_bg_1778965666277.png', 100, 25),
  ('fx-trader-of-the-year', 'FX Trader of the Year', 'Excellence and consistency in financial trading markets.', 'special-recognition', 'Special Recognition', 6, '#4b6ef5', '/awards_gala_night_bg_1778965666277.png', 100, 26),
  ('best-lecturer-of-the-year', 'Best Lecturer of the Year', 'The most impactful, supportive, and engaging educator in the department.', 'special-recognition', 'Special Recognition', 6, '#4b6ef5', '/awards_gala_night_bg_1778965666277.png', 100, 27)
ON DUPLICATE KEY UPDATE
  description = VALUES(description),
  group_key = VALUES(group_key),
  group_name = VALUES(group_name),
  group_sort = VALUES(group_sort),
  accent_color = VALUES(accent_color),
  hero_image = VALUES(hero_image),
  vote_price = VALUES(vote_price),
  sort_order = VALUES(sort_order),
  is_active = 1;

INSERT INTO voting_settings (setting_key, setting_value)
VALUES
  ('eventName', 'NACOS Awards'),
  ('eventEdition', '2026'),
  ('awardsDate', '2026-06-17T18:00:00+01:00'),
  ('votingClosesAt', '2026-06-07T23:59:59+01:00'),
  ('votePrice', '100'),
  ('currency', 'NGN'),
  ('votingOpen', '1'),
  ('paymentProvider', 'korapay'),
  ('paymentProviderLabel', 'Korapay'),
  ('paymentProviderDescription', 'Secured by Korapay test checkout')
ON DUPLICATE KEY UPDATE
  setting_value = VALUES(setting_value);
