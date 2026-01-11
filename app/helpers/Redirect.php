<?php
class Redirect
{
    public static function to($location)
    {
        if (!empty($location)) {
            header('Location: ' . $location);
            exit();
        }
    }
}
