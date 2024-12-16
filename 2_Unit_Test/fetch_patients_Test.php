<?php

namespace Zubair\TestProject;

use PHPUnit\Framework\TestCase;

final class fetch_patients_Test extends TestCase
{

<?php

namespace Zubair\TestProject;

use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
<?php
require 'db_connect.php';

if (isset($_GET['doctor_id'])) {
    $doctor_id = $_GET['doctor_id'];

    $stmt = $pdo->prepare("SELECT Patient.* FROM Patient JOIN DoctorPatient ON Patient.PatientID = DoctorPatient.PatientID WHERE DoctorPatient.DoctorID = :doctor_id");
    $stmt->execute(['doctor_id' => $doctor_id]);
    $patients = $stmt->fetchAll();

    echo json_encode($patients);
    exit();
}
?>
}
}