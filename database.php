<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

class Database {
    private static $dbName = 'esp32_1_db';
    private static $dbHost = 'localhost';
    private static $dbUsername = 'root';
    private static $dbUserPassword = '';
    private static $cont = null;

    private function __construct() {
        // Make the constructor private to prevent creating instances
        die('Init function is not allowed');
    }

    public static function connect() {
        try {
            if (null == self::$cont) {
                self::$cont = new PDO("mysql:host=" . self::$dbHost . ";" . "dbname=" . self::$dbName, self::$dbUsername, self::$dbUserPassword);
                // Set the PDO error mode to exception
                self::$cont->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
        } catch (PDOException $e) {
            // If there's an error, throw an exception
            throw new Exception("Database connection failed: " . $e->getMessage());
        }

        return self::$cont;
    }

    public static function disconnect() {
        self::$cont = null;
    }
}

try {
    // Example usage without creating an instance
    $connection = Database::connect();
    // If you reach here, the connection was successful
} catch (Exception $ex) {
    // Catch and display any unhandled exceptions
    echo json_encode(array('error' => $ex->getMessage()));
    exit(); // Stop execution after displaying the error message
}
?>
