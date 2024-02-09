<?php
ob_start(); // Start output buffering

require 'database.php';

// Initialize response array
$response = array();

// Check if the request method is set and is POST
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the required parameters are set
    if (isset($_POST['id'], $_POST['modes'], $_POST['status'])) {
        // Keep track of post values
        $id = $_POST['id'];
        $modes = $_POST['modes'];
        $status = $_POST['status'];

        try {
            // Attempt to connect to the database
            $pdo = Database::connect();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Use prepared statement to update the status of the modes
            $sql = "UPDATE esp32_01_update SET $modes = ? WHERE id = ?";
            $q = $pdo->prepare($sql);
            $q->execute(array($status, $id));

            // Disconnect from the database
            Database::disconnect();

            // Set success message in the response
            $response['success'] = 'Toggle button status updated successfully.';
        } catch (PDOException $e) {
            // If an exception occurs (database connection error), set an error message
            $response['error'] = 'Database connection failed: ' . $e->getMessage();
        }
    } else {
        // If required parameters are missing, set an error message
        $response['error'] = 'Invalid request. Missing parameters.';
    }
} else {
    // If the request method is not POST, set an error message
    $response['error'] = 'Invalid request method.';
}

// Output the response as JSON
header('Content-Type: application/json');
echo json_encode($response);

ob_end_flush(); // Flush output buffer and send it to the browser
?>
