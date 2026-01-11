<?php
require_once 'app/helpers/session_helper.php';

class DashboardController {
    public function index() {
        // Check if admin is logged in
        requireLogin();

        // Include model to fetch dashboard stats
        require_once 'app/models/DashboardModel.php';
        $model = new DashboardModel();
        
        $stats = $model->getDashboardStats();

        // Pass stats to the view
        require 'app/views/dashboard/index.php';
    }
}
