-- COMP2245 Tutorial 9: the "noticeboard" database for the Try-it app.
--
-- Running this file deletes the noticeboard database and builds it again, so
-- it also resets the app. Run it as root:
--
--   mysql -u root -p < noticeboard.sql
--
-- It creates a MySQL user `comp2245` (password `comp2245-demo`) that can read
-- and change rows in this database only. All names and passwords are made up.
-- This app is deliberately insecure; it is an exercise. Run it on localhost.

DROP DATABASE IF EXISTS noticeboard;
CREATE DATABASE noticeboard CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE noticeboard;

-- Passwords are stored in plain text on purpose. Fixing that is one of the
-- tasks in the tutorial.
CREATE TABLE users (
  id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50)  NOT NULL UNIQUE,
  password VARCHAR(100) NOT NULL
);

INSERT INTO users (username, password) VALUES
  ('ama',  'sunflower'),
  ('devon','trinidad99');

-- Notices belong to a user. The view page lets you read a notice by id.
CREATE TABLE notices (
  id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  title   VARCHAR(120) NOT NULL,
  body    TEXT NOT NULL,
  private TINYINT(1) NOT NULL DEFAULT 0,
  created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO notices (user_id, title, body, private) VALUES
  (1, 'Study group', 'COMP2245 study group Thursdays at 4pm in the lab.', 0),
  (1, 'My locker code', 'Locker 42 code is 7391 — do not share.', 1),
  (2, 'Selling a textbook', 'Intro to Databases, good condition, $20.', 0);

CREATE USER IF NOT EXISTS 'comp2245'@'localhost' IDENTIFIED BY 'comp2245-demo';
GRANT SELECT, INSERT, UPDATE, DELETE ON noticeboard.* TO 'comp2245'@'localhost';
FLUSH PRIVILEGES;
