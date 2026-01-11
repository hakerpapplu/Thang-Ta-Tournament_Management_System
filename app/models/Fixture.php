<?php
require_once 'core/Database.php';
class Fixture {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAll() {
        $this->db->query("SELECT * FROM fixtures ORDER BY id ASC");
        return $this->db->resultSet();
    }

    public function getById($fixture_id) {
        $sql = "SELECT * FROM fixtures WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $fixture_id);
        return $this->db->single();
    }

    public function setWinner($fixture_id, $winner_id) {
        $sql = "UPDATE fixtures SET winner_id = :winner_id WHERE id = :fixture_id";
        $this->db->query($sql);
        $this->db->bind(':winner_id', $winner_id);
        $this->db->bind(':fixture_id', $fixture_id);
        $this->db->execute();
    }

}

