<?php

class HomeController extends Controller
{
    public function index()
    {
        $this->view('home/landing'); // looks for app/views/home/landing.php
    }
}
