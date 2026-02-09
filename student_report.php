<?php
require_once 'database.php';

$student_id = $_GET['id'] ?? null;
$report_type = $_GET['type'] ?? 'summary'; // 'summary' or 'confirmation'
$action = $_GET['action'] ?? 'view'; // 'view' or 'download' (for print/PDF)

if (!$student_id) {
    die("Error: Student ID is required to generate a report.");
}

$profile = getStudentById($student_id);

if (!$profile) {
    die("Error: Student not found.");
}

//setting the headers for 'download' (print view simulation)
if ($action == 'download') {
    //simulates a downloadable/printable file by sending appropriate headers
    header('Content-Type: text/html');
    header('Content-Disposition: attachment; filename="' . $report_type . '_' . $student_id . '.html"');
}

function generateProfileSummaryReport($data) {
    //Report 1: Profile Summary Report (Name, ID, Email, DOB, Course, Enrollment Date)
    $html = "<h2>Profile Summary Report - " . htmlspecialchars($data['full_names_surname']) . "</h2>";
    $html .= "<p>Generated on: " . date('Y-m-d H:i:s') . "</p>";
    $html .= "<hr style='border: 1px solid #ccc;'>";
    $html .= "<table style='width: 100%; border: 1px solid #000; border-collapse: collapse;'>";
    $html .= "<tr><td style='padding: 8px; border: 1px solid #000; background: #eee;'><strong>Name:</strong></td><td style='padding: 8px; border: 1px solid #000;'>" . htmlspecialchars($data['full_names_surname']) . "</td></tr>";
    $html .= "<tr><td style='padding: 8px; border: 1px solid #000; background: #eee;'><strong>Student ID:</strong></td><td style='padding: 8px; border: 1px solid #000;'>" . htmlspecialchars($data['student_id']) . "</td></tr>";
    $html .= "<tr><td style='padding: 8px; border: 1px solid #000; background: #eee;'><strong>Email:</strong></td><td style='padding: 8px; border: 1px solid #000;'>" . htmlspecialchars($data['email']) . "</td></tr>";
    $html .= "<tr><td style='padding: 8px; border: 1px solid #000; background: #eee;'><strong>Date of Birth:</strong></td><td style='padding: 8px; border: 1px solid #000;'>" . htmlspecialchars($data['dob']) . "</td></tr>";
    $html .= "<tr><td style='padding: 8px; border: 1px solid #000; background: #eee;'><strong>Course:</strong></td><td style='padding: 8px; border: 1px solid #000;'>" . htmlspecialchars($data['course_of_study']) . "</td></tr>";
    $html .= "<tr><td style='padding: 8px; border: 1px solid #000; background: #eee;'><strong>Enrollment Date:</strong></td><td style='padding: 8px; border: 1px solid #000;'>" . htmlspecialchars($data['enrollment_date']) . "</td></tr>";
    $html .= "</table>";
    return $html;
}

function generateConfirmationSlip($data) {
    //Report 2: Registration Confirmation Slip (Registration timestamp, Course summary, Status)
    
    //simulates Status based on enrollment (just like in student_profile.php)
    $enroll_ts = strtotime($data['enrollment_date']);
    $status = $enroll_ts <= time() ? 'Active' : 'Pending';

    $html = "<h2>Registration Confirmation Slip</h2>";
    $html .= "<p><strong>University of *(name of university goes here)* </strong></p>";
    $html .= "<hr style='border: 1px solid #1976D2;'>";
    $html .= "<div style='background: #e3f2fd; padding: 15px; border: 1px solid #90caf9; margin-bottom: 20px;'>";
    $html .= "<p><strong>Registration Timestamp:</strong> " . date('Y-m-d H:i:s') . "</p>"; // Using current time as registration metadata
    $html .= "<p><strong>Student ID:</strong> " . htmlspecialchars($data['student_id']) . "</p>";
    $html .= "<p><strong>Student Name:</strong> " . htmlspecialchars($data['full_names_surname']) . "</p>";
    $html .= "</div>";
    
    $html .= "<h3>Course Summary</h3>";
    $html .= "<p><strong>Course Enrolled:</strong> " . htmlspecialchars($data['course_of_study']) . "</p>";
    $html .= "<p><strong>Enrollment Date:</strong> " . htmlspecialchars($data['enrollment_date']) . "</p>";
    $html .= "<p><strong>Current Status:</strong> <span style='font-weight: bold; color: " . ($status == 'Active' ? 'green' : 'orange') . "'>$status</span></p>";
    $html .= "<p style='margin-top: 30px;'>*This slip confirms your registration with the university. Please keep it for your records.</p>";
    
    return $html;
}

//generates the content based on report_type
$report_content = '';
$report_title = '';
if ($report_type == 'summary') {
    $report_content = generateProfileSummaryReport($profile);
    $report_title = 'Profile Summary Report';
} elseif ($report_type == 'confirmation') {
    $report_content = generateConfirmationSlip($profile);
    $report_title = 'Registration Confirmation Slip';
} else {
    $report_content = "<p>Invalid report type specified.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($report_title) ?></title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 40px; }
        .print-options { margin-bottom: 20px; }
        @media print {
            .print-options { display: none; }
        }
    </style>
</head>
<body>
    <?php if ($action == 'view'): ?>
    <div class="print-options">
        <h1><?= htmlspecialchars($report_title) ?></h1>
        <p>Viewing report for Student ID: <?= htmlspecialchars($student_id) ?></p>
        <button onclick="window.print()" style="padding: 10px 15px; cursor: pointer; background: #1976D2; color: white; border: none; border-radius: 4px;">🖨️ Print/Download PDF</button>
        <a href="student_profile.php?id=<?= urlencode($student_id) ?>" style="padding: 10px 15px; margin-left: 10px;">Back to Profile</a>
        <hr>
    </div>
    <?php endif; ?>
    
    <div class="report-content">
        <?= $report_content ?>
    </div>
</body>
</html>
<?php

if ($action == 'download') {
    exit();
}
?>