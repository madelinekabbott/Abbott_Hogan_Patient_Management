<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

require 'db_connect.php';
include 'header.php';

$doctor_id = $_SESSION['doctor_id'];
$type = $_GET['type'];
$patient_id = $_GET['patient_id'];
$time = $_GET['time'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_time = $_POST['time'];
    $new_details = $_POST['details'];
    $new_location = isset($_POST['clinic_location']) ? $_POST['clinic_location'] : null;

    // Update the correct table based on type
    switch ($type) {
        case 'surgery':
            $stmt = $pdo->prepare("UPDATE Surgery SET SurgeryTime = :new_time, SurgeryType = :new_details WHERE PatientID = :patient_id AND SurgeryTime = :time AND DoctorID = :doctor_id");
            break;
        case 'lab':
            $stmt = $pdo->prepare("UPDATE Labs SET LabTime = :new_time, LabType = :new_details, ClinicLocation = :new_location WHERE PatientID = :patient_id AND LabTime = :time AND EXISTS (SELECT 1 FROM DoctorPatient WHERE DoctorID = :doctor_id AND PatientID = :patient_id)");
            break;
        case 'checkup':
            $stmt = $pdo->prepare("UPDATE CheckUp SET CheckTime = :new_time, CheckReason = :new_details WHERE PatientID = :patient_id AND CheckTime = :time AND DoctorID = :doctor_id");
            break;
        default:
            echo "Invalid appointment type.";
            exit();
    }

    // Execute the statement
    // Execute the statement
    $params = [
        ':new_time' => $new_time,
        ':new_details' => $new_details,
        ':patient_id' => $patient_id,
        ':time' => $time,
        ':doctor_id' => $doctor_id
    ];

    if ($type === 'lab') {
        $params[':new_location'] = $new_location; // Only include this parameter for lab appointments
    }

    $stmt->execute($params);


    header("Location: view_appointments.php");
    exit();
}

// Fetch current appointment details for the form
switch ($type) {
    case 'surgery':
        $stmt = $pdo->prepare("SELECT SurgeryTime AS time, SurgeryType AS details FROM Surgery WHERE PatientID = :patient_id AND SurgeryTime = :time AND DoctorID = :doctor_id");
        break;
    case 'lab':
        $stmt = $pdo->prepare("SELECT LabTime AS time, LabType AS details, ClinicLocation AS location FROM Labs WHERE PatientID = :patient_id AND LabTime = :time AND EXISTS (SELECT 1 FROM DoctorPatient WHERE DoctorID = :doctor_id AND PatientID = :patient_id)");
        break;
    case 'checkup':
        $stmt = $pdo->prepare("SELECT CheckTime AS time, CheckReason AS details FROM CheckUp WHERE PatientID = :patient_id AND CheckTime = :time AND DoctorID = :doctor_id");
        break;
    default:
        echo "Invalid appointment type.";
        exit();
}

$stmt->execute([
    ':patient_id' => $patient_id,
    ':time' => $time,
    ':doctor_id' => $doctor_id
]);
$appointment = $stmt->fetch();

if (!$appointment) {
    echo "Appointment not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Appointment</title>
    <link rel="stylesheet" href="patient_management_style.css"> 
</head>
<body>
    <div class="container">
        <h2 class="header">Edit Appointment</h2>
        <form method="post">
            <label for="time">Time:</label>
            <input type="datetime-local" class="input" name="time" id="time" value="<?php echo htmlspecialchars($appointment['time']); ?>" required>
            <br>

            <label for="details">Details:</label>
            <input type="text" class="input" name="details" id="details" value="<?php echo htmlspecialchars($appointment['details']); ?>" required>
            <br>

            <?php if ($type === 'lab'): ?>
                <label for="clinic_location">Clinic Location:</label>
                <input type="text" class="input" name="clinic_location" id="clinic_location" value="<?php echo htmlspecialchars($appointment['location']); ?>">
                <br>
            <?php endif; ?>

            <button type="submit" class="button">Save Changes</button>
        </form>
        <br>
        <a href="view_appointments.php" class="link">Back to Appointments</a>
    </div>
</body>
</html>
