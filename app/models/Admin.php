<?php
require_once 'core/Database.php';

class Admin {
    private $db;

    public function __construct() {
        $this->db = new Database(); // ✅ Uses your custom Database class with PDO
    }

    public function findByUsername($username) {
        $sql = "SELECT * FROM admins WHERE username = :username";
        $this->db->query($sql);
        $this->db->bind(':username', $username);
        return $this->db->single();
    }

}

