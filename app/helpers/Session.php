<?php

class Session {
    public static function exists($name) {
        return (isset($_SESSION[$name]));
    }

    public static function flash($name) {
        if (self::exists($name)) {
            $session = $_SESSION[$name];
            unset($_SESSION[$name]);
            return $session;
        }
        return '';
    }

    public static function put($name, $value) {
        return $_SESSION[$name] = $value;
    }

    public static function delete($name) {
        if (self::exists($name)) {
            unset($_SESSION[$name]);
        }
    }
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }
}
