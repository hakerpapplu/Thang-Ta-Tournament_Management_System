<?php

require_once 'core/Database.php';

class DashboardModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getDashboardStats() {
        $stats = [];

        // Total participants
        $this->db->query("SELECT COUNT(*) as total FROM participants");
        $stats['total_participants'] = $this->db->single()['total'];

        // Total bouts (if 'bouts' table exists)
        $this->db->query("SELECT COUNT(*) as total FROM bouts");
        $stats['total_bouts'] = $this->db->single()['total'];

        // Temporary: No wins/losses available
        $stats['wins'] = 0;   // default value
        $stats['losses'] = 0; // default value

        // Age group breakdown
        $this->db->query("SELECT age_group, COUNT(*) as count FROM participants GROUP BY age_group");
        $stats['age_groups'] = $this->db->resultSet();

        // Weight category breakdown
        $this->db->query("SELECT weight_category, COUNT(*) as count FROM participants GROUP BY weight_category");
        $stats['weight_categories'] = $this->db->resultSet();

        // Event type breakdown
        $this->db->query("SELECT event_type, COUNT(*) as count FROM participants GROUP BY event_type");
        $stats['event_types'] = $this->db->resultSet();

        return $stats;
    }
}
