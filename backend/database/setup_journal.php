<?php
require_once '../config/db.php';

try {
    // Read and execute the SQL file
    $sql = file_get_contents('create_journal_table.sql');
    $pdo->exec($sql);
    echo "Journal entries table created successfully!";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?> 