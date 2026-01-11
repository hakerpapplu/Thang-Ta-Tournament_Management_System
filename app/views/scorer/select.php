<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Scorer Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">

  <div class="bg-white shadow-xl rounded-lg p-8 w-full max-w-xl">
    <h1 class="text-3xl font-bold text-center text-blue-700 mb-6">🎯 Scorer Login for Match</h1>

    <form action="/scorer/enter" method="POST" class="space-y-6">
      <input type="hidden" name="fixture_id" value="<?= $fixture['id'] ?>">

      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Select Your Name</label>
        <select name="scorer_id" required class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="">-- Select Scorer --</option>
          <?php foreach ($scorers as $scorer): ?>
            <option value="<?= $scorer['id'] ?>"><?= htmlspecialchars($scorer['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Select Corner</label>
        <select name="corner" required class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="red">🔴 Red</option>
          <option value="blue">🔵 Blue</option>
        </select>
      </div>

      <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition">
        ➡️ Enter Scoring Panel
      </button>
    </form>
  </div>

</body>
</html>
