<?php

require_once 'database.php';
function getStudentProfileData() {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        return ['error' => 'Student ID is missing.'];
    }
    
    $student_id = $_GET['id'];
    $student_data = getStudentById($student_id);

    if (!$student_data) {
        return ['error' => 'Student not found.'];
    }
    
    //simulating the academic status logic based on the enrollment date (using constants)
    $enroll_ts = strtotime($student_data['enrollment_date']);
    $one_year_ago = strtotime('-1 year');

    if ($enroll_ts > time()) {
        $status = STATUS_PENDING;
        $status_class = 'status-pending';
    } elseif ($enroll_ts < $one_year_ago && date('Y') > date('Y', $enroll_ts) + 4) {
        $status = STATUS_GRADUATED;
        $status_class = 'status-graduated';
    } else {
        $status = STATUS_ACTIVE;
        $status_class = 'status-active';
    }
    
    $student_data['academic_status'] = $status;
    $student_data['status_class'] = $status_class; 
    
    return $student_data;
}

$profile = getStudentProfileData();

if (isset($profile['error'])) {
    $error_msg = $profile['error'];
    $profile = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Student Profile</h1>
        </header>

        <div class="nav-bar">
            <a href="student_management_dashboard.php" class="btn btn-info"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>

        <?php if ($profile): ?>
            <h2><i class="fas fa-user-circle"></i> <?= htmlspecialchars($profile['full_names_surname']) ?></h2>
            
            <div class="profile-card" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <div>
                    <p style="margin: 0; font-size: 1.1em;"><strong>Student ID:</strong> <?= htmlspecialchars($profile['student_id']) ?></p>
                </div>
                <div>
                    <p style="margin: 0;"><strong>Academic Status:</strong> <span class="status-badge <?= $profile['status_class'] ?>"><?= $profile['academic_status'] ?></span></p>
                </div>
            </div>
            
            <section class="profile-details">
                <h3><i class="fas fa-info-circle"></i> Personal & Enrollment Details</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="profile-card">
                        <p><strong>Email:</strong> <?= htmlspecialchars($profile['email']) ?></p>
                        <p><strong>Date of Birth:</strong> <?= htmlspecialchars($profile['dob']) ?></p>
                    </div>
                    <div class="profile-card">
                        <p><strong>Course of Study:</strong> <?= htmlspecialchars($profile['course_of_study']) ?></p>
                        <p><strong>Enrollment Date:</strong> <?= htmlspecialchars($profile['enrollment_date']) ?></p>
                    </div>
                </div>
            </section>
            
            <h3 style="margin-top: 40px;"><i class="fas fa-chart-line"></i> Reports & Management</h3>
            <div class="nav-bar">
                <a href="student_report.php?id=<?= urlencode($profile['student_id']) ?>&type=summary" class="btn btn-primary"><i class="fas fa-file-alt"></i> Profile Summary Report</a>
                <a href="student_report.php?id=<?= urlencode($profile['student_id']) ?>&type=confirmation" class="btn btn-primary"><i class="fas fa-clipboard-check"></i> Registration Slip</a>
            </div>

            <h3 style="margin-top: 40px;"><i class="fas fa-lock"></i> Admin Actions</h3>
            <div class="nav-bar">
                <a href="update_student_info.php?id=<?= urlencode($profile['student_id']) ?>" class="btn btn-info"><i class="fas fa-pen"></i> Update Information</a>
                <a href="javascript:void(0)" 
                   onclick="if(confirm('CONFIRM DELETE: Are you sure you want to permanently delete this student record?')) { window.location.href='delete_function.php?id=<?= urlencode($profile['student_id']) ?>'; }" 
                   class="btn btn-danger"><i class="fas fa-trash-alt"></i> Delete Record</a>
            </div>
               
        <?php else: ?>
            <div class="message-box error-message">❌ Error: <?= htmlspecialchars($error_msg ?? 'Could not load profile.') ?></div>
        <?php endif; ?>
    </div>
</body>
</html>