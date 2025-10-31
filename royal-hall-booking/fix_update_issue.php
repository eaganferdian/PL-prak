<?php
// Script untuk memperbaiki masalah update
require_once 'config/config.php';

echo "<h1>🔧 Fix Update Issue</h1>";

try {
    $db = Database::getInstance();
    echo "<p>✅ Database connection successful</p>";
    
    // Check if database exists
    $result = $db->query("SELECT DATABASE() as db_name");
    $dbName = $result->fetch_assoc()['db_name'];
    echo "<p>✅ Connected to database: $dbName</p>";
    
    // Check if facilities table exists
    $result = $db->query("SHOW TABLES LIKE 'facilities'");
    if ($result->num_rows > 0) {
        echo "<p>✅ Facilities table exists</p>";
    } else {
        echo "<p>❌ Facilities table does not exist</p>";
        echo "<p>Creating facilities table...</p>";
        
        $createTable = "
        CREATE TABLE facilities (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            icon VARCHAR(50),
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if ($db->query($createTable)) {
            echo "<p>✅ Facilities table created successfully</p>";
        } else {
            echo "<p>❌ Failed to create facilities table: " . $db->error . "</p>";
        }
    }
    
    // Check table structure
    echo "<h2>📊 Current Table Structure:</h2>";
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
    
    // Check if there's any data
    $result = $db->query("SELECT COUNT(*) as count FROM facilities");
    $count = $result->fetch_assoc()['count'];
    echo "<p>📊 Facilities count: $count</p>";
    
    if ($count == 0) {
        echo "<p>⚠️ No data found. Inserting sample data...</p>";
        
        $sampleData = [
            ['Projector', '📽️', 'HD Projector and screen'],
            ['Sound System', '🔊', 'Professional audio equipment'],
            ['WiFi', '📶', 'High-speed internet'],
            ['Catering', '🍽️', 'Food and beverage service'],
            ['Air Conditioning', '❄️', 'Climate control'],
            ['Whiteboard', '📋', 'Writing board and markers']
        ];
        
        $stmt = $db->prepare("INSERT INTO facilities (name, icon, description) VALUES (?, ?, ?)");
        foreach ($sampleData as $data) {
            $stmt->bind_param("sss", $data[0], $data[1], $data[2]);
            if ($stmt->execute()) {
                echo "<p>✅ Inserted: " . $data[0] . "</p>";
            } else {
                echo "<p>❌ Failed to insert: " . $data[0] . " - " . $stmt->error . "</p>";
            }
        }
    }
    
    // Test update functionality
    echo "<h2>🔄 Testing Update Functionality:</h2>";
    
    $result = $db->query("SELECT * FROM facilities LIMIT 1");
    if ($facility = $result->fetch_assoc()) {
        echo "<p>Testing update on facility: " . htmlspecialchars($facility['name']) . "</p>";
        
        $newName = $facility['name'] . " (Test Update)";
        $stmt = $db->prepare("UPDATE facilities SET name=?, icon=?, description=? WHERE id=?");
        $stmt->bind_param("sssi", $newName, $facility['icon'], $facility['description'], $facility['id']);
        
        if ($stmt->execute()) {
            echo "<p>✅ Update query executed successfully</p>";
            
            // Verify
            $result = $db->query("SELECT name FROM facilities WHERE id = " . $facility['id']);
            $updated = $result->fetch_assoc();
            if ($updated['name'] === $newName) {
                echo "<p>✅ Update verified - data changed correctly</p>";
            } else {
                echo "<p>❌ Update failed - data not changed</p>";
            }
            
            // Restore original
            $stmt = $db->prepare("UPDATE facilities SET name=? WHERE id=?");
            $stmt->bind_param("si", $facility['name'], $facility['id']);
            $stmt->execute();
            echo "<p>🔄 Original name restored</p>";
            
        } else {
            echo "<p>❌ Update failed: " . $stmt->error . "</p>";
        }
    }
    
    // Check database user permissions
    echo "<h2>🔐 Database User Info:</h2>";
    $result = $db->query("SELECT USER() as current_user, DATABASE() as current_db");
    $userInfo = $result->fetch_assoc();
    echo "<p>Current User: " . $userInfo['current_user'] . "</p>";
    echo "<p>Current Database: " . $userInfo['current_db'] . "</p>";
    
    echo "<h2>✅ Fix Complete!</h2>";
    echo "<p>If update is still not working, please check:</p>";
    echo "<ul>";
    echo "<li>Database user has UPDATE permissions</li>";
    echo "<li>No foreign key constraints blocking updates</li>";
    echo "<li>Form is submitting POST data correctly</li>";
    echo "<li>Controller is receiving the data properly</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>

