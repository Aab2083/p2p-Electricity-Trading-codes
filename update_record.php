
<?php
file_put_contents('received_data_log.txt', date('Y-m-d H:i:s') . "\n" . print_r($_POST, true) . "\n\n", FILE_APPEND);
require 'database.php';

try {
    if (!empty($_POST)) {
        $id = $_POST['id'];
        $status = $_POST['status'];
        $current = $_POST['current'];
        $voltage = $_POST['voltage'];
        $units_transfered = $_POST['units_transfered'];
        $buying_mode = $_POST['buying_mode'];
        $selling_mode = $_POST['selling_mode'];

        date_default_timezone_set("Asia/Jakarta");
        $tm = date("H:i:s");
        $dt = date("Y-m-d");

        // Updating the data in the table
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $updateSql = "UPDATE esp32_01_update SET status = ?, current = ?, voltage = ?, units_transfered = ?, buying_mode = ?, selling_mode = ?, time = ?, date = ? WHERE id = ?";
        $updateQuery = $pdo->prepare($updateSql);
        $updateQuery->execute(array($status, $current, $voltage, $units_transfered, $buying_mode, $selling_mode, $tm, $dt, $id));

        // Entering data into a table
        $id_key;
        $board = $_POST['id'];
        $found_empty = false;

        while ($found_empty == false) {
            $id_key = generate_string_id(10);
            $selectSql = 'SELECT * FROM esp32_01_record WHERE id = ?';
            $selectQuery = $pdo->prepare($selectSql);
            $selectQuery->execute(array($id_key));

            if (!$data = $selectQuery->fetch()) {
                $found_empty = true;
            }
        }

        $insertSql = "INSERT INTO esp32_01_record (id, board, status, current, voltage, units_transfered, buying_mode, selling_mode, time, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $insertQuery = $pdo->prepare($insertSql);
        $insertQuery->execute(array($id_key, $board, $status, $current, $voltage, $units_transfered, $buying_mode, $selling_mode, $tm, $dt));

        Database::disconnect();
    } else {
        throw new Exception("Invalid request. POST data is empty.");

    }
} catch (Exception $e) {
    $response = array("success" => false, "message" => $e->getMessage());
    echo json_encode($response);
}

function generate_string_id($strength = 16) {
    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $input_length = strlen($permitted_chars);
    $random_string = '';
    for ($i = 0; $i < $strength; $i++) {
        $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
    }
    return $random_string;
}
?>
