<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>District Participants</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center py-8">

  <div class="bg-white shadow-xl rounded-lg p-6 w-full max-w-7xl">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-700">
        Participants - <?= htmlspecialchars($_SESSION['verified_district']) ?>
      </h1>
      <div class="flex gap-4">
        <a href="/participants/create" 
           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow">
          ➕ Add Participant
        </a>
        <a href="/participants/logout" 
           class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow">
          🚪 Logout
        </a>
      </div>
    </div>
    
    <!-- Success/Error Message -->
        <?php if (isset($_SESSION['success'])): ?>
            <div id="flash-msg" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <script>
            // Auto-hide the flash message after 5 seconds
            setTimeout(() => {
              const msg = document.getElementById('flash-msg');
              if (msg) msg.style.display = 'none';
            }, 1000);
        </script>

    <div class="overflow-x-auto">
      <table class="w-full border border-gray-200 rounded-lg overflow-hidden text-sm md:text-base">
        <thead class="bg-gray-200 text-gray-700">
          <tr>
            <th class="py-2 px-4 text-left">#</th>
            <th class="py-2 px-4 text-left">Name</th>
            <th class="py-2 px-4">Age</th>
            <th class="py-2 px-4">Gender</th>
            <th class="py-2 px-4">District</th>
            <th class="py-2 px-4">Contact</th>
            <th class="py-2 px-4">Age Group</th>
            <th class="py-2 px-4">Weight Category</th>
            <th class="py-2 px-4">Event Type</th>
            <th class="py-2 px-4 text-center">Actions</th>
          </tr>
        </thead>
        <tbody class="text-gray-600">
          <?php if (!empty($participants)): ?>
            <?php foreach ($participants as $index => $participant): ?>
              <tr class="border-b hover:bg-gray-50">
                <td class="py-2 px-4"><?= $index + 1 ?></td>
                <td class="py-2 px-4"><?= htmlspecialchars($participant['name']) ?></td>
                <td class="py-2 px-4"><?= $participant['age'] ?></td>
                <td class="py-2 px-4"><?= htmlspecialchars($participant['gender']) ?></td>
                <td class="py-2 px-4"><?= htmlspecialchars($participant['district']) ?></td>
                <td class="py-2 px-4"><?= htmlspecialchars($participant['contact']) ?></td>
                <td class="py-2 px-4"><?= htmlspecialchars($participant['age_group']) ?></td>
                <td class="py-2 px-4"><?= htmlspecialchars($participant['weight_category']) ?></td>
                <td class="py-2 px-4"><?= htmlspecialchars($participant['event_type']) ?></td>
                <td class="py-2 px-4 text-center space-x-2">
                  <a href="/participants/edit/<?= $participant['id'] ?>" 
                     class="text-blue-600 hover:underline">Edit</a>
                  <a href="/participants/delete/<?= $participant['id'] ?>" 
                     class="text-red-600 hover:underline"
                     onclick="return confirm('Are you sure you want to delete this participant?');">
                    Delete
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="10" class="text-center py-8 text-gray-500">
                No participants found.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</body>
</html>
