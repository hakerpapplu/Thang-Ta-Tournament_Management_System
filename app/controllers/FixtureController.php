<?php
require_once 'app/models/ParticipantModel.php';
require_once 'app/models/FixtureModel.php';
require_once 'app/helpers/helpers.php';
require_once 'app/helpers/Session.php';
require_once 'app/helpers/Redirect.php';

class FixtureController
{
    private $fixtureModel;
    private $participantModel;

    public function __construct()
    {
        $this->fixtureModel = $this->model('FixtureModel');
        $this->participantModel = $this->model('ParticipantModel');
    }

    public function model($model)
    {
        require_once 'app/models/' . $model . '.php';
        return new $model();
    }

    public function index()
    {
        $event_type = $_GET['event_type'] ?? '';
        $weight_category = $_GET['weight_category'] ?? '';
        $age_group = $_GET['age_group'] ?? '';
        $gender = $_GET['gender'] ?? '';

        
        $fixtures = $this->fixtureModel->getFixturesByFilters($event_type, $weight_category, $age_group, $gender);
        
        
        
        $this->view('fixtures/index', [
            'fixtures' => $fixtures,
            'event_type' => $event_type,
            'weight_category' => $weight_category,
            'age_group' => $age_group,
            'gender' => $gender,
        ]);
        
    }

    public function view($view, $data = [])
    {
        if (file_exists('app/views/' . $view . '.php')) {
            extract($data);
            require_once 'app/views/' . $view . '.php';
        } else {
            die("View does not exist: " . $view);
        }
    }


    public function generate()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $event_type = $_POST['event_type'] ?? '';
            $weight_category = $_POST['weight_category'] ?? '';
            $age_group = $_POST['age_group'] ?? '';
            $gender = $_POST['gender'] ?? '';

            if (!$event_type || !$weight_category || !$age_group || !$gender) {
                Session::flash('error', 'Missing required fields.');
                Redirect::to('/fixtures');
                return;
            }

            // Delete existing fixtures
            $this->fixtureModel->deleteFixtures($event_type, $weight_category, $age_group, $gender);

            // Get participants
            $participants = $this->fixtureModel->getParticipantsByFilters($event_type, $weight_category, $age_group, $gender);

            if (empty($participants)) {
                Session::flash('error', 'No participants found for the selected filters.');
                Redirect::to('/fixtures');
                return;
            }

            // Generate new fixtures
            $this->fixtureModel->generateKnockoutFixtures($participants, $event_type, $weight_category, $age_group, $gender);

            Session::flash('success', 'Fixtures generated successfully!');
            Redirect::to('/fixtures?event_type=' . urlencode($event_type) . '&weight_category=' . urlencode($weight_category) . '&age_group=' . urlencode($age_group) . '&gender=' . urlencode($gender));
        } else {
            Session::flash('error', 'Invalid request method.');
            Redirect::to('/fixtures');
        }
    }


    /*public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fixtureId = $_POST['fixture_id'];
            $eventType = $_POST['event_type'] ?? '';
            $weightCategory = $_POST['weight_category'] ?? '';
            $ageGroup = $_POST['age_group'] ?? '';
            $gender = $_POST['gender'] ?? '';

            $fixture = $this->fixtureModel->getFixtureById($fixtureId);
            if (!$fixture) {
                Session::flash('error', 'Fixture not found.');
                Redirect::to('/fixtures');
                exit;
            }

            // ✅ Use models directly
            $scoreModel = new Score();
            $foulModel = new Foul();

            $totals = $scoreModel->getTotalScoresByRound($fixtureId);
            $fouls = $foulModel->getTotalFouls($fixtureId);

            $winnerId = null;
            if ($totals['red'] > $totals['blue']) {
                $winnerId = $totals['red_id'];
            } elseif ($totals['blue'] > $totals['red']) {
                $winnerId = $totals['blue_id'];
            } else {
                if ($fouls['red'] < $fouls['blue']) {
                    $winnerId = $totals['red_id'];
                } elseif ($fouls['blue'] < $fouls['red']) {
                    $winnerId = $totals['blue_id'];
                } else {
                    Session::flash('error', 'Match is tied. Judge must manually resolve.');
                    Redirect::to('/fixtures');
                    exit;
                }
            }

            // ✅ Update score and winner in fixtures table
            $this->fixtureModel->updateScoresAndWinner($fixtureId, $totals['red'], $totals['blue'], $winnerId);

            // ✅ Generate next round
            $this->fixtureModel->autoGenerateNextRound($eventType, $weightCategory, $ageGroup, $gender);

            Session::flash('success', 'Match finalized from score system.');
            Redirect::to('/fixtures?event_type=' . urlencode($eventType) .
                        '&weight_category=' . urlencode($weightCategory) .
                        '&age_group=' . urlencode($ageGroup) .
                        '&gender=' . urlencode($gender));
        }
    }*/




    public function export()
    {
            // Get values from GET request (i.e., from the export button's URL)
        $event_type = $_GET['event_type'] ?? '';
        $weight_category = $_GET['weight_category'] ?? '';
        $age_group = $_GET['age_group'] ?? '';
        $gender = $_GET['gender'] ?? '';
        
        $fixtures = $this->fixtureModel->getFixturesByFilters($event_type, $weight_category, $age_group, $gender);

        if (empty($fixtures)) {
            Session::flash('error', 'No fixtures found to export.');
            Redirect::to('/fixtures');
        }

        $this->fixtureModel->exportFixturesWithScores($fixtures);
    }

    public function exportWinners($eventType = null, $weightCategory = null, $ageGroup = null, $gender = null)
    {
        // Allow GET or POST fallback
        $eventType      = $eventType      ?? ($_GET['event_type'] ?? null);
        $weightCategory = $weightCategory ?? ($_GET['weight_category'] ?? null);
        $ageGroup       = $ageGroup       ?? ($_GET['age_group'] ?? null);
        $gender         = $gender         ?? ($_GET['gender'] ?? null);
    
        if (!$eventType || !$weightCategory || !$ageGroup || !$gender) {
            Session::flash('error', 'Missing category parameters for export.');
            Redirect::to('/fixtures');
        }
    
        $winners = $this->fixtureModel->getMedalWinnersByCategory($eventType, $weightCategory, $ageGroup, $gender);
    
        if (empty($winners)) {
            Session::flash('error', 'No winners found for the selected category.');
            Redirect::to('/fixtures');
        }
    
        $this->fixtureModel->exportWinners($winners, $eventType, $weightCategory, $ageGroup, $gender);
    }



    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $event_type = $_POST['event_type'] ?? '';
            $weight_category = $_POST['weight_category'] ?? '';
            $age_group = $_POST['age_group'] ?? '';
            $gender = $_POST['gender'] ?? '';

            if ($event_type && $weight_category && $age_group && $gender) {
                $this->fixtureModel->deleteFixtures($event_type, $weight_category, $age_group, $gender);

                Session::flash('success', 'Tournament fixtures deleted successfully.');
            } else {
                Session::flash('error', 'Missing required fields.');
            }
            Redirect::to('/fixtures');
        } else {
            Session::flash('error', 'Invalid request method.');
            Redirect::to('/fixtures');
        }
    }

    public function exportAllResults()
    {
        $results = $this->fixtureModel->getAllResults();
    
        if (empty($results)) {
            Session::flash('error', 'No results available to export.');
            Redirect::to('/dashboard');
        }
    
        $this->fixtureModel->exportAllResults($results);
    }

    


}
?>
