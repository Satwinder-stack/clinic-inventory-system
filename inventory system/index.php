<?php
include 'db_connect.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['quantity'], $_POST['expiration_date'], $_POST['lot_no'])) {
    $name = trim($_POST['name']);
    $quantity = intval($_POST['quantity']);
    $expiration_date = $_POST['expiration_date'];
    $lot_no = trim($_POST['lot_no']);
    $year = date('Y');
    $month = date('n');
    $day = date('j');
    $unit = '';
    if ($name && $quantity && $expiration_date && $lot_no) {
        $stmt = $conn->prepare("INSERT INTO monthly_log (name, expiration_date, lot_no, initial_quantity, final_quantity, year, month, day, unit) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiiiiss", $name, $expiration_date, $lot_no, $quantity, $quantity, $year, $month, $day, $unit);
        $stmt->execute();
        $item_id = $stmt->insert_id;
        $stmt->close();
        log_history($conn, $item_id, 'add', 'name', '', $name);
        log_history($conn, $item_id, 'add', 'expiration_date', '', $expiration_date);
        log_history($conn, $item_id, 'add', 'lot_no', '', $lot_no);
        log_history($conn, $item_id, 'add', 'initial_quantity', '', $quantity);
        log_history($conn, $item_id, 'add', 'final_quantity', '', $quantity);
        header('Location: index.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_delete']) && isset($_POST['delete_ids']) && is_array($_POST['delete_ids'])) {
    $ids = array_map('intval', $_POST['delete_ids']);
    if (count($ids) > 0) {
        $in = implode(',', $ids);
        $result = $conn->query("SELECT * FROM monthly_log WHERE id IN ($in)");
        while ($row = $result->fetch_assoc()) {
            log_history($conn, $row['id'], 'delete', 'name', $row['name'], '');
            log_history($conn, $row['id'], 'delete', 'expiration_date', $row['expiration_date'], '');
            log_history($conn, $row['id'], 'delete', 'lot_no', $row['lot_no'], '');
            log_history($conn, $row['id'], 'delete', 'initial_quantity', $row['initial_quantity'], '');
            log_history($conn, $row['id'], 'delete', 'final_quantity', $row['final_quantity'], '');
        }
        $conn->query("DELETE FROM monthly_log WHERE id IN ($in)");
    }
    header('Location: index.php');
    exit();
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $result = $conn->query("SELECT * FROM monthly_log WHERE id = $id");
    if ($row = $result->fetch_assoc()) {
        log_history($conn, $id, 'delete', 'name', $row['name'], '');
        log_history($conn, $id, 'delete', 'expiration_date', $row['expiration_date'], '');
        log_history($conn, $id, 'delete', 'lot_no', $row['lot_no'], '');
        log_history($conn, $id, 'delete', 'initial_quantity', $row['initial_quantity'], '');
        log_history($conn, $id, 'delete', 'final_quantity', $row['final_quantity'], '');
    }
    $conn->query("DELETE FROM monthly_log WHERE id = $id");
    header('Location: index.php');
    exit();
}

$result = $conn->query("SELECT * FROM monthly_log ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Inventory System</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/index.css">

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var nameInput = document.getElementById('name');
        if (nameInput) nameInput.focus();
        document.body.addEventListener('click', function(e) {
            if (e.target.classList.contains('edit-link')) {
                e.preventDefault();
                var id = e.target.getAttribute('data-id');
                var modal = document.getElementById('editModal');
                var modalContent = document.getElementById('editModalContent');
                modal.style.display = 'block';
                modalContent.innerHTML = '<p>Loading...</p>';
                fetch('edit_monthly_log.php?id=' + id)
                    .then(res => res.text())
                    .then(html => {
                        modalContent.innerHTML = html;
                        var form = document.getElementById('editMonthlyLogForm');
                        if (form) {
                            form.onsubmit = function(ev) {
                                ev.preventDefault();
                                var formData = new FormData(form);
                                fetch('edit_monthly_log.php', {
                                    method: 'POST',
                                    body: formData
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        modal.style.display = 'none';
                                        location.reload();
                                    } else {
                                        alert('Update failed.');
                                    }
                                });
                            };
                        }
                    });
            }
        });
        document.addEventListener('click', function(e) {
            if (e.target.id === 'editModal') {
                e.target.style.display = 'none';
            }
        });
        let deleteModal = document.getElementById('deleteModal');
        let confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        let cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
        let deleteForm = document.getElementById('bulkDeleteForm');
        let deleteIds = [];
        let singleDeleteId = null;
        document.body.addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-link')) {
                e.preventDefault();
                singleDeleteId = e.target.getAttribute('data-id');
                deleteIds = [singleDeleteId];
                document.getElementById('deleteModalMsg').textContent = 'Are you sure you want to delete this entry?';
                deleteModal.style.display = 'block';
            }
        });
        document.getElementById('bulkDeleteBtn').addEventListener('click', function() {
            let checked = document.querySelectorAll('.row-checkbox:checked');
            if (checked.length === 0) {
                alert('Please select at least one entry to delete.');
                return;
            }
            deleteIds = Array.from(checked).map(cb => cb.value);
            singleDeleteId = null;
            document.getElementById('deleteModalMsg').textContent = 'Are you sure you want to delete the selected entry(ies)?';
            deleteModal.style.display = 'block';
        });
        confirmDeleteBtn.addEventListener('click', function() {
            if (singleDeleteId) {
                window.location.href = 'index.php?delete=' + encodeURIComponent(singleDeleteId);
            } else if (deleteIds.length > 0) {
                let form = document.getElementById('bulkDeleteForm');
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'bulk_delete';
                input.value = '1';
                form.appendChild(input);
                form.submit();
            }
            deleteModal.style.display = 'none';
        });
        cancelDeleteBtn.addEventListener('click', function() {
            deleteModal.style.display = 'none';
            singleDeleteId = null;
            deleteIds = [];
        });
        document.getElementById('selectAll').addEventListener('change', function() {
            let checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    });
    </script>
</head>
<body>
    <div class="navbar">
        <div class="title">🏥 Clinic Inventory System</div>
        <div class="nav-links">
            <a href="index.php" class="active" style="background:#007bff;">Home</a>
            <a href="add_reduce_quantity.php">Add/Reduce Quantity</a>
            <a href="monthly_log.php">Record</a>
            <a href="history.php">History</a>
            <a href="download.php">Download</a>
        </div>
    </div>
    <div class="container">
        <h2>Add to the Records</h2>
        <form method="POST" autocomplete="off">
            <label for="name">Item Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" min="0" required>
            <label for="expiration_date">Expiration Date:</label>
            <input type="date" id="expiration_date" name="expiration_date" required>
            <label for="lot_no">Lot No.:</label>
            <input type="text" id="lot_no" name="lot_no" required>
            <button type="submit" style="background:#007bff;">Add Entry</button>
        </form>
        <h2>Records</h2>
        <form id="bulkDeleteForm" method="POST">
        <table>
            <tr>
                <th><input type="checkbox" id="selectAll"></th>
                <th>ID</th>
                <th>Name</th>
                <th>Expiration Date</th>
                <th>Lot No.</th>
                <th>Initial Qty</th>
                <th>Actions</th>
            </tr>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><input type="checkbox" class="row-checkbox" name="delete_ids[]" value="<?= htmlspecialchars($row['id']) ?>"></td>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['expiration_date']) ?></td>
                        <td><?= htmlspecialchars($row['lot_no']) ?></td>
                        <td><?= htmlspecialchars($row['initial_quantity']) ?></td>
                        <td class="action-btns">
                            <a href="#" class="edit-link" data-id="<?= $row['id'] ?>">Edit</a>
                            <a href="#" class="delete-link" data-id="<?= $row['id'] ?>">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="11">No entries in monthly log.</td></tr>
            <?php endif; ?>
        </table>
        <button type="button" id="bulkDeleteBtn" style="margin-top:15px;background:#dc3545;">Delete Selected</button>
        </form>
    </div>
    <div id="editModal">
        <div id="editModalContent"></div>
    </div>
    <div id="deleteModal" style="display:none;position:fixed;z-index:2000;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.4);">
        <div style="background:#fff;max-width:350px;margin:120px auto;padding:30px 25px;border-radius:10px;position:relative;box-shadow:0 8px 25px rgba(0,0,0,0.18);text-align:center;">
            <p id="deleteModalMsg">Are you sure you want to delete the selected entry(ies)?</p>
            <button id="confirmDeleteBtn" style="background:#dc3545;margin-right:10px;">Delete</button>
            <button id="cancelDeleteBtn" style="background:#6c757d;">Cancel</button>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
