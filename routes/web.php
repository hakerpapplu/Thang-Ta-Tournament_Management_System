<?php
Router::get('/', 'HomeController@index');

Router::get('auth/login', 'AuthController@login');
Router::post('auth/login', 'AuthController@login');
Router::get('/logout', 'AuthController@logout');

Router::get('/dashboard', 'DashboardController@index');

Router::get('/participants', 'ParticipantController@index');
Router::get('/participants/district-check', 'ParticipantController@districtCheckForm');
Router::post('/participants/verify-district', 'ParticipantController@verifyDistrict');
Router::get('/participants/create', 'ParticipantController@create');
Router::post('/participants', 'ParticipantController@store');
Router::get('/participants/edit/{id}', 'ParticipantController@edit');
Router::post('/participants/update/{id}', 'ParticipantController@update');
Router::get('/participants/delete/{id}', 'ParticipantController@delete');
Router::get('/participants/export', 'ParticipantController@export');
Router::get('/participants/district-participants', 'ParticipantController@districtParticipants');
Router::get('/participants/logout', 'ParticipantController@logout');

// Existing Routes
Router::get('/fixtures', 'FixtureController@index');
/*Router::post('/fixtures/update', 'FixtureController@update');*/
Router::post('/fixtures/generate', 'FixtureController@generate');
Router::get('/fixtures/export', 'FixtureController@export'); // NEW
Router::post('/fixtures/delete', 'FixtureController@delete');

// New Routes
Router::post('/fixtures/set-scores', 'FixtureController@setScores'); // For setting scores and winners
Router::get('/fixtures/exportWinners', 'FixtureController@exportWinners'); // Export Gold, Silver, Bronze winners
Router::get('/fixtures/generate-rounds', 'FixtureController@generateRounds'); // Handle random BYE and fixtures for different genders

Router::get('/export-test', 'ExportTestController@index');

// --- Admin Routes ---
Router::get('/admin/dashboard', 'AdminController@index');
Router::post('/admin/assign-role', 'AdminController@assignRole');
Router::get('/admin/users', 'AdminController@listUsers');
Router::post('/admin/assign-default', 'AdminController@assignDefault');
Router::post('/admin/apply-defaults', 'AdminController@applyDefaultsToAllFixtures');

// --- Judge Routes ---
Router::get('/judge/match/{id}', 'JudgeController@viewMatch');
Router::post('/judge/start-match', 'JudgeController@startMatch');
Router::get('/judge/panel/{id}', 'JudgeController@panel');
Router::post('/judge/finalize', 'JudgeController@finalizeResult');
Router::post('/judge/start-round', 'JudgeController@startRound');
Router::get('/judge/live-averages/{id}', 'JudgeController@getLiveAverages');
Router::get('/judge/live-scores/{id}', 'JudgeController@getLiveScoresAndFouls');

// --- Scorer Routes ---
Router::get('/scorer/match/{id}', 'ScorerController@viewMatch');
Router::post('/scorer/enter', 'ScorerController@enterMatch');
Router::get('/scorer/panel/{id}', 'ScorerController@panel');
Router::post('/scorer/submit-score', 'ScorerController@submitScore');
Router::post('/scorer/submit-foul', 'ScorerController@submitFoul');
Router::post('/scorer/add-sub-round', 'ScorerController@addSubRound');
Router::post('/scorer/end-match', 'ScorerController@endMatch');
Router::get('/scorer/round-check/{id}', 'ScorerController@getCurrentRound');

// Public Match Display
Router::get('/public/match/{id}', 'PublicController@viewMatch');

// Public Live Averages API (for frontend polling/JS fetch)
Router::get('/public/live-averages/{id}', 'PublicController@getLiveAverages');
Router::get('/dashboard/exportAllResults', 'FixtureController@exportAllResults');







// --- Authentication ---
/*Router::get('/login', 'AuthController@showLogin');
//Router::post('/login', 'AuthController@login');
Router::get('/logout', 'AuthController@logout');*/
