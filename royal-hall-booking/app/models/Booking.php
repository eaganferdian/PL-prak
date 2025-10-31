<?php
class Booking
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Get all bookings dengan filter
    public function all(array $filters = [], int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $where = [];
        $params = [];
        $types = "";

        // Always exclude deleted items unless explicitly requested
        if (!isset($filters['include_deleted']) || !$filters['include_deleted']) {
            $where[] = "(b.is_deleted = FALSE OR b.is_deleted IS NULL)";
        }

        // Search query (search in purpose and room name)
        if (!empty($filters['q'])) {
            $where[] = "(b.purpose LIKE ? OR r.name LIKE ?)";
            $q = '%' . $filters['q'] . '%';
            $params[] = $q;
            $params[] = $q;
            $types .= "ss";
        }

        // Filter by user (untuk user biasa)
        if (isset($filters['user_id'])) {
            $where[] = "b.user_id = ?";
            $params[] = $filters['user_id'];
            $types .= "i";
        }

        // Filter by status
        if (isset($filters['status'])) {
            $where[] = "b.status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }

        // Filter by room
        if (isset($filters['room_id'])) {
            $where[] = "b.room_id = ?";
            $params[] = $filters['room_id'];
            $types .= "i";
        }

        $whereClause = $where ? "WHERE " . implode(" AND ", $where) : "";

        // Build ORDER BY depending on sort filter
        $orderBy = "b.booking_date DESC, b.start_time DESC";
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'date_asc':
                    $orderBy = "b.booking_date ASC, b.start_time ASC";
                    break;
                case 'date_desc':
                    $orderBy = "b.booking_date DESC, b.start_time DESC";
                    break;
                case 'cost_asc':
                    $orderBy = "b.total_cost ASC";
                    break;
                case 'cost_desc':
                    $orderBy = "b.total_cost DESC";
                    break;
            }
        }

        $sql = "SELECT b.*, 
                       u.full_name as user_name, 
                       r.name as room_name, 
                       r.hourly_rate,
                       COALESCE(b.status, 'pending') as status
                FROM bookings b 
                JOIN users u ON b.user_id = u.id 
                JOIN rooms r ON b.room_id = r.id 
                $whereClause 
                ORDER BY $orderBy 
                LIMIT ?, ?";
        
        $params[] = $offset;
        $params[] = $perPage;
        $types .= "ii";

        $stmt = $this->db->prepare($sql);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Count bookings untuk pagination
    public function count(array $filters = []): int
    {
        $where = [];
        $params = [];
        $types = "";

        // Exclude deleted by default
        if (!isset($filters['include_deleted']) || !$filters['include_deleted']) {
            $where[] = "(is_deleted = FALSE OR is_deleted IS NULL)";
        }

        if (isset($filters['user_id'])) {
            $where[] = "user_id = ?";
            $params[] = $filters['user_id'];
            $types .= "i";
        }

        if (isset($filters['status'])) {
            $where[] = "status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }

        if (!empty($filters['q'])) {
            $where[] = "(purpose LIKE ? OR EXISTS (SELECT 1 FROM rooms rr WHERE rr.id = bookings.room_id AND rr.name LIKE ?))";
            $q = '%' . $filters['q'] . '%';
            $params[] = $q;
            $params[] = $q;
            $types .= "ss";
        }

        if (isset($filters['room_id'])) {
            $where[] = "room_id = ?";
            $params[] = $filters['room_id'];
            $types .= "i";
        }

        $whereClause = $where ? "WHERE " . implode(" AND ", $where) : "";

        $sql = "SELECT COUNT(*) as total FROM bookings $whereClause";
        $stmt = $this->db->prepare($sql);

        if ($params) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return (int)$result['total'];
    }

    // Get booking by ID
    public function find(int $id, bool $includeDeleted = false): ?array
    {
        try {
            // First check if the columns exist and create them if they don't
            $checkColumns = $this->db->query("SHOW COLUMNS FROM bookings LIKE 'is_deleted'");
            if ($checkColumns->num_rows === 0) {
                $this->db->query("ALTER TABLE bookings ADD COLUMN is_deleted BOOLEAN DEFAULT FALSE");
                $this->db->query("ALTER TABLE bookings ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL");
            }

            $sql = "SELECT 
                    b.*, 
                    u.full_name as user_name, 
                    u.email as user_email,
                    r.name as room_name, 
                    r.hourly_rate, 
                    r.capacity, 
                    r.location,
                    CASE 
                        WHEN b.status IS NULL THEN 'pending'
                        ELSE b.status 
                    END as status,
                    COALESCE(b.is_deleted, FALSE) as is_deleted
                FROM bookings b 
                JOIN users u ON b.user_id = u.id 
                JOIN rooms r ON b.room_id = r.id 
                WHERE b.id = ?";
                
            if (!$includeDeleted) {
                $sql .= " AND (b.is_deleted = FALSE OR b.is_deleted IS NULL)";
            }
            
            $stmt = $this->db->prepare($sql);
            if ($stmt === false) {
                error_log("Failed to prepare find statement: " . $this->db->error);
                return null;
            }

            $stmt->bind_param("i", $id);
            if (!$stmt->execute()) {
                error_log("Failed to execute find query: " . $stmt->error);
                return null;
            }

            $result = $stmt->get_result()->fetch_assoc();
            
            // Ensure status is set
            if ($result && empty($result['status'])) {
                $result['status'] = 'pending';
            }

            return $result ?: null;

        } catch (Exception $e) {
            error_log("Error in find method: " . $e->getMessage());
            return null;
        }
    }

    // Create new booking
    // CREATE new booking
public function create(array $data): int
{
    // Calculate total cost - FIX TIME FORMAT ISSUE
    try {
        $start = DateTime::createFromFormat('H:i', $data['start_time']);
        $end = DateTime::createFromFormat('H:i', $data['end_time']);
        
        // Validate time parsing
        if (!$start || !$end) {
            throw new Exception('Invalid time format');
        }
        
        $hours = ($end->getTimestamp() - $start->getTimestamp()) / 3600;
        
        // Ensure minimum 1 hour booking
        if ($hours < 1) {
            $hours = 1;
        }
        
        $totalCost = $hours * $data['hourly_rate'];
        
    } catch (Exception $e) {
        // Fallback calculation if time parsing fails
        $hours = 1; // Default 1 hour
        $totalCost = $hours * $data['hourly_rate'];
    }

    $stmt = $this->db->prepare("INSERT INTO bookings (user_id, room_id, purpose, booking_date, start_time, end_time, total_cost) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissssd", $data['user_id'], $data['room_id'], $data['purpose'], $data['booking_date'], $data['start_time'], $data['end_time'], $totalCost);
    $stmt->execute();
    return $this->db->insert_id;
}
    // Update booking status (admin)
    public function updateStatus(int $id, string $status, string $adminNotes = ''): bool
    {
        $stmt = $this->db->prepare("UPDATE bookings SET status = ?, admin_notes = ? WHERE id = ?");
        $stmt->bind_param("ssi", $status, $adminNotes, $id);
        return $stmt->execute();
    }

    // Update booking (user)
    public function update(int $id, array $data): bool
    {
        // Recalculate total cost
        try {
            // Try H:i format first (hours:minutes)
            $start = DateTime::createFromFormat('H:i', $data['start_time']);
            $end = DateTime::createFromFormat('H:i', $data['end_time']);
            
            // If that fails, try H:i:s format (hours:minutes:seconds)
            if (!$start || !$end) {
                $start = DateTime::createFromFormat('H:i:s', $data['start_time']);
                $end = DateTime::createFromFormat('H:i:s', $data['end_time']);
            }

            // Validate time parsing
            if (!$start || !$end) {
                throw new Exception('Invalid time format');
            }

            $hours = ($end->getTimestamp() - $start->getTimestamp()) / 3600;
            
            // Ensure minimum 1 hour booking
            if ($hours < 1) {
                $hours = 1;
            }
            
            $totalCost = $hours * $data['hourly_rate'];
            
        } catch (Exception $e) {
            // Fallback calculation if time parsing fails
            $hours = 1; // Default to 1 hour
            $totalCost = $hours * $data['hourly_rate'];
        }

        $stmt = $this->db->prepare("UPDATE bookings SET purpose=?, booking_date=?, start_time=?, end_time=?, total_cost=? WHERE id=?");
        $stmt->bind_param("ssssdi", $data['purpose'], $data['booking_date'], $data['start_time'], $data['end_time'], $totalCost, $id);
        return $stmt->execute();
    }

    // Soft delete booking (move to trash)
    public function delete(int $id): bool
    {
        try {
            // First, check if the columns exist
            $checkColumns = $this->db->query("SHOW COLUMNS FROM bookings LIKE 'is_deleted'");
            if ($checkColumns->num_rows === 0) {
                // Columns don't exist, create them
                $this->db->query("ALTER TABLE bookings ADD COLUMN is_deleted BOOLEAN DEFAULT FALSE");
                $this->db->query("ALTER TABLE bookings ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL");
            }

            // Now perform the soft delete
            $stmt = $this->db->prepare("UPDATE bookings SET is_deleted = TRUE, deleted_at = CURRENT_TIMESTAMP, status = 'cancelled' WHERE id = ?");
            if ($stmt === false) {
                error_log("Failed to prepare soft delete statement: " . $this->db->error);
                return false;
            }
            
            if (!$stmt->bind_param("i", $id)) {
                error_log("Failed to bind parameter: " . $stmt->error);
                return false;
            }
            
            $result = $stmt->execute();
            if (!$result) {
                error_log("Failed to execute soft delete: " . $stmt->error);
            }
            return $result;
            
        } catch (Exception $e) {
            error_log("Exception in delete method: " . $e->getMessage());
            return false;
        }
    }

    // Permanently delete booking
    public function permanentDelete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM bookings WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Restore booking from trash
    public function restore(int $id): bool
    {
        try {
            // 1. Begin transaction
            $this->db->begin_transaction();

            // 2. Get current booking data
            $stmt = $this->db->prepare("SELECT * FROM bookings WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $booking = $stmt->get_result()->fetch_assoc();

            if (!$booking) {
                throw new Exception("Booking not found");
            }

            // 3. Reset booking to pending state
            $updateSql = "UPDATE bookings 
                         SET is_deleted = FALSE,
                             deleted_at = NULL,
                             status = 'pending',
                             admin_notes = NULL
                         WHERE id = ?";
            
            $updateStmt = $this->db->prepare($updateSql);
            if ($updateStmt === false) {
                throw new Exception("Failed to prepare restore statement: " . $this->db->error);
            }
            
            $updateStmt->bind_param("i", $id);
            if (!$updateStmt->execute()) {
                throw new Exception("Failed to execute restore: " . $updateStmt->error);
            }

            // 4. Verify the update
            $verifyStmt = $this->db->prepare("SELECT id, status, is_deleted FROM bookings WHERE id = ?");
            $verifyStmt->bind_param("i", $id);
            $verifyStmt->execute();
            $updated = $verifyStmt->get_result()->fetch_assoc();

            if ($updated['is_deleted'] || $updated['status'] !== 'pending') {
                throw new Exception("Failed to verify restore operation");
            }

            // 5. Commit transaction
            $this->db->commit();
            return true;

        } catch (Exception $e) {
            // Rollback on error
            $this->db->rollback();
            error_log("Restore failed: " . $e->getMessage());
            return false;
        }
    }

    // Get deleted bookings (trash)
    public function getTrash(array $filters = [], int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $where = ["b.is_deleted = TRUE"];
        $params = [];
        $types = "";

        // Filter by user (untuk user biasa)
        if (isset($filters['user_id'])) {
            $where[] = "b.user_id = ?";
            $params[] = $filters['user_id'];
            $types .= "i";
        }

        $whereClause = implode(" AND ", $where);

        $sql = "SELECT b.*, u.full_name as user_name, r.name as room_name, r.hourly_rate,
                DATEDIFF(DATE_ADD(b.deleted_at, INTERVAL 7 DAY), CURRENT_TIMESTAMP) as days_until_deletion 
                FROM bookings b 
                JOIN users u ON b.user_id = u.id 
                JOIN rooms r ON b.room_id = r.id 
                WHERE $whereClause 
                ORDER BY b.deleted_at DESC 
                LIMIT ?, ?";
        
        $params[] = $offset;
        $params[] = $perPage;
        $types .= "ii";

        $stmt = $this->db->prepare($sql);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Clean up old trash (auto-delete after 7 days)
    public function cleanupTrash(): bool
    {
        $stmt = $this->db->prepare("DELETE FROM bookings WHERE is_deleted = TRUE AND deleted_at < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 7 DAY)");
        return $stmt->execute();
    }

    // Get bookings for calendar
    public function getCalendarEvents(string $month, string $year): array
    {
        $sql = "SELECT b.booking_date, b.start_time, b.end_time, r.name as room_name, 
                       u.full_name as user_name, b.status 
                FROM bookings b 
                JOIN rooms r ON b.room_id = r.id 
                JOIN users u ON b.user_id = u.id 
                WHERE MONTH(b.booking_date) = ? AND YEAR(b.booking_date) = ? 
                AND b.status IN ('pending', 'approved')
                ORDER BY b.booking_date, b.start_time";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $month, $year);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}