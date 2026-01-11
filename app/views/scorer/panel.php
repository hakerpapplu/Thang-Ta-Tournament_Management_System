<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Scoring Panel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Custom animations and utilities */
    @keyframes pulse-glow {
      0%, 100% { box-shadow: 0 0 5px rgba(59, 130, 246, 0.5); }
      50% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.8); }
    }
    
    @keyframes score-pop {
      0% { transform: scale(1); }
      50% { transform: scale(1.1); }
      100% { transform: scale(1); }
    }
    
    .score-btn-active {
      animation: pulse-glow 0.5s ease-in-out;
    }
    
    .score-animate {
      animation: score-pop 0.3s ease-in-out;
    }
    
    .glass-effect {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .corner-gradient-red {
      background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    }
    
    .corner-gradient-blue {
      background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    }
    
    .scoring-area {
      background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    }
    
    /* Improved focus states for accessibility */
    .score-btn:focus {
      outline: 3px solid #3b82f6;
      outline-offset: 2px;
    }
    
    .foul-input:focus {
      outline: 2px solid #ef4444;
      outline-offset: 1px;
    }
    
    /* Status indicators */
    .status-completed {
      background: linear-gradient(135deg, #10b981, #059669);
      color: white;
      padding: 2px 8px;
      border-radius: 12px;
      font-size: 0.75rem;
      font-weight: 600;
    }
    
    .status-active {
      background: linear-gradient(135deg, #3b82f6, #2563eb);
      color: white;
      padding: 2px 8px;
      border-radius: 12px;
      font-size: 0.75rem;
      font-weight: 600;
    }
    
    /* Enhanced table styling */
    .history-table {
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .history-row:hover {
      transform: translateY(-1px);
      transition: transform 0.2s ease;
    }
  </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-blue-50 text-gray-900 min-h-screen py-6 px-4 sm:px-6 lg:px-8">

  <div class="max-w-5xl mx-auto">
    <!-- Main Panel -->
    <div class="glass-effect rounded-3xl shadow-2xl p-6 sm:p-8 space-y-8">

      <!-- Header Section -->
      <header class="text-center space-y-4">
        <h1 class="text-4xl sm:text-5xl font-black bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent tracking-tight">
          🎯 SCORING PANEL
        </h1>
        <div class="w-24 h-1 bg-gradient-to-r from-blue-500 to-purple-500 mx-auto rounded-full"></div>
      </header>

      <!-- Fighters Information -->
      <section class="grid grid-cols-1 sm:grid-cols-2 gap-6" aria-label="Fighter Information">
        <div class="corner-gradient-red text-center p-6 rounded-2xl border-2 border-red-200 shadow-lg transform hover:scale-105 transition-transform duration-200">
          <div class="text-3xl mb-2">🥊</div>
          <h2 class="text-xl font-bold text-red-700 mb-2">Red Corner</h2>
          <p class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($fixture['p1_name']) ?></p>
          <p class="text-sm text-gray-600 mt-1"><?= htmlspecialchars($fixture['p1_district'] ?? '') ?></p>
        </div>
        <div class="corner-gradient-blue text-center p-6 rounded-2xl border-2 border-blue-200 shadow-lg transform hover:scale-105 transition-transform duration-200">
          <div class="text-3xl mb-2">🥊</div>
          <h2 class="text-xl font-bold text-blue-700 mb-2">Blue Corner</h2>
          <p class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($fixture['p2_name']) ?></p>
          <p class="text-sm text-gray-600 mt-1"><?= htmlspecialchars($fixture['p2_district'] ?? '') ?></p>
        </div>
      </section>

      <!-- Match Status -->
      <section class="bg-gradient-to-r from-orange-50 to-yellow-50 p-6 rounded-2xl border-2 border-orange-200 shadow-lg" aria-label="Match Status">
        <div class="text-center space-y-3">
          <div class="flex flex-wrap justify-center gap-3 text-sm">
            <?php foreach ($roundStatuses as $row): ?>
              <span class="flex items-center space-x-2">
                <strong class="text-gray-700">Round <?= $row['round_number'] ?></strong>
                <span class="<?= $row['status'] === 'completed' ? 'status-completed' : 'status-active' ?>">
                  <?= ucfirst($row['status']) ?>
                </span>
              </span>
            <?php endforeach; ?>
          </div>
          <div class="flex justify-center items-center space-x-4">
            <div class="bg-white px-4 py-2 rounded-lg shadow-sm border">
              <span class="text-gray-600 text-sm">Your Corner:</span>
              <strong class="text-lg font-bold uppercase ml-2 <?= $corner === 'red' ? 'text-red-600' : 'text-blue-600' ?>">
                <?= $corner ?>
              </strong>
            </div>
            <div class="bg-white px-4 py-2 rounded-lg shadow-sm border">
              <span class="text-gray-600 text-sm">Sub-round:</span>
              <strong class="text-2xl font-bold text-indigo-700 ml-2">
                <?= $_SESSION['sub_round'][$fixture['id']] ?? 1 ?>
              </strong>
            </div>
          </div>
        </div>
      </section>

      <!-- Scoring Interface -->
      <section class="scoring-area p-8 rounded-2xl shadow-inner border-2 border-gray-200" aria-label="Scoring Interface">
        <h2 class="text-center text-2xl font-bold mb-8 text-gray-800">Score Input</h2>

        <!-- Score Buttons -->
        <div class="flex justify-center gap-8 mb-8">
          <button type="button" 
                  class="score-btn bg-gradient-to-br from-gray-800 to-gray-900 hover:from-black hover:to-gray-800 text-white text-3xl font-bold w-20 h-20 rounded-full shadow-xl transition-all duration-200 transform hover:scale-110 active:scale-95" 
                  data-value="1"
                  aria-label="Award 1 point">
            1
          </button>
          <button type="button" 
                  class="score-btn bg-gradient-to-br from-gray-800 to-gray-900 hover:from-black hover:to-gray-800 text-white text-3xl font-bold w-20 h-20 rounded-full shadow-xl transition-all duration-200 transform hover:scale-110 active:scale-95" 
                  data-value="2"
                  aria-label="Award 2 points">
            2
          </button>
        </div>

        <!-- Score Display -->
        <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-8 mb-8">
          <div class="bg-white px-6 py-4 rounded-2xl shadow-lg border-2 border-gray-200 min-w-[200px] text-center">
            <div class="text-sm font-semibold text-gray-600 mb-1">Selected Points</div>
            <div id="score-list" class="text-red-600 text-2xl font-bold tracking-wider min-h-[2rem]">—</div>
          </div>
          <div class="bg-gradient-to-br from-blue-500 to-blue-600 px-6 py-4 rounded-2xl shadow-lg text-white min-w-[120px] text-center">
            <div class="text-sm font-semibold mb-1">Total</div>
            <div id="score-total" class="text-3xl font-bold">0</div>
          </div>
        </div>

        <!-- Clear Button -->
        <div class="text-center mb-8">
          <button type="button" 
                  id="clear-scores" 
                  class="bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-semibold px-6 py-2 rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105">
            🔄 Clear Scores
          </button>
        </div>

        <!-- Fouls Section -->
        <div class="bg-red-50 p-6 rounded-2xl border-2 border-red-200 mb-8">
          <h3 class="text-lg font-bold text-red-700 mb-4 text-center">⚠️ Fouls (-3 points each)</h3>
          <div class="flex flex-wrap justify-center gap-4">
            <label class="flex items-center space-x-3 bg-white p-3 rounded-lg shadow-sm border border-red-200 cursor-pointer hover:bg-red-50 transition-colors">
              <input type="checkbox" name="fouls[]" value="Cheibi Tare" class="foul-input accent-red-500 w-4 h-4">
              <span class="font-medium text-gray-700">Cheibi Tare</span>
            </label>
            <label class="flex items-center space-x-3 bg-white p-3 rounded-lg shadow-sm border border-red-200 cursor-pointer hover:bg-red-50 transition-colors">
              <input type="checkbox" name="fouls[]" value="Chengoi Tare" class="foul-input accent-red-500 w-4 h-4">
              <span class="font-medium text-gray-700">Chengoi Tare</span>
            </label>
            <label class="flex items-center space-x-3 bg-white p-3 rounded-lg shadow-sm border border-red-200 cursor-pointer hover:bg-red-50 transition-colors">
              <input type="checkbox" name="fouls[]" value="General Foul" class="foul-input accent-red-500 w-4 h-4">
              <span class="font-medium text-gray-700">General Foul</span>
            </label>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="text-center">
          <form id="score-form" method="POST" action="/scorer/submit-score" class="space-y-4">
            <input type="hidden" name="fixture_id" value="<?= $fixture['id'] ?>">
            <input type="hidden" name="round" value="<?= $currentRound['round_number'] ?? 1 ?>">
            <input type="hidden" name="corner" value="<?= $corner ?>">
            <input type="hidden" name="scorer_id" value="<?= $_SESSION['scorer_info'][$fixture['id']]['scorer_id'] ?>">
            <input type="hidden" name="score" id="hidden-score" value="0">
            <input type="hidden" name="selected_fouls" id="hidden-fouls" value="">
            <button type="submit" 
                    class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold px-10 py-4 rounded-2xl shadow-xl transition-all duration-200 transform hover:scale-105 active:scale-95">
              ✅ Submit Scores
            </button>
          </form>
        </div>
      </section>
    </div>

    <!-- History Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
      <!-- Score History -->
      <div class="glass-effect rounded-2xl shadow-xl p-6">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
          <span class="text-3xl mr-3">📊</span>
          Score History
        </h2>
        <div class="overflow-x-auto">
          <table class="w-full text-sm history-table rounded-lg overflow-hidden">
            <thead class="bg-gradient-to-r from-blue-500 to-blue-600 text-white">
              <tr>
                <th class="p-4 text-left font-semibold">Sub-Round</th>
                <th class="p-4 text-left font-semibold">Score</th>
                <th class="p-4 text-left font-semibold">Time</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <?php if (empty($scores)): ?>
                <tr>
                  <td colspan="3" class="p-4 text-center text-gray-500 italic">No scores recorded yet</td>
                </tr>
              <?php else: ?>
                <?php foreach ($scores as $s): ?>
                  <tr class="history-row hover:bg-blue-50">
                    <td class="p-4 font-medium"><?= $s['sub_round'] ?></td>
                    <td class="p-4">
                      <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full font-semibold">
                        <?= $s['score'] ?>
                      </span>
                    </td>
                    <td class="p-4 text-gray-600"><?= date('h:i A', strtotime($s['created_at'])) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Foul History -->
      <div class="glass-effect rounded-2xl shadow-xl p-6">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
          <span class="text-3xl mr-3">🚫</span>
          Foul History
        </h2>
        <div class="overflow-x-auto">
          <table class="w-full text-sm history-table rounded-lg overflow-hidden">
            <thead class="bg-gradient-to-r from-red-500 to-red-600 text-white">
              <tr>
                <th class="p-4 text-left font-semibold">Sub-Round</th>
                <th class="p-4 text-left font-semibold">Type</th>
                <th class="p-4 text-left font-semibold">Time</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <?php if (empty($fouls)): ?>
                <tr>
                  <td colspan="3" class="p-4 text-center text-gray-500 italic">No fouls recorded</td>
                </tr>
              <?php else: ?>
                <?php foreach ($fouls as $f): ?>
                  <tr class="history-row hover:bg-red-50">
                    <td class="p-4 font-medium"><?= $f['sub_round'] ?></td>
                    <td class="p-4">
                      <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full font-semibold">
                        <?= htmlspecialchars($f['reason']) ?>
                      </span>
                    </td>
                    <td class="p-4 text-gray-600"><?= date('h:i A', strtotime($f['created_at'])) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- End Session -->
    <div class="text-center mt-8">
      <div class="glass-effect rounded-2xl shadow-xl p-6">
        <form method="POST" action="/scorer/end-match">
          <input type="hidden" name="fixture_id" value="<?= $fixture['id'] ?>">
          <input type="hidden" name="event_type" value="<?= $fixture['event_type'] ?>">
          <input type="hidden" name="weight_category" value="<?= $fixture['weight_category'] ?>">
          <input type="hidden" name="age_group" value="<?= $fixture['age_group'] ?>">
          <input type="hidden" name="gender" value="<?= $fixture['gender'] ?>">
          <button type="submit"
                  class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-bold py-4 px-8 rounded-2xl shadow-xl transition-all duration-200 transform hover:scale-105 active:scale-95"
                  onclick="return confirm('Are you sure you want to end this scoring session?')">
            🚪 End Scoring & Exit
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Enhanced JavaScript -->
  <script>
    // Core scoring functionality (unchanged logic)
    let scoreClicks = [];
    const maxClicks = 3;
    const scoreBtns = document.querySelectorAll(".score-btn");
    const scoreList = document.getElementById("score-list");
    const scoreTotal = document.getElementById("score-total");
    const hiddenScore = document.getElementById("hidden-score");
    const hiddenFouls = document.getElementById("hidden-fouls");
    const clearBtn = document.getElementById("clear-scores");

    // Enhanced button interactions
    scoreBtns.forEach(btn => {
      btn.addEventListener("click", () => {
        if (scoreClicks.length < maxClicks) {
          // Add visual feedback
          btn.classList.add("score-btn-active");
          setTimeout(() => btn.classList.remove("score-btn-active"), 500);
          
          scoreClicks.push(parseInt(btn.dataset.value));
          updateDisplay();
          
          // Add animation to total display
          scoreTotal.classList.add("score-animate");
          setTimeout(() => scoreTotal.classList.remove("score-animate"), 300);
        }
      });
    });

    // Clear functionality
    clearBtn.addEventListener("click", () => {
      scoreClicks = [];
      updateDisplay();
      
      // Visual feedback for clearing
      scoreList.textContent = "—";
      scoreTotal.classList.add("score-animate");
      setTimeout(() => scoreTotal.classList.remove("score-animate"), 300);
    });

    function updateDisplay() {
      if (scoreClicks.length === 0) {
        scoreList.textContent = "—";
      } else {
        scoreList.textContent = scoreClicks.join(" + ");
      }
      
      const total = scoreClicks.reduce((a, b) => a + b, 0);
      scoreTotal.textContent = total;
      hiddenScore.value = total;
    }

    // Form submission (unchanged logic)
    document.getElementById("score-form").addEventListener("submit", function (e) {
      const foulCheckboxes = document.querySelectorAll(".foul-input:checked");
      const selectedFouls = Array.from(foulCheckboxes).map(el => el.value);
      hiddenFouls.value = selectedFouls.join(",");
    });

    // Round checking (unchanged logic)
    let lastKnownRound = <?= json_encode($currentRound['round_number']) ?>;
    
    function checkForRoundChange() {
      fetch('/scorer/round-check/<?= $fixture['id'] ?>')
        .then(res => res.json())
        .then(data => {
          const currentRound = parseInt(data.round_number);
          if (currentRound && currentRound !== lastKnownRound) {
            if (confirm(`🔁 Round changed to ${currentRound}. Refresh the panel now?`)) {
              window.location.reload();
            }
          }
        })
        .catch(err => console.error('Round check failed:', err));
    }
    
    setInterval(checkForRoundChange, 3000);

    // Keyboard shortcuts for accessibility
    document.addEventListener('keydown', function(e) {
      if (e.key === '1' && !e.target.matches('input')) {
        e.preventDefault();
        document.querySelector('[data-value="1"]').click();
      } else if (e.key === '2' && !e.target.matches('input')) {
        e.preventDefault();
        document.querySelector('[data-value="2"]').click();
      } else if (e.key === 'Escape') {
        clearBtn.click();
      }
    });
  </script>

</body>
</html>