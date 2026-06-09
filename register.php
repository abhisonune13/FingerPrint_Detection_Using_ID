 <?php
include 'db.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $f_id = intval($_POST['fingerprint_id']);

    // HANDLE PROFILE IMAGE UPLOAD
    $profile_path = "";
    if (!empty($_FILES['profile_image']['name'])) {
        $target_dir = "uploads/";
        $profile_path = $target_dir . basename($_FILES["profile_image"]["name"]);
        move_uploaded_file($_FILES["profile_image"]["tmp_name"], $profile_path);
    }

    // HANDLE FINGERPRINT IMAGE UPLOAD
    $finger_path = "";
    if (!empty($_FILES['fingerprint_image']['name'])) {
        $target_dir = "uploads/";
        $finger_path = $target_dir . basename($_FILES["fingerprint_image"]["name"]);
        move_uploaded_file($_FILES["fingerprint_image"]["tmp_name"], $finger_path);
    }

    // INSERT INTO DATABASE
    // Note: We use ON DUPLICATE KEY UPDATE so if ID exists, it updates the user instead of erroring
    $sql = "INSERT INTO users (fingerprint_id, name, profile_image, fingerprint_image) 
            VALUES ($f_id, '$name', '$profile_path', '$finger_path')
            ON DUPLICATE KEY UPDATE name='$name', profile_image='$profile_path', fingerprint_image='$finger_path'";

    if ($conn->query($sql) === TRUE) {
        $message = "<div style='color:#00ff00; margin-bottom:15px;'>✔ User Registered Successfully!</div>";
    } else {
        $message = "<div style='color:#ff4444; margin-bottom:15px;'>✖ Error: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register User</title>
    <style>
        /* SAME GLASSMORPHISM STYLE AS INDEX.PHP */
        body {
            margin: 0; padding: 0; height: 100vh;
            display: flex; justify-content: center; align-items: center;
            background: radial-gradient(circle at center, #0d1b2a 0%, #04090f 100%);
            font-family: 'Segoe UI', sans-serif;
            color: white;
        }

        .container {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.01));
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
            padding: 40px;
            border-radius: 25px;
            width: 400px;
            text-align: center;
        }

        h2 { letter-spacing: 2px; margin-bottom: 25px; }

        input {
            width: 90%; padding: 12px; margin-bottom: 15px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            background: rgba(0, 0, 0, 0.3);
            color: #fff; outline: none;
        }

        /* File input styling */
        input[type="file"] { padding: 8px; font-size: 14px; }
        
        label { display: block; text-align: left; margin-left: 5%; margin-bottom: 5px; font-size: 14px; opacity: 0.8; }

        button {
            width: 100%; padding: 12px; border: none; border-radius: 8px;
            background: #00ff00; color: #000; font-weight: bold;
            cursor: pointer; text-transform: uppercase; margin-top: 10px;
        }
        button:hover { background: #00cc00; }

        .back-link { display: block; margin-top: 20px; color: #fff; text-decoration: none; font-size: 14px; opacity: 0.7; }
        .back-link:hover { opacity: 1; text-decoration: underline; }
    </style>
</head>
<body>

    <div class="container">
        <h2>Register New User</h2>
        
        <?php echo $message; ?>

        <form method="POST" enctype="multipart/form-data">
            <label>Fingerprint ID (Number):</label>
            <input type="number" name="fingerprint_id" required placeholder="e.g. 101">

            <label>Full Name:</label>
            <input type="text" name="name" required placeholder="e.g. John Doe">

            <label>Profile Photo:</label>
            <input type="file" name="profile_image" accept="image/*">

            <label>Fingerprint Image (The Scan):</label>
            <input type="file" name="fingerprint_image" accept="image/*">

            <button type="submit">Save User</button>
        </form>

        <a href="index.php" class="back-link">← Back to Scanner</a>
    </div>

</body>
</html>