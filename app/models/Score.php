<?php
require_once 'core/Database.php';
require_once 'app/models/Foul.php';
class Score {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getScoresByFixture($fixture_id) {
        $sql = "SELECT * FROM scores 
                WHERE fixture_id = :fixture_id 
                ORDER BY round, sub_round, corner";
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $fixture_id);
        return $this->db->resultSet();
    }

    public function getFinalTotalWithFouls($fixtureId)
    {
        $sql = "SELECT participant1_id AS red_id, participant2_id AS blue_id FROM fixtures WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $fixtureId);
        $row = $this->db->single();
    
        $sql = "SELECT corner, FLOOR(AVG(score)) AS avg_score
                FROM scores 
                WHERE fixture_id = :fixture_id
                GROUP BY round, sub_round, corner";
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $fixtureId);
        $scores = $this->db->resultSet();
    
        $totals = ['red' => 0, 'blue' => 0, 'red_id' => $row['red_id'], 'blue_id' => $row['blue_id']];
        foreach ($scores as $s) {
            $totals[$s['corner']] += (int) $s['avg_score'];
        }
    
        // Deduct 3 pts per foul
        $foulModel = new Foul();
        foreach (['red', 'blue'] as $corner) {
            $count = $foulModel->getAverageFouls($fixtureId, null, $corner); // null = all rounds
            $totals[$corner] -= $count * 3;
            if ($totals[$corner] < 0) $totals[$corner] = 0;
        }
    
        return $totals;
    }


    public function getByScorer($fixture_id, $scorer_id) {
        $sql = "SELECT * FROM scores 
                WHERE fixture_id = :fixture_id 
                  AND scorer_id = :scorer_id 
                ORDER BY round, sub_round";
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $fixture_id);
        $this->db->bind(':scorer_id', $scorer_id);
        return $this->db->resultSet();
    }

    public function alreadySubmitted($fixture_id, $round, $sub_round, $scorer_id) {
        $sql = "SELECT id FROM scores 
                WHERE fixture_id = :fixture_id 
                  AND round = :round 
                  AND sub_round = :sub_round 
                  AND scorer_id = :scorer_id";
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $fixture_id);
        $this->db->bind(':round', $round);
        $this->db->bind(':sub_round', $sub_round);
        $this->db->bind(':scorer_id', $scorer_id);
        return $this->db->single(); // returns false if not found
    }

    public function submit($data) {
        $sql = "INSERT INTO scores (fixture_id, round, sub_round, scorer_id, corner, score)
                VALUES (:fixture_id, :round, :sub_round, :scorer_id, :corner, :score)";
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $data['fixture_id']);
        $this->db->bind(':round', $data['round']);
        $this->db->bind(':sub_round', $data['sub_round']);
        $this->db->bind(':scorer_id', $data['scorer_id']);
        $this->db->bind(':corner', $data['corner']);
        $this->db->bind(':score', $data['score']);
        $this->db->execute();
    }

    public function getAverageScores($fixture_id) {
        $sql = "
            SELECT round, sub_round, corner, FLOOR(AVG(score)) AS avg_score
            FROM scores
            WHERE fixture_id = :fixture_id
            GROUP BY round, sub_round, corner
            ORDER BY round, sub_round, corner";
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $fixture_id);
        return $this->db->resultSet();
    }
    
    public function getLiveAveragesByFixture($fixtureId)
    {
        $sql = "SELECT corner, round, sub_round, FLOOR(AVG(score)) AS avg_score
                FROM scores
                WHERE fixture_id = :fixture_id
                GROUP BY round, sub_round, corner";
    
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $fixtureId);
        $rows = $this->db->resultSet();
    
        $totals = ['red' => 0, 'blue' => 0];
        foreach ($rows as $row) {
            $totals[$row['corner']] += (int) $row['avg_score'];
        }
    
        // Deduct fouls
        $foulModel = new Foul();
        foreach (['red', 'blue'] as $corner) {
            $count = $foulModel->getAverageFouls($fixtureId, null, $corner);
            $totals[$corner] -= $count * 3;
            if ($totals[$corner] < 0) $totals[$corner] = 0;
        }
    
        return $totals;
    }

    
    public function getTotalScoresPerRound($fixtureId)
    {
        $sql = "SELECT round, sub_round, corner, FLOOR(AVG(score)) AS avg_score
                FROM scores
                WHERE fixture_id = :fixture_id
                GROUP BY round, sub_round, corner
                ORDER BY round, sub_round";
    
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $fixtureId);
        $rows = $this->db->resultSet();
    
        $totals = [];
    
        foreach ($rows as $row) {
            $round = $row['round'];
            $corner = $row['corner'];
            $avg = (int) $row['avg_score'];
    
            if (!isset($totals[$round])) {
                $totals[$round] = ['red' => 0, 'blue' => 0];
            }
    
            $totals[$round][$corner] += $avg;
        }
    
        // Apply foul deduction
        $foulModel = new Foul();
        foreach ($totals as $round => &$data) {
            foreach (['red', 'blue'] as $corner) {
                $count = $foulModel->getAverageFouls($fixtureId, $round, $corner);
                $data[$corner] -= $count * 3;
                if ($data[$corner] < 0) $data[$corner] = 0;
            }
        }
    
        return $totals;
    }



    
    public function getScoresByScorer($fixtureId, $round, $scorerId)
    {
        $sql = "SELECT * FROM scores
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
                FROM scores
                WHERE fixture_id = :fixture_id AND round = :round AND scorer_id = :scorer_id";
    
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $fixtureId);
        $this->db->bind(':round', (int) $round);
        $this->db->bind(':scorer_id', $scorerId);
        $row = $this->db->single();
    
        return $row && isset($row['last_sub_round']) ? (int) $row['last_sub_round'] : null;
    }

    
    public function insert($data)
    {
        $sql = "INSERT INTO scores (fixture_id, round, sub_round, scorer_id, corner, score)
                VALUES (:fixture_id, :round, :sub_round, :scorer_id, :corner, :score)";
    
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $data['fixture_id']);
        $this->db->bind(':round', $data['round']);
        $this->db->bind(':sub_round', $data['sub_round']);
        $this->db->bind(':scorer_id', $data['scorer_id']);
        $this->db->bind(':corner', $data['corner']);
        $this->db->bind(':score', $data['score']);
        $this->db->execute();
    }

    public function getScoresByFixtureWithScorerName($fixtureId)
    {
        $sql = "SELECT s.*, a.name AS scorer_name
                FROM scores s
                JOIN admins a ON a.id = s.scorer_id
                WHERE s.fixture_id = :fixture_id
                ORDER BY s.created_at ASC";
    
        $this->db->query($sql);
        $this->db->bind(':fixture_id', $fixtureId);
        return $this->db->resultSet();
    }


}
