<?php
require_once 'app/models/ParticipantModel.php';
require_once 'app/helpers/session_helper.php';
require_once 'core/Database.php';

class ParticipantController extends Controller {
    private $participantModel;
    private $db;

    public function __construct() {
        $this->participantModel = new ParticipantModel();
        $this->db = new Database();
    }

    
    public function create()
    {
        if (!isset($_SESSION['verified_district'])) {
            header("Location: /participants/district-check");
            exit;
        }
    
        $district = $_SESSION['verified_district'];
        $this->view('participants/create', ['district' => $district]);
    }


    // List all participants (requires login)
    public function index() {
        requireLogin(); // 🔐 Auth check inside method
        $ageGroup = $_GET['age_group'] ?? null;
        $weightCategory = $_GET['weight_category'] ?? null;
        $eventType = $_GET['event_type'] ?? null;
        $gender = $_GET['gender'] ?? null;
        $district = $_GET['district'] ?? null;

        $participants = $this->participantModel->getFilteredParticipants($ageGroup, $weightCategory, $eventType, $gender, $district);
        require 'app/views/participants/index.php';
    }

    // Handle POST request to add participant
    public function store()
{

        $data = [
            'name'            => trim($_POST['name']),
            'age'             => intval($_POST['age']),
            'gender'          => $_POST['gender'],
            'district'        => trim($_POST['district']),
            'contact'         => trim($_POST['contact']),
            'age_group'       => trim($_POST['age_group']),
            'weight_category' => trim($_POST['weight_category']),
            'event_type'      => $_POST['event_type']
        ];

        if ($this->participantModel->create($data)) {
            if (isset($_SESSION['user'])) {
                // ✅ Admin/Judge user → keep session
                $_SESSION['success'] = "Participant added successfully!";
                header('Location: /participants');
            } else {
                $_SESSION['success'] = "Participant added successfully!";
                header('Location: /participants/district-participants');
            }
        } else {
            if (isset($_SESSION['user'])) {
                $_SESSION['error'] = "Error adding participant!";
                header('Location: /participants');
            } else {
                // No need to destroy session here because it’s an error case
                header('Location: https://mahathangta.in?error=participant_error');
            }
        }
        exit;
    }







    // Show form to edit participant
    public function edit($id) {
        
        $participant = $this->participantModel->getById($id);
        if (!$participant) {
            header('Location: /participants');
        }
        require 'app/views/participants/edit.php';
    }

    // Handle POST request to update participant
    public function update($id) {
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'age' => intval($_POST['age']),
                'gender' => $_POST['gender'],
                'district' => trim($_POST['district']),
                'contact' => trim($_POST['contact']),
                'age_group' => trim($_POST['age_group']),
                'weight_category' => trim($_POST['weight_category']),
                'event_type' => $_POST['event_type']
            ];

            if ($this->participantModel->update($id, $data)) {
                if (isset($_SESSION['user'])) {
                // ✅ Admin/Judge user → keep session
                $_SESSION['success'] = "Participant updated successfully!";
                header('Location: /participants');
            } else {
                $_SESSION['success'] = "Participant updated successfully!";
                header('Location: /participants/district-participants');
            }
            } else {
                $_SESSION['error'] = "Error updating participant!";
            }

          
        }
    }

    // Delete participant
    public function delete($id) {
        
        if ($this->participantModel->delete($id)) {
            if (isset($_SESSION['user'])) {
                // ✅ Admin/Judge user → keep session
                $_SESSION['success'] = "Participant deleted successfully!";
                header('Location: /participants');
            } else {
                $_SESSION['success'] = "Participant deleted successfully!";
                header('Location: /participants/district-participants');
            }
        } else {
            $_SESSION['error'] = "Error deleting participant!";
        }

        
    }

    public function export() {
        requireLogin();
        require 'vendor/autoload.php'; // or your autoload path
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // Headers
        $headers = ['Name', 'Age', 'Gender', 'District', 'Contact', 'Age Group', 'Weight Category', 'Event Type'];
        $sheet->fromArray($headers, NULL, 'A1');
    
        // Fetch filtered participants
        $ageGroup = $_GET['age_group'] ?? null;
        $weightCategory = $_GET['weight_category'] ?? null;
        $eventType = $_GET['event_type'] ?? null;
        $gender = $_GET['gender'] ?? null;
        $district = $_GET['district'] ?? null;
    
        $participants = $this->participantModel->getFilteredParticipants($ageGroup, $weightCategory, $eventType, $gender, $district);
    
        // Insert data
        $row = 2;
        foreach ($participants as $participant) {
            $sheet->fromArray([
                $participant['name'],
                $participant['age'],
                $participant['gender'],
                $participant['district'],
                $participant['contact'],
                $participant['age_group'],
                $participant['weight_category'],
                $participant['event_type']
            ], NULL, "A{$row}");
            $row++;
        }
    
        // Export
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="participants.xlsx"');
        header('Cache-Control: max-age=0');
    
        $writer->save('php://output');
        exit;
    }
    
    public function districtCheckForm()
    {
        $this->view('participants/district-check');
    }
    
    public function verifyDistrict()
    {
        $district = $_POST['district'] ?? '';
        $districtCode = $_POST['district_code'] ?? '';
    
        $sql = "SELECT * FROM districts WHERE name = :name LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':name', $district);
        $row = $this->db->single();
    
        if (!$row) {
            $_SESSION['error'] = "Invalid district.";
            header("Location: /participants/district-check");
            exit;
        }
    
        if ($row['locked']) {
            $_SESSION['error'] = "Registrations for this district are locked.";
            header("Location: /participants/district-check");
            exit;
        }
    
        if ($row['code'] !== $districtCode) {
            $_SESSION['error'] = "Invalid district code.";
            header("Location: /participants/district-check");
            exit;
        }
    
        // ✅ Passed verification → store district in session
        $_SESSION['verified_district'] = $row['name'];
        
        header("Location: /participants/district-participants");
        exit;
    }
    
    public function districtParticipants()
    {
        if (!isset($_SESSION['verified_district'])) {
            header("Location: /participants/district-check");
            exit;
        }
    
        $district = $_SESSION['verified_district'];
    
        // Fetch all participants for this district
        $sql = "SELECT * FROM participants WHERE district = :district ORDER BY created_at DESC";
        $this->db->query($sql);
        $this->db->bind(':district', $district);
        $participants = $this->db->resultSet();
    
        require_once __DIR__ . '/../views/participants/district_participants.php';
    }
    
    public function logout()
    {
        session_unset();
        session_destroy();
        header("Location: https://mahathangta.in");
        exit;
    }


    
}
?>
