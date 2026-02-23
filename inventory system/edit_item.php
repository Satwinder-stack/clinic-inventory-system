<?php
include 'db_connect.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $name = $_POST['name'] ?? '';
    $quantity = $_POST['quantity'] ?? 0;
    $expiration_date = $_POST['expiration_date'] ?? '';
    $lot_no = $_POST['lot_no'] ?? '';

    $result = $conn->query("SELECT * FROM items WHERE id = $id");
    $old = $result && $result->num_rows > 0 ? $result->fetch_assoc() : null;

    $stmt = $conn->prepare("UPDATE items SET name=?, quantity=?, expiration_date=?, lot_no=? WHERE id=?");
    $stmt->bind_param("sissi", $name, $quantity, $expiration_date, $lot_no, $id);
    $success = $stmt->execute();
    $stmt->close();
    $current_year = (int)date('Y');
    $current_month = (int)date('n');
    $stmt = $conn->prepare("UPDATE monthly_log SET final_quantity=? WHERE name=? AND expiration_date=? AND lot_no=? AND year=? AND month=?");
    $stmt->bind_param("isssii", $quantity, $name, $expiration_date, $lot_no, $current_year, $current_month);
    $stmt->execute();
    $stmt->close();

    if ($old) {
        if ($old['name'] !== $name) log_history($conn, $id, 'edit', 'name', $old['name'], $name);
        if ((string)$old['quantity'] !== (string)$quantity) log_history($conn, $id, 'edit', 'quantity', $old['quantity'], $quantity);
        if ($old['expiration_date'] !== $expiration_date) log_history($conn, $id, 'edit', 'expiration_date', $old['expiration_date'], $expiration_date);
        if ($old['lot_no'] !== $lot_no) log_history($conn, $id, 'edit', 'lot_no', $old['lot_no'], $lot_no);
    }
    $conn->close();
    echo json_encode(['success' => $success]);
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM items WHERE id = $id");
    if ($result && $result->num_rows > 0) {
        $item = $result->fetch_assoc();
        ?>
        <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap');
        #editItemForm {
            font-family: 'Roboto', Arial, sans-serif;
        }
        #editItemForm label {
            display: block;
            margin-top: 15px;
            font-weight: 500;
            color: #555;
        }
        #editItemForm input[type="text"],
        #editItemForm input[type="number"],
        #editItemForm input[type="date"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 1em;
            margin-top: 6px;
            box-sizing: border-box;
        }
        #editItemForm input:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.13);
            outline: none;
        }
        #editItemForm button {
            margin-top: 22px;
            padding: 12px 28px;
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: 500;
            transition: background 0.2s;
        }
        #editItemForm button:hover {
            background: #218838;
        }
        </style>
        <form id="editItemForm" autocomplete="off">
            <input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>">
            <label for="edit_name">Item Name:</label>
            <input type="text" id="edit_name" name="name" value="<?= htmlspecialchars($item['name']) ?>" required autocomplete="off">
            <label for="edit_quantity">Quantity:</label>
            <input type="number" id="edit_quantity" name="quantity" value="<?= htmlspecialchars($item['quantity']) ?>" min="0" required autocomplete="off">
            <label for="edit_expiration_date">Expiration Date:</label>
            <input type="date" id="edit_expiration_date" name="expiration_date" value="<?= htmlspecialchars($item['expiration_date']) ?>" required autocomplete="off">
            <label for="edit_lot_no">Lot No.:</label>
            <input type="text" id="edit_lot_no" name="lot_no" value="<?= htmlspecialchars($item['lot_no']) ?>" required autocomplete="off">
            <button type="submit">Save Changes</button>
        </form>
        <?php
    } else {
        echo '<p>Item not found.</p>';
    }
    $conn->close();
    exit();
}
?>
