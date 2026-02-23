<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    header('Content-Type: application/json');
    $id = intval($_POST['edit_id']);
    $name = $_POST['edit_name'];
    $expiration_date = $_POST['edit_expiration_date'];
    $lot_no = $_POST['edit_lot_no'];
    $initial_quantity = intval($_POST['edit_initial_quantity']);
    $final_quantity = intval($_POST['edit_final_quantity']);

    // Load current row for logging
    $current = null;
    $res = $conn->query("SELECT * FROM monthly_log WHERE id = $id");
    if ($res && $res->num_rows > 0) { $current = $res->fetch_assoc(); }

    $stmt = $conn->prepare("UPDATE monthly_log SET name=?, expiration_date=?, lot_no=?, initial_quantity=?, final_quantity=? WHERE id=?");
    $stmt->bind_param("sssiii", $name, $expiration_date, $lot_no, $initial_quantity, $final_quantity, $id);
    $success = $stmt->execute();
    $stmt->close();

    if ($success && $current) {
        if ($current['name'] !== $name) log_history($conn, $id, 'edit', 'name', (string)$current['name'], (string)$name);
        if ($current['expiration_date'] !== $expiration_date) log_history($conn, $id, 'edit', 'expiration_date', (string)$current['expiration_date'], (string)$expiration_date);
        if ($current['lot_no'] !== $lot_no) log_history($conn, $id, 'edit', 'lot_no', (string)$current['lot_no'], (string)$lot_no);
        if ((string)$current['initial_quantity'] !== (string)$initial_quantity) log_history($conn, $id, 'edit', 'initial_quantity', (string)$current['initial_quantity'], (string)$initial_quantity);
        if ((string)$current['final_quantity'] !== (string)$final_quantity) log_history($conn, $id, 'update_quantity', 'final_quantity', (string)$current['final_quantity'], (string)$final_quantity);
    }

    echo json_encode(['success' => $success]);
    $conn->close();
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM monthly_log WHERE id = $id");
    if ($result && $result->num_rows > 0) {
        $entry = $result->fetch_assoc();
    } else {
        echo '<p>Entry not found.</p>';
        $conn->close();
        exit();
    }
} else {
    echo '<p>No entry selected for editing.</p>';
    $conn->close();
    exit();
}
?>

<head>
    <link rel="stylesheet" href="assets/css/edit_monthly_log.css">
</head>
<div id="editModalHeader">
    <h2>Edit Log Entry</h2>
</div>
<form id="editMonthlyLogForm">
    <input type="hidden" name="edit_id" value="<?= htmlspecialchars($entry['id']) ?>">
    <label for="edit_name">Name:</label>
    <input type="text" id="edit_name" name="edit_name" value="<?= htmlspecialchars($entry['name']) ?>" required>
    <label for="edit_expiration_date">Expiration Date:</label>
    <input type="date" id="edit_expiration_date" name="edit_expiration_date" value="<?= htmlspecialchars($entry['expiration_date']) ?>">
    <label for="edit_lot_no">Lot No.:</label>
    <input type="text" id="edit_lot_no" name="edit_lot_no" value="<?= htmlspecialchars($entry['lot_no']) ?>">
    <label for="edit_initial_quantity">Initial Quantity:</label>
    <input type="number" id="edit_initial_quantity" name="edit_initial_quantity" value="<?= htmlspecialchars($entry['initial_quantity']) ?>" required>
    <label for="edit_final_quantity">Final Quantity:</label>
    <input type="number" id="edit_final_quantity" name="edit_final_quantity" value="<?= htmlspecialchars($entry['final_quantity']) ?>" required>
    <button type="submit">Save Changes</button>
</form>
<script>
document.getElementById('editMonthlyLogForm').onsubmit = function(ev) {
    ev.preventDefault();
    var formData = new FormData(this);
    fetch('edit_monthly_log.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.parent.document.getElementById('editModal').style.display = 'none';
            window.parent.location.reload();
        } else {
            alert('Update failed.');
        }
    });
};
</script>
<?php $conn->close(); ?>
