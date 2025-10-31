<?php
class DashboardController extends Controller
{
    private Booking $booking;
    private Room $room;

    public function __construct()
    {
        $this->booking = new Booking();
        $this->room = new Room();
    }

    // Main dashboard
    public function index(): void
    {
        $this->requireAuth();
        
        $userId = $_SESSION['user']['id'];
        $isAdmin = $_SESSION['user']['role'] === 'admin';

        if ($isAdmin) {
            // Admin dashboard stats
            $totalRooms = $this->room->count();
            $totalBookings = $this->booking->count();
            $pendingBookings = $this->booking->count(['status' => 'pending']);
            $todayBookings = $this->booking->count(['status' => 'approved']); // Simplified

            $recentBookings = $this->booking->all([], 1, 5);
        } else {
            // User dashboard stats
            $userFilters = ['user_id' => $userId];
            $totalBookings = $this->booking->count($userFilters);
            $pendingBookings = $this->booking->count(array_merge($userFilters, ['status' => 'pending']));
            $approvedBookings = $this->booking->count(array_merge($userFilters, ['status' => 'approved']));

            $recentBookings = $this->booking->all($userFilters, 1, 5);
        }

        $this->view('dashboard/index', [
            'title' => 'Royal Dashboard',
            'isAdmin' => $isAdmin,
            'stats' => [
                'totalRooms' => $totalRooms ?? 0,
                'totalBookings' => $totalBookings ?? 0,
                'pendingBookings' => $pendingBookings ?? 0,
                'todayBookings' => $todayBookings ?? 0,
                'approvedBookings' => $approvedBookings ?? 0
            ],
            'recentBookings' => $recentBookings
        ]);
    }
}