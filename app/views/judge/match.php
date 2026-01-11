<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Start Match</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-10 px-4">

  <section class="w-full max-w-3xl bg-white shadow-xl rounded-2xl p-6 sm:p-8">
    <h1 class="text-3xl font-extrabold text-center text-gray-800 mb-8">🥋 Start Match</h1>

    <form action="/judge/start-match" method="POST" class="space-y-6">

      <!-- Hidden Fixture ID -->
      <input type="hidden" name="fixture_id" value="<?= $fixture['id'] ?>">

      <!-- Judge Selection -->
      <div>
        <label for="judge_id" class="block text-sm font-semibold text-gray-700 mb-1">Select Your Name</label>
        <select id="judge_id" name="judge_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
          <option value="">-- Select Judge --</option>
          <?php foreach ($judges as $judge): ?>
            <option value="<?= $judge['id'] ?>"><?= htmlspecialchars($judge['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Competitors Info -->
      <div class="grid sm:grid-cols-2 gap-6">
        <div class="bg-red-50 p-4 rounded-lg border border-red-200">
          <p class="text-sm font-semibold text-gray-700">Red Corner</p>
          <p class="text-lg font-bold text-red-600"><?= $fixture['p1_name'] ?? 'TBD' ?></p>
          <p class="text-sm text-gray-500"><?= $fixture['p1_district'] ?? '-' ?></p>
        </div>
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
          <p class="text-sm font-semibold text-gray-700">Blue Corner</p>
          <p class="text-lg font-bold text-blue-700"><?= $fixture['p2_name'] ?? 'TBD' ?></p>
          <p class="text-sm text-gray-500"><?= $fixture['p2_district'] ?? '-' ?></p>
        </div>
      </div>

      <!-- Match Details -->
      <div class="grid sm:grid-cols-3 gap-4 text-sm text-gray-700 font-medium">
        <div class="bg-gray-100 rounded p-3">
          <p class="text-gray-500">Event</p>
          <p><?= htmlspecialchars($fixture['event_type']) ?></p>
        </div>
        <div class="bg-gray-100 rounded p-3">
          <p class="text-gray-500">Weight</p>
          <p><?= htmlspecialchars($fixture['weight_category']) ?></p>
        </div>
        <div class="bg-gray-100 rounded p-3">
          <p class="text-gray-500">Age Group</p>
          <p><?= htmlspecialchars($fixture['age_group']) ?></p>
        </div>
      </div>

      <!-- Submit Button -->
      <div class="text-center">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-6 py-2 rounded-lg transition duration-300">
          ✅ Start Match
        </button>
      </div>

    </form>
  </section>

</body>
</html>
