
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>District Verification</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-blue-100 to-blue-200 min-h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-lg">
        <!-- Header -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">District Verification</h1>
            <p class="text-gray-500 text-sm mt-2">Please select your district and enter the district code to proceed.</p>
        </div>
        
        <!-- ✅ Error Message -->
        <?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="mb-4 p-3 rounded-lg bg-red-100 border border-red-300 text-red-700 text-sm">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST" action="/participants/verify-district" class="space-y-5">
            <!-- District Dropdown -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">District</label>
                <select name="district" required class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none">
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


            <!-- District Code Input -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">District Code</label>
                <input type="text" name="district_code" required placeholder="Enter district code"
                       class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>

            <!-- Submit Button -->
            <button type="submit"
                    class="w-full bg-blue-600 text-white font-semibold px-4 py-3 rounded-lg shadow-md hover:bg-blue-700 transition duration-300">
                Verify & Continue
            </button>
        </form>
    </div>

</body>
</html>
