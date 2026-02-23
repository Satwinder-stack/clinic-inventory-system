<?php
include 'db_connect.php';
// Handle reload for selected IDs via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reload_selected'])) {
    $ids = isset($_POST['ids']) && is_array($_POST['ids']) ? array_map('intval', $_POST['ids']) : [];
    header('Content-Type: application/json');
    if (empty($ids)) {
        echo json_encode(['success' => false, 'message' => 'No IDs provided.']);
        exit();
    }
    // Fetch old and initial quantities for logging
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));
    $stmtSel = $conn->prepare("SELECT id, final_quantity, initial_quantity FROM monthly_log WHERE id IN ($placeholders)");
    if ($stmtSel) {
        $stmtSel->bind_param($types, ...$ids);
        $stmtSel->execute();
        $resSel = $stmtSel->get_result();
        $rows = [];
        while ($r = $resSel->fetch_assoc()) { $rows[$r['id']] = $r; }
        $stmtSel->close();
        // Update
        $in = implode(',', $ids);
        $ok = $conn->query("UPDATE monthly_log SET final_quantity = initial_quantity WHERE id IN ($in)");
        if ($ok) {
            foreach ($ids as $id) {
                if (!isset($rows[$id])) continue;
                $old = (string)$rows[$id]['final_quantity'];
                $new = (string)$rows[$id]['initial_quantity'];
                if ($old !== $new) {
                    log_history($conn, $id, 'update_quantity', 'final_quantity', $old, $new);
                }
            }
        }
        echo json_encode(['success' => (bool)$ok]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reload_quantities'])) {
    // Log per-row changes before global update
    $res = $conn->query("SELECT id, final_quantity, initial_quantity FROM monthly_log");
    $pre = [];
    if ($res) {
        while ($r = $res->fetch_assoc()) { $pre[$r['id']] = $r; }
    }
    $ok = $conn->query("UPDATE monthly_log SET final_quantity = initial_quantity");
    if ($ok) {
        foreach ($pre as $id => $r) {
            $old = (string)$r['final_quantity'];
            $new = (string)$r['initial_quantity'];
            if ($old !== $new) {
                log_history($conn, (int)$id, 'update_quantity', 'final_quantity', $old, $new);
            }
        }
    }
    header('Location: monthly_log.php');
    exit();
}
$result = $conn->query("SELECT * FROM monthly_log ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/monthly_log.css">        
</head>
<body>
    <div class="navbar">
        <div class="title">🏥 Clinic Inventory System</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="add_reduce_quantity.php">Add/Reduce Quantity</a>
            <a href="monthly_log.php" style="background:#007bff;">Record</a>
            <a href="history.php">History</a>
            <a href="download.php">Download</a>
        </div>
    </div>
    <div class="container">
        <h2>RECORDS</h2>
        <form id="reloadForm" method="POST" style="margin-bottom:18px;">
            <button id="reloadBtn" type="button" style="background:#007bff;color:#fff;padding:10px 22px;border:none;border-radius:5px;font-size:1em;font-weight:500;cursor:pointer;">Reload Final Quantity</button>
            <input type="hidden" name="reload_quantities" value="1">
        </form>
        <!-- Reload Confirmation Modal -->
        <div id="reloadConfirmModal" style="display:none;position:fixed;z-index:2500;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.4);">
            <div style="background:#fff;max-width:420px;margin:120px auto;padding:26px 24px;border-radius:10px;position:relative;box-shadow:0 8px 25px rgba(0,0,0,0.18);text-align:center;border:1.5px solid #007bff;">
                <p style="font-size:1.05em;margin-bottom:18px;">Are you sure you want to reload all quantities to their initial quantities?</p>
                <div style="display:flex;gap:10px;justify-content:center;">
                    <button id="reloadConfirmBtn" style="background:#007bff;color:#fff;padding:10px 18px;border:none;border-radius:6px;font-weight:500;cursor:pointer;">Confirm</button>
                    <button id="reloadCancelBtn" style="background:#6c757d;color:#fff;padding:10px 18px;border:none;border-radius:6px;font-weight:500;cursor:pointer;">Cancel</button>
                </div>
            </div>
        </div>
        <!-- Reload Selected Confirmation Modal -->
        <div id="reloadSelectedModal" style="display:none;position:fixed;z-index:2500;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.4);">
            <div style="background:#fff;max-width:460px;margin:120px auto;padding:26px 24px;border-radius:10px;position:relative;box-shadow:0 8px 25px rgba(0,0,0,0.18);text-align:center;border:1.5px solid #007bff;">
                <p style="font-size:1.05em;margin-bottom:18px;">Are you sure you want to reload the final quantities for the selected entries to their initial quantities?</p>
                <div style="display:flex;gap:10px;justify-content:center;">
                    <button id="confirmReloadSelectedBtn" style="background:#007bff;color:#fff;padding:10px 18px;border:none;border-radius:6px;font-weight:500;cursor:pointer;">Confirm</button>
                    <button id="cancelReloadSelectedBtn" style="background:#6c757d;color:#fff;padding:10px 18px;border:none;border-radius:6px;font-weight:500;cursor:pointer;">Cancel</button>
                </div>
            </div>
        </div>
        <div style="margin-bottom:18px;">
            <input type="text" id="searchBar" placeholder="Type to filter by name, lot no., date, or initial qty..." style="width:100%;padding:12px;border:1px solid #ced4da;border-radius:5px;font-size:1.1em;box-sizing:border-box;">
        </div>
        <form id="bulkDeleteForm" method="POST" action="edit_delete.php">
        <button type="button" id="bulkDeleteBtn" style="margin-top: -3em;margin-bottom:12px;background:#dc3545;">Delete Selected</button>
        <button type="button" id="bulkReloadBtn" style="margin-top: -3em;margin-left:10px;margin-bottom:12px;background:#007bff;color:#fff;padding:12px 16px;border:none;border-radius:5px;font-size:1.05em;">Reload Selected</button>
        <table id="monthlyLogTable">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>Name</th>
                    <th>Expiration Date</th>
                    <th>Lot No.</th>
                    <th>Initial Qty</th>
                    <th>Final Qty</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><input type="checkbox" class="row-checkbox" name="delete_monthly_log_ids[]" value="<?= htmlspecialchars($row['id']) ?>"></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['expiration_date']) ?></td>
                        <td><?= htmlspecialchars($row['lot_no']) ?></td>
                        <td><?= htmlspecialchars($row['initial_quantity']) ?></td>
                        <td><?= htmlspecialchars($row['final_quantity']) ?></td>
                        <td class="action-btns">
                            <a href="#" class="edit-link" data-id="<?= $row['id'] ?>">Edit</a>
                            <a href="#" class="delete-link" data-id="<?= $row['id'] ?>">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">No entries in Record.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
                </form>
    </div>
    <div id="editModal" style="display:none;position:fixed;z-index:3000;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.4);">
        <div id="editModalContent" style="background:#fff;max-width:400px;margin:-4em auto 0 auto;padding:30px;border-radius:10px;position:relative;box-shadow:0 8px 25px rgba(0,0,0,0.18);top:10vh;"></div>
    </div>
    <div id="deleteModal" style="display:none;position:fixed;z-index:2000;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.4);">
        <div style="background:#fff;max-width:350px;margin:120px auto;padding:30px 25px;border-radius:10px;position:relative;box-shadow:0 8px 25px rgba(0,0,0,0.18);text-align:center;">
            <p id="deleteModalMsg">Are you sure you want to delete the selected entry(ies)?</p>
            <button id="confirmDeleteBtn" style="background:#dc3545;margin-right:10px;">Delete</button>
            <button id="cancelDeleteBtn" style="background:#6c757d;">Cancel</button>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchBar = document.getElementById('searchBar');
        const tableRows = document.querySelectorAll('#monthlyLogTable tbody tr');
        searchBar.addEventListener('input', function() {
            const filter = searchBar.value.trim().toLowerCase();
            tableRows.forEach(function(row) {
                const name = row.cells[1]?.textContent.toLowerCase() || '';
                const date = row.cells[2]?.textContent.toLowerCase() || '';
                const lot = row.cells[3]?.textContent.toLowerCase() || '';
                const initialQty = row.cells[4]?.textContent.toLowerCase() || '';
                if (
                    name.includes(filter) ||
                    date.includes(filter) ||
                    lot.includes(filter) ||
                    initialQty.includes(filter)
                ) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
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
        const reloadBtn = document.getElementById('reloadBtn');
        const reloadForm = document.getElementById('reloadForm');
        const reloadConfirmModal = document.getElementById('reloadConfirmModal');
        const reloadConfirmBtn = document.getElementById('reloadConfirmBtn');
        const reloadCancelBtn = document.getElementById('reloadCancelBtn');
        if (reloadBtn) {
            reloadBtn.addEventListener('click', function() {
                reloadConfirmModal.style.display = 'block';
            });
        }
        if (reloadCancelBtn) {
            reloadCancelBtn.addEventListener('click', function() {
                reloadConfirmModal.style.display = 'none';
            });
        }
        if (reloadConfirmBtn) {
            reloadConfirmBtn.addEventListener('click', function() {
                reloadConfirmModal.style.display = 'none';
                reloadForm.submit();
            });
        }
        const bulkReloadBtn = document.getElementById('bulkReloadBtn');
        const reloadSelectedModal = document.getElementById('reloadSelectedModal');
        const confirmReloadSelectedBtn = document.getElementById('confirmReloadSelectedBtn');
        const cancelReloadSelectedBtn = document.getElementById('cancelReloadSelectedBtn');
        let reloadSelectedIds = [];
        if (bulkReloadBtn) {
            bulkReloadBtn.addEventListener('click', function() {
                const checked = document.querySelectorAll('.row-checkbox:checked');
                if (checked.length === 0) {
                    alert('Please select at least one entry to reload.');
                    return;
                }
                reloadSelectedIds = Array.from(checked).map(cb => cb.value);
                reloadSelectedModal.style.display = 'block';
            });
        }
        if (cancelReloadSelectedBtn) {
            cancelReloadSelectedBtn.addEventListener('click', function() {
                reloadSelectedModal.style.display = 'none';
                reloadSelectedIds = [];
            });
        }
        if (confirmReloadSelectedBtn) {
            confirmReloadSelectedBtn.addEventListener('click', function() {
                const fd = new FormData();
                fd.append('reload_selected', '1');
                reloadSelectedIds.forEach(id => fd.append('ids[]', id));
                fetch('monthly_log.php', { method: 'POST', body: fd })
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.success) {
                            location.reload();
                        } else {
                            alert('Reload failed.');
                        }
                    })
                    .catch(() => alert('Reload failed.'))
                    .finally(() => {
                        reloadSelectedModal.style.display = 'none';
                        reloadSelectedIds = [];
                    });
            });
        }
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
                window.location.href = 'edit_delete.php?delete_monthly_log=' + encodeURIComponent(singleDeleteId) + '&from=monthly_log.php';
            } else if (deleteIds.length > 0) {
                let form = document.getElementById('bulkDeleteForm');
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'bulk_delete_monthly_log';
                input.value = '1';
                form.appendChild(input);
                let fromInput = document.createElement('input');
                fromInput.type = 'hidden';
                fromInput.name = 'from';
                fromInput.value = 'monthly_log.php';
                form.appendChild(fromInput);
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
</body>
</html>
<?php $conn->close(); ?>
