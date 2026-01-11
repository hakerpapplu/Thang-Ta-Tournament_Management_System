<?php
require_once 'core/Database.php';
class User {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAll() {
        $this->db->query("SELECT * FROM admins ORDER BY name ASC");
        return $this->db->resultSet();
    }

    public function getScorersForFixture($fixture_id) {
        $sql = "SELECT u.id, u.name, a.corner
                FROM assignments a
                JOIN admins u ON u.id = a.user_id
                WHERE a.fixture_id = :fixture_id AND a.role = 'scorer'";
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $fixture_id);
        return $this->db->resultSet();
    }

    public function getById($id) {
        $sql = "SELECT * FROM admins WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    public function findByUsername($username) {
        $sql = "SELECT * FROM admins WHERE username = :username";
        $this->db->query($sql);
        $this->db->bind(':username', $username);
        return $this->db->single();
    }

    public function getAllJudges()
    {
        $sql = "SELECT id, name FROM admins WHERE role = 'judge'";
        $this->db->query($sql);
        return $this->db->resultSet();
    }
    
    public function getAllScorers()
    {
        $sql = "SELECT id, name FROM admins WHERE role = 'scorer'";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

}
