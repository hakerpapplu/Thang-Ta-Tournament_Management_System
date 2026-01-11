<?php
require_once 'core/Database.php';
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class FixtureModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getFixtures($eventType = null, $weightCategory = null, $ageGroup = null, $gender = null)
    {
        if ($eventType && $weightCategory && $ageGroup && $gender) {
            return $this->getFixturesByFilters($eventType, $weightCategory, $ageGroup, $gender);
        } else {
            return $this->getAllFixturesWithParticipants();
        }
    }

    /*public function getAllFixturesWithParticipants() {
        $sql = "SELECT 
                    f.*, 
                    p1.name AS p1_name, 
                    p2.name AS p2_name 
                FROM fixtures f
                LEFT JOIN participants p1 ON f.participant1_id = p1.id
                LEFT JOIN participants p2 ON f.participant2_id = p2.id
                ORDER BY f.round ASC, f.id ASC";

        $this->db->query($sql);
        return $this->db->resultSet();
    }*/
    
    public function getFixturesByFilters($eventType, $weightCategory, $ageGroup, $gender) {
        $sql = "SELECT 
                    f.*, 
                    p1.name AS p1_name, 
                    p2.name AS p2_name 
                FROM fixtures f
                LEFT JOIN participants p1 ON f.participant1_id = p1.id
                LEFT JOIN participants p2 ON f.participant2_id = p2.id
                WHERE f.event_type = :event_type
                  AND f.weight_category = :weight_category
                  AND f.age_group = :age_group
                  AND f.gender = :gender
                ORDER BY f.round ASC, f.id ASC";

        $this->db->query($sql);
        $this->db->bind(':event_type', $eventType);
        $this->db->bind(':weight_category', $weightCategory);
        $this->db->bind(':age_group', $ageGroup);
        $this->db->bind(':gender', $gender);
        
        return $this->db->resultSet();
    }
    
    public function getParticipantsByFilters($eventType, $weightCategory, $ageGroup, $gender) {
        $sql = "SELECT * FROM participants WHERE event_type = :event_type AND weight_category = :weight_category AND age_group = :age_group AND gender = :gender"  ;
        $this->db->query($sql);
        $this->db->bind(':event_type', $eventType);
        $this->db->bind(':weight_category', $weightCategory);
        $this->db->bind(':age_group', $ageGroup);
        $this->db->bind(':gender', $gender);
        
        return $this->db->resultSet();
    }

   public function deleteFixtures($eventType, $weightCategory, $ageGroup, $gender)
    {
        $sql = "DELETE FROM fixtures WHERE event_type = :event_type 
                AND weight_category = :weight_category 
                AND age_group = :age_group 
                AND gender = :gender";
        $this->db->query($sql);
        $this->db->bind(':event_type', $eventType);
        $this->db->bind(':weight_category', $weightCategory);
        $this->db->bind(':age_group', $ageGroup);
        $this->db->bind(':gender', $gender);
    
        $this->db->execute();
    }

    public function generateKnockoutFixtures($participants, $eventType, $weightCategory, $ageGroup, $gender)
{
    $count = count($participants);

    // Case 1: No participants → nothing to do
    if ($count === 0) {
        return;
    }

    // Case 2: Exactly one participant → auto award Gold
    if ($count === 1) {
    $solo = $participants[0];
    if (!empty($solo['id'])) {
        // Award gold directly
        $this->saveResult(
            $solo['id'],
            $eventType,
            $weightCategory,
            $ageGroup,
            $gender,
            'gold'
        );

        // ALSO create a "BYE" fixture so it appears in the bracket view
        $sql = "INSERT INTO fixtures 
            (round, event_type, weight_category, age_group, gender, participant1_id, participant2_id, winner_id, is_bye, medal_type) 
        VALUES 
            (:round, :event_type, :weight_category, :age_group, :gender, :p1, NULL, :winner, 1, 'gold')";

        $this->db->query($sql);
$this->db->bind(':round', 1); // round 1
$this->db->bind(':event_type', $eventType);
$this->db->bind(':weight_category', $weightCategory);
$this->db->bind(':age_group', $ageGroup);
$this->db->bind(':gender', $gender);
$this->db->bind(':p1', $solo['id']);
$this->db->bind(':winner', $solo['id']);
$this->db->execute();
    }
    return; // Fixtures table now has a BYE record
}


    // Case 3: More than one → continue with normal fixture generation
    shuffle($participants); // Randomize the participants
    $round = 1;

    // capture initial count to detect direct final (2 players only)
    $initialCount = $count;
    $isDirectFinal = ($initialCount === 2);

    // Handle BYE if number of participants is odd
    if ($count % 2 !== 0) {
        $byeParticipant = array_shift($participants); // Remove first participant and assign BYE

        // Insert BYE fixture
        $sql = "INSERT INTO fixtures (
                    participant1_id, 
                    participant2_id, 
                    round, 
                    event_type, 
                    weight_category, 
                    age_group, 
                    gender, 
                    is_bye,
                    winner_id
                ) 
                VALUES (
                    :p1, 
                    NULL, 
                    :round, 
                    :event_type, 
                    :weight_category, 
                    :age_group, 
                    :gender, 
                    1,
                    :winner_id
                )";

        $this->db->query($sql);
        $this->db->bind(':p1', $byeParticipant['id']);
        $this->db->bind(':round', $round);
        $this->db->bind(':event_type', $eventType);
        $this->db->bind(':weight_category', $weightCategory);
        $this->db->bind(':age_group', $ageGroup);
        $this->db->bind(':gender', $gender);
        $this->db->bind(':winner_id', $byeParticipant['id']);
        $this->db->execute();
    }

    // Generate remaining Round 1 fixtures
    foreach (array_chunk($participants, 2) as $pair) {
        $p1 = $pair[0]['id'];
        $p2 = isset($pair[1]) ? $pair[1]['id'] : null;
        $isBye = ($p2 === null); // Should never be true here, just in case

        // If this is a direct final (only 2 players originally), mark this fixture as gold at creation
        if ($isDirectFinal) {
            $sql = "INSERT INTO fixtures (
                        participant1_id, participant2_id, round, 
                        event_type, weight_category, age_group, gender, medal_type
                    ) VALUES (
                        :p1, :p2, :round, :event_type, :weight_category, :age_group, :gender, 'gold'
                    )";
            $this->db->query($sql);
            $this->db->bind(':p1', $p1);
            $this->db->bind(':p2', $p2);
            $this->db->bind(':round', $round);
            $this->db->bind(':event_type', $eventType);
            $this->db->bind(':weight_category', $weightCategory);
            $this->db->bind(':age_group', $ageGroup);
            $this->db->bind(':gender', $gender);
            $this->db->execute();
        } else {
            $sql = "INSERT INTO fixtures (
                        participant1_id, 
                        participant2_id, 
                        round, 
                        event_type, 
                        weight_category, 
                        age_group, 
                        gender, 
                        is_bye,
                        winner_id
                    ) 
                    VALUES (
                        :p1, 
                        :p2, 
                        :round, 
                        :event_type, 
                        :weight_category, 
                        :age_group, 
                        :gender, 
                        :is_bye,
                        :winner_id
                    )";

            $this->db->query($sql);
            $this->db->bind(':p1', $p1);
            $this->db->bind(':p2', $p2);
            $this->db->bind(':round', $round);
            $this->db->bind(':event_type', $eventType);
            $this->db->bind(':weight_category', $weightCategory);
            $this->db->bind(':age_group', $ageGroup);
            $this->db->bind(':gender', $gender);
            $this->db->bind(':is_bye', $isBye ? 1 : 0);
            $this->db->bind(':winner_id', $isBye ? $p1 : null);
            $this->db->execute();
        }
    }
}




        // Auto Generate Next Round
        // Auto Generate Next Round (with Bronze Match Support)
    // Auto Generate Next Round (with Bronze Match Support)
    public function autoGenerateNextRound($eventType, $weightCategory, $ageGroup, $gender)
    {
        // 1. Get current max round for given group
        $sql = "SELECT MAX(round) as max_round FROM fixtures
                WHERE event_type = :event_type 
                  AND weight_category = :weight_category 
                  AND age_group = :age_group 
                  AND gender = :gender";
        $this->db->query($sql);
        $this->db->bind(':event_type', $eventType);
        $this->db->bind(':weight_category', $weightCategory);
        $this->db->bind(':age_group', $ageGroup);
        $this->db->bind(':gender', $gender);
        $result = $this->db->single();
    
        if (!$result || !$result['max_round']) {
            return;
        }
        $currentRound = (int) $result['max_round'];
    
        // 2. Avoid generating next round unless at least one score exists (early exit as before)
        $sql = "SELECT COUNT(*) as count FROM fixtures 
                WHERE round = :round 
                  AND event_type = :event_type 
                  AND weight_category = :weight_category 
                  AND age_group = :age_group 
                  AND gender = :gender
                  AND (score_a IS NOT NULL OR score_b IS NOT NULL)";
        $this->db->query($sql);
        $this->db->bind(':round', $currentRound);
        $this->db->bind(':event_type', $eventType);
        $this->db->bind(':weight_category', $weightCategory);
        $this->db->bind(':age_group', $ageGroup);
        $this->db->bind(':gender', $gender);
        $countResult = $this->db->single();
        if (empty($countResult['count'])) {
            return;
        }
    
        // 3. Auto-assign BYE winners (unchanged)
        $sql = "SELECT id, participant1_id FROM fixtures 
                WHERE round = :round 
                  AND event_type = :event_type 
                  AND weight_category = :weight_category 
                  AND age_group = :age_group 
                  AND gender = :gender
                  AND participant2_id IS NULL AND winner_id IS NULL";
        $this->db->query($sql);
        $this->db->bind(':round', $currentRound);
        $this->db->bind(':event_type', $eventType);
        $this->db->bind(':weight_category', $weightCategory);
        $this->db->bind(':age_group', $ageGroup);
        $this->db->bind(':gender', $gender);
        $byeMatches = $this->db->resultSet();
    
        foreach ($byeMatches as $match) {
            $sql = "UPDATE fixtures SET winner_id = :winner_id WHERE id = :id";
            $this->db->query($sql);
            $this->db->bind(':winner_id', $match['participant1_id']);
            $this->db->bind(':id', $match['id']);
            $this->db->execute();
        }
    
        // 4. Ensure all matches in this round have winners before proceeding
        $sql = "SELECT * FROM fixtures 
                WHERE round = :round 
                  AND event_type = :event_type 
                  AND weight_category = :weight_category 
                  AND age_group = :age_group 
                  AND gender = :gender
                  AND winner_id IS NULL";
        $this->db->query($sql);
        $this->db->bind(':round', $currentRound);
        $this->db->bind(':event_type', $eventType);
        $this->db->bind(':weight_category', $weightCategory);
        $this->db->bind(':age_group', $ageGroup);
        $this->db->bind(':gender', $gender);
        $pending = $this->db->resultSet();
    
        if (!empty($pending)) {
            return;
        }
    
        // 5. Get all winners (ordered)
        $sql = "SELECT winner_id FROM fixtures 
                WHERE round = :round 
                  AND event_type = :event_type 
                  AND weight_category = :weight_category 
                  AND age_group = :age_group 
                  AND gender = :gender
                ORDER BY id ASC";
        $this->db->query($sql);
        $this->db->bind(':round', $currentRound);
        $this->db->bind(':event_type', $eventType);
        $this->db->bind(':weight_category', $weightCategory);
        $this->db->bind(':age_group', $ageGroup);
        $this->db->bind(':gender', $gender);
        $winners = $this->db->resultSet();
    
        if (empty($winners)) {
            return;
        }
    
        // Convert to a clean list of winner IDs (defensive)
        $winnerIds = [];
        foreach ($winners as $w) {
            if (!empty($w['winner_id'])) {
                $winnerIds[] = $w['winner_id'];
            }
        }
        if (empty($winnerIds)) {
            return;
        }
    
        // 6. If current round is the Final (i.e. only 1 winner found), generate Bronze match using semifinals
        //    (this preserves your existing flow: when final is completed we create bronze)
        if (count($winnerIds) === 1) {
            $semiFinalRound = $currentRound - 1;
    
            // Fetch semifinal matches (defensive)
            $sql = "SELECT id, participant1_id, participant2_id, winner_id
                    FROM fixtures
                    WHERE round = :round 
                      AND event_type = :event_type 
                      AND weight_category = :weight_category 
                      AND age_group = :age_group 
                      AND gender = :gender";
            $this->db->query($sql);
            $this->db->bind(':round', $semiFinalRound);
            $this->db->bind(':event_type', $eventType);
            $this->db->bind(':weight_category', $weightCategory);
            $this->db->bind(':age_group', $ageGroup);
            $this->db->bind(':gender', $gender);
            $semis = $this->db->resultSet();
    
            if (count($semis) === 2) {
                $losers = [];
                foreach ($semis as $match) {
                    if (!empty($match['winner_id'])) {
                        $loser = ($match['winner_id'] == $match['participant1_id'])
                                 ? $match['participant2_id']
                                 : $match['participant1_id'];
                        if (!empty($loser)) {
                            $losers[] = $loser;
                        }
                    }
                }
    
                if (count($losers) === 2) {
                    // Avoid duplicate bronze fixture for the next round
                    $nextRound = $currentRound + 1;
                    $sql = "SELECT COUNT(*) AS cnt FROM fixtures
                            WHERE round = :round
                              AND event_type = :event_type
                              AND weight_category = :weight_category
                              AND age_group = :age_group
                              AND gender = :gender
                              AND medal_type = 'bronze'";
                    $this->db->query($sql);
                    $this->db->bind(':round', $nextRound);
                    $this->db->bind(':event_type', $eventType);
                    $this->db->bind(':weight_category', $weightCategory);
                    $this->db->bind(':age_group', $ageGroup);
                    $this->db->bind(':gender', $gender);
                    $exists = $this->db->single();
    
                    if (empty($exists['cnt'])) {
                        $sql = "INSERT INTO fixtures (
                                    participant1_id, participant2_id, round, 
                                    event_type, weight_category, age_group, gender, medal_type
                                ) VALUES (
                                    :p1, :p2, :round, :event_type, :weight_category, :age_group, :gender, 'bronze'
                                )";
                        $this->db->query($sql);
                        $this->db->bind(':p1', $losers[0]);
                        $this->db->bind(':p2', $losers[1]);
                        $this->db->bind(':round', $nextRound);
                        $this->db->bind(':event_type', $eventType);
                        $this->db->bind(':weight_category', $weightCategory);
                        $this->db->bind(':age_group', $ageGroup);
                        $this->db->bind(':gender', $gender);
                        $this->db->execute();
                    }
                }
            }
            // After handling bronze generation, do not proceed to create more rounds
            return;
        }
    
        // 7. Prepare to create the next round normally (pair winners). But first fetch metadata
        $sql = "SELECT event_type, weight_category, age_group, gender 
                FROM fixtures 
                WHERE round = :round 
                  AND event_type = :event_type 
                  AND weight_category = :weight_category 
                  AND age_group = :age_group 
                  AND gender = :gender
                LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':round', $currentRound);
        $this->db->bind(':event_type', $eventType);
        $this->db->bind(':weight_category', $weightCategory);
        $this->db->bind(':age_group', $ageGroup);
        $this->db->bind(':gender', $gender);
        $meta = $this->db->single();
    
        $nextRound = $currentRound + 1;
    
        // Defensive: do not create next round if it already exists (prevents duplicates)
        $sql = "SELECT COUNT(*) AS cnt FROM fixtures
                WHERE round = :round
                  AND event_type = :event_type
                  AND weight_category = :weight_category
                  AND age_group = :age_group
                  AND gender = :gender";
        $this->db->query($sql);
        $this->db->bind(':round', $nextRound);
        $this->db->bind(':event_type', $eventType);
        $this->db->bind(':weight_category', $weightCategory);
        $this->db->bind(':age_group', $ageGroup);
        $this->db->bind(':gender', $gender);
        $exists = $this->db->single();
        if (!empty($exists['cnt'])) {
            // next round already created — nothing to do
            return;
        }
    
        // Decide whether the next round will be the Final (gold)
        // If pairing the current winners yields exactly ONE fixture, that fixture is the Final.
        $pairCount = (int) ceil(count($winnerIds) / 2);
        $isNextRoundFinal = ($pairCount === 1);
    
        // 8. Insert next round fixtures (set medal_type='gold' if this will be the final)
        for ($i = 0; $i < count($winnerIds); $i += 2) {
            $p1 = $winnerIds[$i];
            $p2 = $winnerIds[$i + 1] ?? null;
    
            if ($isNextRoundFinal) {
                // Create final with medal_type 'gold'
                $sql = "INSERT INTO fixtures (
                            participant1_id, participant2_id, round, 
                            event_type, weight_category, age_group, gender, medal_type
                        ) VALUES (
                            :p1, :p2, :round, :event_type, :weight_category, :age_group, :gender, 'gold'
                        )";
            } else {
                // Normal intermediate round (no medal_type)
                $sql = "INSERT INTO fixtures (
                            participant1_id, participant2_id, round, 
                            event_type, weight_category, age_group, gender
                        ) VALUES (
                            :p1, :p2, :round, :event_type, :weight_category, :age_group, :gender
                        )";
            }
    
            $this->db->query($sql);
            $this->db->bind(':p1', $p1);
            $this->db->bind(':p2', $p2);
            $this->db->bind(':round', $nextRound);
            // Use meta values to avoid accidental mismatch
            $this->db->bind(':event_type', $meta['event_type']);
            $this->db->bind(':weight_category', $meta['weight_category']);
            $this->db->bind(':age_group', $meta['age_group']);
            $this->db->bind(':gender', $meta['gender']);
            $this->db->execute();
        }
    
        // done
    }

    public function updateScores($fixtureId, $score1, $score2, $winnerId = null)
    {
        // Use the passed winnerId directly, no need to calculate
        $sql = "UPDATE fixtures 
                SET score_a = :score1, score_b = :score2, winner_id = :winner_id
                WHERE id = :id";
    
        $this->db->query($sql);
        $this->db->bind(':score1', $score1);
        $this->db->bind(':score2', $score2);
        $this->db->bind(':winner_id', $winnerId);
        $this->db->bind(':id', $fixtureId);
        $this->db->execute();
    }

    public function getMedalWinners()
{
    // results table is authoritative for medals (saveResult writes here)
    $sql = "SELECT r.id, r.participant_id, p.name AS winner_name, r.event_type, r.weight_category, r.age_group, r.gender, r.medal
            FROM results r
            LEFT JOIN participants p ON r.participant_id = p.id
            ORDER BY r.event_type, r.weight_category, FIELD(r.medal, 'gold', 'silver', 'bronze')";
    $this->db->query($sql);
    return $this->db->resultSet();
}

    
    public function updateScoresAndWinner($fixture_id, $scoreA, $scoreB, $winner_id) {
    // 1. Update the fixture score and winner
    $sql = "UPDATE fixtures
            SET score_a = :score_a,
                score_b = :score_b,
                winner_id = :winner_id,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id";
    $this->db->query($sql);
    $this->db->bind(':score_a', $scoreA);
    $this->db->bind(':score_b', $scoreB);
    $this->db->bind(':winner_id', $winner_id);
    $this->db->bind(':id', $fixture_id);
    $this->db->execute();

    // 2. Fetch match info (fresh)
    $sql = "SELECT * FROM fixtures WHERE id = :id";
    $this->db->query($sql);
    $this->db->bind(':id', $fixture_id);
    $match = $this->db->single();

    if (!$match) {
        return;
    }

    $currentRound   = (int) $match['round'];
    $eventType      = $match['event_type'];
    $weightCategory = $match['weight_category'];
    $ageGroup       = $match['age_group'];
    $gender         = $match['gender'];

    // 3. Count remaining matches in this exact round (use round =)
    $sql = "SELECT COUNT(*) AS remaining
            FROM fixtures
            WHERE event_type = :event_type
              AND weight_category = :weight_category
              AND age_group = :age_group
              AND gender = :gender
              AND round = :round
              AND winner_id IS NULL";
    $this->db->query($sql);
    $this->db->bind(':event_type', $eventType);
    $this->db->bind(':weight_category', $weightCategory);
    $this->db->bind(':age_group', $ageGroup);
    $this->db->bind(':gender', $gender);
    $this->db->bind(':round', $currentRound);
    $remaining = $this->db->single();

    // 4. If this fixture itself is explicitly a bronze match — handle and return
    if (($match['medal_type'] ?? '') === 'bronze') {
        if (!empty($winner_id)) {
            // Winner of bronze match gets Bronze 1
            $this->saveResult($winner_id, $eventType, $weightCategory, $ageGroup, $gender, 'bronze');
    
            // Loser of bronze match gets Bronze 2
            $loserId = ($winner_id == $match['participant1_id']) 
                       ? $match['participant2_id'] 
                       : $match['participant1_id'];
    
            if (!empty($loserId)) {
                $this->saveResult($loserId, $eventType, $weightCategory, $ageGroup, $gender, 'bronze2');
            }
        }
        return;
    }


    // 5. Determine whether this round is the final round.
    // Get max round
    $sql = "SELECT MAX(round) AS max_round FROM fixtures
            WHERE event_type = :event_type 
              AND weight_category = :weight_category 
              AND age_group = :age_group 
              AND gender = :gender";
    $this->db->query($sql);
    $this->db->bind(':event_type', $eventType);
    $this->db->bind(':weight_category', $weightCategory);
    $this->db->bind(':age_group', $ageGroup);
    $this->db->bind(':gender', $gender);
    $maxRow = $this->db->single();
    $maxRound = (int)($maxRow['max_round'] ?? 0);

    // Count fixtures in current round
    $sql = "SELECT COUNT(*) AS cnt FROM fixtures
            WHERE event_type = :event_type
              AND weight_category = :weight_category
              AND age_group = :age_group
              AND gender = :gender
              AND round = :round";
    $this->db->query($sql);
    $this->db->bind(':event_type', $eventType);
    $this->db->bind(':weight_category', $weightCategory);
    $this->db->bind(':age_group', $ageGroup);
    $this->db->bind(':gender', $gender);
    $this->db->bind(':round', $currentRound);
    $cntRow = $this->db->single();
    $fixturesCountInRound = (int)($cntRow['cnt'] ?? 0);

    // Check if any fixture in this round already had medal_type = 'gold'
    $sql = "SELECT id FROM fixtures
            WHERE event_type = :event_type
              AND weight_category = :weight_category
              AND age_group = :age_group
              AND gender = :gender
              AND round = :round
              AND medal_type = 'gold'
            LIMIT 1";
    $this->db->query($sql);
    $this->db->bind(':event_type', $eventType);
    $this->db->bind(':weight_category', $weightCategory);
    $this->db->bind(':age_group', $ageGroup);
    $this->db->bind(':gender', $gender);
    $this->db->bind(':round', $currentRound);
    $goldFixtureRow = $this->db->single();
    $thisRoundHasGold = !empty($goldFixtureRow['id']);
    $goldFixtureId = $goldFixtureRow['id'] ?? null;

    // A round is considered final if it's the highest round AND (it has exactly one fixture OR it explicitly contains a gold fixture)
    $isFinalRound = ($currentRound === $maxRound) && ($fixturesCountInRound === 1 || $thisRoundHasGold);

    // 6. Final handling: award gold & silver only when this round is final and all matches are completed
    if ($isFinalRound && $remaining['remaining'] == 0) {

        // Determine the actual gold fixture row we should use
        if (($match['medal_type'] ?? '') === 'gold') {
            $goldMatch = $match;
            $goldMatchId = $fixture_id;
        } elseif ($thisRoundHasGold && $goldFixtureId) {
            // fetch that gold row
            $sql = "SELECT * FROM fixtures WHERE id = :id LIMIT 1";
            $this->db->query($sql);
            $this->db->bind(':id', $goldFixtureId);
            $goldMatch = $this->db->single();
            $goldMatchId = $goldFixtureId;
        } elseif ($fixturesCountInRound === 1) {
            // only one fixture in this round -> current match is the gold match
            $goldMatch = $match;
            $goldMatchId = $fixture_id;
        } else {
            // no clear gold match found; bail defensively
            $goldMatch = null;
            $goldMatchId = null;
        }

        if (!empty($goldMatch) && !empty($goldMatch['winner_id'])) {
            $goldWinnerId = $goldMatch['winner_id'];
            $goldLoserId = ($goldWinnerId == $goldMatch['participant1_id']) ? $goldMatch['participant2_id'] : $goldMatch['participant1_id'];

            // Save Gold & Silver
            $this->saveResult($goldWinnerId, $eventType, $weightCategory, $ageGroup, $gender, 'gold');
            if (!empty($goldLoserId)) {
                $this->saveResult($goldLoserId, $eventType, $weightCategory, $ageGroup, $gender, 'silver');
            }

            // Ensure the gold fixture row is tagged correctly (update the correct id)
            if (!empty($goldMatchId)) {
                $sql = "UPDATE fixtures SET medal_type = 'gold' WHERE id = :id";
                $this->db->query($sql);
                $this->db->bind(':id', $goldMatchId);
                $this->db->execute();
            }

            // Bronze handling: look at semifinals (same as earlier)
            $semiFinalRound = $currentRound - 1;
            $sql = "SELECT * FROM fixtures
                    WHERE round = :round
                      AND event_type = :event_type
                      AND weight_category = :weight_category
                      AND age_group = :age_group
                      AND gender = :gender";
            $this->db->query($sql);
            $this->db->bind(':round', $semiFinalRound);
            $this->db->bind(':event_type', $eventType);
            $this->db->bind(':weight_category', $weightCategory);
            $this->db->bind(':age_group', $ageGroup);
            $this->db->bind(':gender', $gender);
            $semis = $this->db->resultSet();

            // Collect semifinal losers
            $losers = [];
            foreach ($semis as $semi) {
                if (!empty($semi['winner_id'])) {
                    $loser = ($semi['winner_id'] == $semi['participant1_id'])
                             ? $semi['participant2_id']
                             : $semi['participant1_id'];
                    if (!empty($loser)) {
                        $losers[] = $loser;
                    }
                }
            }

            // Bronze handling: either auto-award or create bronze fixture (avoid duplicates)
            if (count($losers) === 1) {
                // Only one loser → auto bronze
                $this->saveResult($losers[0], $eventType, $weightCategory, $ageGroup, $gender, 'bronze');
            } elseif (count($losers) === 2) {
                $nextRound = $currentRound + 1;

                // Avoid creating duplicate bronze fixture: quick check for an existing bronze in next round
                $sql = "SELECT COUNT(*) AS cnt FROM fixtures
                        WHERE round = :round
                          AND event_type = :event_type
                          AND weight_category = :weight_category
                          AND age_group = :age_group
                          AND gender = :gender
                          AND medal_type = 'bronze'";
                $this->db->query($sql);
                $this->db->bind(':round', $nextRound);
                $this->db->bind(':event_type', $eventType);
                $this->db->bind(':weight_category', $weightCategory);
                $this->db->bind(':age_group', $ageGroup);
                $this->db->bind(':gender', $gender);
                $already = $this->db->single();

                if (empty($already['cnt'])) {
                    $sql = "INSERT INTO fixtures (
                                participant1_id, participant2_id, round,
                                event_type, weight_category, age_group, gender, medal_type
                            ) VALUES (
                                :p1, :p2, :round, :event_type, :weight_category, :age_group, :gender, 'bronze'
                            )";
                    $this->db->query($sql);
                    $this->db->bind(':p1', $losers[0]);
                    $this->db->bind(':p2', $losers[1]);
                    $this->db->bind(':round', $nextRound);
                    $this->db->bind(':event_type', $eventType);
                    $this->db->bind(':weight_category', $weightCategory);
                    $this->db->bind(':age_group', $ageGroup);
                    $this->db->bind(':gender', $gender);
                    $this->db->execute();
                }
            }
        }
    }

    // done
}



/**
 * Insert or update medal result in results table
 */
    private function saveResult($participantId, $eventType, $weightCategory, $ageGroup, $gender, $medal) {
    // Fetch participant district
    $sql = "SELECT district FROM participants WHERE id = :pid LIMIT 1";
    $this->db->query($sql);
    $this->db->bind(':pid', $participantId);
    $participant = $this->db->single();
    $district = $participant['district'] ?? null;

    // Check if result already exists for this participant + event
    $sql = "SELECT id FROM results
            WHERE participant_id = :pid
              AND event_type = :event_type
              AND weight_category = :weight_category
              AND age_group = :age_group
              AND gender = :gender";
    $this->db->query($sql);
    $this->db->bind(':pid', $participantId);
    $this->db->bind(':event_type', $eventType);
    $this->db->bind(':weight_category', $weightCategory);
    $this->db->bind(':age_group', $ageGroup);
    $this->db->bind(':gender', $gender);
    $existing = $this->db->single();

    if ($existing) {
        // Update medal + district if already exists
        $sql = "UPDATE results 
                SET medal = :medal, district = :district, updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':medal', $medal);
        $this->db->bind(':district', $district);
        $this->db->bind(':id', $existing['id']);
        $this->db->execute();
    } else {
        // Insert new row with district
        $sql = "INSERT INTO results (
                    participant_id, medal, event_type, weight_category, age_group, gender, district
                ) VALUES (
                    :pid, :medal, :event_type, :weight_category, :age_group, :gender, :district
                )";
        $this->db->query($sql);
        $this->db->bind(':pid', $participantId);
        $this->db->bind(':medal', $medal);
        $this->db->bind(':event_type', $eventType);
        $this->db->bind(':weight_category', $weightCategory);
        $this->db->bind(':age_group', $ageGroup);
        $this->db->bind(':gender', $gender);
        $this->db->bind(':district', $district);
        $this->db->execute();
    }
}


    // In FixtureModel.php
    public function getFixtureById($id)
    {
        $sql = "SELECT 
                    f.*, 
                    p1.name AS p1_name, 
                    p1.district AS p1_district, 
                    p2.name AS p2_name, 
                    p2.district AS p2_district
                FROM fixtures f
                LEFT JOIN participants p1 ON f.participant1_id = p1.id
                LEFT JOIN participants p2 ON f.participant2_id = p2.id
                WHERE f.id = :id";
    
        $this->db->query($sql);
        $this->db->bind(':id', $id);
    
        return $this->db->single();  // Returns one fixture with both participants' names and districts
    }




        public function exportFixturesWithScores($fixtures)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(['ID', 'Participant 1', 'Participant 2', 'Score1', 'Score2', 'Winner', 'Round'], null, 'A1');
    
        $row = 2;
        foreach ($fixtures as $fixture) {
            // Access score_a and score_b instead of score1 and score2
            $score1 = isset($fixture['score_a']) && is_numeric($fixture['score_a']) ? $fixture['score_a'] : 0;
            $score2 = isset($fixture['score_b']) && is_numeric($fixture['score_b']) ? $fixture['score_b'] : 0;
            $winnerId = isset($fixture['winner_id']) ? $fixture['winner_id'] : 'N/A';
    
            $sheet->setCellValue('A' . $row, $fixture['id']);
            $sheet->setCellValue('B' . $row, $fixture['p1_name']);
            $sheet->setCellValue('C' . $row, $fixture['p2_name']);
            $sheet->setCellValue('D' . $row, $score1); // Now using score_a
            $sheet->setCellValue('E' . $row, $score2); // Now using score_b
            $sheet->setCellValue('F' . $row, $winnerId);
            $sheet->setCellValue('G' . $row, $fixture['round']);
            $row++;
        }
    
        // Set the filename and headers as before
        $filename = 'fixtures_with_scores_' . date('Y-m-d_H-i-s') . '.xlsx'; 
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
    
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }


    public function getMedalWinnersByCategory($eventType, $weightCategory, $ageGroup, $gender)
    {
        $sql = "SELECT r.id, r.participant_id, p.name as winner_name, 
                       r.event_type, r.weight_category, r.age_group, r.gender, 
                       r.medal, r.district
                FROM results r
                JOIN participants p ON r.participant_id = p.id
                WHERE r.event_type = :event_type
                  AND r.weight_category = :weight_category
                  AND r.age_group = :age_group
                  AND r.gender = :gender
                ORDER BY FIELD(r.medal, 'Gold', 'Silver', 'Bronze', 'Bronze2')";
        
        $this->db->query($sql);
        $this->db->bind(':event_type', $eventType);
        $this->db->bind(':weight_category', $weightCategory);
        $this->db->bind(':age_group', $ageGroup);
        $this->db->bind(':gender', $gender);
        return $this->db->resultSet();
    }




    
    // Method to export winners
    public function exportWinners($winners, $eventType, $weightCategory, $ageGroup, $gender)
{
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Title row (merged for clarity)
    $title = "$eventType - $weightCategory - $ageGroup - $gender Winners";
    $sheet->mergeCells('A1:E1');
    $sheet->setCellValue('A1', $title);
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

    // Column Headers (added District as column E)
    $sheet->fromArray(['Result ID', 'Winner Name', 'Medal', 'Participant ID', 'District'], null, 'A2');
    
    $row = 3;
    foreach ($winners as $winner) {
        $sheet->setCellValue('A' . $row, $winner['id']);
        $sheet->setCellValue('B' . $row, $winner['winner_name']);
        $sheet->setCellValue('C' . $row, ucfirst($winner['medal']));
        $sheet->setCellValue('D' . $row, $winner['participant_id']);
        $sheet->setCellValue('E' . $row, $winner['district'] ?? '');
        $row++;
    }

    $filename = 'winners_' . preg_replace('/\s+/', '_', strtolower($eventType . '_' . $weightCategory . '_' . $ageGroup . '_' . $gender)) . '_' . date('Y-m-d_H-i-s') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment;filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}


    public function lockFixture($fixtureId)
    {
        $sql = "UPDATE fixtures SET is_locked = 1 WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $fixtureId);
        return $this->db->execute(); // boolean
    }
    
    public function isFixtureLocked($fixtureId)
    {
        $sql = "SELECT is_locked FROM fixtures WHERE id = :id LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $fixtureId);
        $row = $this->db->single(); // returns assoc array or false/null if none
    
        if (!$row) {
            return false;
        }
    
        // normalize integer/boolean to boolean
        return (bool) $row['is_locked'];
    }
    
    public function getAllResults()
{
    $sql = "SELECT r.id, r.participant_id, p.name as winner_name, 
                   r.event_type, r.weight_category, r.age_group, r.gender, 
                   r.medal, r.district
            FROM results r
            JOIN participants p ON r.participant_id = p.id
            ORDER BY r.event_type, r.gender, r.age_group, r.weight_category,
                     FIELD(r.medal, 'Gold', 'Silver', 'Bronze', 'Bronze2')";
    
    $this->db->query($sql);
    return $this->db->resultSet();
}

    
    public function exportAllResults($results)
{
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('All Results');

    $row = 1;

    // Group results by event_type, gender, age_group, weight_category
    $grouped = [];
    foreach ($results as $res) {
        $key = $res['event_type'] . ' | ' . $res['gender'] . ' | ' . $res['age_group'] . ' | ' . $res['weight_category'];
        $grouped[$key][] = $res;
    }

    foreach ($grouped as $groupKey => $winners) {
        // Add group title row
        $sheet->mergeCells("A{$row}:E{$row}");
        $sheet->setCellValue("A{$row}", $groupKey . " Winners");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(13);
        $row++;

        // Column headers
        $sheet->fromArray(['Result ID', 'Winner Name', 'Medal', 'Participant ID', 'District'], null, 'A' . $row);
        $sheet->getStyle("A{$row}:E{$row}")->getFont()->setBold(true);
        $row++;

        // Winners under this group
        foreach ($winners as $winner) {
            $sheet->setCellValue('A' . $row, $winner['id']);
            $sheet->setCellValue('B' . $row, $winner['winner_name']);
            $sheet->setCellValue('C' . $row, ucfirst($winner['medal']));
            $sheet->setCellValue('D' . $row, $winner['participant_id']);
            $sheet->setCellValue('E' . $row, $winner['district'] ?? '');
            $row++;
        }

        $row++; // empty row between groups
    }

    $filename = 'all_results_' . date('Y-m-d_H-i-s') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment;filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}


}
?>
