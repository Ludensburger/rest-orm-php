CREATE DATABASE rest_api;

use rest_api;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    token VARCHAR(500) NULL,
    token_expires_at DATETIME NULL,
    created_at DATETIME NOT NULL
);

