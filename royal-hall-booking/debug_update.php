<?php
// Debug script untuk menguji update functionality
require_once 'config/config.php';

echo "<h1>🔧 Debug Update Functionality</h1>";

try {
    $db = Database::getInstance();
    echo "<p>✅ Database connection successful</p>";
    
    // Test 1: Check if facilities table exists and has data
    $result = $db->query("SELECT COUNT(*) as count FROM facilities");
    $count = $result->fetch_assoc()['count'];
    echo "<p>✅ Facilities table has $count records</p>";
    
    // Test 2: Show current facilities
    echo "<h2>📋 Current Facilities:</h2>";
    $result = $db->query("SELECT * FROM facilities ORDER BY id");
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Icon</th><th>Description</th><th>Created</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['icon']) . "</td>";
        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test 3: Test update functionality
    echo "<h2>🔄 Testing Update Functionality:</h2>";
    
    // Get first facility for testing
    $result = $db->query("SELECT * FROM facilities LIMIT 1");
    if ($facility = $result->fetch_assoc()) {
        $originalName = $facility['name'];
        $testName = $originalName . " (Updated)";
        
        echo "<p>Testing update on facility ID: " . $facility['id'] . "</p>";
        echo "<p>Original name: " . htmlspecialchars($originalName) . "</p>";
        echo "<p>New name: " . htmlspecialchars($testName) . "</p>";
        
        // Test update
        $stmt = $db->prepare("UPDATE facilities SET name=?, icon=?, description=? WHERE id=?");
        $stmt->bind_param("sssi", $testName, $facility['icon'], $facility['description'], $facility['id']);
        
        if ($stmt->execute()) {
            echo "<p>✅ Update query executed successfully</p>";
            
            // Verify update
            $result = $db->query("SELECT name FROM facilities WHERE id = " . $facility['id']);
            $updated = $result->fetch_assoc();
            if ($updated['name'] === $testName) {
                echo "<p>✅ Update verified - data changed correctly</p>";
            } else {
                echo "<p>❌ Update failed - data not changed</p>";
            }
            
            // Restore original name
            $stmt = $db->prepare("UPDATE facilities SET name=? WHERE id=?");
            $stmt->bind_param("si", $originalName, $facility['id']);
            $stmt->execute();
            echo "<p>🔄 Original name restored</p>";
            
        } else {
            echo "<p>❌ Update query failed: " . $stmt->error . "</p>";
        }
    }
    
    // Test 4: Check database permissions
    echo "<h2>🔐 Database Permissions Test:</h2>";
    $result = $db->query("SHOW GRANTS FOR CURRENT_USER()");
    echo "<p>Current user permissions:</p>";
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>" . htmlspecialchars($row['Grants for ' . DB_USER . '@localhost']) . "</li>";
    }
    echo "</ul>";
    
    // Test 5: Check table structure
    echo "<h2>📊 Table Structure:</h2>";
    $result = $db->query("DESCRIBE facilities");
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>

