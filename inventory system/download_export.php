<?php
include 'db_connect.php';

$table = isset($_GET['table']) ? $_GET['table'] : 'monthly_log';
$date = date('Y-m-d_H-i-s');

switch ($table) {
    case 'monthly_log':
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=clinic_monthly_log_' . $date . '.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Name', 'Expiration Date', 'Lot No.', 'Initial Qty', 'Final Qty']);
        $result = $conn->query("SELECT name, expiration_date, lot_no, initial_quantity, final_quantity FROM monthly_log ORDER BY id DESC");
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                fputcsv($output, [
                    $row['name'],
                    $row['expiration_date'],
                    $row['lot_no'],
                    $row['initial_quantity'],
                    $row['final_quantity']
                ]);
            }
        }
        fclose($output);
        break;
    case 'history':
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=clinic_history_' . $date . '.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Item ID', 'Action', 'Field Changed', 'Old Value', 'New Value', 'Action Time']);
        $result = $conn->query("SELECT id, item_id, action, field_changed, old_value, new_value, action_time FROM history ORDER BY id DESC");
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                fputcsv($output, [
                    $row['id'],
                    $row['item_id'],
                    $row['action'],
                    $row['field_changed'],
                    $row['old_value'],
                    $row['new_value'],
                    $row['action_time']
                ]);
            }
        }
        fclose($output);
        break;
    default:
        http_response_code(400);
        echo 'Invalid export table.';
        break;
}
$conn->close();
exit();
