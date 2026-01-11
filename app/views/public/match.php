<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Live Match Scoreboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">

<div class="max-w-6xl mx-auto p-6">
    <!-- Match Header -->
    <div class="text-center mb-8">
        <span class="px-3 py-1 bg-red-100 text-red-600 rounded-full text-sm font-semibold">● LIVE MATCH</span>
        <h1 class="text-3xl font-bold mt-4">Live Match Scoreboard</h1>
        <div class="flex justify-center gap-3 mt-3">
            <span class="px-3 py-1 bg-blue-100 text-blue-600 rounded-full"><?= $fixture['event_type'] ?></span>
            <span class="px-3 py-1 bg-purple-100 text-purple-600 rounded-full"> <?= $fixture['age_group'] ?></span>
            <span class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded-full"><?= $fixture['weight_category'] ?> </span>
            <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full"><?= ucfirst($fixture['gender']) ?></span>
        </div>
    </div>

    <!-- Player Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        <div class="bg-red-100 border border-red-300 rounded-xl p-6">
            <h2 class="text-2xl font-bold text-red-700"><?= htmlspecialchars($red['name']) ?></h2>
            <p class="text-gray-700">District: <strong><?= htmlspecialchars($red['district']) ?></strong></p>
            <div class="mt-3 bg-red-600 text-white px-3 py-1 rounded-lg inline-block">RED CORNER</div>
        </div>
        <div class="bg-blue-100 border border-blue-300 rounded-xl p-6">
            <h2 class="text-2xl font-bold text-blue-700"><?= htmlspecialchars($blue['name']) ?></h2>
            <p class="text-gray-700">District: <strong><?= htmlspecialchars($blue['district']) ?></strong></p>
            <div class="mt-3 bg-blue-600 text-white px-3 py-1 rounded-lg inline-block">BLUE CORNER</div>
        </div>
    </div>

    <!-- Live Average Scores -->
    <h2 class="text-xl font-bold mb-4">Live Average Scores</h2>
    <div id="live-averages" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
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
                <span class="text-2xl">📊</span> Round-wise Totals
            </h2>
        </div>
        <div class="p-6 space-y-4">
            <?php foreach ($roundScores as $roundNum => $score): ?>
                <div class="animate-slide-in bg-gradient-to-r from-gray-50 to-gray-100 border-l-4 
                    <?= ($round && $round['round_number'] == $roundNum && $round['is_active']) ? 'border-green-500' : 'border-gray-400' ?> 
                    p-5 rounded-r-xl">
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
</div>

<!-- JS Auto-refresh for live averages -->
<script>
function loadLiveAverages() {
    fetch('/public/live-averages/<?= $fixture['id'] ?>')
        .then(res => res.json())
        .then(data => {
            document.querySelector('#avg-red .text-5xl').innerText = data.red ?? '--';
            document.querySelector('#avg-blue .text-5xl').innerText = data.blue ?? '--';
        });
}
setInterval(loadLiveAverages, 3000); // refresh every 3 sec
loadLiveAverages();
</script>

</body>
</html>
