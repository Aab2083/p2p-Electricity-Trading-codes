<?php
include 'database.php';

// Initialize response array
$response = array();

try {
    // Attempt to connect to the database
    $pdo = Database::connect();

    // Check if the 'id' parameter is set in the POST request
    if (!empty($_POST['id'])) {
        // Sanitize the 'id' parameter to prevent SQL injection
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);

        // Retrieve data from the database based on the 'id'
        $sql = 'SELECT * FROM esp32_01_update WHERE id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Format date
            $date = date_create($row['date']);
            $dateFormat = date_format($date, "d-m-Y");

            // Prepare data array
            $data = array(
                'id' => $row['id'],
                'status' => $row['status'],
                'current' => $row['current'],
                'voltage' => $row['voltage'],
                'units_transfered' => $row['units_transfered'],
                'buying_mode' => $row['buying_mode'],
                'selling_mode' => $row['selling_mode'],
                'ls_time' => $row['time'],
                'ls_date' => $dateFormat
            );

            // Add the data to the response array
            $response = $data;
        } else {
            // If no data was retrieved, set an error message
            $response['error'] = 'No data found for the given ID.';
        }
    } else {
        // If 'id' is not present in the request, set an error message
        $response['error'] = 'Invalid request. Missing ID.';
    }

    // Disconnect from the database
    Database::disconnect();

} catch (PDOException $e) {
    // If an exception occurs (database connection error), set an error message
    $response['error'] = 'Database connection failed: ' . $e->getMessage();
}

// Output the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
