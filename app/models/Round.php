<?php
require_once 'core/Database.php';
class Round {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getByFixture($fixture_id) {
        $sql = "SELECT * FROM rounds WHERE fixture_id = :fixture_id ORDER BY round_number ASC";
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $fixture_id);
        return $this->db->resultSet();
    }

    public function deactivateAll($fixture_id) {
        $sql = "UPDATE rounds SET is_active = 0 WHERE fixture_id = :fixture_id";
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $fixture_id);
        $this->db->execute();
    }

    
    
    public function getActiveRound($fixture_id) {
        $sql = "SELECT * FROM rounds 
                WHERE fixture_id = :fixture_id 
                  AND is_active = 1 
                LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $fixture_id);
        return $this->db->single();
    }
    
    public function startRound($fixtureId, $roundNumber)
    {
        // Deactivate all other rounds for this fixture
        $sql = "UPDATE rounds 
        SET is_active = 0, status = 'completed' 
        WHERE fixture_id = :fixture_id";

        $this->db->query($sql);
        $this->db->bind(':fixture_id', $fixtureId);
        $this->db->execute();

    
        // Insert new round or update if it already exists
        $sql = "INSERT INTO rounds (fixture_id, round_number, status, is_active)
                VALUES (:fixture_id, :round_number, 'in_progress', 1)
                ON DUPLICATE KEY UPDATE status = 'in_progress', is_active = 1";
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $fixtureId);
        $this->db->bind(':round_number', $roundNumber);
        $this->db->execute();
    }
    
    public function getRoundStatuses($fixtureId)
    {
        $sql = "SELECT round_number, status, is_active FROM rounds 
                WHERE fixture_id = :fixture_id 
                ORDER BY round_number ASC";
    
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $fixtureId);
        return $this->db->resultSet();
    }



}
