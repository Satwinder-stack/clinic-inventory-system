<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Data</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/download.css">
</head>
    <div class="navbar">
        <div class="title">🏥 Clinic Inventory System</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="add_reduce_quantity.php">Add/Reduce Quantity</a>
            <a href="monthly_log.php">Record</a>
            <a href="history.php">History</a>
            <a href="download.php" class="active" style="background:#007bff;">Download</a>
        </div>
    </div>
    <div class="container">
        <h1>Download Data</h1>
        <div class="desc">Download the current Record log as an Excel-compatible file (CSV).</div>
        <a href="download_export.php?table=monthly_log" class="download-btn">Download Monthly Log</a>
    </div>
</body>
</html>
