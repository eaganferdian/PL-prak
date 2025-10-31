<?php
class Room
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Get all rooms dengan pagination dan search
    public function all(string $search = '', int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT r.*, GROUP_CONCAT(f.name) as facility_names 
                FROM rooms r 
                LEFT JOIN room_facilities rf ON r.id = rf.room_id 
                LEFT JOIN facilities f ON rf.facility_id = f.id 
                WHERE r.is_active = TRUE 
                AND (r.name LIKE CONCAT('%', ?, '%') OR r.description LIKE CONCAT('%', ?, '%'))
                GROUP BY r.id 
                ORDER BY r.name 
                LIMIT ?, ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssii", $search, $search, $offset, $perPage);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Count rooms untuk pagination
    public function count(string $search = ''): int
    {
        $sql = "SELECT COUNT(*) as total FROM rooms 
                WHERE is_active = TRUE 
                AND (name LIKE CONCAT('%', ?, '%') OR description LIKE CONCAT('%', ?, '%'))";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $search, $search);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return (int)$result['total'];
    }

    // Get room by ID
    public function find(int $id): ?array
    {
        $sql = "SELECT r.*, GROUP_CONCAT(f.id) as facility_ids, GROUP_CONCAT(f.name) as facility_names 
                FROM rooms r 
                LEFT JOIN room_facilities rf ON r.id = rf.room_id 
                LEFT JOIN facilities f ON rf.facility_id = f.id 
                WHERE r.id = ? 
                GROUP BY r.id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ?: null;
    }

    // Create new room
    public function create(array $data): int
    {
        $this->db->begin_transaction();
        
        try {
            // Insert room
            $stmt = $this->db->prepare("INSERT INTO rooms (name, description, capacity, location, hourly_rate) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssisd", $data['name'], $data['description'], $data['capacity'], $data['location'], $data['hourly_rate']);
            $stmt->execute();
            $roomId = $this->db->insert_id;

            // Insert facilities
            if (!empty($data['facilities'])) {
                $facilityStmt = $this->db->prepare("INSERT INTO room_facilities (room_id, facility_id) VALUES (?, ?)");
                foreach ($data['facilities'] as $facilityId) {
                    $facilityStmt->bind_param("ii", $roomId, $facilityId);
                    $facilityStmt->execute();
                }
            }

            $this->db->commit();
            return $roomId;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    // Update room
    public function update(int $id, array $data): bool
    {
        $this->db->begin_transaction();
        
        try {
            // Update room
            $stmt = $this->db->prepare("UPDATE rooms SET name=?, description=?, capacity=?, location=?, hourly_rate=? WHERE id=?");
            $stmt->bind_param("ssisdi", $data['name'], $data['description'], $data['capacity'], $data['location'], $data['hourly_rate'], $id);
            $stmt->execute();

            // Update facilities
            $this->db->query("DELETE FROM room_facilities WHERE room_id = $id");
            
            if (!empty($data['facilities'])) {
                $facilityStmt = $this->db->prepare("INSERT INTO room_facilities (room_id, facility_id) VALUES (?, ?)");
                foreach ($data['facilities'] as $facilityId) {
                    $facilityStmt->bind_param("ii", $id, $facilityId);
                    $facilityStmt->execute();
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    // Delete room (soft delete)
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE rooms SET is_active = FALSE WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Get all facilities
    public function getFacilities(): array
    {
        $result = $this->db->query("SELECT * FROM facilities ORDER BY name");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Check room availability
    public function checkAvailability(int $roomId, string $date, string $startTime, string $endTime, ?int $excludeBookingId = null): bool
    {
        $sql = "SELECT COUNT(*) as conflict 
                FROM bookings 
                WHERE room_id = ? 
                AND booking_date = ? 
                AND status IN ('pending', 'approved')
                AND ((start_time BETWEEN ? AND ?) OR (end_time BETWEEN ? AND ?) OR (? BETWEEN start_time AND end_time))
                AND id != ?";
        
        $stmt = $this->db->prepare($sql);
        $excludeId = $excludeBookingId ?? 0;
        $stmt->bind_param("issssssi", $roomId, $date, $startTime, $endTime, $startTime, $endTime, $startTime, $excludeId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['conflict'] == 0;
    }

    // Get available rooms for date/time
    public function getAvailableRooms(string $date, string $startTime, string $endTime): array
    {
        $sql = "SELECT r.*, GROUP_CONCAT(f.name) as facility_names 
                FROM rooms r 
                LEFT JOIN room_facilities rf ON r.id = rf.room_id 
                LEFT JOIN facilities f ON rf.facility_id = f.id 
                WHERE r.is_active = TRUE 
                AND r.id NOT IN (
                    SELECT room_id FROM bookings 
                    WHERE booking_date = ? 
                    AND status IN ('pending', 'approved')
                    AND ((start_time BETWEEN ? AND ?) OR (end_time BETWEEN ? AND ?) OR (? BETWEEN start_time AND end_time))
                )
                GROUP BY r.id 
                ORDER BY r.name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssssss", $date, $startTime, $endTime, $startTime, $endTime, $startTime);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}