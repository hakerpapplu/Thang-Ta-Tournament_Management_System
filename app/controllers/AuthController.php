<?php

require_once 'app/models/Admin.php';

class AuthController extends Controller {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);
    
            if (empty($username) || empty($password)) {
                return $this->view('auth/login', ['error' => 'All fields are required.']);
            }
    
            $adminModel = $this->model('Admin');
            $admin = $adminModel->findByUsername($username);
    
            if ($admin && password_verify($password, $admin['password'])) {
                session_start();
                $_SESSION['user'] = [
                    'id'       => $admin['id'],
                    'username' => $admin['username'],
                    'role'     => $admin['role'],
                ];
    
                // ✅ Role-based redirection
                switch ($admin['role']) {
                    case 'scorer':
                        header("Location: /fixtures");
                        break;
                    case 'judge':
                        header("Location: /dashboard"); // or maybe /fixtures if you prefer
                        break;
                    case 'admin':
                    default:
                        header("Location: /dashboard");
                        break;
                }
                exit;
            } else {
                return $this->view('auth/login', ['error' => 'Invalid credentials.']);
            }
        } else {
            return $this->view('auth/login'); // ✅ return added
        }
    }



    public function logout()
    {
        session_unset();
        session_destroy();
        header("Location: https://mahathangta.in");
        exit;
    }
}
