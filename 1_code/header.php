<?php

$dashboardUrl = 'login.php';

if (isset($_SESSION['admin_id'])) {
    $dashboardUrl = 'admin_dashboard.php';
}
elseif (isset($_SESSION['doctor_id'])) {
    $dashboardUrl = 'doctor_dashboard.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Management System</title>
    <link rel="stylesheet" href="patient_management_style.css">
</head>
<body>
    <header>
        <nav>
            <a href="<?php echo htmlspecialchars($dashboardUrl); ?>" class="dashboard-icon">
                <img src="https://vectorified.com/images/home-icon-png-white-33.png" alt="Dashboard Icon" class="dashboard-img">
            </a> 
        </nav>
        <h1>Patient Management System</h1>
    </header>
</body>
</html>
