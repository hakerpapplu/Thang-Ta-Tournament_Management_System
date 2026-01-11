<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Judge Panel - Match Control</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Custom animations and enhancements */
    @keyframes pulse-success {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.02); }
    }
    
    @keyframes slideIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-slide-in { animation: slideIn 0.3s ease-out; }
    .animate-pulse-success { animation: pulse-success 2s ease-in-out infinite; }
    
    .gradient-bg {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .glass-card {
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, 0.95);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .score-card {
      transition: all 0.3s ease;
    }
    
    .score-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    /* Custom scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
      width: 6px;
      height: 6px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 3px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
      background: #c1c1c1;
      border-radius: 3px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
      background: #a1a1a1;
    }
    
    /* Status indicators */
    .status-active { @apply bg-green-100 border-green-300 text-green-800; }
    .status-completed { @apply bg-gray-100 border-gray-300 text-gray-700; }
    .status-pending { @apply bg-yellow-100 border-yellow-300 text-yellow-800; }
  </style>
</head>
<body class="min-h-screen gradient-bg">
  <div class="min-h-screen py-6 px-4 flex items-center justify-center">
    <main class="w-full max-w-6xl glass-card shadow-2xl rounded-3xl overflow-hidden">
      
      <!-- Header Section -->
      <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-8 text-center">
        <h1 class="text-4xl font-bold mb-2 flex items-center justify-center gap-3">
          <span class="text-5xl">🎮</span>
          Judge Panel - Match Control
        </h1>
        <div class="w-24 h-1 bg-white/30 mx-auto rounded-full"></div>
      </div>

      <div class="p-8 space-y-8">
        
        <!-- Match Information Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
          <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
              <span class="text-2xl">ℹ️</span>
              Match Information
            </h2>
          </div>
          <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="space-y-1">
              <p class="text-sm text-gray-500 font-medium">Event</p>
              <p class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($fixture['event_type']) ?></p>
            </div>
            <div class="space-y-1">
              <p class="text-sm text-gray-500 font-medium">Weight Category</p>
              <p class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($fixture['weight_category']) ?></p>
            </div>
            <div class="space-y-1">
              <p class="text-sm text-gray-500 font-medium">Age Group</p>
              <p class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($fixture['age_group']) ?></p>
            </div>
            <div class="space-y-1 md:col-span-2 lg:col-span-1">
              <p class="text-sm text-gray-500 font-medium">Red Corner</p>
              <p class="text-lg font-semibold text-red-600 flex items-center gap-2">
                <span class="w-4 h-4 bg-red-500 rounded-full"></span>
                <?= htmlspecialchars($fixture['p1_name']) ?>
                <span class="text-sm text-gray-500">(<?= htmlspecialchars($fixture['p1_district']) ?>)</span>
              </p>
            </div>
            <div class="space-y-1 md:col-span-2 lg:col-span-1">
              <p class="text-sm text-gray-500 font-medium">Blue Corner</p>
              <p class="text-lg font-semibold text-blue-600 flex items-center gap-2">
                <span class="w-4 h-4 bg-blue-500 rounded-full"></span>
                <?= htmlspecialchars($fixture['p2_name']) ?>
                <span class="text-sm text-gray-500">(<?= htmlspecialchars($fixture['p2_district']) ?>)</span>
              </p>
            </div>
          </div>
        </div>

        <!-- Round Control Section -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
          <div class="bg-gradient-to-r from-yellow-50 to-orange-50 px-6 py-4 border-b">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
              <span class="text-2xl">⏱️</span>
              Round Control
            </h2>
          </div>
          <div class="p-6">
            <!-- Round Status -->
            <div class="mb-6 p-4 rounded-xl border-2 border-dashed <?php echo $round ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-gray-50'; ?>">
              <div class="text-center">
                <?php if ($round): ?>
                  <div class="flex items-center justify-center gap-3 text-lg font-semibold text-green-800">
                    <span class="animate-pulse text-2xl">🟢</span>
                    Round <?= $round['round_number'] ?> - <?= ucfirst($round['status']) ?>
                  </div>
                <?php else: ?>
                  <div class="text-gray-600 font-medium">
                    <span class="text-2xl">⏸️</span>
                    No active round yet
                  </div>
                <?php endif; ?>
              </div>
            </div>

            <!-- Round Control Buttons -->
            <form method="POST" action="/judge/start-round" class="flex flex-wrap gap-4 justify-center">
              <input type="hidden" name="fixture_id" value="<?= $fixture['id'] ?>">
              
              <button name="round" value="2"
                class="bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-semibold px-8 py-3 rounded-xl shadow-lg transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-yellow-300">
                <span class="flex items-center gap-2">
                  <span class="text-xl">2️⃣</span>
                  Start Round 2
                </span>
              </button>
              
              <button name="round" value="3"
                class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-semibold px-8 py-3 rounded-xl shadow-lg transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-orange-300">
                <span class="flex items-center gap-2">
                  <span class="text-xl">3️⃣</span>
                  Start Round 3 (Tie-break)
                </span>
              </button>
            </form>
          </div>
        </div>

        <!-- Live Averages Section -->
        <div id="live-averages" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div id="avg-red" class="score-card bg-gradient-to-br from-red-500 to-red-600 text-white p-8 rounded-2xl shadow-xl text-center">
            <div class="text-3xl font-bold mb-2">🔴 Red Corner</div>
            <div class="text-5xl font-black">--</div>
            <div class="text-red-100 font-medium mt-2">Average Score</div>
          </div>
          <div id="avg-blue" class="score-card bg-gradient-to-br from-blue-500 to-blue-600 text-white p-8 rounded-2xl shadow-xl text-center">
            <div class="text-3xl font-bold mb-2">🔵 Blue Corner</div>
            <div class="text-5xl font-black">--</div>
            <div class="text-blue-100 font-medium mt-2">Average Score</div>
          </div>
        </div>

        <!-- Round-wise Totals -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
          <div class="bg-gradient-to-r from-purple-50 to-indigo-50 px-6 py-4 border-b">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
              <span class="text-2xl">📊</span>
              Round-wise Totals
            </h2>
          </div>
          <div class="p-6 space-y-4">
            <?php foreach ($roundScores as $roundNum => $score): ?>
              <div class="animate-slide-in bg-gradient-to-r from-gray-50 to-gray-100 border-l-4 <?= ($round && $round['round_number'] == $roundNum && $round['is_active']) ? 'border-green-500' : 'border-gray-400' ?> p-5 rounded-r-xl">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <span class="text-2xl">
                      <?= ($round && $round['round_number'] == $roundNum && $round['is_active']) ? "🟢" : "✅" ?>
                    </span>
                    <div>
                      <div class="font-bold text-lg text-gray-800">Round <?= $roundNum ?></div>
                      <div class="text-sm text-gray-600">
                        <?= ($round && $round['round_number'] == $roundNum && $round['is_active']) ? "In Progress" : "Completed" ?>
                      </div>
                    </div>
                  </div>
                  <div class="flex items-center gap-8">
                    <div class="text-center">
                      <div class="text-2xl font-bold text-red-600"><?= $score['red'] ?></div>
                      <div class="text-xs text-red-500 font-medium">RED</div>
                    </div>
                    <div class="text-center">
                      <div class="text-2xl font-bold text-blue-600"><?= $score['blue'] ?></div>
                      <div class="text-xs text-blue-500 font-medium">BLUE</div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Scores Table -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
          <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
              <span class="text-2xl">📈</span>
              Detailed Scores
            </h2>
          </div>
          <div class="overflow-x-auto custom-scrollbar">
            <table id="score-table" class="w-full">
              <thead class="bg-gray-50">
                <tr class="text-left">
                  <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Judge</th>
                  <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Corner</th>
                  <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Round</th>
                  <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Sub-Round</th>
                  <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Score</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <?php foreach ($scores as $index => $score): ?>
                  <tr class="<?= $index % 2 === 0 ? 'bg-white' : 'bg-gray-50/50' ?> hover:bg-blue-50/50 transition-colors duration-150">
                    <td class="px-6 py-4 text-sm font-medium text-gray-800"><?= htmlspecialchars($score['scorer_name']) ?></td>
                    <td class="px-6 py-4 text-sm">
                      <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold
                        <?= $score['corner'] === 'red' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' ?>">
                        <span class="w-2 h-2 rounded-full <?= $score['corner'] === 'red' ? 'bg-red-500' : 'bg-blue-500' ?>"></span>
                        <?= ucfirst($score['corner']) ?>
                      </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700"><?= $score['round'] ?></td>
                    <td class="px-6 py-4 text-sm text-gray-700"><?= $score['sub_round'] ?></td>
                    <td class="px-6 py-4 text-sm font-bold text-gray-900"><?= $score['score'] ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Fouls Table -->
        <div class="bg-white rounded-2xl shadow-lg border border-red-100 overflow-hidden">
          <div class="bg-gradient-to-r from-red-50 to-pink-50 px-6 py-4 border-b">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
              <span class="text-2xl">🚫</span>
              Fouls & Violations
            </h2>
          </div>
          <div class="overflow-x-auto custom-scrollbar">
            <table id="foul-table" class="w-full">
              <thead class="bg-red-50">
                <tr class="text-left">
                  <th class="px-6 py-4 text-xs font-semibold text-red-700 uppercase tracking-wider">Judge</th>
                  <th class="px-6 py-4 text-xs font-semibold text-red-700 uppercase tracking-wider">Corner</th>
                  <th class="px-6 py-4 text-xs font-semibold text-red-700 uppercase tracking-wider">Round</th>
                  <th class="px-6 py-4 text-xs font-semibold text-red-700 uppercase tracking-wider">Sub-Round</th>
                  <th class="px-6 py-4 text-xs font-semibold text-red-700 uppercase tracking-wider">Reason</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-red-100">
                <?php foreach ($fouls as $index => $foul): ?>
                  <tr class="<?= $index % 2 === 0 ? 'bg-white' : 'bg-red-50/30' ?> hover:bg-red-50 transition-colors duration-150">
                    <td class="px-6 py-4 text-sm font-medium text-gray-800"><?= htmlspecialchars($foul['scorer_name']) ?></td>
                    <td class="px-6 py-4 text-sm">
                      <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold
                        <?= $foul['corner'] === 'red' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' ?>">
                        <span class="w-2 h-2 rounded-full <?= $foul['corner'] === 'red' ? 'bg-red-500' : 'bg-blue-500' ?>"></span>
                        <?= ucfirst($foul['corner']) ?>
                      </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700"><?= $foul['round'] ?></td>
                    <td class="px-6 py-4 text-sm text-gray-700"><?= $foul['sub_round'] ?></td>
                    <td class="px-6 py-4 text-sm text-gray-800"><?= htmlspecialchars($foul['reason']) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Winner Selection Section -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
          <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
              <span class="text-2xl">🏆</span>
              Match Result
            </h2>
          </div>
          <div class="p-6">
            <?php if ($fixture['winner_id']) : ?>
              <div class="animate-pulse-success bg-gradient-to-r from-green-100 to-emerald-100 border-2 border-green-300 p-6 rounded-2xl text-center">
                <div class="text-6xl mb-4">🏆</div>
                <div class="text-2xl font-bold text-green-800 mb-2">Match Winner</div>
                <div class="text-3xl font-black text-green-900">
                  <?= $fixture['winner_id'] == $fixture['participant1_id'] 
                    ? htmlspecialchars($fixture['p1_name']) 
                    : htmlspecialchars($fixture['p2_name']) ?>
                </div>
                <div class="text-sm text-green-700 mt-2 font-medium">Match has been finalized</div>
              </div>
            <?php else: ?>
              <form method="POST" action="/judge/finalize" class="space-y-6">
                
                <input type="hidden" name="fixture_id" value="<?= $fixture['id'] ?>">
                <input type="hidden" name="event_type" value="<?= $fixture['event_type'] ?>">
                <input type="hidden" name="weight_category" value="<?= $fixture['weight_category'] ?>">
                <input type="hidden" name="age_group" value="<?= $fixture['age_group'] ?>">
                <input type="hidden" name="gender" value="<?= $fixture['gender'] ?>">
                
                <div class="text-center mb-6">
                  <p class="text-lg text-gray-600 mb-4">Select the match winner to finalize the result</p>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    
                    <label class="group cursor-pointer">
                      <input type="radio" name="winner_id" value="<?= $fixture['participant1_id'] ?>" required class="sr-only">
                      <div class="bg-gradient-to-br from-red-50 to-red-100 border-3 border-red-200 group-hover:border-red-400 group-hover:shadow-lg rounded-2xl p-6 text-center transition-all duration-200 transform group-hover:scale-105">
                        <div class="text-4xl mb-3">🥊</div>
                        <div class="text-red-600 font-bold text-xl mb-2">Red Corner</div>
                        <div class="text-gray-800 font-semibold text-lg"><?= htmlspecialchars($fixture['p1_name']) ?></div>
                        <div class="w-full h-4 bg-red-500 rounded-full mt-4 opacity-20 group-hover:opacity-60 transition-opacity"></div>
                      </div>
                    </label>
                    
                    <label class="group cursor-pointer">
                      <input type="radio" name="winner_id" value="<?= $fixture['participant2_id'] ?>" required class="sr-only">
                      <div class="bg-gradient-to-br from-blue-50 to-blue-100 border-3 border-blue-200 group-hover:border-blue-400 group-hover:shadow-lg rounded-2xl p-6 text-center transition-all duration-200 transform group-hover:scale-105">
                        <div class="text-4xl mb-3">🥊</div>
                        <div class="text-blue-600 font-bold text-xl mb-2">Blue Corner</div>
                        <div class="text-gray-800 font-semibold text-lg"><?= htmlspecialchars($fixture['p2_name']) ?></div>
                        <div class="w-full h-4 bg-blue-500 rounded-full mt-4 opacity-20 group-hover:opacity-60 transition-opacity"></div>
                      </div>
                    </label>
                    
                  </div>
                </div>
                
                <div class="text-center">
                  <button type="submit" class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold text-xl px-12 py-4 rounded-2xl shadow-xl transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-green-300">
                    <span class="flex items-center gap-3">
                      <span class="text-2xl">✅</span>
                      Finalize Winner
                    </span>
                  </button>
                </div>
                
              </form>
            <?php endif; ?>
          </div>
        </div>

      </div>
    </main>
  </div>

  <!-- JavaScript for live updates -->
  <script>
    // Live averages update function
    function fetchLiveAverages() {
        fetch('/judge/live-averages/<?= $fixture['id'] ?>')
            .then(response => response.json())
            .then(data => {
                const redAvg = document.getElementById('avg-red');
                const blueAvg = document.getElementById('avg-blue');
                
                redAvg.innerHTML = `
                  <div class="text-3xl font-bold mb-2">🔴 Red Corner</div>
                  <div class="text-5xl font-black">${data.red}</div>
                  <div class="text-red-100 font-medium mt-2">Average Score</div>
                `;
                
                blueAvg.innerHTML = `
                  <div class="text-3xl font-bold mb-2">🔵 Blue Corner</div>
                  <div class="text-5xl font-black">${data.blue}</div>
                  <div class="text-blue-100 font-medium mt-2">Average Score</div>
                `;
            })
            .catch(err => console.error("Live score fetch failed", err));
    }

    // Live scores and fouls update function
    function fetchLiveScoresAndFouls() {
      fetch('/judge/live-scores/<?= $fixture['id'] ?>')
        .then(res => res.json())
        .then(data => {
          // Update Scores Table
          const scoreTbody = document.querySelector('#score-table tbody');
          if (scoreTbody) {
            scoreTbody.innerHTML = '';
            data.scores.forEach((score, index) => {
              const cornerBadge = score.corner === 'red' 
                ? `<span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                     <span class="w-2 h-2 rounded-full bg-red-500"></span>
                     Red
                   </span>`
                : `<span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                     <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                     Blue
                   </span>`;
              
              const row = `
                <tr class="${index % 2 === 0 ? 'bg-white' : 'bg-gray-50/50'} hover:bg-blue-50/50 transition-colors duration-150 animate-slide-in">
                  <td class="px-6 py-4 text-sm font-medium text-gray-800">${score.scorer_name}</td>
                  <td class="px-6 py-4 text-sm">${cornerBadge}</td>
                  <td class="px-6 py-4 text-sm text-gray-700">${score.round}</td>
                  <td class="px-6 py-4 text-sm text-gray-700">${score.sub_round}</td>
                  <td class="px-6 py-4 text-sm font-bold text-gray-900">${score.score}</td>
                </tr>
              `;
              scoreTbody.innerHTML += row;
            });
          }

          // Update Fouls Table
          const foulTbody = document.querySelector('#foul-table tbody');
          if (foulTbody) {
            foulTbody.innerHTML = '';
            data.fouls.forEach((foul, index) => {
              const cornerBadge = foul.corner === 'red' 
                ? `<span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                     <span class="w-2 h-2 rounded-full bg-red-500"></span>
                     Red
                   </span>`
                : `<span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                     <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                     Blue
                   </span>`;
              
              const row = `
                <tr class="${index % 2 === 0 ? 'bg-white' : 'bg-red-50/30'} hover:bg-red-50 transition-colors duration-150 animate-slide-in">
                  <td class="px-6 py-4 text-sm font-medium text-gray-800">${foul.scorer_name}</td>
                  <td class="px-6 py-4 text-sm">${cornerBadge}</td>
                  <td class="px-6 py-4 text-sm text-gray-700">${foul.round}</td>
                  <td class="px-6 py-4 text-sm text-gray-700">${foul.sub_round}</td>
                  <td class="px-6 py-4 text-sm text-gray-800">${foul.reason}</td>
                </tr>
              `;
              foulTbody.innerHTML += row;
            });
          }
        })
        .catch(err => console.error('Polling error:', err));
    }

    // Radio button interaction enhancement
    document.querySelectorAll('input[name="winner_id"]').forEach(radio => {
      radio.addEventListener('change', function() {
        // Remove active state from all labels
        document.querySelectorAll('label.group').forEach(label => {
          label.classList.remove('ring-4', 'ring-green-300');
          const div = label.querySelector('div');
          div.classList.remove('border-green-500', 'bg-green-100');
        });
        
        // Add active state to selected label
        if (this.checked) {
          const parentLabel = this.closest('label');
          parentLabel.classList.add('ring-4', 'ring-green-300');
          const div = parentLabel.querySelector('div');
          div.classList.add('border-green-500', 'bg-green-100');
        }
      });
    });

    // Initialize polling intervals
    setInterval(fetchLiveAverages, 5000);
    setInterval(fetchLiveScoresAndFouls, 5000);

    // Initial fetch
    fetchLiveAverages();
    fetchLiveScoresAndFouls();

    // Add smooth scrolling behavior
    document.documentElement.style.scrollBehavior = 'smooth';
  </script>

</body>
</html>