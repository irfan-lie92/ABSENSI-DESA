<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get current user information
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get all staff members
$stmt = $pdo->prepare("SELECT * FROM staff ORDER BY position_order");
$stmt->execute();
$staff_members = $stmt->fetchAll();

// Get attendance records for current week
$current_week = isset($_GET['week']) ? $_GET['week'] : date('W');
$current_year = isset($_GET['year']) ? $_GET['year'] : date('Y');

$week_start = new DateTime();
$week_start->setISODate($current_year, $current_week);
$week_end = clone $week_start;
$week_end->modify('+6 days');

$stmt = $pdo->prepare("
    SELECT * FROM attendance 
    WHERE date BETWEEN ? AND ?
    ORDER BY staff_id, date
");
$stmt->execute([$week_start->format('Y-m-d'), $week_end->format('Y-m-d')]);
$attendance_records = $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Hadir Perangkat Desa</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
</head>
<body>
    <?php include 'templates/header.php'; ?>
    
    <div class="container">
        <div class="header-text">
            <h4>DAFTAR HADIR</h4>
        </div>
        
        <div class="isi">
            <div class="grid-container">
                <div class="grid-item">
                    <p>BULAN DAN TAHUN :</p>
                </div>
                <div class="grid-item">
                    <p><?php echo $week_start->format('F Y'); ?></p>
                </div>
                <div class="grid-item">
                    <p>MINGGU :</p>
                </div>
                <div class="grid-item">
                    <p>KE-<?php echo $current_week; ?></p>
                </div>
            </div>
        </div>
        
        <table class="table">
            <thead>
                <tr>
                    <th rowspan="2">No.</th>
                    <th rowspan="2">Nama</th>
                    <th rowspan="2">Jabatan</th>
                    <th colspan="7">Tanggal</th>
                    <th rowspan="2">KET</th>
                </tr>
                <tr>
                    <?php
                    $current_date = clone $week_start;
                    for ($i = 0; $i < 7; $i++) {
                        echo "<th>" . $current_date->format('d') . "</th>";
                        $current_date->modify('+1 day');
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($staff_members as $index => $staff): ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo htmlspecialchars($staff['name']); ?></td>
                    <td><?php echo htmlspecialchars($staff['position']); ?></td>
                    <?php
                    $current_date = clone $week_start;
                    for ($i = 0; $i < 7; $i++) {
                        $date_key = $current_date->format('Y-m-d');
                        $attendance = isset($attendance_records[$staff['id']][$date_key]) 
                            ? $attendance_records[$staff['id']][$date_key] 
                            : null;
                        
                        echo "<td>";
                        if ($attendance) {
                            echo "<img src='" . htmlspecialchars($attendance['signature']) . "' class='signature-image'>";
                            echo "<span class='date'>" . $current_date->format('d') . "</span>";
                        } else {
                            echo "<canvas class='signature-pad' data-staff-id='" . $staff['id'] . "' data-date='" . $date_key . "'></canvas>";
                            echo "<span class='date'></span>";
                        }
                        echo "</td>";
                        
                        $current_date->modify('+1 day');
                    }
                    ?>
                    <td></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php include 'templates/footer.php'; ?>
    </div>
    
    <script src="js/attendance.js"></script>
</body>
</html>