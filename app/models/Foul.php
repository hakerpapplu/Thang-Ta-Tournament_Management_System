<?php
require_once 'core/Database.php';
class Foul {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getFoulsByFixture($fixture_id) {
        $sql = "SELECT * FROM fouls 
                WHERE fixture_id = :fixture_id 
                ORDER BY round, sub_round";
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $fixture_id);
        return $this->db->resultSet();
    }

    public function getTotalFouls($fixtureId)
    {
        $sql = "SELECT corner, COUNT(*) AS foul_count
                FROM fouls 
                WHERE fixture_id = :fixture_id
                GROUP BY corner";
    
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $fixtureId);
        $rows = $this->db->resultSet();
    
        $totals = ['red' => 0, 'blue' => 0];
        foreach ($rows as $row) {
            $totals[$row['corner']] = (int) $row['foul_count'] * 3; // -3 per foul
        }
    
        return $totals;
    }


    private function getRedId($fixture_id) {
        $this->db->query("SELECT participant1_id FROM fixtures WHERE id = :id");
        $this->db->bind(':id', $fixture_id);
        return $this->db->single()['participant1_id'];
    }

    public function getByScorer($fixture_id, $scorer_id) {
        $sql = "SELECT * FROM fouls 
                WHERE fixture_id = :fixture_id 
                  AND participant_id = :scorer_id 
                ORDER BY round, sub_round";
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $fixture_id);
        $this->db->bind(':scorer_id', $scorer_id);
        return $this->db->resultSet();
    }

    public function submit($data) {
        $sql = "INSERT INTO fouls (fixture_id, round, sub_round, participant_id, type)
                VALUES (:fixture_id, :round, :sub_round, :participant_id, :type)";
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $data['fixture_id']);
        $this->db->bind(':round', $data['round']);
        $this->db->bind(':sub_round', $data['sub_round']);
        $this->db->bind(':participant_id', $data['participant_id']);
        $this->db->bind(':type', $data['type']);
        $this->db->execute();
    }
    
    public function getFoulsByScorer($fixtureId, $round, $scorerId)
    {
        $sql = "SELECT * FROM fouls
                WHERE fixture_id = :fixture_id AND round = :round AND scorer_id = :scorer_id
                ORDER BY sub_round ASC";
    
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $fixtureId);
        $this->db->bind(':round', $round);
        $this->db->bind(':scorer_id', $scorerId);
        return $this->db->resultSet();
    }
    
    public function getLastSubRound($fixtureId, $round, $scorerId)
    {
        $sql = "SELECT MAX(sub_round) AS last_sub_round
                FROM fouls
                WHERE fixture_id = :fixture_id AND round = :round AND scorer_id = :scorer_id";
    
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $fixtureId);
        $this->db->bind(':round', $round);
        $this->db->bind(':scorer_id', $scorerId);
        $row = $this->db->single();
    
        return $row ? (int) $row['last_sub_round'] : null;
    }
    
    public function insert($data)
    {
        $sql = "INSERT INTO fouls (fixture_id, round, sub_round, scorer_id, corner, reason)
                VALUES (:fixture_id, :round, :sub_round, :scorer_id, :corner, :reason)";
    
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $data['fixture_id']);
        $this->db->bind(':round', $data['round']);
        $this->db->bind(':sub_round', $data['sub_round']);
        $this->db->bind(':scorer_id', $data['scorer_id']);
        $this->db->bind(':corner', $data['corner']);
        $this->db->bind(':reason', $data['reason']);
        $this->db->execute();
    }
    
    public function getFoulsByFixtureWithScorerName($fixtureId)
    {
        $sql = "SELECT f.*, a.name AS scorer_name
                FROM fouls f
                JOIN admins a ON a.id = f.scorer_id
                WHERE f.fixture_id = :fixture_id
                ORDER BY f.created_at ASC";
    
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $fixtureId);
        return $this->db->resultSet();
    }
    
    public function getAverageFouls($fixtureId, $round = null, $corner = null)
    {
        $sql = "SELECT COUNT(*) as foul_count FROM fouls WHERE fixture_id = :fixture_id";
    
        if ($round !== null) {
            $sql .= " AND round = :round";
        }
    
        if ($corner !== null) {
            $sql .= " AND corner = :corner";
        }
    
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $fixtureId);
        if ($round !== null) $this->db->bind(':round', $round);
        if ($corner !== null) $this->db->bind(':corner', $corner);
    
        $row = $this->db->single();
        $count = (int) $row['foul_count'];
    
        // Assume 3 scorers per corner
        return round($count / 3);
    }


}
