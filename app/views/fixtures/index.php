<!DOCTYPE html>

<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$role = $_SESSION['user']['role'] ?? 'public'; 
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fixtures | Tournament Bracket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <style>
        :root {
            --primary: #3b82f6;
            --primary-dark: #1e40af;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --purple: #8b5cf6;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-800: #1f2937;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --border-radius: 0.75rem;
            --border-radius-lg: 1rem;
            --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Enhanced Animations */
        @keyframes slideInFromTop {
            from { 
                opacity: 0; 
                transform: translateY(-20px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }
        
        @keyframes slideInFromRight {
            from { 
                opacity: 0; 
                transform: translateX(20px); 
            }
            to { 
                opacity: 1; 
                transform: translateX(0); 
            }
        }
        
        @keyframes scaleIn {
            from { 
                opacity: 0; 
                transform: scale(0.95); 
            }
            to { 
                opacity: 1; 
                transform: scale(1); 
            }
        }
        
        @keyframes shimmer {
            0% { 
                background-position: -200px 0; 
            }
            100% { 
                background-position: calc(200px + 100%) 0; 
            }
        }
        
        @keyframes pulse {
            0%, 100% { 
                opacity: 1; 
            }
            50% { 
                opacity: 0.5; 
            }
        }

        /* Animation Classes */
        .animate-slideInFromTop {
            animation: slideInFromTop 0.6s ease-out forwards;
        }
        
        .animate-slideInFromRight {
            animation: slideInFromRight 0.4s ease-out forwards;
        }
        
        .animate-scaleIn {
            animation: scaleIn 0.3s ease-out forwards;
        }

        /* Enhanced Card Components */
        .card {
            background: white;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-lg);
            transition: var(--transition);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .card:hover {
            box-shadow: var(--shadow-xl);
            transform: translateY(-2px);
        }
        
        .card-header {
            padding: 1.5rem 2rem 1rem;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .card-body {
            padding: 2rem;
        }

        /* Enhanced Form Components */
        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 0.75rem;
            transition: var(--transition);
        }
        
        .form-input {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: var(--border-radius);
            font-size: 0.875rem;
            transition: var(--transition);
            background-color: white;
            appearance: none;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .form-input:hover:not(:focus) {
            border-color: #cbd5e1;
        }
        
        /* Select Arrow Enhancement */
        .form-select {
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.25rem;
            padding-right: 2.5rem;
        }

        /* Enhanced Button System */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.875rem 1.5rem;
            font-weight: 600;
            font-size: 0.875rem;
            border-radius: var(--border-radius);
            transition: var(--transition);
            border: none;
            cursor: pointer;
            text-decoration: none;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.2), transparent 70%);
            transform: translateX(-100%);
            transition: transform 0.6s;
        }
        
        .btn:hover::before {
            transform: translateX(100%);
        }
        
        .btn:active {
            transform: translateY(1px);
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        /* Button Variants */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }
        
        .btn-primary:hover:not(:disabled) {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #1e3a8a 100%);
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            color: white;
        }
        
        .btn-secondary:hover:not(:disabled) {
            background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            color: white;
        }
        
        .btn-success:hover:not(:disabled) {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);
            transform: translateY(-2px);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
            color: white;
        }
        
        .btn-warning:hover:not(:disabled) {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.3);
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
            color: white;
        }
        
        .btn-danger:hover:not(:disabled) {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.3);
            transform: translateY(-2px);
        }
        
        .btn-purple {
            background: linear-gradient(135deg, var(--purple) 0%, #7c3aed 100%);
            color: white;
        }
        
        .btn-purple:hover:not(:disabled) {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            box-shadow: 0 10px 15px -3px rgba(139, 92, 246, 0.3);
            transform: translateY(-2px);
        }

        /* Enhanced Notification System */
        .notification {
            position: relative;
            border-radius: var(--border-radius-lg);
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid;
            box-shadow: var(--shadow-md);
            backdrop-filter: blur(10px);
        }
        
        .notification-success {
            background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%);
            border-left-color: var(--success);
            color: #065f46;
        }
        
        .notification-error {
            background: linear-gradient(135deg, #fef2f2 0%, #fef7f7 100%);
            border-left-color: var(--danger);
            color: #991b1b;
        }

        /* Enhanced Tournament Bracket */
        .bracket-container {
            display: flex;
            justify-content: center;
            align-items: start;
            gap: 3rem;
            padding: 2rem 0;
            overflow-x: auto;
            min-height: 60vh;
        }
        
        .bracket-round {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            min-width: 280px;
            position: relative;
        }
        
        .bracket-round-header {
            text-align: center;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 2rem;
            padding: 1rem;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .match-card {
            background: white;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-lg);
            padding: 1.5rem;
            transition: var(--transition);
            border: 2px solid transparent;
            position: relative;
            cursor: pointer;
        }
        
        .match-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: var(--border-radius-lg);
            padding: 2px;
            background: linear-gradient(135deg, transparent, rgba(59, 130, 246, 0.1), transparent);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: exclude;
            opacity: 0;
            transition: var(--transition);
        }
        
        .match-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
            border-color: rgba(59, 130, 246, 0.2);
        }
        
        .match-card:hover::before {
            opacity: 1;
        }
        
        .participant {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1rem;
            margin: 0.25rem 0;
            border-radius: var(--border-radius);
            font-weight: 600;
            font-size: 1rem;
            transition: var(--transition);
        }
        
        .participant-red {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            color: #dc2626;
            border: 2px solid #fca5a5;
        }
        
        .participant-blue {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            color: #2563eb;
            border: 2px solid #93c5fd;
        }
        
        .participant-bye {
            background: linear-gradient(135deg, #fefce8 0%, #fef3c7 100%);
            color: #d97706;
            border: 2px solid #fcd34d;
            font-style: italic;
        }
        
        .vs-divider {
            text-align: center;
            font-weight: 600;
            color: #6b7280;
            margin: 0.5rem 0;
            font-size: 0.875rem;
        }
        
        .winner-announcement {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            color: #166534;
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-top: 1rem;
            border: 2px solid #bbf7d0;
            text-align: center;
        }
        
        .match-score {
            color: #6b7280;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            text-align: center;
        }
        
        .medal-match {
            position: relative;
            overflow: hidden;
        }
        
        .medal-match::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 0;
            height: 0;
            border-left: 40px solid transparent;
            border-top: 40px solid;
        }
        
        .medal-gold::after {
            border-top-color: #fbbf24;
        }
        
        .medal-bronze::after {
            border-top-color: #f97316;
        }
        
        .medal-title {
            font-weight: 700;
            font-size: 1rem;
            text-align: center;
            margin-bottom: 1rem;
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
        }
        
        .medal-gold-title {
            color: #d97706;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        }
        
        .medal-bronze-title {
            color: #ea580c;
            background: linear-gradient(135deg, #fed7aa 0%, #fdba74 100%);
        }

        /* Enhanced Layout */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Enhanced Responsive Design */
        @media (max-width: 768px) {
            .bracket-container {
                gap: 1.5rem;
                padding: 1rem 0;
            }
            
            .bracket-round {
                min-width: 250px;
            }
            
            .match-card {
                padding: 1rem;
            }
            
            .btn {
                padding: 0.75rem 1rem;
                font-size: 0.8125rem;
            }
            
            .card-body {
                padding: 1.5rem;
            }
        }

        /* Loading States */
        .loading {
            pointer-events: none;
            position: relative;
        }
        
        .loading::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.8);
            border-radius: inherit;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Accessibility Enhancements */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
        
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        /* Focus Indicators */
        .btn:focus-visible,
        .form-input:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }
        
        .match-card:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 4px;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen font-sans">
    
    <!-- Sidebar -->
    <?php if ($role === 'admin' || $role === 'judge'): ?>
        <?php require_once 'app/views/partials/sidebar.php'; ?>
    <?php endif; ?>

    <div class="flex min-h-screen">
        <!-- Main Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
            <div class="container">
                <!-- Success Notification Script -->
                <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const notification = document.createElement('div');
                            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slideInFromRight';
                            notification.innerHTML = `
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Results submitted successfully!
                                </div>
                            `;
                            document.body.appendChild(notification);
                            setTimeout(() => notification.remove(), 4000);
                        });
                    </script>
                <?php endif; ?>

                <?php require_once 'app/helpers/Session.php'; ?>

                <!-- Enhanced Flash Messages -->
                <?php if (Session::exists('success')) : ?>
                    <div class="notification notification-success animate-slideInFromTop" role="alert" aria-live="polite">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="font-semibold"><?= Session::flash('success'); ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (Session::exists('error')) : ?>
                    <div class="notification notification-error animate-slideInFromTop" role="alert" aria-live="assertive">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-semibold"><?= Session::flash('error'); ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Tournament Filters Card -->
                <div class="card animate-slideInFromTop mb-8">
                    <div class="card-header">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z"></path>
                            </svg>
                            Tournament Filters
                        </h2>
                    </div>
                    <div class="card-body">
                        <form action="/fixtures" method="GET" class="space-y-6">
                            <!-- Form Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <!-- Gender -->
                                <div class="form-group">
                                    <label for="gender" class="form-label">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Gender
                                    </label>
                                    <select name="gender" id="gender" required class="form-input form-select" aria-describedby="gender-help">
                                        <option value="">Select Gender</option>
                                        <option value="Male" <?= (isset($gender) && $gender == 'Male') ? 'selected' : '' ?>>Male</option>
                                        <option value="Female" <?= (isset($gender) && $gender == 'Female') ? 'selected' : '' ?>>Female</option>
                                    </select>
                                    <div id="gender-help" class="sr-only">Choose the gender category for the tournament</div>
                                </div>

                                <!-- Age Group -->
                                <div class="form-group">
                                    <label for="age_group" class="form-label">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Age Group
                                    </label>
                                    <select name="age_group" id="age_group" required class="form-input form-select" aria-describedby="age-help">
                                        <option value="">Select Age Group</option>
                                        <option value="Under 14" <?= (isset($age_group) && $age_group == 'Under 14') ? 'selected' : '' ?>>Under 14 (Sub-Junior)</option>
                                        <option value="Under 18" <?= (isset($age_group) && $age_group == 'Under 18') ? 'selected' : '' ?>>Under 18 (Junior)</option>
                                        <option value="Over 18" <?= (isset($age_group) && $age_group == 'Over 18') ? 'selected' : '' ?>>Over 18 (Senior)</option>
                                    </select>
                                    <div id="age-help" class="sr-only">Select the age category for the tournament</div>
                                </div>

                                <!-- Weight Category -->
                                <div class="form-group">
                                    <label for="weight_category" class="form-label">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                                        </svg>
                                        Weight Category
                                    </label>
                                    <select id="weight_category" name="weight_category" required class="form-input form-select" aria-describedby="weight-help">
                                        <option value="">Select Weight</option>
                                        <?php if (isset($weight_category)): ?>
                                            <option selected><?= htmlspecialchars($weight_category) ?></option>
                                        <?php endif; ?>
                                    </select>
                                    <div id="weight-help" class="sr-only">Choose the weight category based on selected gender and age group</div>
                                </div>

                                <!-- Event Type -->
                                <div class="form-group">
                                    <label for="event_type" class="form-label">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                        Event Type
                                    </label>
                                    <select name="event_type" id="event_type" required class="form-input form-select" aria-describedby="event-help">
                                        <option value="">Select Event Type</option>
                                        <option value="Phunaba-Ama" <?= (isset($event_type) && $event_type == 'Phunaba-Ama') ? 'selected' : '' ?>>Phunaba-Ama</option>
                                        <option value="Phunaba-Anishuba" <?= (isset($event_type) && $event_type == 'Phunaba-Anishuba') ? 'selected' : '' ?>>Phunaba-Anishuba</option>
                                    </select>
                                    <div id="event-help" class="sr-only">Select the type of tournament event</div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                                <button type="submit" class="btn btn-primary">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Apply Filters
                                </button>
                                <?php if ($role === 'scorer'): ?>
                                    <a href="/logout" class="btn btn-danger">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7"></path>
                                        </svg>
                                        Logout
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tournament Management Card -->
                <?php if ($role === 'admin' || $role === 'judge'): ?>
                    <div class="card animate-slideInFromTop mb-8">
                        <div class="card-header">
                            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                                </svg>
                                Tournament Management
                            </h2>
                        </div>
                        <div class="card-body">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                <!-- Generate Fixtures -->
                                <form action="/fixtures/generate" method="POST" class="contents">
                                    <input type="hidden" name="event_type" value="<?= htmlspecialchars($event_type ?? '') ?>">
                                    <input type="hidden" name="weight_category" value="<?= htmlspecialchars($weight_category ?? '') ?>">
                                    <input type="hidden" name="age_group" value="<?= htmlspecialchars($age_group ?? '') ?>">
                                    <input type="hidden" name="gender" value="<?= htmlspecialchars($gender ?? '') ?>">
                                    <button type="submit" class="btn btn-warning w-full" aria-describedby="generate-help">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Generate Bracket
                                    </button>
                                    <div id="generate-help" class="sr-only">Create tournament bracket for selected criteria</div>
                                </form>

                                <!-- Export Fixtures -->
                                <a href="/fixtures/export?event_type=<?= urlencode($event_type ?? '') ?>&weight_category=<?= urlencode($weight_category ?? '') ?>&age_group=<?= urlencode($age_group ?? '') ?>&gender=<?= urlencode($gender ?? '') ?>" 
                                   class="btn btn-success w-full" aria-describedby="export-fixtures-help">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Export Fixtures
                                </a>
                                <div id="export-fixtures-help" class="sr-only">Download tournament fixtures as a file</div>

                                <!-- Export Winners -->
                                <a href="/fixtures/exportWinners?event_type=<?= urlencode($event_type ?? '') ?>&weight_category=<?= urlencode($weight_category ?? '') ?>&age_group=<?= urlencode($age_group ?? '') ?>&gender=<?= urlencode($gender ?? '') ?>" 
                                   class="btn btn-purple w-full" aria-describedby="export-winners-help">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                    </svg>
                                    Export Winners
                                </a>
                                <div id="export-winners-help" class="sr-only">Download tournament winners list</div>

                                <!-- Delete Fixtures -->
                                <?php if (!empty($fixtures)) : ?>
                                    <form action="/fixtures/delete" method="POST" class="contents" onsubmit="return confirm('Are you sure you want to delete this bracket? This action cannot be undone.');">
                                        <input type="hidden" name="event_type" value="<?= htmlspecialchars($event_type ?? '') ?>">
                                        <input type="hidden" name="weight_category" value="<?= htmlspecialchars($weight_category ?? '') ?>">
                                        <input type="hidden" name="age_group" value="<?= htmlspecialchars($age_group ?? '') ?>">
                                        <input type="hidden" name="gender" value="<?= htmlspecialchars($gender ?? '') ?>">
                                        <button type="submit" class="btn btn-danger w-full" aria-describedby="delete-help">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete Bracket
                                        </button>
                                        <div id="delete-help" class="sr-only">Permanently delete the current tournament bracket</div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Tournament Bracket Header -->
                <div class="text-center mb-12 animate-slideInFromTop">
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                        🏆 Tournament Bracket
                    </h1>
                    <?php if (!empty($event_type) || !empty($weight_category) || !empty($age_group) || !empty($gender)): ?>
                        <div class="flex flex-wrap justify-center gap-2 text-sm">
                            <?php if (!empty($gender)): ?>
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full font-semibold"><?= htmlspecialchars($gender) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($age_group)): ?>
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full font-semibold"><?= htmlspecialchars($age_group) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($weight_category)): ?>
                                <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full font-semibold"><?= htmlspecialchars($weight_category) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($event_type)): ?>
                                <span class="px-3 py-1 bg-orange-100 text-orange-800 rounded-full font-semibold"><?= htmlspecialchars($event_type) ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tournament Bracket Display -->
                <?php if (empty($fixtures)) : ?>
                    <div class="card animate-scaleIn">
                        <div class="card-body text-center py-16">
                            <svg class="w-24 h-24 text-gray-300 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-600 mb-2">No Tournament Bracket Available</h3>
                            <p class="text-gray-500 mb-6">Please select your tournament criteria and generate the bracket to get started.</p>
                            <?php if ($role === 'admin' || $role === 'judge'): ?>
                                <p class="text-sm text-gray-400">Use the tournament management tools above to create a new bracket.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="card animate-scaleIn">
                        <div class="card-body p-4 sm:p-6">
                            <?php 
                            // Split fixtures into rounds and finals
                            $rounds = [];
                            $finals = [];
                            foreach ($fixtures as $fixture) {
                                if (!empty($fixture['medal_type'])) {
                                    $finals[] = $fixture; // Gold/Bronze
                                } else {
                                    $rounds[$fixture['round']][] = $fixture; // Normal rounds
                                }
                            }
                            ?>

                            <div class="bracket-container">
                                <!-- Render non-medal rounds -->
                                <?php foreach ($rounds as $roundNumber => $roundFixtures) : ?>
                                    <div class="bracket-round">
                                        <div class="bracket-round-header text-blue-700">
                                            Round <?= htmlspecialchars((string)$roundNumber) ?>
                                        </div>

                                        <?php foreach ($roundFixtures as $index => $fixture) :
                                            $role = $_SESSION['user']['role'] ?? 'public';
                                            $fid = (int)($fixture['id'] ?? 0);
                                            switch ($role) {
                                                case 'judge': $url = "/judge/match/{$fid}"; break;
                                                case 'scorer': $url = "/scorer/match/{$fid}"; break;
                                                default: $url = "/public/match/{$fid}";
                                            }
                                            $safeUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
                                        ?>
                                            <a href="<?= $safeUrl ?>" 
                                               class="match-card animate-slideInFromRight" 
                                               style="animation-delay: <?= $index * 0.1 ?>s"
                                               data-fixture-id="<?= $fid ?>"
                                               role="button"
                                               tabindex="0"
                                               aria-label="View match details for <?= htmlspecialchars($fixture['p1_name'] ?? 'TBD') ?> vs <?= htmlspecialchars($fixture['p2_name'] ?? 'TBD') ?>">

                                                <!-- Red Corner Participant -->
                                                <div class="participant participant-red">
                                                    <span><?= htmlspecialchars($fixture['p1_name'] ?? 'TBD') ?></span>
                                                </div>

                                                <!-- VS Divider -->
                                                <div class="vs-divider">vs</div>

                                                <!-- Blue Corner Participant -->
                                                <div class="participant <?= empty($fixture['p2_name']) ? 'participant-bye' : 'participant-blue' ?>">
                                                    <span>
                                                        <?= !empty($fixture['p2_name']) 
                                                            ? htmlspecialchars($fixture['p2_name']) 
                                                            : 'BYE' ?>
                                                    </span>
                                                </div>

                                                <!-- Winner Announcement -->
                                                <?php if (!empty($fixture['winner_id'])) : ?>
                                                    <div class="winner-announcement">
                                                        <div class="font-bold mb-1">
                                                            🏆 Winner: 
                                                            <?= ($fixture['winner_id'] == $fixture['participant1_id']) 
                                                                ? htmlspecialchars($fixture['p1_name'] ?? 'Unknown') 
                                                                : htmlspecialchars($fixture['p2_name'] ?? 'Unknown') ?>
                                                        </div>
                                                        <div class="match-score">
                                                            <?= htmlspecialchars($fixture['p1_name'] ?? 'Unknown') ?>: <?= htmlspecialchars($fixture['score_a'] ?? '0') ?> - 
                                                            <?= htmlspecialchars($fixture['p2_name'] ?? 'Unknown') ?>: <?= htmlspecialchars($fixture['score_b'] ?? '0') ?>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="text-center text-gray-500 text-sm mt-3 font-medium">
                                                        Match Pending
                                                    </div>
                                                <?php endif; ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endforeach; ?>

                                <!-- Finals Column -->
                                <?php if (!empty($finals)) : ?>
                                    <div class="bracket-round">
                                        <div class="bracket-round-header text-yellow-700">
                                            Finals
                                        </div>
                                        <?php foreach ($finals as $index => $fixture) :
                                            $role = $_SESSION['user']['role'] ?? 'public';
                                            $fid = (int)($fixture['id'] ?? 0);
                                            switch ($role) {
                                                case 'judge': $url = "/judge/match/{$fid}"; break;
                                                case 'scorer': $url = "/scorer/match/{$fid}"; break;
                                                default: $url = "/public/match/{$fid}";
                                            }
                                            $safeUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
                                            
                                            $medalClass = '';
                                            $title = '';
                                            $titleClass = '';
                                            if ($fixture['medal_type'] === 'gold') {
                                                $medalClass = 'medal-gold';
                                                $title = '🥇 Gold Medal Match';
                                                $titleClass = 'medal-gold-title';
                                            }
                                            if ($fixture['medal_type'] === 'bronze') {
                                                $medalClass = 'medal-bronze';
                                                $title = '🥉 Bronze Medal Match';
                                                $titleClass = 'medal-bronze-title';
                                            }
                                        ?>
                                            <a href="<?= $safeUrl ?>" 
                                               class="match-card medal-match <?= $medalClass ?> animate-slideInFromRight" 
                                               style="animation-delay: <?= $index * 0.1 + 0.3 ?>s"
                                               data-fixture-id="<?= $fid ?>"
                                               role="button"
                                               tabindex="0"
                                               aria-label="View <?= $title ?> details for <?= htmlspecialchars($fixture['p1_name'] ?? 'TBD') ?> vs <?= htmlspecialchars($fixture['p2_name'] ?? 'TBD') ?>">

                                                <!-- Medal Title -->
                                                <?php if ($title) : ?>
                                                    <div class="medal-title <?= $titleClass ?>">
                                                        <?= $title ?>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- Red Corner Participant -->
                                                <div class="participant participant-red">
                                                    <span><?= htmlspecialchars($fixture['p1_name'] ?? 'TBD') ?></span>
                                                </div>

                                                <!-- VS Divider -->
                                                <div class="vs-divider">vs</div>

                                                <!-- Blue Corner Participant -->
                                                <div class="participant <?= empty($fixture['p2_name']) ? 'participant-bye' : 'participant-blue' ?>">
                                                    <span>
                                                        <?= !empty($fixture['p2_name']) 
                                                            ? htmlspecialchars($fixture['p2_name']) 
                                                            : 'BYE' ?>
                                                    </span>
                                                </div>

                                                <!-- Winner Announcement -->
                                                <?php if (!empty($fixture['winner_id'])) : ?>
                                                    <div class="winner-announcement">
                                                        <div class="font-bold mb-1">
                                                            🏆 Winner: 
                                                            <?= ($fixture['winner_id'] == $fixture['participant1_id']) 
                                                                ? htmlspecialchars($fixture['p1_name'] ?? 'Unknown') 
                                                                : htmlspecialchars($fixture['p2_name'] ?? 'Unknown') ?>
                                                        </div>
                                                        <div class="match-score">
                                                            <?= htmlspecialchars($fixture['p1_name'] ?? 'Unknown') ?>: <?= htmlspecialchars($fixture['score_a'] ?? '0') ?> - 
                                                            <?= htmlspecialchars($fixture['p2_name'] ?? 'Unknown') ?>: <?= htmlspecialchars($fixture['score_b'] ?? '0') ?>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="text-center text-gray-500 text-sm mt-3 font-medium">
                                                        Match Pending
                                                    </div>
                                                <?php endif; ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        // Weight category options data
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
            }
        };

        // Get form elements
        const genderSelect = document.querySelector('select[name="gender"]');
        const ageGroupSelect = document.querySelector('select[name="age_group"]');
        const weightCategorySelect = document.getElementById('weight_category');

        /**
         * Updates weight category options based on selected gender and age group
         */
        function updateWeightCategories() {
            const gender = genderSelect.value;
            const ageGroup = ageGroupSelect.value;

            // Clear previous options
            weightCategorySelect.innerHTML = '<option value="">Select Weight</option>';

            // Add loading state
            if (gender && ageGroup) {
                weightCategorySelect.classList.add('loading');
                
                // Simulate slight delay for better UX
                setTimeout(() => {
                    if (weightOptions[ageGroup] && weightOptions[ageGroup][gender]) {
                        weightOptions[ageGroup][gender].forEach(weight => {
                            const option = document.createElement('option');
                            option.value = weight;
                            option.textContent = weight;
                            weightCategorySelect.appendChild(option);
                        });
                    }
                    weightCategorySelect.classList.remove('loading');
                }, 150);
            }
        }

        /**
         * Enhanced form interaction handlers
         */
        function initializeFormHandlers() {
            // Add change event listeners
            genderSelect.addEventListener('change', updateWeightCategories);
            ageGroupSelect.addEventListener('change', updateWeightCategories);

            // Add enhanced visual feedback for form interactions
            const formInputs = document.querySelectorAll('.form-input');
            formInputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('focused');
                });
            });

            // Preload weight categories if values are already set
            if (genderSelect.value && ageGroupSelect.value) {
                updateWeightCategories();
            }
        }

        /**
         * Enhanced accessibility features
         */
        function initializeAccessibility() {
            // Add keyboard navigation for match cards
            const matchCards = document.querySelectorAll('.match-card');
            matchCards.forEach(card => {
                card.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            });

            // Add focus management for better keyboard navigation
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        if (this.tagName === 'BUTTON') {
                            e.preventDefault();
                            this.click();
                        }
                    }
                });
            });
        }

        /**
         * Initialize enhanced interactions
         */
        function initializeEnhancements() {
            // Add smooth scrolling for better UX
            document.documentElement.style.scrollBehavior = 'smooth';

            // Add loading states for form submissions
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const submitButton = this.querySelector('button[type="submit"]');
                    if (submitButton) {
                        submitButton.classList.add('loading');
                        submitButton.disabled = true;
                        
                        const originalText = submitButton.innerHTML;
                        submitButton.innerHTML = `
                            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        `;
                    }
                });
            });
        }

        // Initialize all enhancements when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            initializeFormHandlers();
            initializeAccessibility();
            initializeEnhancements();
        });

        // Add responsive handling for bracket container
        function handleBracketResize() {
            const bracketContainer = document.querySelector('.bracket-container');
            if (bracketContainer) {
                const rounds = bracketContainer.querySelectorAll('.bracket-round');
                if (rounds.length > 3 && window.innerWidth < 768) {
                    bracketContainer.style.justifyContent = 'flex-start';
                } else {
                    bracketContainer.style.justifyContent = 'center';
                }
            }
        }

        // Add resize listener
        window.addEventListener('resize', handleBracketResize);
        document.addEventListener('DOMContentLoaded', handleBracketResize);
    </script>

</body>
</html>