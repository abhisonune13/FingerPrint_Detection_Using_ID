<?php
include 'db.php';
date_default_timezone_set('Asia/Kolkata');
$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Logs</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #0d1b2a;
            color: white;
            text-align: center;
            padding: 20px;
        }

        .header-section {
            margin-bottom: 30px;
        }

        .btn-back {
            text-decoration: none;
            color: #00ff00;
            border: 1px solid #00ff00;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 14px;
            transition: 0.3s;
        }

        .btn-back:hover {
            background: rgba(0, 255, 0, 0.1);
        }

        table {
            width: 90%;
            max-width: 900px;
            margin: 20px auto;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            overflow: hidden;
        }

        th {
            background: rgba(0, 123, 255, 0.3);
            padding: 15px;
            text-transform: uppercase;
            font-size: 14px;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* MINI SCANNER CSS */
        .table-scan-container {
            position: relative;
            width: 50px;
            height: 50px;
            margin: 0 auto;
            border: 1px solid #00ff00;
            border-radius: 4px;
            overflow: hidden;
            background: #000;
        }

        .table-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .laser-line-sm {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: #00ff00;
            box-shadow: 0 0 8px #00ff00;
            z-index: 5;
            animation: scanSmall 2s linear infinite;
        }

        @keyframes scanSmall {
            0% { top: 0%; }
            50% { top: 100%; }
            100% { top: 0%; }
        }

        .status-present {
            color: #00ff00;
            font-weight: bold;
            font-size: 12px;
            background: rgba(0, 255, 0, 0.1);
            padding: 5px 10px;
            border-radius: 4px;
        }
    </style>
</head>
<body>

    <div class="header-section">
        <a href="index.php" class="btn-back">← BACK TO SCANNER</a>
        <h2 style="margin-top: 30px;">LOGS FOR: <?php echo date('d M Y'); ?></h2>
    </div>

    <table>
        <thead>
            <tr>
                <th>SR. NO</th>
                <th>NAME</th>
                <th>FINGERPRINT</th>
                <th>TIME IN</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // SQL JOIN to get name and image from users table
            $sql = "SELECT attendance.*, users.name, users.profile_image 
                    FROM attendance 
                    JOIN users ON attendance.fingerprint_id = users.fingerprint_id 
                    WHERE DATE(attendance.scan_time) = '$today' 
                    ORDER BY attendance.scan_time DESC";
            
            $result = $conn->query($sql);
            $sn = 1;

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    // Path check: uses DB path or default placeholder
                    $img = !empty($row['profile_image']) ? $row['profile_image'] : "Fingerprints/default.jpg";
                    ?>
                    <tr>
                        <td><?php echo $sn++; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td>
                            <div class="table-scan-container">
                                <div class="laser-line-sm"></div>
                                <img src="<?php echo $img; ?>" class="table-img">
                            </div>
                        </td>
                        <td><?php echo date('h:i:s A', strtotime($row['scan_time'])); ?></td>
                        <td><span class="status-present">PRESENT</span></td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='5'>No records found for today.</td></tr>";
            }
            ?>
        </tbody>
    </table>

</body>
</html>