<?php
include 'header_no_dash.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <link rel="stylesheet" href="student_management_style.css">
    <script>
        function showErrorPopup(message) {
            alert(message);
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Log In</h2>
        <form action="authenticate.php" method="POST">
            <label for="username">User ID (Tutor/Admin):</label>
            <input type="text" id="username" name="username" required><br><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>

            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="tutor">Tutor</option>
                <option value="admin">Admin</option>
            </select><br><br>

            <input type="submit" value="Log In" class="button">
        </form>
    </div>
    <?php
    if (isset($_GET['error'])) {
        if ($_GET['error'] === 'tutor') {
            echo "<script>showErrorPopup('Invalid credentials. If you are having trouble logging in, please contact an admin to reset your password.');</script>";
        } elseif ($_GET['error'] === 'admin') {
            echo "<script>showErrorPopup('Invalid credentials. If you need to reset your password, please contact the developer.');</script>";
        }
    }
    ?>
</body>
</html>
