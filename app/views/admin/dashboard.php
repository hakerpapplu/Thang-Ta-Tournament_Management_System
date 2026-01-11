<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Assign Default Role</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen p-6">

  <div class="max-w-5xl mx-auto bg-white shadow-lg rounded-lg p-8">
    
    <form method="POST" action="/admin/assign-default" class="space-y-6">
      <h2 class="text-2xl font-bold text-gray-800 border-b pb-2">Assign Default Role to Category</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block mb-1 font-semibold text-sm text-gray-700">Event Type</label>
          <input type="text" name="event_type" required class="w-full border border-gray-300 p-2 rounded focus:ring focus:ring-blue-200" />
        </div>

        <div>
          <label class="block mb-1 font-semibold text-sm text-gray-700">Gender</label>
          <select name="gender" required class="w-full border border-gray-300 p-2 rounded focus:ring focus:ring-blue-200">
            <option value="">--Select--</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
          </select>
        </div>

        <div>
          <label class="block mb-1 font-semibold text-sm text-gray-700">Weight Category</label>
          <input type="text" name="weight_category" required class="w-full border border-gray-300 p-2 rounded focus:ring focus:ring-blue-200" />
        </div>

        <div>
          <label class="block mb-1 font-semibold text-sm text-gray-700">Age Group</label>
          <input type="text" name="age_group" required class="w-full border border-gray-300 p-2 rounded focus:ring focus:ring-blue-200" />
        </div>

       <div>
          <label class="block mb-1 font-semibold text-sm text-gray-700">User</label>
          <select name="user_id" required class="w-full border border-gray-300 p-2 rounded focus:ring focus:ring-blue-200">
            <option value="">--Select User--</option>
            <?php foreach ($users as $user): ?>
              <option value="<?= htmlspecialchars($user['id']) ?>">
                <?= htmlspecialchars($user['name']) ?>
                <?php if (!empty($user['role'])): ?>
                  (<?= htmlspecialchars($user['role']) ?>)
                <?php endif; ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>


        <div>
          <label class="block mb-1 font-semibold text-sm text-gray-700">Role</label>
          <select name="role" required class="w-full border border-gray-300 p-2 rounded focus:ring focus:ring-blue-200" onchange="toggleCorner(this.value)">
            <option value="judge">Judge</option>
            <option value="scorer">Scorer</option>
          </select>
        </div>

        <div id="cornerSelect" class="md:col-span-2 hidden">
          <label class="block mb-1 font-semibold text-sm text-gray-700">Corner (only for Scorer)</label>
          <select name="corner" class="w-full border border-gray-300 p-2 rounded focus:ring focus:ring-blue-200">
            <option value="red">Red</option>
            <option value="blue">Blue</option>
          </select>
        </div>
      </div>

      <div class="pt-4">
        <button type="submit" class="bg-blue-600 text-white font-semibold px-6 py-2 rounded hover:bg-blue-700 transition">
          Assign Default
        </button>
      </div>
      <div class="pt-6 text-right">
          <form method="POST" action="/admin/apply-defaults" onsubmit="return confirm('Are you sure you want to apply default roles to all fixtures?');">
            <button type="submit" class="bg-green-600 text-white font-semibold px-6 py-2 rounded hover:bg-green-700 transition">
              Apply Defaults to All Fixtures
            </button>
          </form>
      </div>
    </form>
  </div>
  


  <div class="max-w-6xl mx-auto mt-12">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Current Default Assignments</h2>

    <div class="overflow-x-auto shadow ring-1 ring-gray-300 rounded-lg">
      <table class="min-w-full divide-y divide-gray-200 text-sm text-left bg-white">
        <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-700">
          <tr>
            <th class="px-4 py-3">Event Type</th>
            <th class="px-4 py-3">Gender</th>
            <th class="px-4 py-3">Weight Category</th>
            <th class="px-4 py-3">Age Group</th>
            <th class="px-4 py-3">User</th>
            <th class="px-4 py-3">Role</th>
            <th class="px-4 py-3">Corner</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <?php if (!empty($default_assignments) && is_array($default_assignments)): ?>
              <?php foreach ($default_assignments as $row): ?>
                <tr class="hover:bg-gray-50">
                  <td class="px-4 py-2"><?= htmlspecialchars($row['event_type']) ?></td>
                  <td class="px-4 py-2"><?= htmlspecialchars($row['gender']) ?></td>
                  <td class="px-4 py-2"><?= htmlspecialchars($row['weight_category']) ?></td>
                  <td class="px-4 py-2"><?= htmlspecialchars($row['age_group']) ?></td>
                  <td class="px-4 py-2"><?= htmlspecialchars($row['name']) ?></td>
                  <td class="px-4 py-2"><?= htmlspecialchars($row['role']) ?></td>
                  <td class="px-4 py-2"><?= htmlspecialchars($row['corner'] ?? '-') ?></td>
                </tr>
              <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="px-4 py-3 text-center text-gray-500">
              No default assignments found.
            </td>
          </tr>
        <?php endif; ?>

        </tbody>
      </table>
    </div>
  </div>

  <script>
    function toggleCorner(role) {
      const cornerDiv = document.getElementById('cornerSelect');
      if (role === 'scorer') {
        cornerDiv.classList.remove('hidden');
      } else {
        cornerDiv.classList.add('hidden');
      }
    }
  </script>
</body>
</html>
