<!DOCTYPE html>
<html>
<head>
    <title>Register New User</title>
    <style>
        body { font-family: sans-serif; background: #0d1b2a; color: white; padding: 50px; text-align: center; }
        .reg-card { background: rgba(255,255,255,0.1); padding: 30px; border-radius: 15px; display: inline-block; }
        input { padding: 10px; margin: 10px; border-radius: 5px; border: none; width: 80%; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="reg-card">
        <h2>Register New Person</h2>
        <form method="POST">
            <input type="number" name="f_id" placeholder="Fingerprint ID (Unique Number)" required><br>
            <input type="text" name="u_name" placeholder="Full Name" required><br>
            <button type="submit" name="register">ADD TO SYSTEM</button>
        </form>

        <?php
        if (isset($_POST['register'])) {
            include 'db.php';
            $f_id = $_POST['f_id'];
            $u_name = $_POST['u_name'];

            $sql = "INSERT INTO users (fingerprint_id, name) VALUES ('$f_id', '$u_name')";
            
            if ($conn->query($sql) === TRUE) {
                echo "<p style='color: #00ff00;'>User added successfully!</p>";
            } else {
                echo "<p style='color: #ff4444;'>Error: ID already exists!</p>";
            }
        }
        ?>
        <br><a href="index.php" style="color: #007bff;">Back to Scanner</a>
    </div>
</body>
</html>