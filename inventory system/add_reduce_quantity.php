<?php
include 'db_connect.php';

if (isset($_GET['search'])) {
    $search = '%' . $conn->real_escape_string($_GET['search']) . '%';
    $result = $conn->query("SELECT id, name, final_quantity, expiration_date, lot_no, initial_quantity FROM monthly_log WHERE name LIKE '$search' ORDER BY name ASC");
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($items);
    exit;
}

if (isset($_POST['item_id'], $_POST['amount'], $_POST['action'])) {
    $item_id = intval($_POST['item_id']);
    $amount = intval($_POST['amount']);
    $action = $_POST['action'];
    $stmt = $conn->prepare("SELECT final_quantity FROM monthly_log WHERE id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $stmt->bind_result($current_qty);
    if ($stmt->fetch()) {
        $stmt->close();
        if ($action === 'add') {
            $new_qty = $current_qty + $amount;
        } else if ($action === 'reduce') {
            $new_qty = max(0, $current_qty - $amount);
        } else if ($action === 'finalize') {
            $new_qty = $amount;
        } else {
            $new_qty = $current_qty;
        }
        $stmt = $conn->prepare("UPDATE monthly_log SET final_quantity = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_qty, $item_id);
        $success = $stmt->execute();
        $stmt->close();
        if ($success && (string)$current_qty !== (string)$new_qty) {
            log_history($conn, $item_id, 'update_quantity', 'final_quantity', (string)$current_qty, (string)$new_qty);
        }
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'new_qty' => $new_qty
        ]);
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Entry not found.']);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_delete']) && isset($_POST['delete_ids']) && is_array($_POST['delete_ids'])) {
    $ids = array_map('intval', $_POST['delete_ids']);
    if (count($ids) > 0) {
        // Preload rows for logging before deletion
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        $stmtPre = $conn->prepare("SELECT id, name, expiration_date, lot_no, final_quantity FROM monthly_log WHERE id IN ($placeholders)");
        if ($stmtPre) {
            $stmtPre->bind_param($types, ...$ids);
            $stmtPre->execute();
            $resPre = $stmtPre->get_result();
            $preRows = [];
            while ($r = $resPre->fetch_assoc()) { $preRows[$r['id']] = $r; }
            $stmtPre->close();
        }
        $in = implode(',', $ids);
        $conn->query("DELETE FROM monthly_log WHERE id IN ($in)");
        if (!empty($preRows)) {
            foreach ($ids as $id) {
                if (!isset($preRows[$id])) continue;
                $r = $preRows[$id];
                $old = sprintf('%s | %s | %s | qty=%s', $r['name'], $r['expiration_date'], $r['lot_no'], $r['final_quantity']);
                log_history($conn, $id, 'delete', 'monthly_log_row', $old, '');
            }
        }
    }
    header('Location: add_reduce_quantity.php');
    exit();
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Log before delete
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
    $conn->query("DELETE FROM monthly_log WHERE id = $id");
    header('Location: add_reduce_quantity.php');
    exit();
}

$all = $conn->query("SELECT id, name, final_quantity, expiration_date, lot_no, initial_quantity FROM monthly_log ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add/Reduce Quantity</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/add_reduce_quantity.css">
</head>
<body>
    <div class="navbar">
        <div class="title">🏥 Clinic Inventory System</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="add_reduce_quantity.php" class="active" style="background:#007bff;">Add/Reduce Quantity</a>
            <a href="monthly_log.php">Record</a>
            <a href="history.php">History</a>
            <a href="download.php">Download</a>
        </div>
    </div>
    <div class="container">
        <h2>Add/Reduce Quantity</h2>
        <div style="position:relative;">
            <input type="text" id="searchBar" placeholder="Type a name to search..." autocomplete="off" style="background:#fff;">
            <div id="searchResults" style="display:none;"></div>
        </div>
        <form id="bulkDeleteForm" method="POST" style="margin-bottom: 0;">
        <button type="button" id="bulkDeleteBtn" class="bulk-delete-btn">Delete Selected</button>
        <table id="itemsTable">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll" class="styled-checkbox"></th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Expiration Date</th>
                    <th>Lot No.</th>
                    <th>Initial Qty</th>
                    <th>Final Qty</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($all && $all->num_rows > 0): ?>
                <?php while($row = $all->fetch_assoc()): ?>
                    <tr data-id="<?= htmlspecialchars($row['id']) ?>">
                        <td><input type="checkbox" class="row-checkbox styled-checkbox" name="delete_ids[]" value="<?= htmlspecialchars($row['id']) ?>"></td>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['expiration_date']) ?></td>
                        <td><?= htmlspecialchars($row['lot_no']) ?></td>
                        <td><?= htmlspecialchars($row['initial_quantity']) ?></td>
                        <td class="final-qty"><?= htmlspecialchars($row['final_quantity']) ?></td>
                        <td class="action-btns">
                            <a href="#" class="edit-link" data-id="<?= $row['id'] ?>">Edit</a>
                            <a href="#" class="delete-link" data-id="<?= $row['id'] ?>">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8">No entries in monthly log.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
        <input type="hidden" name="bulk_delete" value="1">
        </form>
    </div>
    <div class="modal-bg" id="modalBg">
        <div class="modal" id="qtyModal">
            <h3 id="modalItemName"></h3>
            <div class="current-qty">Current Quantity: <span id="modalCurrentQty"></span></div>
            <label for="modalAmount">Quantity:</label>
            <input type="number" id="modalAmount" min="1">
            <div style="margin-top:18px;">
                <button class="add" id="modalAddBtn">Add</button>
                <button class="reduce" id="modalReduceBtn">Reduce</button>
                <button class="finalize-btn" id="modalFinalizeBtn">Finalize</button>
                <button class="close-btn" id="modalCloseBtn">Close</button>
            </div>
            <div class="msg" id="modalMsg"></div>
        </div>
    </div>
    <div id="deleteModal" style="display:none;position:fixed;z-index:2000;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.4);">
        <div style="background:#fff;max-width:350px;margin:120px auto;padding:30px 25px;border-radius:10px;position:relative;box-shadow:0 8px 25px rgba(0,0,0,0.18);text-align:center;">
            <p id="deleteModalMsg">Are you sure you want to delete the selected entry(ies)?</p>
            <button id="confirmDeleteBtn" style="background:#dc3545;margin-right:10px;">Delete</button>
            <button id="cancelDeleteBtn" style="background:#6c757d;">Cancel</button>
        </div>
    </div>
    <script>
    let searchBar = document.getElementById('searchBar');
    let searchResults = document.getElementById('searchResults');
    document.addEventListener('DOMContentLoaded', function() {
        if (searchBar) searchBar.focus();
    });
    let modalBg = document.getElementById('modalBg');
    let qtyModal = document.getElementById('qtyModal');
    let modalItemName = document.getElementById('modalItemName');
    let modalCurrentQty = document.getElementById('modalCurrentQty');
    let modalAmount = document.getElementById('modalAmount');
    let modalAddBtn = document.getElementById('modalAddBtn');
    let modalReduceBtn = document.getElementById('modalReduceBtn');
    let modalFinalizeBtn = document.getElementById('modalFinalizeBtn');
    let modalCloseBtn = document.getElementById('modalCloseBtn');
    let modalMsg = document.getElementById('modalMsg');
    let selectedItem = null;
    let results = [];
    let activeIdx = -1;
    let itemsTable = document.getElementById('itemsTable').getElementsByTagName('tbody')[0];
    let deleteModal = document.getElementById('deleteModal');
    let confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    let cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
    let deleteForm = document.getElementById('bulkDeleteForm');
    let deleteIds = [];
    let singleDeleteId = null;

    function formatSearchResult(item) {
        return `Name: ${item.name} | quantity: ${item.final_quantity} | expiration date: ${item.expiration_date} | lot no. ${item.lot_no}`;
    }

    function updateTableRowQty(id, newQty) {
        let row = itemsTable.querySelector(`tr[data-id='${id}']`);
        if (row) {
            let qtyCell = row.querySelector('.final-qty');
            if (qtyCell) qtyCell.textContent = newQty;
        }
    }

    searchBar.addEventListener('input', function() {
        let val = this.value.trim();
        if (val.length === 0) {
            searchResults.innerHTML = '';
            searchResults.style.display = 'none';
            return;
        }
        fetch('add_reduce_quantity.php?search=' + encodeURIComponent(val))
            .then(res => res.json())
            .then(data => {
                results = data;
                searchResults.innerHTML = '';
                if (results.length === 0) {
                    searchResults.style.display = 'none';
                    return;
                }
                results.forEach((item, idx) => {
                    let div = document.createElement('div');
                    div.className = 'result-item' + (idx === 0 ? ' active' : '');
                    div.textContent = formatSearchResult(item);
                    div.dataset.idx = idx;
                    div.addEventListener('mousedown', function(e) {
                        e.preventDefault();
                        openModal(results[idx]);
                    });
                    searchResults.appendChild(div);
                });
                searchResults.style.display = 'block';
                activeIdx = 0;
            });
    });

    searchBar.addEventListener('keydown', function(e) {
        if (searchResults.style.display === 'block' && results.length > 0) {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeIdx = (activeIdx + 1) % results.length;
                updateActiveResult();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeIdx = (activeIdx - 1 + results.length) % results.length;
                updateActiveResult();
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (activeIdx >= 0 && activeIdx < results.length) {
                    openModal(results[activeIdx]);
                }
            }
        }
    });

    function updateActiveResult() {
        let items = searchResults.querySelectorAll('.result-item');
        items.forEach((item, idx) => {
            if (idx === activeIdx) item.classList.add('active');
            else item.classList.remove('active');
        });
    }

    function openModal(item) {
        selectedItem = {...item};
        modalItemName.textContent = item.name;
        modalCurrentQty.textContent = item.final_quantity;
        modalAmount.value = '';
        modalMsg.textContent = '';
        modalBg.style.display = 'block';
        modalAmount.focus();
        searchResults.style.display = 'none';
        searchBar.value = '';
        modalWorkingQty = parseInt(item.final_quantity, 10);
    }

    let modalWorkingQty = 0;

    let enterStage = 0;
    modalAmount.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            let val = modalAmount.value.trim();
            if (val !== '') {
                let amount = parseInt(val, 10);
                if (!selectedItem || isNaN(amount) || amount === 0) {
                    modalMsg.textContent = 'Please enter a valid quantity.';
                    modalMsg.style.color = 'red';
                    return;
                }
                if (amount > 0) {
                    modalWorkingQty = parseInt(modalWorkingQty, 10) + amount;
                } else if (amount < 0) {
                    modalWorkingQty = Math.max(0, parseInt(modalWorkingQty, 10) + amount);
                }
                modalCurrentQty.textContent = modalWorkingQty;
                modalMsg.textContent = 'Press Enter again to finalize, or type another value.';
                modalAmount.value = '';
                enterStage = 1;
                e.preventDefault();
            } else if (enterStage > 0 && val === '') {
                modalAddBtn.disabled = true;
                modalReduceBtn.disabled = true;
                modalFinalizeBtn.disabled = true;
                fetch('add_reduce_quantity.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'item_id=' + encodeURIComponent(selectedItem.id) + '&amount=' + encodeURIComponent(modalWorkingQty) + '&action=finalize'
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        modalMsg.textContent = 'Quantity updated! New quantity: ' + data.new_qty;
                        modalMsg.style.color = 'green';
                        modalCurrentQty.textContent = data.new_qty;
                        selectedItem.final_quantity = data.new_qty;
                        updateTableRowQty(selectedItem.id, data.new_qty);
                        modalBg.style.display = 'none';
                        location.reload();
                    } else {
                        modalMsg.textContent = 'Failed to update quantity.';
                        modalMsg.style.color = 'red';
                    }
                    modalAddBtn.disabled = false;
                    modalReduceBtn.disabled = false;
                    modalFinalizeBtn.disabled = false;
                })
                .catch(() => {
                    modalMsg.textContent = 'Error updating quantity.';
                    modalMsg.style.color = 'red';
                    modalAddBtn.disabled = false;
                    modalReduceBtn.disabled = false;
                    modalFinalizeBtn.disabled = false;
                    modalBg.style.display = 'none';
                    location.reload();
                });
                enterStage = 0;
                e.preventDefault();
            }
        } else if (e.key === 'Escape') {
            modalBg.style.display = 'none';
            if (searchBar) searchBar.focus();
        }
    });
    function openModal(item) {
        selectedItem = {...item};
        modalItemName.textContent = item.name;
        modalCurrentQty.textContent = item.final_quantity;
        modalAmount.value = '';
        modalMsg.textContent = '';
        modalBg.style.display = 'block';
        modalAmount.focus();
        searchResults.style.display = 'none';
        searchBar.value = '';
        modalWorkingQty = parseInt(item.final_quantity, 10);
        enterStage = 0;
    }
    modalAddBtn.onclick = function() {
        let amount = parseInt(modalAmount.value, 10);
        if (!selectedItem || isNaN(amount) || amount < 1) {
            modalMsg.textContent = 'Please enter a valid quantity.';
            modalMsg.style.color = 'red';
            return;
        }
        modalWorkingQty = parseInt(modalWorkingQty, 10) + amount;
        modalCurrentQty.textContent = modalWorkingQty;
        modalMsg.textContent = '';
        modalAmount.value = '';
    };
    modalReduceBtn.onclick = function() {
        let amount = parseInt(modalAmount.value, 10);
        if (!selectedItem || isNaN(amount) || amount < 1) {
            modalMsg.textContent = 'Please enter a valid quantity.';
            modalMsg.style.color = 'red';
            return;
        }
        modalWorkingQty = Math.max(0, parseInt(modalWorkingQty, 10) - amount);
        modalCurrentQty.textContent = modalWorkingQty;
        modalMsg.textContent = '';
        modalAmount.value = '';
    };
    modalFinalizeBtn.onclick = function() {
        if (!selectedItem) return;
        modalAddBtn.disabled = true;
        modalReduceBtn.disabled = true;
        modalFinalizeBtn.disabled = true;
        fetch('add_reduce_quantity.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'item_id=' + encodeURIComponent(selectedItem.id) + '&amount=' + encodeURIComponent(modalWorkingQty) + '&action=finalize'
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                modalMsg.textContent = 'Quantity updated! New quantity: ' + data.new_qty;
                modalMsg.style.color = 'green';
                modalCurrentQty.textContent = data.new_qty;
                selectedItem.final_quantity = data.new_qty;
                updateTableRowQty(selectedItem.id, data.new_qty);
            } else {
                modalMsg.textContent = 'Failed to update quantity.';
                modalMsg.style.color = 'red';
            }
            modalAddBtn.disabled = false;
            modalReduceBtn.disabled = false;
            modalFinalizeBtn.disabled = false;
            modalBg.style.display = 'none';
            if (searchBar) searchBar.focus();
        })
        .catch(() => {
            modalMsg.textContent = 'Error updating quantity.';
            modalMsg.style.color = 'red';
            modalAddBtn.disabled = false;
            modalReduceBtn.disabled = false;
            modalFinalizeBtn.disabled = false;
            modalBg.style.display = 'none';
            if (searchBar) searchBar.focus();
        });
    };
    modalCloseBtn.onclick = function() {
        location.reload();
    };

    itemsTable.addEventListener('click', function(e) {
        if (e.target.classList.contains('edit-link')) {
            e.preventDefault();
            let row = e.target.closest('tr');
            if (row) {
                let id = row.getAttribute('data-id');
                let name = row.children[2].textContent;
                let final_quantity = row.querySelector('.final-qty').textContent;
                let expiration_date = row.children[3].textContent;
                let lot_no = row.children[4].textContent;
                openModal({id, name, final_quantity, expiration_date, lot_no});
            }
        } else if (e.target.classList.contains('delete-link')) {
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
            window.location.href = 'add_reduce_quantity.php?delete=' + encodeURIComponent(singleDeleteId);
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
    modalBg.onclick = function(e) {
        if (e.target === modalBg) {
            modalBg.style.display = 'none';
            if (searchBar) searchBar.focus();
        }
    };
    </script>
</body>
</html>
<?php $conn->close(); ?>
