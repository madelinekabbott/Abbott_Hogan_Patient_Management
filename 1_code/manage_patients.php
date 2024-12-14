<?php
session_start();
require 'db_connect.php';
include 'header.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    $stmt = $pdo->prepare("DELETE FROM DoctorPatient WHERE PatientID = :patient_id");
    $stmt->execute(['patient_id' => $delete_id]);

    $stmt = $pdo->prepare("DELETE FROM Patient WHERE PatientID = :patient_id");
    $stmt->execute(['patient_id' => $delete_id]);

    header("Location: manage_patients.php?deleted=true");
    exit();
}

$stmt = $pdo->query("
    SELECT 
        PatientID, PatientName, DOB, Address, City
    FROM 
        Patient
    ORDER BY 
        PatientName ASC
");
$patients = $stmt->fetchAll();

$deleted = isset($_GET['deleted']) && $_GET['deleted'] === 'true';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Patients</title>
    <link rel="stylesheet" href="patient_management_style.css">
    <script>
        function confirmDelete(patientName) {
            return confirm("Are you sure you want to delete " + patientName + "?");
        }
    </script>
</head>
<body>
    <div class="container">
        <h2 class="header">Manage Patients</h2>
        <?php if ($deleted): ?>
            <p class="success"></p>
        <?php endif; ?>
        <table class="table">
            <thead>
                <tr>
                    <th class="table-header">Name</th>
                    <th class="table-header">Date of Birth</th>
                    <th class="table-header">Address</th>
                    <th class="table-header">City</th>
                    <th class="table-header">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($patients)): ?>
                    <?php foreach ($patients as $patient): ?>
                        <tr>
                            <td class="table-cell"><?php echo htmlspecialchars($patient['PatientName']); ?></td>
                            <td class="table-cell"><?php echo htmlspecialchars($patient['DOB']); ?></td>
                            <td class="table-cell"><?php echo htmlspecialchars($patient['Address']); ?></td>
                            <td class="table-cell"><?php echo htmlspecialchars($patient['City']); ?></td>
                            <td class="table-cell">
                                <a href="view_patient.php?patient_id=<?php echo $patient['PatientID']; ?>" class="button-link">
                                    <button class="button">View Details</button>
                                </a>
                                <a href="manage_patients.php?delete=<?php echo $patient['PatientID']; ?>"
                                   class="button-link"
                                   onclick="return confirmDelete('<?php echo htmlspecialchars($patient['PatientName']); ?>');">
                                    <button class="button button-delete">Delete Record</button>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="table-cell" colspan="5">No patients found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <br>
        <a href="create_patient.php" class="button">Create New Patient</a>
        <br><br>
        <a href="logout.php" class="link">Logout</a>
    </div>
</body>
</html>
