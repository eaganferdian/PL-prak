<?php
class RoomController extends Controller
{
    private Room $room;

    public function __construct()
    {
        $this->room = new Room();
    }

   // List all rooms
public function index(): void
{
    $this->requireAuth();
    
    $search = trim($_GET['q'] ?? '');
    $capacity = $_GET['capacity'] ?? '';
    $location = $_GET['location'] ?? '';
    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = 9; // Changed to 9 for 3-column grid

    // You'll need to update your Room model to handle these filters
    $rooms = $this->room->all($search, $page, $perPage, $capacity, $location);
    $total = $this->room->count($search, $capacity, $location);
    $pages = ceil($total / $perPage);

    $this->view('rooms/index', [
        'title' => 'Royal Halls & Chambers',
        'rooms' => $rooms,
        'search' => $search,
        'capacity' => $capacity,
        'location' => $location,
        'page' => $page,
        'pages' => $pages,
        'total' => $total
    ]);
}
    // Show room details
    public function show(): void
    {
        $this->requireAuth();
        
        $id = (int)($_GET['id'] ?? 0);
        $room = $this->room->find($id);

        if (!$room) {
            $this->flash('error', 'Royal chamber not found');
            $this->redirect('?c=room&a=index');
            return;
        }

        $this->view('rooms/show', [
            'title' => $room['name'],
            'room' => $room
        ]);
    }

    // Show create form (admin only)
    public function create(): void
    {
        $this->requireAdmin();
        
        $facilities = $this->room->getFacilities();

        $this->view('rooms/create', [
            'title' => 'Add Royal Chamber',
            'facilities' => $facilities
        ]);
    }

    // Store new room (admin only)
    public function store(): void
    {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?c=room&a=create');
            return;
        }

        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'capacity' => (int)($_POST['capacity'] ?? 0),
            'location' => trim($_POST['location'] ?? ''),
            'hourly_rate' => (float)($_POST['hourly_rate'] ?? 0),
            'facilities' => $_POST['facilities'] ?? []
        ];

        // Validation
        $errors = [];
        if (empty($data['name'])) $errors[] = 'Room name is required';
        if (empty($data['description'])) $errors[] = 'Description is required';
        if ($data['capacity'] < 1) $errors[] = 'Capacity must be at least 1';
        if ($data['hourly_rate'] <= 0) $errors[] = 'Hourly rate must be positive';

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->flash('error', $error);
            }
            $this->redirect('?c=room&a=create');
            return;
        }

        try {
            $roomId = $this->room->create($data);
            $this->flash('success', 'Royal chamber added successfully!');
            $this->redirect('?c=room&a=index');
        } catch (Exception $e) {
            $this->flash('error', 'Failed to add chamber: ' . $e->getMessage());
            $this->redirect('?c=room&a=create');
        }
    }

    // Show edit form (admin only)
    public function edit(): void
    {
        $this->requireAdmin();
        
        $id = (int)($_GET['id'] ?? 0);
        $room = $this->room->find($id);
        $facilities = $this->room->getFacilities();

        if (!$room) {
            $this->flash('error', 'Royal chamber not found');
            $this->redirect('?c=room&a=index');
            return;
        }

        $this->view('rooms/edit', [
            'title' => 'Edit Royal Chamber',
            'room' => $room,
            'facilities' => $facilities
        ]);
    }

    // Update room (admin only)
    public function update(): void
    {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?c=room&a=index');
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'capacity' => (int)($_POST['capacity'] ?? 0),
            'location' => trim($_POST['location'] ?? ''),
            'hourly_rate' => (float)($_POST['hourly_rate'] ?? 0),
            'facilities' => $_POST['facilities'] ?? []
        ];

        // Validation
        $errors = [];
        if (empty($data['name'])) $errors[] = 'Room name is required';
        if (empty($data['description'])) $errors[] = 'Description is required';
        if ($data['capacity'] < 1) $errors[] = 'Capacity must be at least 1';
        if ($data['hourly_rate'] <= 0) $errors[] = 'Hourly rate must be positive';

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->flash('error', $error);
            }
            $this->redirect("?c=room&a=edit&id=$id");
            return;
        }

        if ($this->room->update($id, $data)) {
            $this->flash('success', 'Royal chamber updated successfully!');
            $this->redirect('?c=room&a=index');
        } else {
            $this->flash('error', 'Failed to update chamber');
            $this->redirect("?c=room&a=edit&id=$id");
        }
    }

    // Delete room (admin only)
    public function delete(): void
    {
        $this->requireAdmin();
        
        $id = (int)($_POST['id'] ?? 0);

        if ($this->room->delete($id)) {
            $this->flash('success', 'Royal chamber archived successfully!');
        } else {
            $this->flash('error', 'Failed to archive chamber');
        }

        $this->redirect('?c=room&a=index');
    }

    // Check room availability
    public function availability(): void
    {
        $this->requireAuth();
        
        $date = $_GET['date'] ?? date('Y-m-d');
        $startTime = $_GET['start_time'] ?? '09:00';
        $endTime = $_GET['end_time'] ?? '17:00';

        $availableRooms = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['check'])) {
            $date = $_POST['date'] ?? $_GET['date'] ?? date('Y-m-d');
            $startTime = $_POST['start_time'] ?? $_GET['start_time'] ?? '09:00';
            $endTime = $_POST['end_time'] ?? $_GET['end_time'] ?? '17:00';

            $availableRooms = $this->room->getAvailableRooms($date, $startTime, $endTime);
        }

        $this->view('rooms/availability', [
            'title' => 'Check Chamber Availability',
            'availableRooms' => $availableRooms,
            'date' => $date,
            'startTime' => $startTime,
            'endTime' => $endTime
        ]);
    }
}