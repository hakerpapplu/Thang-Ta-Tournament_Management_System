<?php
require_once 'core/Database.php';

class Assignment {
    private $db;

    public function __construct() {
        $this->db = new Database(); // your custom DB wrapper
    }

    public function assignToFixture($fixture_id, $user_id, $role, $corner = null) {
        $sql = "INSERT INTO assignments (fixture_id, user_id, role, corner)
                VALUES (:fixture_id, :user_id, :role, :corner)";
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $fixture_id);
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':role', $role);
        $this->db->bind(':corner', $corner);
        $this->db->execute();
    }

    public function getByFixture($fixture_id) {
        $sql = "SELECT * FROM assignments WHERE fixture_id = :fixture_id";
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $fixture_id);
        return $this->db->resultSet();
    }

    public function applyDefaultsToFixture($fixture) {
        $sql = "SELECT * FROM default_assignments 
                WHERE event_type = :event_type 
                AND gender = :gender 
                AND weight_category = :weight_category 
                AND age_group = :age_group";
        $this->db->query($sql);
        $this->db->bind(':event_type', $fixture['event_type']);
        $this->db->bind(':gender', $fixture['gender']);
        $this->db->bind(':weight_category', $fixture['weight_category']);
        $this->db->bind(':age_group', $fixture['age_group']);
        $defaults = $this->db->resultSet();

        foreach ($defaults as $row) {
            $sql = "INSERT IGNORE INTO assignments (fixture_id, user_id, role, corner)
                    VALUES (:fixture_id, :user_id, :role, :corner)";
            $this->db->query($sql);
            $this->db->bind(':fixture_id', $fixture['id']);
            $this->db->bind(':user_id', $row['user_id']);
            $this->db->bind(':role', $row['role']);
            $this->db->bind(':corner', $row['corner']);
            $this->db->execute();
        }
    }

    public function getScorerAssignment($fixture_id, $user_id) {
        $sql = "SELECT * FROM assignments 
                WHERE fixture_id = :fixture_id 
                  AND user_id = :user_id 
                  AND role = 'scorer'";
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $fixture_id);
        $this->db->bind(':user_id', $user_id);
        return $this->db->single();
    }
}
