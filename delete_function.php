<?php
require_once 'database.php';
$mysqli = connectDB();

$student_id = $_GET['id'] ?? null;
$log_file = 'deleted_log.txt';

if (!$student_id) {
    header("Location: student_management_dashboard.php?status=error&msg=" . urlencode("Error: Student ID is missing."));
    exit();
}

//collects the record before it is deleted for logging
$record_to_delete = getStudentById($student_id);

if (!$record_to_delete) {
    header("Location: student_management_dashboard.php?status=error&msg=" . urlencode("Error: Student not found or already deleted."));
    exit();
}

//the deleted log is now recorderd to a text file
$log_entry = "[" . date('Y-m-d H:i:s') . "] DELETED - ID: " . $student_id . " | Name: " . $record_to_delete['full_names_surname'] . " | Course: " . $record_to_delete['course_of_study'] . "\n";
file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);


//uses the DELETE SQL statement through PHP 
$sql = "DELETE FROM students WHERE student_id = ?";

if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("s", $student_id);
    
    if ($stmt->execute()) {
        $msg = "Record for " . htmlspecialchars($record_to_delete['full_names_surname']) . " (" . $student_id . ") successfully deleted and logged.";
        $status = 'success';
    } else {
        error_log("Error executing DELETE: " . $stmt->error);
        $msg = "Database deletion failed.";
        $status = 'error';
    }
    $stmt->close();
} else {
    error_log("Error preparing DELETE statement: " . $mysqli->error);
    $msg = "System error preparing database query.";
    $status = 'error';
}

closeDB();
header("Location: student_management_dashboard.php?status={$status}&msg=" . urlencode($msg));
exit();
?>