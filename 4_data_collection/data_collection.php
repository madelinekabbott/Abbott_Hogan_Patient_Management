<?php
// data_collection.php
// Description: Data collection script to retrieve and display patient management data separately from labs.

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "patient_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1. List all patients (excluding lab results)
echo "<h2>Patient Information</h2>";
$sql = "SELECT PatientID, PatientName, Diagnoses, Prescriptions, DOB, City, Address
        FROM Patient";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>Patient ID</th>
                <th>Name</th>
                <th>Diagnoses</th>
                <th>Prescriptions</th>
                <th>Date of Birth</th>
                <th>City</th>
                <th>Address</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["PatientID"] . "</td>
                <td>" . $row["PatientName"] . "</td>
                <td>" . ($row["Diagnoses"] ?? 'N/A') . "</td>
                <td>" . ($row["Prescriptions"] ?? 'N/A') . "</td>
                <td>" . $row["DOB"] . "</td>
                <td>" . $row["City"] . "</td>
                <td>" . $row["Address"] . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No patient data found.";
}

// 2. Separate section for Lab Results
echo "<h2>Lab Results</h2>";
$sql = "SELECT Labs.LabTime, Labs.LabType, Labs.LabResult, Patient.PatientName, Labs.ClinicLocation
        FROM Labs
        JOIN Patient ON Labs.PatientID = Patient.PatientID";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>Lab Time</th>
                <th>Lab Type</th>
                <th>Lab Result</th>
                <th>Patient Name</th>
                <th>Clinic Location</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["LabTime"] . "</td>
                <td>" . $row["LabType"] . "</td>
                <td>" . ($row["LabResult"] ?? 'Pending') . "</td>
                <td>" . $row["PatientName"] . "</td>
                <td>" . $row["ClinicLocation"] . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No lab results found.";
}

// 3. Doctor-patient assignments
echo "<h2>Doctor-Patient Assignments</h2>";
$sql = "SELECT Doctor.DoctorName, Patient.PatientName
        FROM DoctorPatient
        JOIN Doctor ON DoctorPatient.DoctorID = Doctor.DoctorID
        JOIN Patient ON DoctorPatient.PatientID = Patient.PatientID";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1'><tr><th>Doctor Name</th><th>Patient Name</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["DoctorName"] . "</td><td>" . $row["PatientName"] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "No doctor-patient assignments found.";
}

// 4. Upcoming appointments and surgeries
echo "<h2>Upcoming Check-ups and Surgeries</h2>";
$sql = "SELECT CheckUp.CheckTime AS EventTime, CheckUp.CheckReason AS EventType, Patient.PatientName
        FROM CheckUp
        JOIN Patient ON CheckUp.PatientID = Patient.PatientID
        WHERE CheckUp.CheckTime >= NOW()
        UNION
        SELECT Surgery.SurgeryTime AS EventTime, Surgery.SurgeryType AS EventType, Patient.PatientName
        FROM Surgery
        JOIN Patient ON Surgery.PatientID = Patient.PatientID
        WHERE Surgery.SurgeryTime >= NOW()
        ORDER BY EventTime ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr><th>Event Time</th><th>Event Type</th><th>Patient Name</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["EventTime"] . "</td>
                <td>" . $row["EventType"] . "</td>
                <td>" . $row["PatientName"] . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No upcoming events found.";
}

// Close connection
$conn->close();
?>
