<?php
class BookingController extends Controller
{
    private Booking $booking;
    private Room $room;

    public function __construct()
    {
        $this->booking = new Booking();
        $this->room = new Room();
    }

    // List bookings (admin sees all, user sees their own)
    public function index(): void
    {
        $this->requireAuth();
        
        $filters = [];
        $isAdmin = $_SESSION['user']['role'] === 'admin';
        
        if (!$isAdmin) {
            $filters['user_id'] = $_SESSION['user']['id'];
        }
        if (isset($_GET['status']) && in_array($_GET['status'], ['pending', 'approved', 'rejected', 'completed', 'cancelled'])) {
            $filters['status'] = $_GET['status'];
        }

        $search = trim($_GET['q'] ?? '');
        if ($search !== '') {
            $filters['q'] = $search;
        }

        // Sorting
        $sort = trim($_GET['sort'] ?? '');
        if ($sort !== '') {
            $filters['sort'] = $sort;
        }
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;

        $bookings = $this->booking->all($filters, $page, $perPage);
        $total = $this->booking->count($filters);
        $pages = ceil($total / $perPage);

        $this->view('bookings/index', [
            'title' => 'Royal Bookings',
            'bookings' => $bookings,
            'isAdmin' => $isAdmin,
            'search' => $search,
            'page' => $page,
            'pages' => $pages,
            'total' => $total,
            'statusFilter' => $filters['status'] ?? ''
        ]);
    }

    // Show booking details
    public function show(): void
    {
        $this->requireAuth();
        
        $id = (int)($_GET['id'] ?? 0);
        $booking = $this->booking->find($id);

        if (!$booking) {
            $this->flash('error', 'Booking not found');
            $this->redirect('?c=booking&a=index');
            return;
        }

        // Check if user owns this booking or is admin
        $isOwner = $booking['user_id'] == $_SESSION['user']['id'];
        $isAdmin = $_SESSION['user']['role'] === 'admin';
        
        if (!$isOwner && !$isAdmin) {
            $this->flash('error', 'Access denied');
            $this->redirect('?c=booking&a=index');
            return;
        }

        $this->view('bookings/show', [
            'title' => 'Booking Details',
            'booking' => $booking,
            'isAdmin' => $isAdmin
        ]);
    }

    // Show create booking form
    public function create(): void
    {
        $this->requireAuth();
        
        $roomId = (int)($_GET['room_id'] ?? 0);
        $date = $_GET['date'] ?? date('Y-m-d');
        $startTime = $_GET['start_time'] ?? '09:00';
        $endTime = $_GET['end_time'] ?? '17:00';

        $room = null;
        if ($roomId > 0) {
            $room = $this->room->find($roomId);
        }

        $rooms = $this->room->all('', 1, 100); // Get all rooms for dropdown

        $this->view('bookings/create', [
            'title' => 'Book Royal Chamber',
            'room' => $room,
            'rooms' => $rooms,
            'date' => $date,
            'startTime' => $startTime,
            'endTime' => $endTime
        ]);
    }

    // Store new booking
    public function store(): void
    {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?c=booking&a=create');
            return;
        }

        $roomId = (int)($_POST['room_id'] ?? 0);
        $room = $this->room->find($roomId);

        if (!$room) {
            $this->flash('error', 'Invalid royal chamber selected');
            $this->redirect('?c=booking&a=create');
            return;
        }

        $data = [
            'user_id' => $_SESSION['user']['id'],
            'room_id' => $roomId,
            'purpose' => trim($_POST['purpose'] ?? ''),
            'booking_date' => $_POST['booking_date'] ?? '',
            'start_time' => $_POST['start_time'] ?? '',
            'end_time' => $_POST['end_time'] ?? '',
            'hourly_rate' => $room['hourly_rate']
        ];

        // Validation
        $errors = [];
        if (empty($data['purpose'])) $errors[] = 'Booking purpose is required';
        if (empty($data['booking_date'])) $errors[] = 'Booking date is required';
        if (empty($data['start_time'])) $errors[] = 'Start time is required';
        if (empty($data['end_time'])) $errors[] = 'End time is required';

        // Date validation
        $bookingDate = DateTime::createFromFormat('Y-m-d', $data['booking_date']);
        $today = new DateTime();
        if (!$bookingDate || $bookingDate < $today) {
            $errors[] = 'Booking date must be today or in the future';
        }

        // Time validation
        $start = DateTime::createFromFormat('H:i', $data['start_time']);
        $end = DateTime::createFromFormat('H:i', $data['end_time']);
        if (!$start || !$end || $start >= $end) {
            $errors[] = 'Invalid time range. End time must be after start time';
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->flash('error', $error);
            }
            $this->redirect('?c=booking&a=create');
            return;
        }

        // Check availability
        if (!$this->room->checkAvailability($roomId, $data['booking_date'], $data['start_time'], $data['end_time'])) {
            $this->flash('error', 'Royal chamber is not available for the selected date and time');
            $this->redirect('?c=booking&a=create');
            return;
        }

        try {
            $bookingId = $this->booking->create($data);
            $this->flash('success', 'Royal chamber booked successfully! Awaiting approval from the Royal Administrator.');
            $this->redirect('?c=booking&a=show&id=' . $bookingId);
        } catch (Exception $e) {
            $this->flash('error', 'Failed to book chamber: ' . $e->getMessage());
            $this->redirect('?c=booking&a=create');
        }
    }

    // Show edit booking form
    public function edit(): void
    {
        $this->requireAuth();
        
        $id = (int)($_GET['id'] ?? 0);
        $booking = $this->booking->find($id);

        if (!$booking) {
            $this->flash('error', 'Booking not found');
            $this->redirect('?c=booking&a=index');
            return;
        }

        // Only owner can edit pending bookings
        $isOwner = $booking['user_id'] == $_SESSION['user']['id'];
        $canEdit = $isOwner && $booking['status'] === 'pending';
        
        if (!$canEdit) {
            $this->flash('error', 'You can only edit pending bookings');
            $this->redirect('?c=booking&a=index');
            return;
        }

        $rooms = $this->room->all('', 1, 100);

        $this->view('bookings/edit', [
            'title' => 'Edit Booking',
            'booking' => $booking,
            'rooms' => $rooms
        ]);
    }

    // Update booking
    public function update(): void
    {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?c=booking&a=index');
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $booking = $this->booking->find($id);

        if (!$booking) {
            $this->flash('error', 'Booking not found');
            $this->redirect('?c=booking&a=index');
            return;
        }

        // Only owner can edit pending bookings
        $isOwner = $booking['user_id'] == $_SESSION['user']['id'];
        $canEdit = $isOwner && $booking['status'] === 'pending';
        
        if (!$canEdit) {
            $this->flash('error', 'You can only edit pending bookings');
            $this->redirect('?c=booking&a=index');
            return;
        }

        $roomId = (int)($_POST['room_id'] ?? 0);
        $room = $this->room->find($roomId);

        $data = [
            'purpose' => trim($_POST['purpose'] ?? ''),
            'booking_date' => $_POST['booking_date'] ?? '',
            'start_time' => $_POST['start_time'] ?? '',
            'end_time' => $_POST['end_time'] ?? '',
            'hourly_rate' => $room['hourly_rate']
        ];

        // Validation (same as store)
        $errors = [];
        if (empty($data['purpose'])) $errors[] = 'Booking purpose is required';
        // ... (validation logic sama seperti store)

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->flash('error', $error);
            }
            $this->redirect("?c=booking&a=edit&id=$id");
            return;
        }

        // Check availability (exclude current booking)
        if (!$this->room->checkAvailability($roomId, $data['booking_date'], $data['start_time'], $data['end_time'], $id)) {
            $this->flash('error', 'Royal chamber is not available for the selected date and time');
            $this->redirect("?c=booking&a=edit&id=$id");
            return;
        }

        if ($this->booking->update($id, $data)) {
            $this->flash('success', 'Booking updated successfully!');
            $this->redirect('?c=booking&a=show&id=' . $id);
        } else {
            $this->flash('error', 'Failed to update booking');
            $this->redirect("?c=booking&a=edit&id=$id");
        }
    }

    // Move booking to trash
    public function delete(): void
    {
        $this->requireAuth();
        
        $id = (int)($_POST['id'] ?? 0);
        $booking = $this->booking->find($id);

        if (!$booking) {
            $this->flash('error', 'Booking not found');
            $this->redirect('?c=booking&a=index');
            return;
        }

        // Only owner can delete pending bookings, admin can delete any
        $isOwner = $booking['user_id'] == $_SESSION['user']['id'];
        $isAdmin = $_SESSION['user']['role'] === 'admin';
        $canDelete = $isAdmin || ($isOwner && $booking['status'] === 'pending');
        
        if (!$canDelete) {
            $this->flash('error', 'You cannot delete this booking');
            $this->redirect('?c=booking&a=index');
            return;
        }

        if ($this->booking->delete($id)) {
            $this->flash('success', 'Booking moved to Archives.');
            $this->redirect('?c=booking&a=trash');
        } else {
            $this->flash('error', 'Failed to move booking to Archives');
            $this->redirect('?c=booking&a=index');
        }
    }

    // Show trash bin
    public function trash(): void
    {
        $this->requireAuth();
        
        $filters = [];
        if ($_SESSION['user']['role'] !== 'admin') {
            $filters['user_id'] = $_SESSION['user']['id'];
        }

        // Clean up old trash (auto-delete after 7 days)
        $this->booking->cleanupTrash();

        $trashedBookings = $this->booking->getTrash($filters);

        $this->view('bookings/trash', [
            'title' => 'Royal Booking Archives',
            'trashedBookings' => $trashedBookings
        ]);
    }

    // Restore booking from trash
    public function restore(): void
    {
        $this->requireAuth();
        
        $id = (int)($_POST['id'] ?? 0);
        $booking = $this->booking->find($id, true); // Include deleted items in search

        if (!$booking) {
            $this->flash('error', 'Booking not found');
            $this->redirect('?c=booking&a=trash');
            return;
        }

        // Only owner or admin can restore
        $isOwner = $booking['user_id'] == $_SESSION['user']['id'];
        $isAdmin = $_SESSION['user']['role'] === 'admin';
        
        if (!$isOwner && !$isAdmin) {
            $this->flash('error', 'You cannot restore this booking');
            $this->redirect('?c=booking&a=trash');
            return;
        }

        if ($this->booking->restore($id)) {
            // Force refresh the booking data
            $refreshedBooking = $this->booking->find($id);
            if ($refreshedBooking && $refreshedBooking['status'] === 'pending') {
                $this->flash('success', 'Booking restored successfully and is now pending approval.');
            } else {
                $this->flash('success', 'Booking restored successfully!');
            }
            $this->redirect('?c=booking&a=index');
        } else {
            $this->flash('error', 'Failed to restore booking');
            $this->redirect('?c=booking&a=trash');
        }
    }

    // Permanently delete booking
    public function permanentDelete(): void
    {
        $this->requireAuth();
        
        $id = (int)($_POST['id'] ?? 0);
        $booking = $this->booking->find($id, true); // Include deleted items in search

        if (!$booking) {
            $this->flash('error', 'Booking not found');
            $this->redirect('?c=booking&a=trash');
            return;
        }

        // Only owner or admin can permanently delete
        $isOwner = $booking['user_id'] == $_SESSION['user']['id'];
        $isAdmin = $_SESSION['user']['role'] === 'admin';
        
        if (!$isOwner && !$isAdmin) {
            $this->flash('error', 'You cannot delete this booking');
            $this->redirect('?c=booking&a=trash');
            return;
        }

        if ($this->booking->permanentDelete($id)) {
            $this->flash('success', 'Booking permanently deleted.');
        } else {
            $this->flash('error', 'Failed to delete booking');
        }

        $this->redirect('?c=booking&a=trash');
    }

    // Update booking status (admin only)
    public function updateStatus(): void
    {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?c=booking&a=index');
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $adminNotes = trim($_POST['admin_notes'] ?? '');

        if (!in_array($status, ['approved', 'rejected', 'completed'])) {
            $this->flash('error', 'Invalid status');
            $this->redirect('?c=booking&a=index');
            return;
        }

        if ($this->booking->updateStatus($id, $status, $adminNotes)) {
            $statusText = ucfirst($status);
            $this->flash('success', "Booking $statusText successfully!");
        } else {
            $this->flash('error', 'Failed to update booking status');
        }

        $this->redirect('?c=booking&a=index');
    }

    // Calendar view
    public function calendar(): void
    {
        $this->requireAuth();
        
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');

        $events = $this->booking->getCalendarEvents($month, $year);

        // Generate calendar data
        $firstDay = mktime(0, 0, 0, $month, 1, $year);
        $daysInMonth = date('t', $firstDay);
        $firstDayOfWeek = date('w', $firstDay);

        $this->view('bookings/calendar', [
            'title' => 'Royal Booking Calendar',
            'events' => $events,
            'month' => $month,
            'year' => $year,
            'daysInMonth' => $daysInMonth,
            'firstDayOfWeek' => $firstDayOfWeek
        ]);
    }
}