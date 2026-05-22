-- ============================================================
--  FindHouses — Complete MySQL Database Schema
--  Run this file once in phpMyAdmin or mysql CLI:
--    mysql -u root -p < database.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS find_houses
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE find_houses;

-- ────────────────────────────────────────────────────────────
--  USERS
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id            INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    first_name    VARCHAR(80)      NOT NULL,
    last_name     VARCHAR(80)      NOT NULL,
    username      VARCHAR(80)      NOT NULL UNIQUE,
    email         VARCHAR(180)     NOT NULL UNIQUE,
    password_hash VARCHAR(255)     NOT NULL,
    phone         VARCHAR(30)               DEFAULT NULL,
    address       VARCHAR(255)              DEFAULT NULL,
    about         TEXT                      DEFAULT NULL,
    avatar        VARCHAR(255)              DEFAULT 'default-avatar.jpg',
    role          ENUM('user','agent','admin') NOT NULL DEFAULT 'user',
    created_at    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

-- ────────────────────────────────────────────────────────────
--  PROPERTIES
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS properties (
    id            INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    user_id       INT UNSIGNED     NOT NULL,
    title         VARCHAR(255)     NOT NULL,
    description   TEXT             NOT NULL,
    status        ENUM('For Sale','For Rent') NOT NULL DEFAULT 'For Sale',
    type          ENUM('House','Apartment','Commercial','Lot','Garage','Villa') NOT NULL DEFAULT 'House',
    price         DECIMAL(12,2)    NOT NULL,
    area_sqft     DECIMAL(10,2)              DEFAULT NULL,
    bedrooms      TINYINT UNSIGNED           DEFAULT NULL,
    bathrooms     TINYINT UNSIGNED           DEFAULT NULL,
    rooms         TINYINT UNSIGNED           DEFAULT NULL,
    garages       TINYINT UNSIGNED           DEFAULT NULL,
    property_age  VARCHAR(20)                DEFAULT NULL,
    year_built    YEAR                       DEFAULT NULL,
    address       VARCHAR(255)               DEFAULT NULL,
    city          VARCHAR(100)               DEFAULT NULL,
    state         VARCHAR(100)               DEFAULT NULL,
    country       VARCHAR(100)               DEFAULT NULL,
    latitude      DECIMAL(10,7)              DEFAULT NULL,
    longitude     DECIMAL(10,7)              DEFAULT NULL,
    listing_status ENUM('active','pending','inactive') NOT NULL DEFAULT 'active',
    featured      TINYINT(1)       NOT NULL DEFAULT 0,
    views         INT UNSIGNED     NOT NULL DEFAULT 0,
    cover_image   VARCHAR(255)               DEFAULT NULL,
    created_at    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ────────────────────────────────────────────────────────────
--  PROPERTY IMAGES
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS property_images (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    property_id INT UNSIGNED NOT NULL,
    filename    VARCHAR(255) NOT NULL,
    sort_order  TINYINT UNSIGNED NOT NULL DEFAULT 0,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ────────────────────────────────────────────────────────────
--  PROPERTY FEATURES  (air conditioning, pool, wifi, etc.)
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS property_features (
    property_id INT UNSIGNED NOT NULL,
    feature     VARCHAR(100) NOT NULL,
    PRIMARY KEY (property_id, feature),
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ────────────────────────────────────────────────────────────
--  FAVORITES  (users bookmarking properties)
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS favorites (
    user_id     INT UNSIGNED NOT NULL,
    property_id INT UNSIGNED NOT NULL,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, property_id),
    FOREIGN KEY (user_id)     REFERENCES users(id)      ON DELETE CASCADE,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ────────────────────────────────────────────────────────────
--  REVIEWS
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS reviews (
    id          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    property_id INT UNSIGNED     NOT NULL,
    user_id     INT UNSIGNED              DEFAULT NULL,
    name        VARCHAR(120)     NOT NULL,
    email       VARCHAR(180)     NOT NULL,
    rating      TINYINT UNSIGNED NOT NULL DEFAULT 5,
    body        TEXT             NOT NULL,
    created_at  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)     REFERENCES users(id)      ON DELETE SET NULL
) ENGINE=InnoDB;

-- ────────────────────────────────────────────────────────────
--  MESSAGES  (contact-agent form on property page)
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS messages (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    property_id INT UNSIGNED          DEFAULT NULL,
    sender_name VARCHAR(120) NOT NULL,
    sender_email VARCHAR(180) NOT NULL,
    sender_phone VARCHAR(30)          DEFAULT NULL,
    body        TEXT         NOT NULL,
    owner_id    INT UNSIGNED          DEFAULT NULL,
    is_read     TINYINT(1)   NOT NULL DEFAULT 0,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE SET NULL,
    FOREIGN KEY (owner_id)    REFERENCES users(id)      ON DELETE SET NULL
) ENGINE=InnoDB;

-- ────────────────────────────────────────────────────────────
--  CONTACT  (general contact-us form)
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS contact_submissions (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name       VARCHAR(120) NOT NULL,
    email      VARCHAR(180) NOT NULL,
    phone      VARCHAR(30)          DEFAULT NULL,
    subject    VARCHAR(255)         DEFAULT NULL,
    body       TEXT         NOT NULL,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

-- ────────────────────────────────────────────────────────────
--  BLOG POSTS
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS blog_posts (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    author_id   INT UNSIGNED          DEFAULT NULL,
    title       VARCHAR(255) NOT NULL,
    slug        VARCHAR(255) NOT NULL UNIQUE,
    excerpt     TEXT                  DEFAULT NULL,
    body        LONGTEXT     NOT NULL,
    cover_image VARCHAR(255)          DEFAULT NULL,
    category    VARCHAR(80)           DEFAULT NULL,
    published   TINYINT(1)   NOT NULL DEFAULT 1,
    views       INT UNSIGNED NOT NULL DEFAULT 0,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ────────────────────────────────────────────────────────────
--  NEWSLETTER SUBSCRIPTIONS
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS newsletter (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email      VARCHAR(180) NOT NULL UNIQUE,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

-- ────────────────────────────────────────────────────────────
--  PAYMENT METHODS  (stored card info — masked)
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS payment_methods (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id      INT UNSIGNED NOT NULL,
    card_type    VARCHAR(20)  NOT NULL DEFAULT 'Visa',
    last_four    CHAR(4)      NOT NULL,
    cardholder   VARCHAR(120) NOT NULL,
    expiry_month CHAR(2)      NOT NULL,
    expiry_year  CHAR(4)      NOT NULL,
    is_default   TINYINT(1)   NOT NULL DEFAULT 0,
    created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ────────────────────────────────────────────────────────────
--  INVOICES
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS invoices (
    id          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    user_id     INT UNSIGNED     NOT NULL,
    property_id INT UNSIGNED              DEFAULT NULL,
    amount      DECIMAL(12,2)    NOT NULL,
    description VARCHAR(255)     NOT NULL,
    status      ENUM('paid','pending','overdue') NOT NULL DEFAULT 'pending',
    issued_at   DATE             NOT NULL,
    due_at      DATE                       DEFAULT NULL,
    paid_at     DATETIME                   DEFAULT NULL,
    created_at  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id)     REFERENCES users(id)      ON DELETE CASCADE,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
--  SAMPLE SEED DATA
-- ============================================================

-- Demo user  (password: password123)
INSERT INTO users (first_name, last_name, username, email, password_hash, phone, role, about)
VALUES
  ('Mary',  'Smith',  'mary',       'mary@example.com',    '$2y$12$aohfkFfx5wkWdZJlN2zPmur4CskOUkmq5xgLniF3puWdiI0Rd0z8W', '+1 555 100 0001', 'user',  'Passionate about finding the perfect home.'),
  ('Admin', 'User',   'admin',      'admin@findhouses.com','$2y$12$aohfkFfx5wkWdZJlN2zPmur4CskOUkmq5xgLniF3puWdiI0Rd0z8W', '+1 555 000 0000', 'admin', 'Site administrator.'),
  ('Carls', 'Jhons',  'carlsjhons', 'carls@findhouses.com','$2y$12$aohfkFfx5wkWdZJlN2zPmur4CskOUkmq5xgLniF3puWdiI0Rd0z8W', '+1 555 100 0003', 'agent', 'Senior Real Estate Agent based in NYC.');

-- Demo properties (owner = mary, id=1)
INSERT INTO properties
  (user_id, title, description, status, type, price, area_sqft, bedrooms, bathrooms, rooms, garages, year_built, address, city, state, country, latitude, longitude, listing_status, featured, cover_image)
VALUES
  (1,'Real Luxury Family Villa','This stunning luxury villa offers breathtaking city views and premium finishes throughout. Featuring an open-plan living area, gourmet kitchen, private pool, and rooftop terrace.','For Sale','House',150000.00,720,6,4,7,2,2020,'Est St, 77','New York','NY','USA',40.7660,-73.9800,'active',1,'b-11.jpg'),
  (1,'Modern Downtown Penthouse','Modern penthouse apartment in the heart of NYC. Floor-to-ceiling windows, stunning views, high-end appliances.','For Rent','Apartment',2400.00,480,3,2,4,1,2018,'5th Ave, Upper East Side','New York','NY','USA',40.7731,-73.9597,'active',1,'fp-12.jpg'),
  (1,'Spacious Suburban Home','Large family home in the beautiful Beverly Hills neighborhood. Great school district, quiet street.','For Sale','House',289000.00,890,5,3,6,2,2015,'Maple Drive','Los Angeles','CA','USA',34.0736,-118.4004,'active',0,'b-12.jpg'),
  (1,'Beachfront Condo','Stunning beachfront condominium with direct ocean access. Sunrise views every morning.','For Sale','Apartment',410000.00,640,4,3,5,1,2019,'Ocean Drive','Miami','FL','USA',25.7617,-80.1918,'active',1,'b-1.jpg'),
  (1,'Hilltop Colonial Estate','Magnificent colonial-style estate set on a hilltop with sweeping bay views.','For Sale','House',520000.00,1100,5,4,7,3,2010,'Hillside Rd','San Francisco','CA','USA',37.7749,-122.4194,'active',1,'fp-10.jpg'),
  (1,'Luxury City Apartment','Sleek downtown apartment in the heart of Chicago. Walking distance to everything.','For Rent','Apartment',3100.00,560,2,2,3,1,2021,'Downtown','Chicago','IL','USA',41.8827,-87.6233,'active',0,'fp-11.jpg');

-- Features for property 1
INSERT INTO property_features (property_id, feature) VALUES
  (1,'Air Conditioning'),(1,'Swimming Pool'),(1,'Gym'),(1,'WiFi'),
  (1,'Central Heating'),(1,'Alarm'),(1,'TV Cable'),(1,'Laundry Room'),
  (1,'Parking'),(1,'Balcony');

-- Sample reviews
INSERT INTO reviews (property_id, user_id, name, email, rating, body) VALUES
  (1, 1, 'Mary Smith',     'mary@example.com',   5, 'Absolutely stunning property. The views are breathtaking and the finishes are top notch!'),
  (1, NULL,'Abraham Tyron','abr@example.com',     4, 'Great location and very spacious. The agent was really helpful throughout the process.'),
  (1, NULL,'Lisa Williams', 'lisa@example.com',   5, 'Dream home! Could not be happier with the purchase. Highly recommend this listing.');

-- Sample blog posts
INSERT INTO blog_posts (author_id, title, slug, excerpt, body, cover_image, category, published) VALUES
  (1,'10 Tips for First-Time Home Buyers','10-tips-first-time-home-buyers','Buying your first home can be overwhelming. Here are ten expert tips to guide you through the process.','<p>Buying your first home is one of the most exciting — and daunting — experiences of your life. Here are ten tips to make it easier...</p><p>1. Get pre-approved for a mortgage before you start looking...</p><p>2. Know your budget and stick to it...</p>','b-1.jpg','Buying','1'),
  (1,'How to Stage Your Home for a Quick Sale','how-to-stage-home-quick-sale','First impressions matter. Learn how professional staging can help you sell faster and for more money.','<p>Staging your home properly can mean the difference between a quick sale at asking price and months on the market...</p>','b-2.jpg','Selling','1'),
  (1,'Understanding Mortgage Rates in 2026','understanding-mortgage-rates-2026','Mortgage rates have been fluctuating. Here is what buyers need to know heading into the rest of 2026.','<p>Interest rates play a crucial role in the overall cost of purchasing a home...</p>','b-3.jpg','Finance','1');

-- Sample contact submission
INSERT INTO contact_submissions (name, email, phone, subject, body) VALUES
  ('John Doe','john@example.com','+1 555 999 8888','Property Inquiry','I am interested in the beachfront condo listing. Please contact me.');

-- Sample invoice
INSERT INTO invoices (user_id, property_id, amount, description, status, issued_at, due_at) VALUES
  (1, 1, 299.00, 'Featured Listing — Real Luxury Family Villa', 'paid',   '2026-01-10', '2026-01-20'),
  (1, 2, 199.00, 'Standard Listing — Modern Downtown Penthouse', 'pending','2026-02-01', '2026-02-15'),
  (1, 3, 149.00, 'Standard Listing — Spacious Suburban Home',    'overdue','2025-12-01', '2025-12-15');

-- ────────────────────────────────────────────────────────────
--  BLOG COMMENTS
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS blog_comments (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    blog_id    INT UNSIGNED NOT NULL,
    user_id    INT UNSIGNED          DEFAULT NULL,
    name       VARCHAR(120) NOT NULL,
    email      VARCHAR(180) NOT NULL,
    body       TEXT         NOT NULL,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (blog_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ────────────────────────────────────────────────────────────
--  PASSWORD RESETS
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS password_resets (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email      VARCHAR(180) NOT NULL,
    token      VARCHAR(64)  NOT NULL,
    expires_at DATETIME     NOT NULL,
    used       TINYINT(1)   NOT NULL DEFAULT 0,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX (token),
    INDEX (email)
) ENGINE=InnoDB;


