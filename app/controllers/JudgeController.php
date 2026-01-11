<?php
require_once 'app/helpers/role_guard.php';
require_once 'app/models/Fixture.php';
require_once 'app/models/Score.php';
require_once 'app/models/Foul.php';
require_once 'app/models/Round.php';
require_once 'app/models/User.php';
require_once 'app/models/FixtureModel.php';

class JudgeController extends Controller {
    private $db;

    public function __construct() {
        requireRole('judge');
        $this->db = new Database();
    }

    public function finalizeResult()
    {
        session_start();
        requireRole('judge');
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fixtureId = $_POST['fixture_id'];
            $winnerId = $_POST['winner_id'] ?? null;
            $eventType = $_POST['event_type'] ?? '';
            $weightCategory = $_POST['weight_category'] ?? '';
            $ageGroup = $_POST['age_group'] ?? '';
            $gender = $_POST['gender'] ?? '';
    
            if (!$winnerId) {
                header("Location: /judge/panel/$fixtureId?error=winner_not_selected");
                exit;
            }
    
            $scoreModel = $this->model('Score');
            $totals = $scoreModel->getFinalTotalWithFouls($fixtureId);
    
            $fixtureModel = $this->model('FixtureModel');
    
            // Update scores and winner
            $fixtureModel->updateScoresAndWinner(
                $fixtureId,
                $totals['red'],
                $totals['blue'],
                $winnerId
            );
    
            // Auto-generate next round
            $fixtureModel->autoGenerateNextRound($eventType, $weightCategory, $ageGroup, $gender);
    
            // 🔒 Lock this fixture
            $fixtureModel->lockFixture($fixtureId);
    
            // Build the redirect URL with original query parameters
            $queryParams = [
                'gender' => $gender,
                'age_group' => $ageGroup,
                'weight_category' => $weightCategory,
                'event_type' => $eventType,
                'success' => '1'
            ];
            $queryString = http_build_query($queryParams);
            header("Location: /fixtures?$queryString");
            exit;
        }
    }

    
    public function viewMatch($id)
    {
        
        requireRole('judge');

        $fixtureModel = $this->model('FixtureModel');
        $fixture = $fixtureModel->getFixtureById($id);

        if (!$fixture) {
            die("Fixture not found.");
        }
        
        if ($fixtureModel->isFixtureLocked($id)) {
            header("Location: /fixtures?error=fixture_locked");
            exit;
        }

        $userModel = $this->model('User');
        $judges = $userModel->getAllJudges();

        $this->view('judge/match', [
            'fixture' => $fixture,
            'judges' => $judges
        ]);
    }

    public function startMatch()
    {
        session_start();
        requireRole('judge');

        $fixtureId = $_POST['fixture_id'];
        $judgeId = $_POST['judge_id'];

        // Store judge in session
        $_SESSION['match_judges'][$fixtureId] = $judgeId;

        // Start round 1
        $roundModel = $this->model('Round');
        $roundModel->startRound($fixtureId, 1);

        header("Location: /judge/panel/$fixtureId");
        exit;
    }
    
    public function startRound()
    {
        session_start();
        requireRole('judge');
    
        $fixtureId = $_POST['fixture_id'] ?? null;
        $roundNumber = $_POST['round'] ?? null;
    
        if (!$fixtureId || !$roundNumber) {
            header("Location: /fixtures?error=missing_params");
            exit;
        }
    
        $roundModel = $this->model('Round');
        $roundModel->startRound($fixtureId, $roundNumber);
    
        header("Location: /judge/panel/$fixtureId?status=round$roundNumber_started");
        exit;
    }


    public function panel($id)
    {
        
        requireRole('judge');
    
        $fixtureModel = $this->model('FixtureModel');
        $roundModel = $this->model('Round');
        $scoreModel = $this->model('Score');
        $foulModel = $this->model('Foul');
        $userModel = $this->model('User');
    
        $fixture = $fixtureModel->getFixtureById($id);
        $round = $roundModel->getActiveRound($id);
        $scores = $scoreModel->getScoresByFixture($id);
        $fouls = $foulModel->getFoulsByFixture($id);
        $roundScores = $scoreModel->getTotalScoresPerRound($id);
    
        // get scorer names
        foreach ($scores as &$score) {
            $user = $userModel->getById($score['scorer_id']);
            $score['scorer_name'] = $user['name'] ?? 'Unknown';
        }
    
        foreach ($fouls as &$foul) {
            $user = $userModel->getById($foul['scorer_id']);
            $foul['scorer_name'] = $user['name'] ?? 'Unknown';
        }
    
        $this->view('judge/panel', [
            'fixture' => $fixture,
            'round' => $round,
            'scores' => $scores,
            'fouls' => $fouls,
            'roundScores' => $roundScores,
        ]);
    }
    
    public function getLiveAverages($id)
    {
        header('Content-Type: application/json');
    
        $scoreModel = $this->model('Score');
        $averages = $scoreModel->getLiveAveragesByFixture($id);
    
        echo json_encode($averages);
        exit;
    }
    
    public function getLiveScoresAndFouls($id)
    {
        header('Content-Type: application/json');
    
        $scoreModel = $this->model('Score');
        $foulModel = $this->model('Foul');
    
        $scores = $scoreModel->getScoresByFixtureWithScorerName($id);
        $fouls  = $foulModel->getFoulsByFixtureWithScorerName($id);
    
        echo json_encode([
            'scores' => $scores,
            'fouls'  => $fouls
        ]);
        exit;
    }
    
}
