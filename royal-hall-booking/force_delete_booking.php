<?php
require_once 'app/models/Database.php';

class BookingCleaner
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function forceDelete($id)
    {
        try {
            // Begin transaction
            $this->db->begin_transaction();

            // 1. Delete from bookings table
            $stmt = $this->db->prepare("DELETE FROM bookings WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->db->error);
            }
            
            $stmt->bind_param("i", $id);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            if ($stmt->affected_rows === 0) {
                throw new Exception("No booking found with ID: " . $id);
            }

            // Commit transaction
            $this->db->commit();
            echo "Successfully deleted booking #" . $id;

        } catch (Exception $e) {
            // Rollback on error
            $this->db->rollback();
            echo "Error: " . $e->getMessage();
        }
    }
}

// Execute if ID is provided
if (isset($_GET['id'])) {
    $cleaner = new BookingCleaner();
    $cleaner->forceDelete($_GET['id']);
}
?>