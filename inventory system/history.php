<?php
include 'db_connect.php';
$result = $conn->query("SELECT * FROM history ORDER BY action_time DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory History</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/history.css">

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchBar = document.getElementById('searchBar');
        function filterTable() {
            const filter = (searchBar.value || '').toLowerCase().trim();
            document.querySelectorAll('#historyTable tbody tr').forEach(function(row) {
                const text = Array.from(row.cells).map(td => td.textContent.toLowerCase()).join(' ');
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        }
        searchBar.addEventListener('input', filterTable);
    });
    </script>
</head>
<body>
    <div class="navbar">
        <div class="title">🏥 Clinic Inventory System</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="add_reduce_quantity.php">Add/Reduce Quantity</a>
            <a href="monthly_log.php">Record</a>
            <a href="history.php" style="background:#007bff;">History</a>
            <a href="download.php">Download</a>
        </div>
    </div>
    <div class="container">
        <h1>Inventory History</h1>
        <div style="margin-bottom:18px;">
            <input type="text" id="searchBar" placeholder="Search history by any field..." style="width:100%;">
        </div>
        <table id="historyTable">
            <thead>
            <tr>
                <th>Time</th>
                <th>Item ID</th>
                <th>Action</th>
                <th>Field Changed</th>
                <th>Old Value</th>
                <th>New Value</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['action_time']) ?></td>
                        <td><?= htmlspecialchars($row['item_id']) ?></td>
                        <td class="action-<?= htmlspecialchars($row['action']) ?>"><?= htmlspecialchars(ucfirst($row['action'])) ?></td>
                        <td><?= htmlspecialchars($row['field_changed']) ?></td>
                        <td><?= htmlspecialchars($row['old_value']) ?></td>
                        <td><?= htmlspecialchars($row['new_value']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7">No history found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php $conn->close(); ?>
