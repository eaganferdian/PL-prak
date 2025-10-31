<?php
class Database
{
    private static ?mysqli $instance = null;

    public static function getInstance(): mysqli
    {
        if (self::$instance === null) {
            self::$instance = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if (self::$instance->connect_errno) {
                throw new Exception('DB connection failed: ' . self::$instance->connect_error);
            }
            
            if (!self::$instance->set_charset(DB_CHARSET)) {
                throw new Exception('Error setting charset: ' . self::$instance->error);
            }
        }
        return self::$instance;
    }
}