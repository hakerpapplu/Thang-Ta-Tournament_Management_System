<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Participants</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-100 font-sans">
<?php require_once 'app/views/partials/sidebar.php'; ?>
<div class="flex h-screen">
    

    <div class="flex-1 p-6 overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Participants</h1>
            <a href="/participants/create" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded transition duration-300">
                + Add Participant
            </a>
        </div>

        <!-- Filter + Export Form -->
        <div class="bg-white p-4 rounded shadow mb-6">
            <form method="GET" action="/participants" class="flex flex-wrap gap-4 items-center">
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-1">Age Group</label>
                    <select name="age_group" class="w-48 border-gray-300 rounded p-2 focus:ring focus:ring-blue-200">
                        <option value="">All Age Groups</option>
                        <option value="Under 14">Under 14 (Sub-Junior)</option>
                        <option value="Under 18">Under 18 (Junior)</option>
                        <option value="Over 18">Over 18 (Senior)</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-1">Gender</label>
                    <select name="gender" class="w-48 border-gray-300 rounded p-2 focus:ring focus:ring-blue-200">
                        <option value="">Both Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-1">Weight Category</label>
                    <select id="weight_category" name="weight_category" class="w-48 border-gray-300 rounded p-2 focus:ring focus:ring-blue-200">
                        <option value="">All Weight Categories</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-1">District</label>
                    <select name="district" class="w-48 border-gray-300 rounded p-2 focus:ring focus:ring-blue-200">
                        <option value="">All Districts</option>
                        <option value="">-- Select District --</option>
                        <option value="Ahmednagar">Ahmednagar</option>
                        <option value="Ahmednagar Gramin">Ahmednagar Gramin</option>
                        <option value="Akola">Akola</option>
                        <option value="Akola Gramin">Akola Gramin</option>
                        <option value="Amravati">Amravati</option>
                        <option value="Amravati Gramin">Amravati Gramin</option>
                        <option value="Aurangabad">Aurangabad</option>
                        <option value="Aurangabad Gramin">Aurangabad Gramin</option>
                        <option value="Beed">Beed</option>
                        <option value="Beed Gramin">Beed Gramin</option>
                        <option value="Bhandara">Bhandara</option>
                        <option value="Bhandara Gramin">Bhandara Gramin</option>
                        <option value="Buldhana">Buldhana</option>
                        <option value="Buldhana Gramin">Buldhana Gramin</option>
                        <option value="Chandrapur">Chandrapur</option>
                        <option value="Chandrapur Gramin">Chandrapur Gramin</option>
                        <option value="Chhatrapati Sambhajinagar">Chhatrapati Sambhajinagar (formerly Aurangabad)</option>
                        <option value="Chhatrapati Sambhajinagar Gramin">Chhatrapati Sambhajinagar Gramin</option>
                        <option value="Dharashiv">Dharashiv (formerly Osmanabad)</option>
                        <option value="Dharashiv Gramin">Dharashiv Gramin</option>
                        <option value="Gadchiroli">Gadchiroli</option>
                        <option value="Gadchiroli Gramin">Gadchiroli Gramin</option>
                        <option value="Gondia">Gondia</option>
                        <option value="Gondia Gramin">Gondia Gramin</option>
                        <option value="Hingoli">Hingoli</option>
                        <option value="Hingoli Gramin">Hingoli Gramin</option>
                        <option value="Jalgaon">Jalgaon</option>
                        <option value="Jalgaon Gramin">Jalgaon Gramin</option>
                        <option value="Jalna">Jalna</option>
                        <option value="Jalna Gramin">Jalna Gramin</option>
                        <option value="Kolhapur">Kolhapur</option>
                        <option value="Kolhapur Gramin">Kolhapur Gramin</option>
                        <option value="Latur">Latur</option>
                        <option value="Latur Gramin">Latur Gramin</option>
                        <option value="Mumbai City">Mumbai City</option>
                        <option value="Mumbai City Gramin">Mumbai City Gramin</option>
                        <option value="Mumbai Suburban">Mumbai Suburban</option>
                        <option value="Mumbai Suburban Gramin">Mumbai Suburban Gramin</option>
                        <option value="Nagpur">Nagpur</option>
                        <option value="Nagpur Gramin">Nagpur Gramin</option>
                        <option value="Nanded">Nanded</option>
                        <option value="Nanded Gramin">Nanded Gramin</option>
                        <option value="Nandurbar">Nandurbar</option>
                        <option value="Nandurbar Gramin">Nandurbar Gramin</option>
                        <option value="Nashik">Nashik</option>
                        <option value="Nashik Gramin">Nashik Gramin</option>
                        <option value="Palghar">Palghar</option>
                        <option value="Palghar Gramin">Palghar Gramin</option>
                        <option value="Parbhani">Parbhani</option>
                        <option value="Parbhani Gramin">Parbhani Gramin</option>
                        <option value="Pune">Pune</option>
                        <option value="Pune Gramin">Pune Gramin</option>
                        <option value="Raigad">Raigad</option>
                        <option value="Raigad Gramin">Raigad Gramin</option>
                        <option value="Ratnagiri">Ratnagiri</option>
                        <option value="Ratnagiri Gramin">Ratnagiri Gramin</option>
                        <option value="Sangli">Sangli</option>
                        <option value="Sangli Gramin">Sangli Gramin</option>
                        <option value="Satara">Satara</option>
                        <option value="Satara Gramin">Satara Gramin</option>
                        <option value="Sindhudurg">Sindhudurg</option>
                        <option value="Sindhudurg Gramin">Sindhudurg Gramin</option>
                        <option value="Solapur">Solapur</option>
                        <option value="Solapur Gramin">Solapur Gramin</option>
                        <option value="Thane">Thane</option>
                        <option value="Thane Gramin">Thane Gramin</option>
                        <option value="Wardha">Wardha</option>
                        <option value="Wardha Gramin">Wardha Gramin</option>
                        <option value="Washim">Washim</option>
                        <option value="Washim Gramin">Washim Gramin</option>
                        <option value="Yavatmal">Yavatmal</option>
                        <option value="Yavatmal Gramin">Yavatmal Gramin</option>
                        <option value="Pimpri Chinchwad">Pimpri Chinchwad</option>
                        <option value="Pimpri Chinchwad Gramin">Pimpri Chinchwad Gramin</option>
                        <option value="Mira-Bhayander">Mira-Bhayander</option>
                        <option value="Mira-Bhayander Gramin">Mira-Bhayander Gramin</option>
                        <option value="Kalyan-Dombivli">Kalyan-Dombivli</option>
                        <option value="Bhiwandi">Bhiwandi</option> 
                        <option value="Panvel">Panvel</option>
                        <option value="Panvel Gramin">Panvel Gramin</option>
                        <option value="Ahilynagar Corporation">Ahilynagar Corporation</option>
                        <option value="Ahilynagar Corporation Gramin">Ahilynagar Corporation Gramin</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-1">Event Type</label>
                    <select name="event_type" class="w-48 border-gray-300 rounded p-2 focus:ring focus:ring-blue-200">
                        <option value="">All Event Types</option>
                        <option value="Phunaba-Ama">Phunaba-Ama</option>
                        <option value="Phunaba-Anishuba">Phunaba-Anishuba</option>
                    </select>
                </div>
                
                <div class="flex items-end gap-2 mt-4 sm:mt-0">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded transition duration-300">
                        Filter
                    </button>

                    <a href="/participants/export?<?php echo http_build_query($_GET); ?>" 
                       class="bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2 rounded transition duration-300">
                        Export to Excel
                    </a>
                </div>
            </form>
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

        <!-- Table Section -->
        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-100 text-gray-700 text-sm uppercase">
                    <tr>
                        <th class="py-3 px-4 text-left">#</th>
                        <th class="py-3 px-4 text-left">Name</th>
                        <th class="py-3 px-4 text-left">Age</th>
                        <th class="py-3 px-4 text-left">Gender</th>
                        <th class="py-3 px-4 text-left">District</th>
                        <th class="py-3 px-4 text-left">Contact</th>
                        <th class="py-3 px-4 text-left">Age Group</th>
                        <th class="py-3 px-4 text-left">Weight Category</th>
                        <th class="py-3 px-4 text-left">Event Type</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600">
                    <?php if (!empty($participants)): ?>
                        <?php foreach ($participants as $index => $participant): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2 px-4"><?php echo $index + 1; ?></td>
                                <td class="py-2 px-4"><?php echo htmlspecialchars($participant['name']); ?></td>
                                <td class="py-2 px-4"><?php echo $participant['age']; ?></td>
                                <td class="py-2 px-4"><?php echo htmlspecialchars($participant['gender']); ?></td>
                                <td class="py-2 px-4"><?php echo htmlspecialchars($participant['district']); ?></td>
                                <td class="py-2 px-4"><?php echo htmlspecialchars($participant['contact']); ?></td>
                                <td class="py-2 px-4"><?php echo htmlspecialchars($participant['age_group']); ?></td>
                                <td class="py-2 px-4"><?php echo htmlspecialchars($participant['weight_category']); ?></td>
                                <td class="py-2 px-4"><?php echo htmlspecialchars($participant['event_type']); ?></td>
                                <td class="py-2 px-4 text-center space-x-2">
                                    <a href="/participants/edit/<?php echo $participant['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                                    <a href="/participants/delete/<?php echo $participant['id']; ?>" 
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
</div>

<script>
    const weightOptions = {
        "Under 14": {
            "Male": ["-21 Kg","-25 Kg", "-29 Kg", "-33 Kg", "-37 Kg", "-41 Kg", "-45 Kg", "-49 Kg", "-53 Kg", "+53 Kg"],
            "Female": ["-21 Kg","-25 Kg", "-29 Kg", "-33 Kg", "-37 Kg", "-41 Kg", "-45 Kg", "-49 Kg", "-53 Kg", "+53 Kg"]
        },
        
        "Under 18": {
            "Male": ["-44 Kg", "-48 Kg", "-52 Kg", "-56 Kg", "-60 Kg", "-65 Kg", "-70 Kg", "-75 Kg", "-80 Kg", "+80 Kg"],
            "Female": ["-40 Kg", "-44 Kg", "-48 Kg", "-52 Kg", "-56 Kg", "-60 Kg", "-65 Kg", "-70 Kg", "-75 Kg", "+75 Kg"]
        },
        
        "Over 18": {
            "Male": ["-46 Kg", "-50 Kg", "-54 Kg", "-58 Kg", "-62 Kg", "-66 Kg", "-70 Kg", "-75 Kg", "-80 Kg", "+80 Kg"],
            "Female": ["-44 Kg", "-48 Kg", "-52 Kg", "-56 Kg", "-60 Kg", "-64 Kg", "-68 Kg", "-72 Kg", "-76 Kg", "+76 Kg"]
        },
        
    };

    const genderSelect = document.querySelector('select[name="gender"]');
    const ageGroupSelect = document.querySelector('select[name="age_group"]');
    const weightCategorySelect = document.getElementById('weight_category');

    function updateWeightCategories() {
        const gender = genderSelect.value;
        const ageGroup = ageGroupSelect.value;

        // Clear previous options
        weightCategorySelect.innerHTML = '<option value="">All Weight Categories</option>';

        if (gender && ageGroup && weightOptions[ageGroup] && weightOptions[ageGroup][gender]) {
            weightOptions[ageGroup][gender].forEach(weight => {
                const option = document.createElement('option');
                option.value = weight;
                option.textContent = weight;
                weightCategorySelect.appendChild(option);
            });
        }
    }

    genderSelect.addEventListener('change', updateWeightCategories);
    ageGroupSelect.addEventListener('change', updateWeightCategories);
</script>

</body>
</html>
