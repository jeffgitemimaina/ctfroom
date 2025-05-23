CREATE DATABASE IF NOT EXISTS judge_system;
USE judge_system;

CREATE TABLE judges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(100) NOT NULL
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judge_id INT,
    user_id INT,
    points INT NOT NULL CHECK (points >= 1 AND points <= 100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (judge_id) REFERENCES judges(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO users (name) VALUES ('Alice'), ('Bob'), ('Charlie');
INSERT INTO judges (username, display_name) VALUES ('judge1', 'Judge One'), ('judge2', 'Judge Two');