<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Participant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-100 font-sans">

<div class="flex h-screen">
    <div class="flex-1 p-8 overflow-y-auto">
        <div class="max-w-3xl mx-auto bg-white rounded-lg shadow p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Add New Participant</h1>

            <!-- Error Handling -->
            <?php if (isset($errors) && !empty($errors)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
                    <ul class="list-disc list-inside">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
           


            <form action="/participants" method="POST" class="space-y-6">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Name</label>
                    <input type="text" name="name" required class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Date of Birth</label>
                    <input type="date" id="dob" name="dob" required class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                </div>
                
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Age</label>
                    <input type="number" id="age" name="age" readonly required class="w-full px-4 py-2 border rounded bg-gray-100 cursor-not-allowed focus:outline-none" />
                </div>


                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Gender</label>
                    <select name="gender" required class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">Select</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>

                <!-- views/participants/create.php -->
                <div>
                    <label class="block text-gray-700 font-semibold">District</label>
                    <input type="text" value="<?= htmlspecialchars($district) ?>" readonly class="w-full border rounded p-2 bg-gray-100" />
                    <input type="hidden" name="district" value="<?= htmlspecialchars($district) ?>" />
                </div>


                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Contact</label>
                    <input 
                        type="text" 
                        name="contact" 
                        required 
                        pattern="[0-9]{10}"
                        maxlength="10"
                        inputmode="numeric"
                        title="Please enter a 10-digit contact number"
                        class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-blue-500 focus:outline-none" 
                    />
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Age Group</label>
                    <select id="age_group" name="age_group" required class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">Select</option>
                        <option value="Under 14">Under 14 (Sub-Junior)</option>
                        <option value="Under 18">Under 18 (Junior)</option>
                        <option value="Over 18">Over 18 (Senior)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Weight Category</label>
                    <select id="weight_category" name="weight_category" required class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">Select</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Event Type</label>
                    <select name="event_type" required class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">Select</option>
                        <option value="Phunaba-Ama">Phunaba-Ama</option>
                        <option value="Phunaba-Anishuba">Phunaba-Anishuba</option>
                    </select>
                </div>

                <div class="flex items-center justify-between mt-8">
                    <a href="<?php echo isset($_SESSION['user']) ? '/participants' : '/participants/district-participants'; ?>" 
                       class="text-gray-600 hover:text-blue-600 font-semibold transition">
                        ← Back
                    </a>

                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded transition duration-300">
                        Save Participant
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const dobInput = document.getElementById('dob');
    const ageInput = document.getElementById('age');

    dobInput.addEventListener('change', () => {
        const dob = new Date(dobInput.value);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const m = today.getMonth() - dob.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
            age--;
        }

        ageInput.value = age >= 0 ? age : '';
    });
</script>

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
        weightCategorySelect.innerHTML = '<option value="">Select</option>';

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
