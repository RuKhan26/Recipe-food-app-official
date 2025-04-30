<?php
require_once __DIR__ . '/config/database.php';

try {
    $db = getDBConnection();
    echo "Database connection successful!\n";

    // Check if meal_plans table exists
    $result = $db->query("SHOW TABLES LIKE 'meal_plans'");
    if ($result->rowCount() > 0) {
        echo "meal_plans table exists!\n";
        
        // Show table structure
        $result = $db->query("DESCRIBE meal_plans");
        echo "Table structure:\n";
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            print_r($row);
        }
    } else {
        echo "meal_plans table does not exist!\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?> 