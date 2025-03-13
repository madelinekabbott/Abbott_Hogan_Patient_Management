<?php
session_start();
$isAdmin = isset($_SESSION['admin_id']);
$isTutor = isset($_SESSION['Tutor_id']);

if (!$isAdmin && !$isTutor) {
    header("Location: login.php");
    exit();
}

require 'db_connect.php';
include 'header.php';

$type = $_GET['type'];
$student_id = $_GET['student_id'];
$time = $_GET['time'];

$tutor_id = $isTutor ? $_SESSION['tutor_id'] : null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_time = $_POST['time'];
    $new_details = $_POST['details'];
    $new_location = isset($_POST['tutor_location']) ? $_POST['tutor_location'] : null;

    switch ($type) {
        case 'homeworkhelp':
            $stmt = $pdo->prepare(
                $isTutor ? "UPDATE HomeworkHelp SET HwTime = :new_time, HwType = :new_details WHERE StudentID = :student_id AND HwTime = :time AND TutorID = :tutor_id" :
                            "UPDATE HomeworkHelp SET HwTime = :new_time, HwType = :new_details WHERE StudentID = :student_id AND HwTime = :time"
            );
            break;
        case 'testprep':
            $stmt = $pdo->prepare(
                $isTutor ? "UPDATE TestPrep SET TestPrepTime = :new_time, TestPrepType = :new_details, TutorLocation = :new_location WHERE StudentID = :student_id AND TestPrepTime = :time AND EXISTS (SELECT 1 FROM TutorStudent WHERE TutorID = :tutor_id AND StudentID = :student_id)" :
                            "UPDATE TestPrep SET TestPrepTime = :new_time, TestPrepType = :new_details, TutorLocation = :new_location WHERE StudentID = :student_id AND TestPrepTime = :time"
            );
            break;
        case 'meetup':
            $stmt = $pdo->prepare(
                $isTutor ? "UPDATE MeetUp SET MeetUpTime = :new_time, CheckReason = :new_details WHERE StudentID = :student_id AND MeetUpTime = :time AND TutorID = :tutor_id" :
                            "UPDATE MeetUp SET MeetUpTime = :new_time, CheckReason = :new_details WHERE StudentID = :student_id AND MeetUpTime = :time"
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
    case 'homeworkhelp':
        $stmt = $pdo->prepare(
            $isTutor ? "SELECT HwTime AS time, HwType AS details FROM HomeworkHelp WHERE StudentID = :student_id AND HwTime = :time AND TutorID = :tutor_id" :
                        "SELECT HwTime AS time, HwType AS details FROM HomeworkHelp WHERE StudentID = :student_id AND HwTime = :time"
        );
        break;
    case 'testprep':
        $stmt = $pdo->prepare(
            $isTutor ? "SELECT TestPrepTime AS time, TestPrepType AS details, TutorLocation AS location FROM TestPrep WHERE StudentID = :student_id AND TestPrepTime = :time AND EXISTS (SELECT 1 FROM TutorStudent WHERE TutorID = :tutor_id AND StudentID = :student_id)" :
                        "SELECT TestPrepTime AS time, TestPrepType AS details, TutorLocation AS location FROM TestPrep WHERE StudentID = :student_id AND TestPrepTime = :time"
        );
        break;
    case 'meetup':
        $stmt = $pdo->prepare(
            $isTutor ? "SELECT MeetUpTime AS time, MeetUpReason AS details FROM MeetUp WHERE StudentID = :student_id AND MeetUpTime = :time AND TutorID = :tutor_id" :
                        "SELECT MeetUpTime AS time, MeetUpReason AS details FROM MeetUp WHERE StudentID = :student_id AND MeetUpTime = :time"
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

            <?php if ($type === 'testprep'): ?>
                <label for="tutor_location">Tutor Location:</label>
                <input type="text" class="input" name="tutor_location" id="tutor_location" value="<?php echo htmlspecialchars($appointment['location']); ?>">
                <br>
            <?php endif; ?>

            <button type="submit" class="button">Save Changes</button>
        </form>
        <br>
        <a href="view_appointments.php" class="link">Back to Appointments</a>
    </div>
</body>
</html>
