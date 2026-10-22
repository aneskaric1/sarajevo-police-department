-- Sarajevo Police Department: DRAFT database schema (planning only, Milestone 1).
-- Milestone 2 will create the real database and add sample data.
-- Run order matters because of the foreign keys.

CREATE DATABASE IF NOT EXISTS police_department CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE police_department;

CREATE TABLE users (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  badge_number  VARCHAR(20)  NOT NULL UNIQUE,
  first_name    VARCHAR(50)  NOT NULL,
  last_name     VARCHAR(50)  NOT NULL,
  email         VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  `rank`        VARCHAR(50)  NOT NULL DEFAULT 'Patrol Officer',
  role          ENUM('admin','user') NOT NULL DEFAULT 'user',
  status        ENUM('active','pending') NOT NULL DEFAULT 'pending',
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE citizens (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  first_name    VARCHAR(50) NOT NULL,
  last_name     VARCHAR(50) NOT NULL,
  national_id   CHAR(13)    NOT NULL UNIQUE,
  date_of_birth DATE        NOT NULL,
  phone         VARCHAR(30),
  address       VARCHAR(150)
);

CREATE TABLE vehicles (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  plate_number VARCHAR(15) NOT NULL UNIQUE,
  make         VARCHAR(50) NOT NULL,
  model        VARCHAR(50) NOT NULL,
  colour       VARCHAR(30),
  status       ENUM('registered','stolen') NOT NULL DEFAULT 'registered',
  citizen_id   INT UNSIGNED NOT NULL,
  FOREIGN KEY (citizen_id) REFERENCES citizens(id)
);

CREATE TABLE violation_types (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code          VARCHAR(10)  NOT NULL UNIQUE,
  name          VARCHAR(80)  NOT NULL,
  standard_fine DECIMAL(8,2) NOT NULL,
  description   VARCHAR(255)
);

CREATE TABLE tickets (
  id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ticket_number     VARCHAR(20)  NOT NULL UNIQUE,
  citizen_id        INT UNSIGNED NOT NULL,
  vehicle_id        INT UNSIGNED NULL,
  violation_type_id INT UNSIGNED NOT NULL,
  officer_id        INT UNSIGNED NOT NULL,
  location          VARCHAR(150) NOT NULL,
  issued_at         DATETIME     NOT NULL,
  fine_amount       DECIMAL(8,2) NOT NULL,
  status            ENUM('unpaid','paid','contested','cancelled') NOT NULL DEFAULT 'unpaid',
  FOREIGN KEY (citizen_id)        REFERENCES citizens(id),
  FOREIGN KEY (vehicle_id)        REFERENCES vehicles(id),
  FOREIGN KEY (violation_type_id) REFERENCES violation_types(id),
  FOREIGN KEY (officer_id)        REFERENCES users(id)
);

CREATE TABLE incidents (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  case_number VARCHAR(20)  NOT NULL UNIQUE,
  title       VARCHAR(120) NOT NULL,
  type        VARCHAR(50)  NOT NULL,
  description TEXT,
  location    VARCHAR(150) NOT NULL,
  reported_at DATETIME     NOT NULL,
  status      ENUM('open','investigating','closed') NOT NULL DEFAULT 'open',
  officer_id  INT UNSIGNED NULL,
  FOREIGN KEY (officer_id) REFERENCES users(id)
);

-- Many-to-many: one incident involves many citizens, one citizen can be in many incidents.
CREATE TABLE incident_citizens (
  incident_id INT UNSIGNED NOT NULL,
  citizen_id  INT UNSIGNED NOT NULL,
  involvement ENUM('victim','witness','suspect') NOT NULL,
  PRIMARY KEY (incident_id, citizen_id),
  FOREIGN KEY (incident_id) REFERENCES incidents(id) ON DELETE CASCADE,
  FOREIGN KEY (citizen_id)  REFERENCES citizens(id)
);
