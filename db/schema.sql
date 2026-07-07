-- ============================================================
--  CareerCompass — Full Database Schema
--  Import this in phpMyAdmin or run: mysql -u root career_advisor < schema.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS career_advisor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE career_advisor;

-- ── Users (students + admins) ─────────────────────────────
CREATE TABLE users (
  user_id     INT AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(100)  NOT NULL,
  email       VARCHAR(100)  NOT NULL UNIQUE,
  password    VARCHAR(255)  NOT NULL,
  role        ENUM('student','admin') DEFAULT 'student',
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ── Career Paths ──────────────────────────────────────────
CREATE TABLE career_paths (
  career_id       INT AUTO_INCREMENT PRIMARY KEY,
  title           VARCHAR(150) NOT NULL,
  level           ENUM('After 10th','After 12th','Degree') NOT NULL,
  stream          VARCHAR(100),
  description     TEXT,
  required_degree VARCHAR(100),
  job_roles       TEXT,
  further_study   TEXT,
  avg_salary      VARCHAR(50),
  duration        VARCHAR(50),
  created_at      DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ── Roadmaps ──────────────────────────────────────────────
CREATE TABLE roadmaps (
  roadmap_id  INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  title       VARCHAR(100) NOT NULL,
  steps       TEXT,
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- ── Blogs ─────────────────────────────────────────────────
CREATE TABLE blogs (
  blog_id      INT AUTO_INCREMENT PRIMARY KEY,
  author_id    INT NOT NULL,
  title        VARCHAR(150) NOT NULL,
  content      TEXT NOT NULL,
  excerpt      VARCHAR(300),
  tags         VARCHAR(200),
  category     VARCHAR(80),
  cover_emoji  VARCHAR(10) DEFAULT '📘',
  status       ENUM('draft','published') DEFAULT 'draft',
  published_at DATETIME,
  created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (author_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- ── Feedback ──────────────────────────────────────────────
CREATE TABLE feedback (
  feedback_id  INT AUTO_INCREMENT PRIMARY KEY,
  user_id      INT,
  name         VARCHAR(100),
  email        VARCHAR(100),
  rating       TINYINT DEFAULT 5,
  message      TEXT NOT NULL,
  status       ENUM('pending','reviewed') DEFAULT 'pending',
  submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
);

-- ── Contact Messages ──────────────────────────────────────
CREATE TABLE contact_messages (
  contact_id   INT AUTO_INCREMENT PRIMARY KEY,
  name         VARCHAR(100) NOT NULL,
  email        VARCHAR(100) NOT NULL,
  subject      VARCHAR(150),
  message      TEXT NOT NULL,
  submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ── Ads ───────────────────────────────────────────────────
CREATE TABLE ads (
  ad_id       INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(150) NOT NULL,
  image_path  VARCHAR(255),
  link_url    VARCHAR(255),
  alt_text    VARCHAR(200),
  position    ENUM('header_banner','footer_banner') NOT NULL,
  is_active   TINYINT(1) DEFAULT 1,
  start_date  DATE,
  end_date    DATE,
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ── Seed: Default Admin ───────────────────────────────────
-- Password: admin123  (bcrypt hash)
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@careercompass.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- ── Seed: Sample Career Paths ─────────────────────────────
INSERT INTO career_paths (title, level, stream, description, required_degree, job_roles, further_study, avg_salary, duration) VALUES
('Science (PCM)', 'After 10th', 'Science', 'Physics, Chemistry, Maths stream for engineering aspirants.', '10th Pass', 'Student', 'B.Tech, B.Sc, BCA', '—', '2 years'),
('Science (PCB)', 'After 10th', 'Science', 'Physics, Chemistry, Biology stream for medical aspirants.', '10th Pass', 'Student', 'MBBS, BPharm, Nursing', '—', '2 years'),
('Commerce', 'After 10th', 'Commerce', 'Accounts, Economics, Business Studies.', '10th Pass', 'Student', 'B.Com, BBA, CA', '—', '2 years'),
('Arts / Humanities', 'After 10th', 'Arts', 'History, Geography, Political Science, Psychology.', '10th Pass', 'Student', 'BA, Law, Journalism', '—', '2 years'),
('B.Tech / B.E.', 'After 12th', 'Science (PCM)', 'Bachelor of Technology in various engineering disciplines.', '12th Science (PCM)', 'Software Engineer, Civil Engineer, Mech Engineer', 'M.Tech, MBA, MS Abroad', '₹4–12 LPA', '4 years'),
('MBBS', 'After 12th', 'Science (PCB)', 'Bachelor of Medicine and Surgery.', '12th Science (PCB)', 'Doctor, Surgeon, Researcher', 'MD, MS, DM', '₹8–25 LPA', '5.5 years'),
('BCA', 'After 12th', 'Science / Commerce', 'Bachelor of Computer Applications.', '12th Any Stream', 'Programmer, Web Developer, DBA', 'MCA, M.Sc CS, MBA IT', '₹3–8 LPA', '3 years'),
('B.Com', 'After 12th', 'Commerce', 'Bachelor of Commerce.', '12th Commerce', 'Accountant, Auditor, Finance Analyst', 'M.Com, MBA, CA, CPA', '₹2–6 LPA', '3 years'),
('BBA', 'After 12th', 'Commerce / Arts', 'Bachelor of Business Administration.', '12th Any Stream', 'Manager, Entrepreneur, HR', 'MBA, PGDM', '₹3–7 LPA', '3 years'),
('BA / B.Sc', 'After 12th', 'Arts / Science', 'General undergraduate in Arts or Science.', '12th Any Stream', 'Teacher, Researcher, Civil Services', 'MA, M.Sc, PhD, LLB', '₹2–5 LPA', '3 years'),
('Software Engineer', 'Degree', 'Engineering / CS', 'Design, develop and maintain software systems.', 'B.Tech CS / BCA / B.Sc CS', 'Junior Dev, Senior Dev, Architect', 'M.Tech, MS, MBA', '₹5–30 LPA', 'Career'),
('Data Scientist', 'Degree', 'Engineering / CS / Stats', 'Analyse data using ML and statistical models.', 'B.Tech / B.Sc Stats / BCA', 'Data Analyst, ML Engineer, AI Researcher', 'M.Tech AI/ML, MS Data Science', '₹6–35 LPA', 'Career'),
('CA (Chartered Accountant)', 'Degree', 'Commerce', 'Professional accountancy and finance certification.', 'B.Com / BBA / Any Graduate', 'Auditor, Finance Manager, CFO', 'ACCA, CFA, MBA Finance', '₹7–25 LPA', 'Career'),
('Doctor (MD/MS)', 'Degree', 'Medical', 'Postgraduate medical specialisation.', 'MBBS', 'Specialist Doctor, Surgeon, Consultant', 'DM, MCh, Fellowship', '₹15–60 LPA', 'Career');

-- ── Seed: Sample Blogs ────────────────────────────────────
INSERT INTO blogs (author_id, title, content, excerpt, tags, category, cover_emoji, status, published_at) VALUES
(1, 'How to Choose Between Science and Commerce After 10th', 'Choosing the right stream after 10th is one of the most critical decisions...\n\nScience stream opens doors to engineering (B.Tech), medicine (MBBS), research, and technology careers. If you enjoy solving problems, experimenting, and have a strong interest in math or biology, Science is for you.\n\nCommerce stream leads to CA, MBA, finance, banking, and business careers. If you love economics, accounts, and entrepreneurship, Commerce suits you best.\n\nArts/Humanities is perfect for those interested in law, civil services, journalism, psychology, and social work.\n\nKey factors to consider:\n1. Your interest and passion\n2. Career goals and job market\n3. Academic strengths\n4. Parental guidance\n\nRemember — there is no "better" stream. The best stream is the one that aligns with your strengths and goals.', 'Choosing between Science and Commerce after 10th is a life-changing decision. Here is a complete guide to help you decide.', 'stream selection,10th,career,science,commerce', 'Career Guidance', '🎯', 'published', NOW()),
(1, 'Top 10 Career Options After 12th Science (PCM)', 'If you are a PCM student wondering what to do after 12th, you have more options than just B.Tech...\n\n1. B.Tech/B.E. — Most popular choice for engineering roles\n2. B.Sc — For research and academic paths\n3. BCA — For software and IT careers\n4. NDA — For defence services\n5. Merchant Navy — Lucrative maritime career\n6. B.Arch — Architecture for creative minds\n7. Pilot Training — Aviation career\n8. BSc Forensic Science — Crime investigation\n9. BSc Data Science — Future-ready analytics career\n10. BCA + MCA — Full computer applications path\n\nChoose based on your interest, budget, and long-term vision.', 'PCM students have more than just B.Tech as an option. Explore top 10 career paths available after 12th Science.', 'PCM,12th science,career options,B.Tech,BCA', 'Career Guidance', '🔬', 'published', NOW()),
(1, 'MCA vs MBA: Which is Better for a BCA Graduate?', 'After completing BCA, two paths are most popular — MCA and MBA. Both are excellent but serve very different goals.\n\nMCA (Master of Computer Applications):\n- Technical depth in programming, databases, networking\n- Best for software developers, system architects\n- Average salary: ₹5–18 LPA\n- Top recruiters: TCS, Infosys, Wipro, startups\n\nMBA (Master of Business Administration):\n- Management, leadership, strategy, marketing\n- Best for those wanting to move into management or entrepreneurship\n- Average salary: ₹6–25 LPA (top B-schools)\n- Top recruiters: Consulting firms, banks, FMCG companies\n\nVerdict: If you love coding → MCA. If you want to lead teams and build businesses → MBA. If you want both → MCA + MBA dual degree or MBA with IT specialization.', 'MCA vs MBA is the most common dilemma for BCA graduates. This guide breaks down both options to help you decide.', 'MCA,MBA,BCA,postgraduate,career', 'Higher Education', '🎓', 'published', NOW());

-- ── Seed: Sample Ads ─────────────────────────────────────
INSERT INTO ads (title, image_path, link_url, alt_text, position, is_active) VALUES
('Header Promo', '', 'https://example.com', 'Special Offer — Click to Learn More', 'header_banner', 1),
('Footer Promo', '', 'https://example.com', 'Explore Our Partner Courses', 'footer_banner', 1);
