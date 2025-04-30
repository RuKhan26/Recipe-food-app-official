<?php
require_once '../config/db.php';

try {
    // Read the SQL file
    $sql = file_get_contents('sample_recipes.sql');
    
    // Execute the SQL
    $pdo->exec($sql);
    
    echo "Sample recipes imported successfully!";
} catch (PDOException $e) {
    echo "Error importing recipes: " . $e->getMessage();
}
?> 