<?php
require_once 'app/models/FixtureModel.php';
require_once 'app/models/Score.php';
require_once 'app/models/ParticipantModel.php';
require_once 'app/models/Round.php';

class PublicController extends Controller
{
    public function viewMatch($id)
    {
        $fixtureModel = new FixtureModel();
        $userModel    = new ParticipantModel();
        $roundModel   = $this->model('Round');
        $scoreModel   = $this->model('Score');

        $fixture = $fixtureModel->getFixtureById($id);
        $round   = $roundModel->getActiveRound($id);

        if (!$round) {
            header("Location: /fixtures?error=match_not_started");
            exit;
        }
        
        if ($fixtureModel->isFixtureLocked($id)) {
            header("Location: /fixtures?error=fixture_locked");
            exit;
        }

        $red  = $userModel->getById($fixture['participant1_id']);
        $blue = $userModel->getById($fixture['participant2_id']);

        // Preload round-wise totals
        $roundScores = $scoreModel->getTotalScoresPerRound($id);

        return $this->view('public/match', [
            'fixture'     => $fixture,
            'red'         => $red,
            'blue'        => $blue,
            'round'       => $round,
            'roundScores' => $roundScores
        ]);
    }

    // JSON API for live averages (similar to judge)
    public function getLiveAverages($id)
    {
        header('Content-Type: application/json');

        $scoreModel = $this->model('Score');
        $averages   = $scoreModel->getLiveAveragesByFixture($id);

        echo json_encode($averages);
        exit;
    }
}

