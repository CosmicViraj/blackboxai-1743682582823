<?php
require 'db_connect.php';

$tables = [
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255),
        email VARCHAR(255) UNIQUE,
        password VARCHAR(255),
        role ENUM('freelancer', 'employer'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS jobs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255),
        description TEXT,
        employer_id INT,
        budget DECIMAL(10,2),
        category VARCHAR(50),
        status ENUM('active', 'closed'),
        FOREIGN KEY (employer_id) REFERENCES users(id)
    )",
    
    "CREATE TABLE IF NOT EXISTS applications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        job_id INT,
        freelancer_id INT,
        proposal TEXT,
        status ENUM('pending', 'accepted', 'rejected'),
        FOREIGN KEY (job_id) REFERENCES jobs(id),
        FOREIGN KEY (freelancer_id) REFERENCES users(id)
    )",
    
    "CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sender_id INT,
        receiver_id INT,
        content TEXT,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (sender_id) REFERENCES users(id),
        FOREIGN KEY (receiver_id) REFERENCES users(id)
    )"
];

foreach ($tables as $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Table created successfully<br>";
    } else {
        echo "Error creating table: " . $conn->error . "<br>";
    }
}

$conn->close();
echo "Database setup complete!";
?>