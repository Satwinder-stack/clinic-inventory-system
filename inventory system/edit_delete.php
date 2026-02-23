<?php
include 'db_connect.php';


if (isset($_POST['bulk_delete']) && isset($_POST['delete_ids']) && is_array($_POST['delete_ids'])) {
    $ids = array_map('intval', $_POST['delete_ids']);
    foreach ($ids as $id) {
        $result = $conn->query("SELECT * FROM items WHERE id = $id");
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            log_history($conn, $id, 'delete', 'name', $row['name'], '');
            log_history($conn, $id, 'delete', 'quantity', $row['quantity'], '');
            log_history($conn, $id, 'delete', 'expiration_date', $row['expiration_date'], '');
            log_history($conn, $id, 'delete', 'lot_no', $row['lot_no'], '');
        }
        if (isset($row)) {
            $stmt2 = $conn->prepare("DELETE FROM monthly_log WHERE name=? AND expiration_date=? AND lot_no=?");
            $stmt2->bind_param("sss", $row['name'], $row['expiration_date'], $row['lot_no']);
            $stmt2->execute();
            $stmt2->close();
        }
        $stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
    $redirect = 'index.php';
    if (isset($_POST['from']) && $_POST['from'] === 'inventory.php') {
        $redirect = 'inventory.php';
    }
    header('Location: ' . $redirect);
    exit();
}
if (isset($_POST['bulk_delete_monthly_log']) && isset($_POST['delete_monthly_log_ids']) && is_array($_POST['delete_monthly_log_ids'])) {
    $ids = array_map('intval', $_POST['delete_monthly_log_ids']);
    foreach ($ids as $id) {
        // Preload for logging
        $stmtPre = $conn->prepare("SELECT name, expiration_date, lot_no, final_quantity FROM monthly_log WHERE id = ?");
        $stmtPre->bind_param("i", $id);
        if ($stmtPre->execute()) {
            $res = $stmtPre->get_result();
            if ($row = $res->fetch_assoc()) {
                $old = sprintf('%s | %s | %s | qty=%s', $row['name'], $row['expiration_date'], $row['lot_no'], $row['final_quantity']);
                log_history($conn, $id, 'delete', 'monthly_log_row', $old, '');
            }
        }
        $stmtPre->close();
        // Delete
        $stmt = $conn->prepare("DELETE FROM monthly_log WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
    $redirect = 'monthly_log.php';
    if (isset($_POST['from']) && $_POST['from'] === 'monthly_log.php') {
        $redirect = 'monthly_log.php';
    }
    header('Location: ' . $redirect);
    exit();
}
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $result = $conn->query("SELECT * FROM items WHERE id = $id");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        log_history($conn, $id, 'delete', 'name', $row['name'], '');
        log_history($conn, $id, 'delete', 'quantity', $row['quantity'], '');
        log_history($conn, $id, 'delete', 'expiration_date', $row['expiration_date'], '');
        log_history($conn, $id, 'delete', 'lot_no', $row['lot_no'], '');
    }
    if (isset($row)) {
        $stmt2 = $conn->prepare("DELETE FROM monthly_log WHERE name=? AND expiration_date=? AND lot_no=?");
        $stmt2->bind_param("sss", $row['name'], $row['expiration_date'], $row['lot_no']);
        $stmt2->execute();
        $stmt2->close();
    }
    $stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $redirect = 'index.php';
    if (isset($_GET['from']) && $_GET['from'] === 'inventory.php') {
        $redirect = 'inventory.php';
    }
    header('Location: ' . $redirect);
    exit();
}
if (isset($_GET['delete_monthly_log']) && is_numeric($_GET['delete_monthly_log'])) {
    $id = intval($_GET['delete_monthly_log']);
    // Preload for logging
    $stmtPre = $conn->prepare("SELECT name, expiration_date, lot_no, final_quantity FROM monthly_log WHERE id = ?");
    $stmtPre->bind_param("i", $id);
    if ($stmtPre->execute()) {
        $res = $stmtPre->get_result();
        if ($row = $res->fetch_assoc()) {
            $old = sprintf('%s | %s | %s | qty=%s', $row['name'], $row['expiration_date'], $row['lot_no'], $row['final_quantity']);
            log_history($conn, $id, 'delete', 'monthly_log_row', $old, '');
        }
    }
    $stmtPre->close();
    $stmt = $conn->prepare("DELETE FROM monthly_log WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $redirect = 'monthly_log.php';
    if (isset($_GET['from']) && $_GET['from'] === 'monthly_log.php') {
        $redirect = 'monthly_log.php';
    }
    header('Location: ' . $redirect);
    exit();
}

if (isset($_POST['edit_id'])) {
    $id = intval($_POST['edit_id']);
    $name = $_POST['edit_name'] ?? '';
    $quantity = $_POST['edit_quantity'] ?? 0;
    $expiration_date = $_POST['edit_expiration_date'] ?? '';
    $lot_no = $_POST['edit_lot_no'] ?? '';
    // Load current item for logging
    $old = null;
    $res = $conn->query("SELECT * FROM items WHERE id = $id");
    if ($res && $res->num_rows > 0) { $old = $res->fetch_assoc(); }
    $stmt = $conn->prepare("UPDATE items SET name=?, quantity=?, expiration_date=?, lot_no=? WHERE id=?");
    $stmt->bind_param("sissi", $name, $quantity, $expiration_date, $lot_no, $id);
    $stmt->execute();
    $stmt->close();
    if ($old) {
        if ($old['name'] !== $name) log_history($conn, $id, 'edit', 'name', (string)$old['name'], (string)$name);
        if ((string)$old['quantity'] !== (string)$quantity) log_history($conn, $id, 'edit', 'quantity', (string)$old['quantity'], (string)$quantity);
        if ($old['expiration_date'] !== $expiration_date) log_history($conn, $id, 'edit', 'expiration_date', (string)$old['expiration_date'], (string)$expiration_date);
        if ($old['lot_no'] !== $lot_no) log_history($conn, $id, 'edit', 'lot_no', (string)$old['lot_no'], (string)$lot_no);
    }
    header('Location: index.php');
    exit();
}

$item = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM items WHERE id = $id");
    if ($result && $result->num_rows > 0) {
        $item = $result->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Item</title>
    <link rel="stylesheet" href="assets/css/edit_delete.css">
</head>
<body>
    <div class="container">
        <?php if ($item): ?>
            <h2>Edit Item</h2>
            <form method="POST">
                <input type="hidden" name="edit_id" value="<?= htmlspecialchars($item['id']) ?>">
                <label for="edit_name">Item Name:</label>
                <input type="text" id="edit_name" name="edit_name" value="<?= htmlspecialchars($item['name']) ?>" required>
                <label for="edit_quantity">Quantity:</label>
                <input type="number" id="edit_quantity" name="edit_quantity" value="<?= htmlspecialchars($item['quantity']) ?>" min="0" required>
                <label for="edit_expiration_date">Expiration Date:</label>
                <input type="date" id="edit_expiration_date" name="edit_expiration_date" value="<?= htmlspecialchars($item['expiration_date']) ?>" required>
                <label for="edit_lot_no">Lot No.:</label>
                <input type="text" id="edit_lot_no" name="edit_lot_no" value="<?= htmlspecialchars($item['lot_no']) ?>" required>
                <button type="submit">Save Changes</button>
            </form>
        <?php else: ?>
            <p>Item not found or no item selected for editing.</p>
        <?php endif; ?>
        <a href="index.php">&larr; Back to Inventory</a>
    </div>
</body>
</html>
<?php $conn->close(); ?>
