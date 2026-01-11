<?php

class Controller {
    public function model($model) {
        require_once 'app/models/' . $model . '.php';
        return new $model;
    }

    public function view($view, $data = []) {
        extract($data); // ✅ This makes $users, $fixtures, etc. available directly
        require_once 'app/views/' . $view . '.php';
    }
}
