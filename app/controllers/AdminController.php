<?php
require_once 'app/models/User.php';
require_once 'app/models/Fixture.php';
require_once 'app/models/Assignment.php';
require_once 'app/helpers/role_guard.php';
require_once 'core/Database.php';

class AdminController extends Controller {
    private $db;
    public function __construct() {
        $this->db = new Database();
        requireRole('admin');
    }

    public function index() {
        
        $fixtures = (new Fixture())->getAll();
        $users = (new User())->getAll();

        $sql = "SELECT da.*, u.name 
                FROM default_assignments da
                JOIN admins u ON u.id = da.user_id
                ORDER BY event_type, gender, weight_category, age_group";
        
        
        $this->db->query($sql);
        $default_assignments = $this->db->resultSet();

        return $this->view('admin/dashboard', [
            'fixtures' => $fixtures,
            'users' => $users,
            'default_assignments' => $default_assignments
        ]);


    }


    public function assignRole() {
        $fixture_id = $_POST['fixture_id'];
        $user_id = $_POST['user_id'];
        $role = $_POST['role'];
        $corner = ($role === 'scorer') ? $_POST['corner'] : null;

        $assignment = new Assignment();
        $assignment->assignToFixture($fixture_id, $user_id, $role, $corner);

        header("Location: /admin/dashboard");
        exit;
    }

   public function assignDefault() {
        $event_type = $_POST['event_type'] ?? null;
        $gender = $_POST['gender'] ?? null;
        $weight_category = $_POST['weight_category'] ?? null;
        $age_group = $_POST['age_group'] ?? null;
        $user_id = $_POST['user_id'] ?? null;
        $role = $_POST['role'] ?? null;
        $corner = ($role === 'scorer') ? ($_POST['corner'] ?? null) : null;
    
        if (!$user_id || !$role || !$event_type || !$gender || !$weight_category || !$age_group) {
            die("Missing required POST data.");
        }
    
        $sql = "INSERT INTO default_assignments 
                    (event_type, gender, weight_category, age_group, user_id, role, corner)
                VALUES 
                    (:event_type, :gender, :weight_category, :age_group, :user_id, :role, :corner)
                ON DUPLICATE KEY UPDATE 
                    corner = VALUES(corner)";
    
        $this->db->query($sql);
        $this->db->bind(':event_type', $event_type);
        $this->db->bind(':gender', $gender);
        $this->db->bind(':weight_category', $weight_category);
        $this->db->bind(':age_group', $age_group);
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':role', $role);
        $this->db->bind(':corner', $corner);
        $this->db->execute();
    
        header("Location: /admin/dashboard?success=default-assigned");
        exit;
    }






    public function listUsers() {
        $users = (new User())->getAll();
        return $this->view('admin/users', compact('users'));
    }
    
    public function applyDefaultsToAllFixtures() {
        // 1. Get all fixtures
        $this->db->query("SELECT * FROM fixtures");
        $fixtures = $this->db->resultSet();
    
        // 2. Apply defaults to each fixture
        $assignment = new Assignment();
        foreach ($fixtures as $fixture) {
            $assignment->applyDefaultsToFixture($fixture);
        }
    
        // 3. Redirect or confirm
        header("Location: /admin/dashboard?success=defaults-applied");
        exit;
    }

}
