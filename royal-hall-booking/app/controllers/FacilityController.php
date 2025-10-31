<?php
class FacilityController extends Controller
{
    private Facility $facility;

    public function __construct()
    {
        $this->facility = new Facility();
    }

    // List facilities (admin only)
    public function index(): void
    {
        $this->requireAdmin();
        
        $search = trim($_GET['q'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;

        $facilities = $this->facility->all($search, $page, $perPage);
        $total = $this->facility->count($search);
        $pages = ceil($total / $perPage);

        $this->view('facilities/index', [
            'title' => 'Royal Facilities',
            'facilities' => $facilities,
            'search' => $search,
            'page' => $page,
            'pages' => $pages,
            'total' => $total
        ]);
    }

    // Show create form
    public function create(): void
    {
        $this->requireAdmin();
        
        $this->view('facilities/create', [
            'title' => 'Add Royal Facility'
        ]);
    }

    // Store new facility
    public function store(): void
    {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?c=facility&a=create');
            return;
        }

        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'icon' => trim($_POST['icon'] ?? ''),
            'description' => trim($_POST['description'] ?? '')
        ];

        // Validation
        $errors = [];
        if (empty($data['name'])) $errors[] = 'Facility name is required';
        if (empty($data['icon'])) $errors[] = 'Icon is required';

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->flash('error', $error);
            }
            $this->redirect('?c=facility&a=create');
            return;
        }

        try {
            $facilityId = $this->facility->create($data);
            $this->flash('success', 'Royal facility added successfully!');
            $this->redirect('?c=facility&a=index');
        } catch (Exception $e) {
            $this->flash('error', 'Failed to add facility: ' . $e->getMessage());
            $this->redirect('?c=facility&a=create');
        }
    }

    // Show edit form
    public function edit(): void
    {
        $this->requireAdmin();
        
        $id = (int)($_GET['id'] ?? 0);
        $facility = $this->facility->find($id);

        if (!$facility) {
            $this->flash('error', 'Facility not found');
            $this->redirect('?c=facility&a=index');
            return;
        }

        $this->view('facilities/edit', [
            'title' => 'Edit Royal Facility',
            'facility' => $facility
        ]);
    }

    // Update facility
    public function update(): void
    {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('?c=facility&a=index');
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'icon' => trim($_POST['icon'] ?? ''),
            'description' => trim($_POST['description'] ?? '')
        ];

        // Validation
        $errors = [];
        if (empty($data['name'])) $errors[] = 'Facility name is required';
        if (empty($data['icon'])) $errors[] = 'Icon is required';

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->flash('error', $error);
            }
            $this->redirect("?c=facility&a=edit&id=$id");
            return;
        }

        if ($this->facility->update($id, $data)) {
            $this->flash('success', 'Royal facility updated successfully!');
            $this->redirect('?c=facility&a=index');
        } else {
            $this->flash('error', 'Failed to update facility');
            $this->redirect("?c=facility&a=edit&id=$id");
        }
    }

    // Delete facility
    public function delete(): void
    {
        $this->requireAdmin();
        
        $id = (int)($_POST['id'] ?? 0);

        try {
            if ($this->facility->delete($id)) {
                $this->flash('success', 'Royal facility deleted successfully!');
            } else {
                $this->flash('error', 'Failed to delete facility');
            }
        } catch (Exception $e) {
            $this->flash('error', $e->getMessage());
        }

        $this->redirect('?c=facility&a=index');
    }
}