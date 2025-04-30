<?php
require_once '../config/db.php';

try {
    // Check if recipes table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'recipes'");
    if ($stmt->rowCount() === 0) {
        echo "Recipes table does not exist!\n";
        exit;
    }

    // Count recipes
    $stmt = $pdo->query("SELECT COUNT(*) FROM recipes");
    $count = $stmt->fetchColumn();
    echo "Number of recipes in database: " . $count . "\n";

    // Show all recipes
    $stmt = $pdo->query("SELECT * FROM recipes");
    $recipes = $stmt->fetchAll();
    
    echo "\nAll recipes:\n";
    foreach ($recipes as $recipe) {
        echo "ID: " . $recipe['id'] . "\n";
        echo "Name: " . $recipe['name'] . "\n";
        echo "Ingredients: " . $recipe['ingredients'] . "\n";
        echo "Instructions: " . $recipe['instructions'] . "\n";
        echo "-------------------\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 