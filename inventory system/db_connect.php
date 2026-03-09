<?php
$servername = "sql302.infinityfree.com";
$username   = "if0_40719545";
$password   = "satwinder09";
$dbname     = "if0_40719545_clinic_inventory";

$conn = new mysqli($servername, $username, $password, $dbname);



if (!function_exists('log_history')) {
    function log_history($conn, $item_id, $action, $field_changed, $old_value, $new_value) {
        $stmt = $conn->prepare(
            "INSERT INTO history (item_id, action, field_changed, old_value, new_value)
             VALUES (?, ?, ?, ?, ?)"
        );
        if ($stmt) {
            $stmt->bind_param("issss", $item_id, $action, $field_changed, $old_value, $new_value);
            $stmt->execute();
            $stmt->close();
        }
    }
}
?>
