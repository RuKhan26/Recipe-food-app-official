<?php
require_once '../config/db.php';

try {
    // Check if recipes table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'recipes'");
    if ($stmt->rowCount() == 0) {
        // Table doesn't exist, create it
        $pdo->exec("CREATE TABLE IF NOT EXISTS recipes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            ingredients TEXT NOT NULL,
            instructions TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        echo "Recipes table created successfully!\n";
    }

    // Count existing recipes
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM recipes");
    $count = $stmt->fetch()['count'];
    echo "Current number of recipes: $count\n";

    // Import new recipes
    $sql = file_get_contents('sample_recipes.sql');
    $pdo->exec($sql);
    
    // Count recipes after import
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM recipes");
    $newCount = $stmt->fetch()['count'];
    echo "New number of recipes: $newCount\n";
    
    if ($newCount > $count) {
        echo "New recipes imported successfully!\n";
    } else {
        echo "No new recipes were imported. The recipes might already exist.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?> 