<?php
session_start();
$isAdmin = isset($_SESSION['admin_id']);
$isDoctor = isset($_SESSION['Tutor_id']);

if (!$isAdmin && !$isTutor) {
    header("Location: login.php");
    exit();
}

require 'db_connect.php';
include 'header.php';

$type = $_GET['type'];
$student_id = $_GET['student_id'];
$time = $_GET['time'];

$doctor_id = $isTutor ? $_SESSION['tutor_id'] : null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_time = $_POST['time'];
    $new_details = $_POST['details'];
    $new_location = isset($_POST['clinic_location']) ? $_POST['clinic_location'] : null;

    switch ($type) {
        case 'surgery':
            $stmt = $pdo->prepare(
                $isTutor ? "UPDATE Surgery SET SurgeryTime = :new_time, SurgeryType = :new_details WHERE StudentID = :student_id AND SurgeryTime = :time AND TutorID = :tutor_id" :
                            "UPDATE Surgery SET SurgeryTime = :new_time, SurgeryType = :new_details WHERE StudentID = :student_id AND SurgeryTime = :time"
            );
            break;
        case 'lab':
            $stmt = $pdo->prepare(
                $isTutor ? "UPDATE Labs SET LabTime = :new_time, LabType = :new_details, ClinicLocation = :new_location WHERE StudentID = :patient_id AND LabTime = :time AND EXISTS (SELECT 1 FROM TutorStudent WHERE TutorID = :tutor_id AND StudentID = :student_id)" :
                            "UPDATE Labs SET LabTime = :new_time, LabType = :new_details, ClinicLocation = :new_location WHERE StudentID = :patient_id AND LabTime = :time"
            );
            break;
        case 'checkup':
            $stmt = $pdo->prepare(
                $isTutor ? "UPDATE CheckUp SET CheckTime = :new_time, CheckReason = :new_details WHERE PatientID = :patient_id AND CheckTime = :time AND DoctorID = :doctor_id" :
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
        ':student_id' => $student_id,
        ':time' => $time
    ];

    if ($isTutor) {
        $params[':tutor_id'] = $tutor_id;
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
            $isTutor ? "SELECT SurgeryTime AS time, SurgeryType AS details FROM Surgery WHERE StudentID = :student_id AND SurgeryTime = :time AND TutorID = :tutor_id" :
                        "SELECT SurgeryTime AS time, SurgeryType AS details FROM Surgery WHERE StudentID = :student_id AND SurgeryTime = :time"
        );
        break;
    case 'lab':
        $stmt = $pdo->prepare(
            $isTutor ? "SELECT LabTime AS time, LabType AS details, ClinicLocation AS location FROM Labs WHERE StudentID = :student_id AND LabTime = :time AND EXISTS (SELECT 1 FROM TutorStudent WHERE TutorID = :tutor_id AND StudentID = :student_id)" :
                        "SELECT LabTime AS time, LabType AS details, ClinicLocation AS location FROM Labs WHERE StudentID = :student_id AND LabTime = :time"
        );
        break;
    case 'checkup':
        $stmt = $pdo->prepare(
            $isTutor ? "SELECT CheckTime AS time, CheckReason AS details FROM CheckUp WHERE StudentID = :student_id AND CheckTime = :time AND TutorID = :tutor_id" :
                        "SELECT CheckTime AS time, CheckReason AS details FROM CheckUp WHERE StudentID = :student_id AND CheckTime = :time"
        );
        break;
    default:
        echo "Invalid appointment type.";
        exit();
}

$params = [
    ':student_id' => $student_id,
    ':time' => $time
];

if ($isTutor) {
    $params[':tutor_id'] = $tutor_id;
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
    <link rel="stylesheet" href="student_management_style.css"> 
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
