<!-- main dashboard view -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 font-sans">
    <?php require_once 'app/views/partials/sidebar.php'; ?>

<div class="flex h-screen overflow-hidden">
    

    <div class="flex-1 p-6 overflow-y-auto">
        <!-- Heading -->
        <h1 class="text-3xl font-bold text-gray-800 mb-6">📊 Dashboard</h1>

       <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition">
                <h2 class="text-gray-500 text-sm">Total Participants</h2>
                <p class="text-3xl font-extrabold text-blue-600"><?= $stats['total_participants'] ?></p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition">
                <h2 class="text-gray-500 text-sm">Total Bouts</h2>
                <p class="text-3xl font-extrabold text-indigo-500"><?= $stats['total_bouts'] ?? 'N/A' ?></p> <!-- Assuming you need a field for total bouts -->
            </div>
            <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition">
                <h2 class="text-gray-500 text-sm">Wins</h2>
                <p class="text-3xl font-extrabold text-green-500"><?= $stats['wins'] ?? 'N/A' ?></p> <!-- Assuming you need a field for wins -->
            </div>
            <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition">
                <h2 class="text-gray-500 text-sm">Losses</h2>
                <p class="text-3xl font-extrabold text-red-500"><?= $stats['losses'] ?? 'N/A' ?></p> <!-- Assuming you need a field for losses -->
            </div>
            <!-- Export All Results -->
            <a href="/dashboard/exportAllResults" 
               class="btn btn-green w-full" aria-describedby="export-all-help">
               <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M9 12l2 2 4-4M7 20h10a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z"/>
               </svg>
               Export All Results
            </a>
        </div>
        
        <!-- Charts Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white p-4 rounded-xl shadow-md">
                <h3 class="text-center font-semibold text-gray-700 mb-2">Participants by Age Group</h3>
                <canvas id="ageChart" class="w-full h-64"></canvas>
            </div>
        
            <div class="bg-white p-4 rounded-xl shadow-md">
                <h3 class="text-center font-semibold text-gray-700 mb-2">By Weight Category</h3>
                <canvas id="weightChart" class="w-full h-64"></canvas>
            </div>
        
            <div class="bg-white p-4 rounded-xl shadow-md">
                <h3 class="text-center font-semibold text-gray-700 mb-2">Event Types</h3>
                <canvas id="eventChart" class="w-full h-64"></canvas>
            </div>
        </div>

    </div>
</div>

<!-- Chart Setup -->
<script>
    const ageCtx = document.getElementById('ageChart').getContext('2d');
    const weightCtx = document.getElementById('weightChart').getContext('2d');
    const eventCtx = document.getElementById('eventChart').getContext('2d');

    new Chart(ageCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($stats['age_groups'], 'age_group')) ?>,
            datasets: [{
                label: 'Participants by Age Group',
                data: <?= json_encode(array_column($stats['age_groups'], 'count')) ?>,
                backgroundColor: '#60a5fa'
            }]
        }
    });

    new Chart(weightCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($stats['weight_categories'], 'weight_category')) ?>,
            datasets: [{
                label: 'Participants by Weight Category',
                data: <?= json_encode(array_column($stats['weight_categories'], 'count')) ?>,
                backgroundColor: '#34d399'
            }]
        }
    });

    new Chart(eventCtx, {
        type: 'pie',
        data: {
            labels: <?= json_encode(array_column($stats['event_types'], 'event_type')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($stats['event_types'], 'count')) ?>,
                backgroundColor: ['#fbbf24', '#f87171', '#60a5fa', '#10b981']
            }]
        }
    });
</script>

</body>
</html>
