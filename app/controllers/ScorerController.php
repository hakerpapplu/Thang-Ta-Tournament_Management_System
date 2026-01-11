<?php
require_once 'app/helpers/role_guard.php';
require_once 'app/models/Fixture.php';
require_once 'app/models/Assignment.php';
require_once 'app/models/Score.php';
require_once 'app/models/Foul.php';
require_once 'app/models/Round.php';

class ScorerController extends Controller {
    private $db;

    public function __construct() {
        requireRole('scorer');
        $this->db = new Database();
    }

    // View assigned match
    public function viewMatch($id)
    {
        requireRole('scorer');
    
        $fixtureModel = $this->model('FixtureModel');
        $roundModel = $this->model('Round');
        $userModel = $this->model('User');
    
        $fixture = $fixtureModel->getFixtureById($id);
        $round = $roundModel->getActiveRound($id);
    
        if (!$round) {
            header("Location: /fixtures?error=match_not_started");
            exit;
        }
        
        if ($fixtureModel->isFixtureLocked($id)) {
            header("Location: /fixtures?error=fixture_locked");
            exit;
        }
        
        $scorers = $userModel->getAllScorers();
    
        $this->view('scorer/select', [
            'fixture' => $fixture,
            'scorers' => $scorers
        ]);
    }
    
    public function enterMatch()
    {
        requireRole('scorer');
    
        $fixtureId = $_POST['fixture_id'];
        $scorerId = $_POST['scorer_id'];
        $corner = $_POST['corner'];
    
        if (!$fixtureId || !$scorerId || !$corner) {
            header("Location: /fixtures?error=invalid_scorer_data");
            exit;
        }
    
        $_SESSION['scorer_info'][$fixtureId] = [
            'scorer_id' => $scorerId,
            'corner' => $corner
        ];
    
        header("Location: /scorer/panel/$fixtureId");
        exit;
    }
    
    public function panel($id)
    {
        requireRole('scorer');
    
        $fixtureModel = $this->model('FixtureModel');
        $roundModel = $this->model('Round');
        $scoreModel = $this->model('Score');
        $foulModel = $this->model('Foul');
    
        $fixture = $fixtureModel->getFixtureById($id);
        $currentRound = $roundModel->getActiveRound($id);
    
        // ✅ Fix: Use correct variable name
        $currentRoundNumber = $currentRound['round_number'] ?? 1;
    
        // ✅ Reset sub-round if round changed
        if (!isset($_SESSION['scorer_round'][$id]) || $_SESSION['scorer_round'][$id] !== $currentRoundNumber) {
            $_SESSION['sub_round'][$id] = 1;
            $_SESSION['scorer_round'][$id] = $currentRoundNumber;
        }
    
        // ✅ Get scorer info from session
        $scorerId = $_SESSION['scorer_info'][$id]['scorer_id'] ?? null;
        $corner = $_SESSION['scorer_info'][$id]['corner'] ?? null;
        $subRound = $_SESSION['sub_round'][$id] ?? 1;
    
        // ✅ Score + foul history for current round
        $scores = $scoreModel->getScoresByScorer($id, $currentRoundNumber, $scorerId);
        $fouls = $foulModel->getFoulsByScorer($id, $currentRoundNumber, $scorerId);
    
        $roundStatuses = $roundModel->getRoundStatuses($id);
    
        return $this->view('scorer/panel', compact(
            'fixture', 'currentRound', 'scores', 'fouls',
            'scorerId', 'corner', 'subRound', 'roundStatuses'
        ));
    }





    public function submitScore()
    {
        requireRole('scorer');
    
        $fixtureId = $_POST['fixture_id'];
        $round = $_POST['round'];
        $corner = $_POST['corner'];
        $scorerId = $_POST['scorer_id'];
        $score = (int) $_POST['score'];
        $foulTypes = explode(',', $_POST['selected_fouls'] ?? '');
    
        if ($score > 6 || $score < 0) {
            header("Location: /scorer/panel/$fixtureId?error=invalid_score");
            exit;
        }
    
        $scoreModel = $this->model('Score');
        $foulModel = $this->model('Foul');
    
        $subRound = $_SESSION['sub_round'][$fixtureId] ?? 1;
    
        // Insert score
        $scoreModel->insert([
            'fixture_id' => $fixtureId,
            'round' => $round,
            'sub_round' => $subRound,
            'scorer_id' => $scorerId,
            'corner' => $corner,
            'score' => $score
        ]);
    
        // Insert multiple fouls
        foreach ($foulTypes as $reason) {
            $reason = trim($reason);
            if (!empty($reason)) {
                $foulModel->insert([
                    'fixture_id' => $fixtureId,
                    'round' => $round,
                    'sub_round' => $subRound,
                    'scorer_id' => $scorerId,
                    'corner' => $corner,
                    'reason' => $reason
                ]);
            }
        }

        // Increment sub-round
        $_SESSION['sub_round'][$fixtureId] = $subRound + 1;
    
        header("Location: /scorer/panel/$fixtureId?success=score_added");
        exit;
    }


    
    public function submitFoul()
    {
       
        requireRole('scorer');
    
        $fixtureId = $_POST['fixture_id'];
        $round = $_POST['round'];
        $corner = $_POST['corner'];
        $scorerId = $_POST['scorer_id'];
        $reason = trim($_POST['reason']);
    
        $foulModel = $this->model('Foul');
    
        // Get next sub-round
        $lastSubRound = $foulModel->getLastSubRound($fixtureId, $round, $scorerId);
        $subRound = $lastSubRound ? $lastSubRound + 1 : 1;
    
        if (empty($reason)) {
            header("Location: /scorer/panel/$fixtureId?error=empty_reason");
            exit;
        }
    
        $foulModel->insert([
            'fixture_id' => $fixtureId,
            'round' => $round,
            'sub_round' => $subRound,
            'scorer_id' => $scorerId,
            'corner' => $corner,
            'reason' => $reason
        ]);
    
        header("Location: /scorer/panel/$fixtureId?success=foul_added");
        exit;
    }
    
    public function addSubRound()
    {
        
        requireRole('scorer');
    
        $fixtureId = $_POST['fixture_id'];
       
        $_SESSION['sub_round'][$fixtureId]++;
        
        header("Location: /scorer/panel/$fixtureId?subround_updated");
        exit;
    }
    
    // In ScorerController.php
    public function endMatch()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fixtureId = $_POST['fixture_id'];
    
            // Clear scorer info + sub_round session
            unset($_SESSION['scorer_info'][$fixtureId]);
            unset($_SESSION['sub_round'][$fixtureId]);
    
            // Rebuild query params from POST (like finalizeResult)
            $queryParams = [
                'gender' => $_POST['gender'] ?? '',
                'age_group' => $_POST['age_group'] ?? '',
                'weight_category' => $_POST['weight_category'] ?? '',
                'event_type' => $_POST['event_type'] ?? '',
                'success' => '1'
            ];
            $queryString = http_build_query($queryParams);
    
            header("Location: /fixtures?$queryString");
            exit;
        }
    }

    public function getCurrentRound($id)
    {
        header('Content-Type: application/json');
    
        $roundModel = $this->model('Round');
        $active = $roundModel->getActiveRound($id);
    
        echo json_encode([
            'round_number' => $active['round_number'] ?? null,
        ]);
        exit;
    }

    
}
