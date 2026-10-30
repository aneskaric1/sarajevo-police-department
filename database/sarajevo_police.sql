-- Sarajevo Police Department: database schema and sample data (Milestone 2).
-- Import in phpMyAdmin (Import tab) or from a terminal:
--   mysql -u root -p < database/sarajevo_police.sql
-- Running it again drops the database and starts from clean sample data.

DROP DATABASE IF EXISTS sarajevo_police;
CREATE DATABASE sarajevo_police CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sarajevo_police;

-- ---------------------------------------------------------------------------
-- Tables (in this order because of the foreign keys)
-- ---------------------------------------------------------------------------

-- Officers and administrators who log in to the portal.
CREATE TABLE users (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  badge_number  VARCHAR(20)  NOT NULL UNIQUE,
  first_name    VARCHAR(50)  NOT NULL,
  last_name     VARCHAR(50)  NOT NULL,
  email         VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  officer_rank  VARCHAR(50)  NOT NULL DEFAULT 'Patrol Officer',
  role          ENUM('admin','user') NOT NULL DEFAULT 'user',
  status        ENUM('active','pending') NOT NULL DEFAULT 'pending',
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- People known to the department (drivers, victims, witnesses, suspects).
CREATE TABLE citizens (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  first_name    VARCHAR(50)  NOT NULL,
  last_name     VARCHAR(50)  NOT NULL,
  national_id   CHAR(13)     NOT NULL UNIQUE,
  date_of_birth DATE         NOT NULL,
  phone         VARCHAR(30)  NULL,
  address       VARCHAR(150) NULL
);

-- Each vehicle has one owner; a citizen can own many vehicles.
CREATE TABLE vehicles (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  plate_number VARCHAR(15)  NOT NULL UNIQUE,
  make         VARCHAR(50)  NOT NULL,
  model        VARCHAR(50)  NOT NULL,
  colour       VARCHAR(30)  NULL,
  status       ENUM('registered','stolen') NOT NULL DEFAULT 'registered',
  citizen_id   INT UNSIGNED NOT NULL,
  FOREIGN KEY (citizen_id) REFERENCES citizens(id)
);

-- Catalogue of violations with the standard fine.
CREATE TABLE violation_types (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code          VARCHAR(10)  NOT NULL UNIQUE,
  name          VARCHAR(80)  NOT NULL,
  standard_fine DECIMAL(8,2) NOT NULL,
  description   VARCHAR(255) NULL
);

-- A fine issued by an officer to a citizen, optionally for a vehicle.
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
  FOREIGN KEY (vehicle_id)        REFERENCES vehicles(id) ON DELETE SET NULL,
  FOREIGN KEY (violation_type_id) REFERENCES violation_types(id),
  FOREIGN KEY (officer_id)        REFERENCES users(id)
);

-- A case reported to the police and handled by one officer.
CREATE TABLE incidents (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  case_number VARCHAR(20)  NOT NULL UNIQUE,
  title       VARCHAR(120) NOT NULL,
  type        VARCHAR(50)  NOT NULL,
  description TEXT         NULL,
  location    VARCHAR(150) NOT NULL,
  reported_at DATETIME     NOT NULL,
  status      ENUM('open','investigating','closed') NOT NULL DEFAULT 'open',
  officer_id  INT UNSIGNED NULL,
  FOREIGN KEY (officer_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Many-to-many between incidents and citizens: who was involved and how.
-- It has its own id so that the generic BaseDao CRUD works for it too;
-- the UNIQUE key stops the same citizen from being added twice to one incident.
CREATE TABLE incident_citizens (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  incident_id INT UNSIGNED NOT NULL,
  citizen_id  INT UNSIGNED NOT NULL,
  involvement ENUM('victim','witness','suspect') NOT NULL,
  UNIQUE KEY uq_incident_citizen (incident_id, citizen_id),
  FOREIGN KEY (incident_id) REFERENCES incidents(id) ON DELETE CASCADE,
  FOREIGN KEY (citizen_id)  REFERENCES citizens(id)
);

-- ---------------------------------------------------------------------------
-- Sample data
-- Test accounts: admin@spd.test / admin123 and officer@spd.test / user123.
-- All other officers also use the password user123.
-- ---------------------------------------------------------------------------

INSERT INTO users (id, badge_number, first_name, last_name, email, password_hash, officer_rank, role, status) VALUES
(1, 'A-001', 'Anes',  'Karic',  'admin@spd.test',       '$2y$10$DYDIQrODV8X0Uihq3fMyE.r0gP5XOyyWClxwsZTJXPkc41sI3TWiK', 'Chief Inspector', 'admin', 'active'),
(2, 'P-214', 'Emir',  'Kovac',  'officer@spd.test',     '$2y$10$oe4u1c7zUWQH2QWLi9egVuLeHx/pBvU/AqlyyIHq8GepSlSeK3UU2', 'Patrol Officer',  'user',  'active'),
(3, 'P-188', 'Dino',  'Delic',  'dino.delic@spd.test',  '$2y$10$oe4u1c7zUWQH2QWLi9egVuLeHx/pBvU/AqlyyIHq8GepSlSeK3UU2', 'Patrol Officer',  'user',  'active'),
(4, 'P-302', 'Maja',  'Begic',  'maja.begic@spd.test',  '$2y$10$oe4u1c7zUWQH2QWLi9egVuLeHx/pBvU/AqlyyIHq8GepSlSeK3UU2', 'Sergeant',        'user',  'active'),
(5, 'P-331', 'Haris', 'Zukic',  'haris.zukic@spd.test', '$2y$10$oe4u1c7zUWQH2QWLi9egVuLeHx/pBvU/AqlyyIHq8GepSlSeK3UU2', 'Patrol Officer',  'user',  'pending'),
(6, 'I-045', 'Selma', 'Hasic',  'selma.hasic@spd.test', '$2y$10$oe4u1c7zUWQH2QWLi9egVuLeHx/pBvU/AqlyyIHq8GepSlSeK3UU2', 'Inspector',       'user',  'active');

INSERT INTO citizens (id, first_name, last_name, national_id, date_of_birth, phone, address) VALUES
(1, 'Adnan', 'Mujic',   '0101990170012', '1990-01-01', '061 555 101', 'Hamdije Kresevljakovica 4, Sarajevo'),
(2, 'Lejla', 'Hadzic',  '1505985175034', '1985-05-15', '062 555 202', 'Obala Kulina bana 17, Sarajevo'),
(3, 'Tarik', 'Softic',  '2203001170056', '2001-03-22', '063 555 303', 'Ferhadija 30, Sarajevo'),
(4, 'Sara',  'Basic',   '0912978175078', '1978-12-09', '061 555 404', 'Titova 8, Sarajevo'),
(5, 'Kenan', 'Imamovic','1407995170091', '1995-07-14', '062 555 505', 'Grbavicka 21, Sarajevo'),
(6, 'Amra',  'Selimovic','3008988175013','1988-08-30', '063 555 606', 'Dzemala Bijedica 112, Sarajevo'),
(7, 'Nedim', 'Ramic',   '0203003170025', '2003-02-02', '061 555 707', 'Branilaca Sarajeva 5, Sarajevo'),
(8, 'Ajla',  'Dedic',   '1911972175047', '1972-11-19', '062 555 808', 'Zmaja od Bosne 50, Sarajevo');

INSERT INTO vehicles (id, plate_number, make, model, colour, status, citizen_id) VALUES
(1, 'A12-K-345', 'Volkswagen', 'Golf',    'Grey',  'registered', 1),
(2, 'K34-E-901', 'Skoda',      'Octavia', 'Blue',  'registered', 2),
(3, 'J56-M-112', 'Opel',       'Corsa',   'Red',   'registered', 3),
(4, 'E78-A-560', 'Ford',       'Focus',   'White', 'stolen',     4),
(5, 'O21-T-778', 'Renault',    'Clio',    'Black', 'registered', 5),
(6, 'M90-J-234', 'Toyota',     'Yaris',   'Silver','registered', 6),
(7, 'T45-O-667', 'Audi',       'A4',      'Black', 'registered', 1),
(8, 'K11-A-909', 'Peugeot',    '208',     'Blue',  'stolen',     8);

INSERT INTO violation_types (id, code, name, standard_fine, description) VALUES
(1, 'V-01', 'Speeding',             80.00,  'Driving above the posted speed limit'),
(2, 'V-02', 'Illegal parking',      30.00,  'Parking where it is not allowed'),
(3, 'V-03', 'Red light',            120.00, 'Crossing on a red signal'),
(4, 'V-04', 'No seat belt',         50.00,  'Driver or passenger without a seat belt'),
(5, 'V-05', 'Expired registration', 60.00,  'Vehicle registration is not valid'),
(6, 'V-06', 'Phone while driving',  100.00, 'Using a mobile phone while driving');

INSERT INTO tickets (id, ticket_number, citizen_id, vehicle_id, violation_type_id, officer_id, location, issued_at, fine_amount, status) VALUES
(1,  'TCK-88231', 1, 1, 1, 2, 'Zmaja od Bosne, near Marijin Dvor', '2026-10-21 08:40:00', 80.00,  'unpaid'),
(2,  'TCK-88230', 2, 2, 2, 3, 'Ferhadija',                         '2026-10-21 11:15:00', 30.00,  'paid'),
(3,  'TCK-88227', 3, 3, 3, 4, 'Skenderija crossroads',             '2026-10-20 17:05:00', 120.00, 'contested'),
(4,  'TCK-88219', 4, 4, 4, 2, 'Titova',                            '2026-10-18 09:30:00', 50.00,  'paid'),
(5,  'TCK-88205', 1, 7, 5, 3, 'Grbavica',                          '2026-10-16 14:20:00', 60.00,  'cancelled'),
(6,  'TCK-88198', 5, 5, 6, 2, 'Dzemala Bijedica',                  '2026-10-14 07:55:00', 100.00, 'unpaid'),
(7,  'TCK-88187', 6, 6, 1, 4, 'Stup interchange',                  '2026-10-12 16:45:00', 80.00,  'paid'),
(8,  'TCK-88176', 7, NULL, 2, 3, 'Bascarsija',                     '2026-10-10 12:10:00', 30.00,  'unpaid'),
(9,  'TCK-88160', 2, 2, 1, 6, 'Alipasina',                         '2026-10-07 18:30:00', 80.00,  'paid'),
(10, 'TCK-88152', 3, 3, 4, 2, 'Kosevo',                            '2026-10-05 10:00:00', 50.00,  'unpaid');

INSERT INTO incidents (id, case_number, title, type, description, location, reported_at, status, officer_id) VALUES
(1, 'INC-2026-0412', 'Phone stolen at the market',  'Theft',            'A phone was taken from a bag at a market stall.', 'Markale market, Mula Mustafe Baseskije', '2026-10-21 10:20:00', 'investigating', 2),
(2, 'INC-2026-0411', 'Two cars collided',           'Traffic accident', 'Rear-end collision, no injuries.',                'Zmaja od Bosne, Otoka',                  '2026-10-21 07:50:00', 'open',          3),
(3, 'INC-2026-0409', 'Loud party at night',         'Disturbance',      'Neighbours reported loud music after midnight.',  'Titova 8',                               '2026-10-20 00:40:00', 'closed',        2),
(4, 'INC-2026-0405', 'Graffiti on school wall',     'Vandalism',        'Spray paint on the front wall of the school.',    'Second Gymnasium, Sutjeska',             '2026-10-19 08:10:00', 'investigating', 4),
(5, 'INC-2026-0398', 'Wallet stolen on the tram',   'Theft',            'Wallet taken on tram line 3.',                    'Tram stop Skenderija',                   '2026-10-17 16:30:00', 'closed',        3),
(6, 'INC-2026-0390', 'Stolen Ford Focus',           'Vehicle theft',    'Owner reported the car missing from the parking lot.', 'Titova parking lot',               '2026-10-15 06:15:00', 'investigating', 6),
(7, 'INC-2026-0384', 'Fight outside a cafe',        'Assault',          'Two men fought outside a cafe; one was injured.', 'Ferhadija 12',                           '2026-10-13 23:05:00', 'closed',        4),
(8, 'INC-2026-0377', 'Bicycle missing',             'Theft',            'A bicycle was taken from the bike rack.',         'Faculty of Economics, Trg Oslobodjenja', '2026-10-11 13:00:00', 'open',          NULL);

INSERT INTO incident_citizens (id, incident_id, citizen_id, involvement) VALUES
(1,  1, 2, 'victim'),
(2,  1, 7, 'suspect'),
(3,  2, 1, 'victim'),
(4,  2, 5, 'witness'),
(5,  3, 4, 'witness'),
(6,  4, 3, 'suspect'),
(7,  5, 6, 'victim'),
(8,  6, 4, 'victim'),
(9,  7, 7, 'suspect'),
(10, 7, 5, 'victim'),
(11, 7, 8, 'witness'),
(12, 8, 3, 'victim');
