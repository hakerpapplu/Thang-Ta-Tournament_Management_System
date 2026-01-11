<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Event Access Portal</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 min-h-screen flex flex-col items-center justify-center px-4 relative">

  <!-- Flash Messages -->
  <?php if (isset($_GET['error']) && $_GET['error'] === 'participant_exists'): ?>
    <div id="flash-msg" class="fixed top-6 z-50 bg-red-100 border border-red-400 text-red-700 px-6 py-3 rounded-lg shadow-md text-center text-lg font-medium">
      ❗ Participant already exists.
    </div>
  <?php elseif (isset($_GET['success']) && $_GET['success'] === 'participant_registered'): ?>
    <div id="flash-msg" class="fixed top-6 z-50 bg-green-100 border border-green-400 text-green-700 px-6 py-3 rounded-lg shadow-md text-center text-lg font-medium">
      🎉 Participant registered successfully!
    </div>
  <?php elseif (isset($_GET['error']) && $_GET['error'] === 'participant_error'): ?>
    <div id="flash-msg" class="fixed top-6 z-50 bg-red-100 border border-red-400 text-red-700 px-6 py-3 rounded-lg shadow-md text-center text-lg font-medium">
      ❌ Failed to register participant. Please try again.
    </div>
  <?php endif; ?>

  <script>
    // Auto-hide the flash message after 5 seconds
    setTimeout(() => {
      const msg = document.getElementById('flash-msg');
      if (msg) msg.style.display = 'none';
    }, 5000);
  </script>

  <!-- Main Portal Card -->
  <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12 max-w-3xl w-full text-center space-y-8 mt-4">
    <h1 class="text-3xl md:text-4xl font-extrabold text-gray-800">🎉 Welcome to the Event Portal</h1>
    <p class="text-gray-600 text-lg">Please select your access below:</p>

    <div class="flex flex-col md:flex-row justify-center gap-6 mt-6">
      
      <!-- Participant Access -->
      <a href="/participants/create" class="group w-full md:w-1/2 bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-xl transition-all duration-300 shadow-lg transform hover:scale-105">
        <div class="flex items-center justify-center gap-3 text-lg font-semibold">
          <svg class="w-6 h-6 group-hover:animate-bounce" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5s-3 1.343-3 3 1.343 3 3 3zm0 2c-2.67 0-8 1.337-8 4v3h16v-3c0-2.663-5.33-4-8-4z"/>
          </svg>
          Register as Participant
        </div>
      </a>

      <!-- Admin Access -->
      <a href="/app/views/auth/login.php" class="group w-full md:w-1/2 bg-gray-800 hover:bg-gray-900 text-white py-4 rounded-xl transition-all duration-300 shadow-lg transform hover:scale-105">
        <div class="flex items-center justify-center gap-3 text-lg font-semibold">
          <svg class="w-6 h-6 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4s-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.337-8 4v2h16v-2c0-2.663-5.33-4-8-4z"/>
          </svg>
          Admin Login
        </div>
      </a>

    </div>

    <!-- Live Scores Access -->
    <div class="mt-6">
      <a href="/fixtures" class="group inline-block bg-green-600 hover:bg-green-700 text-white py-4 px-8 rounded-xl transition-all duration-300 shadow-lg transform hover:scale-105">
        <div class="flex items-center justify-center gap-3 text-lg font-semibold">
          <svg class="w-6 h-6 group-hover:animate-pulse" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M9 19V6h13M9 6L5 9l4 3"/>
          </svg>
          See Live Scores
        </div>
      </a>
    </div>

    <p class="text-sm text-gray-500 mt-6">Need help? Contact the event coordinator.</p>
  </div>

</body>
</html>
