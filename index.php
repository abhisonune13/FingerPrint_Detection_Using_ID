 <?php
// 1. ENABLE ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';
date_default_timezone_set('Asia/Kolkata');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biometric Security System</title>
    <style>
        /* GLOBAL SETTINGS */
        * { box-sizing: border-box; }
        
        body {
            margin: 0; padding: 0; height: 100vh;
            display: flex; justify-content: center; align-items: center;
            background: radial-gradient(circle at center, #0d1b2a 0%, #04090f 100%);
            overflow: hidden; 
            font-family: 'Segoe UI', sans-serif;
        }

        /* FLOATING BACKGROUND SQUARES */
        .bg-container {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;
        }

        .orange-box {
            position: absolute; background: #ff6200; box-shadow: 0 0 12px #ff6200;
            opacity: 0; animation: floatBoxes 7s infinite linear;
        }

        @keyframes floatBoxes {
            0% { transform: translateY(110vh) translateX(0) rotate(0deg); opacity: 0; }
            20% { opacity: 0.8; }
            50% { transform: translateY(50vh) translateX(20px) rotate(180deg); }
            80% { opacity: 0.8; }
            100% { transform: translateY(-20vh) translateX(-20px) rotate(360deg); opacity: 0; }
        }

        /* THE MAIN GLASS BOX */
        .container {
            position: relative;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0.02));
            backdrop-filter: blur(10px); 
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-top: 1px solid rgba(255, 255, 255, 0.4); 
            border-left: 1px solid rgba(255, 255, 255, 0.4); 
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
            padding: 40px;
            border-radius: 25px;
            width: 380px;
            text-align: center;
            z-index: 10;
        }

        /* SCANNER WINDOW */
        .scan-box {
            width: 140px; height: 140px; margin: 0 auto 25px;
            border: 2px solid rgba(255, 255, 255, 0.3); 
            border-radius: 12px;
            position: relative; 
            background: rgba(0,0,0,0.5); 
            display: flex; justify-content: center; align-items: center;
            overflow: hidden;
            box-shadow: inset 0 0 20px #000;
        }

        .laser-line {
            position: absolute; top: 0; left: 0; width: 100%; height: 4px;
            background: #00ff00; 
            box-shadow: 0 0 15px #00ff00, 0 0 30px #00ff00;
            z-index: 10;
            animation: scanning 2s linear infinite;
        }

        @keyframes scanning {
            0%, 100% { top: 0%; }
            50% { top: 100%; }
        }

        /* TEXT AND INPUTS */
        h2 { 
            color: #fff; letter-spacing: 4px; margin-bottom: 30px; 
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        }
        
        input[type="number"] {
            width: 90%; padding: 12px; 
            border: 1px solid rgba(255, 255, 255, 0.3); 
            border-radius: 8px; 
            background: rgba(0, 0, 0, 0.3); 
            color: #fff;
            text-align: center; font-size: 18px; margin-bottom: 15px; outline: none;
            backdrop-filter: blur(5px);
        }

        button {
            width: 100%; padding: 12px; border: none; border-radius: 8px;
            background: #00ff00; color: #000; font-weight: bold;
            cursor: pointer; text-transform: uppercase; transition: 0.3s;
        }

        button:hover { background: #00cc00; box-shadow: 0 0 25px rgba(0, 255, 0, 0.7); }

        .result-card { margin-top: 20px; padding: 15px; border-radius: 8px; font-weight: bold; }
        
        .footer-links { margin-top: 25px; }
        .footer-links a { color: #fff; text-decoration: none; font-size: 12px; margin: 0 10px; opacity: 0.8; }
        .footer-links a:hover { opacity: 1; text-decoration: underline; }
    </style>
</head>
<body>

    <div class="bg-container">
        <?php 
        for($i=0; $i<80; $i++) {
            $left = rand(0, 100); 
            $delay = rand(0, 10); 
            $size = rand(3, 8); 
            $duration = rand(4, 9);
            echo "<div class='orange-box' style='left: {$left}%; animation-delay: {$delay}s; animation-duration: {$duration}s; width: {$size}px; height: {$size}px;'></div>";
        }
        ?>
    </div>

    <div class="container">
        <h2>Biometric Access</h2>

        <div class="scan-box" id="displayBox">
            <div class="laser-line"></div>
            <span style="color: #ffffff; opacity: 0.4; font-weight: bold; letter-spacing: 2px;">READY</span>
        </div>

        <form method="GET">
            <input type="number" name="scan_id" placeholder="ENTER ID" required autofocus>
            <button type="submit">Scan Now</button>
        </form>

        <?php
        if (isset($_GET['scan_id'])) {
            $id = intval($_GET['scan_id']);
            
            // SQL QUERY
            $sql = "SELECT name, profile_image, fingerprint_image FROM users WHERE fingerprint_id = $id"; 
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $name = $row['name'];
                
                // IMAGE LOGIC
                $fingerprintPath = !empty($row['fingerprint_image']) ? $row['fingerprint_image'] : "Fingerprints/generic_print.png";

                // JAVASCRIPT UPDATE
                echo "<script>
                    document.getElementById('displayBox').innerHTML = `
                        <div class='laser-line'></div>
                        <img src='$fingerprintPath' style='width:90%; height:90%; object-fit:contain; filter: sepia(100%) hue-rotate(90deg) saturate(300%); opacity: 0.9;'>
                    `;
                </script>";

                $today = date('Y-m-d');
                $check = $conn->query("SELECT id FROM attendance WHERE fingerprint_id = $id AND DATE(scan_time) = '$today'");

                if ($check->num_rows > 0) {
                    // UPDATED TEXT HERE
                    echo "<div class='result-card' style='background: rgba(255, 165, 0, 0.2); color: #ffa500; border: 1px solid #ffa500;'>
                            ⚠ Already Marked your Attendance
                          </div>";
                } else {
                    $conn->query("INSERT INTO attendance (fingerprint_id) VALUES ($id)");
                    echo "<div class='result-card' style='background: rgba(0, 255, 0, 0.2); color: #00ff00; border: 1px solid #00ff00;'>
                            ✔ ACCESS GRANTED: $name
                          </div>";
                }
            } else {
                echo "<div class='result-card' style='background: rgba(255, 0, 0, 0.2); color: #ff4444; border: 1px solid #ff4444;'>
                        ✖ INVALID ID
                      </div>";
            }
        }
        ?>

        <div class="footer-links">
            <a href="view_attendance.php">View Attendance Logs</a>
            <a href="register.php">Register New User</a>
        </div>
    </div>
</body>
</html>