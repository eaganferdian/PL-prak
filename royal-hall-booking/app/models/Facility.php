<?php
class Facility
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Get all facilities
    public function all(string $search = '', int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM facilities 
                WHERE name LIKE CONCAT('%', ?, '%') 
                ORDER BY name 
                LIMIT ?, ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sii", $search, $offset, $perPage);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Count facilities untuk pagination
    public function count(string $search = ''): int
    {
        $sql = "SELECT COUNT(*) as total FROM facilities 
                WHERE name LIKE CONCAT('%', ?, '%')";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $search);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return (int)$result['total'];
    }

    // Get facility by ID
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM facilities WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ?: null;
    }

    // Create new facility
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO facilities (name, icon, description) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $data['name'], $data['icon'], $data['description']);
        $stmt->execute();
        return $this->db->insert_id;
    }

    // Update facility
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("UPDATE facilities SET name=?, icon=?, description=? WHERE id=?");
        $stmt->bind_param("sssi", $data['name'], $data['icon'], $data['description'], $id);
        return $stmt->execute();
    }

    // Delete facility
    public function delete(int $id): bool
    {
        // Check if facility is used in any room
        $checkStmt = $this->db->prepare("SELECT COUNT(*) as usage_count FROM room_facilities WHERE facility_id = ?");
        $checkStmt->bind_param("i", $id);
        $checkStmt->execute();
        $result = $checkStmt->get_result()->fetch_assoc();

        if ($result['usage_count'] > 0) {
            throw new Exception("Cannot delete facility that is currently in use by rooms");
        }

        $stmt = $this->db->prepare("DELETE FROM facilities WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}