CREATE DATABASE IF NOT EXISTS student_portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE student_portfolio;
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS roles (
    id TINYINT UNSIGNED PRIMARY KEY,
    code VARCHAR(16) NOT NULL UNIQUE,
    name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(64) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(120) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    email VARCHAR(254) NOT NULL,
    role_id TINYINT UNSIGNED NOT NULL DEFAULT 2,
    FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS achievement_types (
    id SMALLINT UNSIGNED PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS event_levels (
    id TINYINT UNSIGNED PRIMARY KEY,
    name VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS statuses (
    id TINYINT UNSIGNED PRIMARY KEY,
    name VARCHAR(60) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS achievements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    type_id SMALLINT UNSIGNED NOT NULL,
    level_id TINYINT UNSIGNED NOT NULL,
    status_id TINYINT UNSIGNED NOT NULL DEFAULT 1,
    event_name VARCHAR(180) NOT NULL,
    event_date DATE NOT NULL,
    original_file VARCHAR(255) NOT NULL,
    stored_file VARCHAR(48) NOT NULL UNIQUE,
    submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (type_id) REFERENCES achievement_types(id),
    FOREIGN KEY (level_id) REFERENCES event_levels(id),
    FOREIGN KEY (status_id) REFERENCES statuses(id),
    KEY achievements_by_student (user_id, id),
    KEY achievements_by_type_status (type_id, status_id, id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reviews (
    achievement_id INT UNSIGNED PRIMARY KEY,
    comment TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (achievement_id) REFERENCES achievements(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT IGNORE INTO roles VALUES (1, 'admin', 'Куратор'), (2, 'student', 'Студент');
INSERT IGNORE INTO achievement_types VALUES
    (1, 'Олимпиада'), (2, 'Конкурс профмастерства'), (3, 'Волонтёрская деятельность'),
    (4, 'Спортивное соревнование'), (5, 'Научная конференция'), (6, 'Творческий конкурс');
INSERT IGNORE INTO event_levels VALUES
    (1, 'Международный'), (2, 'Всероссийский'), (3, 'Региональный'), (4, 'Локальный');
INSERT IGNORE INTO statuses VALUES (1, 'На проверке'), (2, 'Подтверждено'), (3, 'Отклонено');
INSERT IGNORE INTO users (id, login, password_hash, full_name, phone, email, role_id) VALUES
    (1, 'Admin', '$2y$10$X82Me1A9sl5q21VFLeo0Tu.YYHNTbHCDeXcz22ZKmo.6q94VSEhHS',
     'Куратор портфолио', '8(999)123-45-67', 'admin@example.test', 1);
