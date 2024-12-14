<?php
session_start();
$isAdmin = isset($_SESSION['admin_id']);
$isDoctor = isset($_SESSION['doctor_id']);

if (!$isAdmin && !$isDoctor) {
    header("Location: login.php");
    exit();
}

require 'db_connect.php';
include 'header.php';

$type = $_GET['type'];
$patient_id = $_GET['patient_id'];
$time = $_GET['time'];

$doctor_id = $isDoctor ? $_SESSION['doctor_id'] : null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_time = $_POST['time'];
    $new_details = $_POST['details'];
    $new_location = isset($_POST['clinic_location']) ? $_POST['clinic_location'] : null;

    switch ($type) {
        case 'surgery':
            $stmt = $pdo->prepare(
                $isDoctor ? "UPDATE Surgery SET SurgeryTime = :new_time, SurgeryType = :new_details WHERE PatientID = :patient_id AND SurgeryTime = :time AND DoctorID = :doctor_id" :
                            "UPDATE Surgery SET SurgeryTime = :new_time, SurgeryType = :new_details WHERE PatientID = :patient_id AND SurgeryTime = :time"
            );
            break;
        case 'lab':
            $stmt = $pdo->prepare(
                $isDoctor ? "UPDATE Labs SET LabTime = :new_time, LabType = :new_details, ClinicLocation = :new_location WHERE PatientID = :patient_id AND LabTime = :time AND EXISTS (SELECT 1 FROM DoctorPatient WHERE DoctorID = :doctor_id AND PatientID = :patient_id)" :
                            "UPDATE Labs SET LabTime = :new_time, LabType = :new_details, ClinicLocation = :new_location WHERE PatientID = :patient_id AND LabTime = :time"
            );
            break;
        case 'checkup':
            $stmt = $pdo->prepare(
                $isDoctor ? "UPDATE CheckUp SET CheckTime = :new_time, CheckReason = :new_details WHERE PatientID = :patient_id AND CheckTime = :time AND DoctorID = :doctor_id" :
                            "UPDATE CheckUp SET CheckTime = :new_time, CheckReason = :new_details WHERE PatientID = :patient_id AND CheckTime = :time"
            );
            break;
        default:
            echo "Invalid appointment type.";
            exit();
    }

    $params = [
        ':new_time' => $new_time,
        ':new_details' => $new_details,
        ':patient_id' => $patient_id,
        ':time' => $time
    ];

    if ($isDoctor) {
        $params[':doctor_id'] = $doctor_id;
    }

    if ($type === 'lab') {
        $params[':new_location'] = $new_location; 
    }

    $stmt->execute($params);

    header("Location: view_appointments.php");
    exit();
}

switch ($type) {
    case 'surgery':
        $stmt = $pdo->prepare(
            $isDoctor ? "SELECT SurgeryTime AS time, SurgeryType AS details FROM Surgery WHERE PatientID = :patient_id AND SurgeryTime = :time AND DoctorID = :doctor_id" :
                        "SELECT SurgeryTime AS time, SurgeryType AS details FROM Surgery WHERE PatientID = :patient_id AND SurgeryTime = :time"
        );
        break;
    case 'lab':
        $stmt = $pdo->prepare(
            $isDoctor ? "SELECT LabTime AS time, LabType AS details, ClinicLocation AS location FROM Labs WHERE PatientID = :patient_id AND LabTime = :time AND EXISTS (SELECT 1 FROM DoctorPatient WHERE DoctorID = :doctor_id AND PatientID = :patient_id)" :
                        "SELECT LabTime AS time, LabType AS details, ClinicLocation AS location FROM Labs WHERE PatientID = :patient_id AND LabTime = :time"
        );
        break;
    case 'checkup':
        $stmt = $pdo->prepare(
            $isDoctor ? "SELECT CheckTime AS time, CheckReason AS details FROM CheckUp WHERE PatientID = :patient_id AND CheckTime = :time AND DoctorID = :doctor_id" :
                        "SELECT CheckTime AS time, CheckReason AS details FROM CheckUp WHERE PatientID = :patient_id AND CheckTime = :time"
        );
        break;
    default:
        echo "Invalid appointment type.";
        exit();
}

$params = [
    ':patient_id' => $patient_id,
    ':time' => $time
];

if ($isDoctor) {
    $params[':doctor_id'] = $doctor_id;
}

$stmt->execute($params);
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
