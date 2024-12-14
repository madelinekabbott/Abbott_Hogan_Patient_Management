<?php
session_start();
require 'db_connect.php';
include 'header.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$selected_doctor_id = $_GET['doctor_id'] ?? null;

$stmt = $pdo->query("SELECT DoctorID, DoctorName FROM Doctor ORDER BY DoctorName ASC");
$all_doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

$doctor = null;
$patients = [];

if ($selected_doctor_id) {
    $stmt = $pdo->prepare("
        SELECT DoctorID, DoctorName, Department, password, DOB, Address, Email, PhoneNumber
        FROM Doctor
        WHERE DoctorID = :doctor_id
    ");
    $stmt->execute(['doctor_id' => $selected_doctor_id]);
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($doctor) {
        $stmt = $pdo->prepare("
            SELECT Patient.PatientID, Patient.PatientName, Patient.ContactNumber
            FROM Patient
            JOIN DoctorPatient ON Patient.PatientID = DoctorPatient.PatientID
            WHERE DoctorPatient.DoctorID = :doctor_id
            ORDER BY Patient.PatientName ASC
        ");
        $stmt->execute(['doctor_id' => $selected_doctor_id]);
        $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

function censorPassword($password) {
    if (empty($password)) {
        return '';
    }
    return str_repeat('*', 8);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Doctors</title>
    <link rel="stylesheet" href="patient_management_style.css"> 
</head>
<body>
    <div class="container">
        <h2>Manage Doctors</h2>
        <p>Select a doctor from the dropdown to view their information.</p>

        <form action="manage_doctors.php" method="GET">
            <label for="doctor_id">Doctor:</label>
            <select name="doctor_id" id="doctor_id" required>
                <option value="">-- Select a Doctor --</option>
                <?php foreach ($all_doctors as $doc): ?>
                    <option value="<?php echo htmlspecialchars($doc['DoctorID']); ?>" 
                        <?php echo ($selected_doctor_id == $doc['DoctorID']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($doc['DoctorName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="submit" value="View Doctor" class="button">
        </form>

        <?php if ($selected_doctor_id && $doctor): ?>
            <h3>Doctor Information</h3>
            <p><strong>Doctor ID:</strong> <?php echo htmlspecialchars($doctor['DoctorID']); ?></p>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($doctor['DoctorName']); ?></p>
            <p><strong>Department:</strong> <?php echo htmlspecialchars($doctor['Department']); ?></p>
            <p><strong>Password:</strong> 
                <span id="passwordField" data-password="<?php echo htmlspecialchars($doctor['password']); ?>">
                    <?php echo censorPassword($doctor['password']); ?>
                </span>
                <button type="button" id="togglePasswordBtn">Show password?</button>
            </p>
            <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($doctor['DOB']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($doctor['Address']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($doctor['Email']); ?></p>
            <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($doctor['PhoneNumber']); ?></p>

            <a href="edit_doctor.php?doctor_id=<?php echo urlencode($doctor['DoctorID']); ?>" class="button">Edit Doctor Information</a>

            <h3>Patients</h3>
            <?php if (!empty($patients)): ?>
                <ul class="patients">
                    <?php foreach ($patients as $patient): ?>
                        <li>
                            <?php echo htmlspecialchars($patient['PatientName']); ?> - 
                            <?php echo htmlspecialchars($patient['ContactNumber']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>This doctor has no patients assigned currently.</p>
            <?php endif; ?>

            <!-- Button to manage patients under this doctor -->
            <a href="manage_doctor_patients.php?doctor_id=<?php echo urlencode($doctor['DoctorID']); ?>" class="button">
                Manage Patients for Dr. <?php echo htmlspecialchars($doctor['DoctorName']); ?>
            </a>

        <?php elseif ($selected_doctor_id && !$doctor): ?>
            <p>No doctor found with the specified ID.</p>
        <?php endif; ?>

        <hr>
        <a href="create_doctor.php" class="button">Create New Doctor</a>
        
    </div>

    <script>
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passwordField = document.getElementById('passwordField');

        let isPasswordHidden = true; // current state is hidden (asterisks)

        toggleBtn.addEventListener('click', function() {
            if (isPasswordHidden) {
                // Show the actual password
                passwordField.textContent = passwordField.getAttribute('data-password');
                toggleBtn.textContent = "Hide password?";
            } else {
                // Hide the password again (censored)
                passwordField.textContent = "********";
                toggleBtn.textContent = "Show password?";
            }
            isPasswordHidden = !isPasswordHidden;
        });
    </script>
</body>
</html>
