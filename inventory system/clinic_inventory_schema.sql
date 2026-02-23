-- Create database
CREATE DATABASE IF NOT EXISTS clinic_inventory;
USE clinic_inventory;

-- Table: monthly_log
CREATE TABLE IF NOT EXISTS monthly_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    expiration_date DATE NOT NULL,
    lot_no VARCHAR(100) NOT NULL,
    initial_quantity INT NOT NULL,
    final_quantity INT NOT NULL,
    year INT NOT NULL,
    month INT NOT NULL,
    unit VARCHAR(50),
    day INT
);

-- Table: history
CREATE TABLE IF NOT EXISTS history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT,
    action VARCHAR(50) NOT NULL,
    field_changed VARCHAR(100) NOT NULL,
    old_value VARCHAR(255),
    new_value VARCHAR(255),
    action_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
