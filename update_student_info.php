<?php
require_once 'database.php';
$mysqli = connectDB();
$student_id = $_GET['id'] ?? null;
$error = '';
$success = '';

//load current student data if the ID is provided
$current_data = null;
if ($student_id) {
    $current_data = getStudentById($student_id);
    if (!$current_data) {
        $error = "Error: Student not found with ID: " . htmlspecialchars($student_id);
        $student_id = null; //prevent submission if no data found
    }
} else {
    $error = "Error: Student ID is required for update.";
}

//handles form submission (Update MySQL using secure PHP logic)
if ($_SERVER["REQUEST_METHOD"] == "POST" && $student_id) {
    
    //collects and sanitises user inputs inputs
    $full_names_surname = trim($_POST['full_names_surname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $course_of_study = trim($_POST['course_of_study'] ?? '');
    $enrollment_date = trim($_POST['enrollment_date'] ?? '');
    $submitted_id = trim($_POST['student_id'] ?? '');

    $update_errors = [];

    //basic validation check 
    if (empty($full_names_surname) || empty($email) || empty($dob) || empty($course_of_study) || empty($enrollment_date)) {
        $update_errors[] = "All fields are required.";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $update_errors[] = "Invalid email format.";
    }
    
    //CRITICAL: Ensures the student_id from the form matches the GET parameter for security
    if ($submitted_id !== $student_id) {
        $update_errors[] = "Security error: Mismatching Student ID detected.";
    }

    if (empty($update_errors)) {
        //sql injection prevention
        $sql = "UPDATE students SET full_names_surname = ?, email = ?, dob = ?, course_of_study = ?, enrollment_date = ? WHERE student_id = ?";

        if ($stmt = $mysqli->prepare($sql)) {
            //Binding parameters
            $stmt->bind_param("ssssss", $full_names_surname, $email, $dob, $course_of_study, $enrollment_date, $student_id);
            
            if ($stmt->execute()) {
                $success = "Student record successfully updated!";
                //re-collects the data to display the updated values in the form
                $current_data = getStudentById($student_id); 
            } else {
                error_log("Error executing UPDATE: " . $stmt->error);
                $error = "Database update failed. Check logs.";
            }
            $stmt->close();
        } else {
            error_log("Error preparing UPDATE statement: " . $mysqli->error);
            $error = "System error preparing database query.";
        }
    } else {
        $error = implode(" ", $update_errors);
    }
}

closeDB();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Student</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Update Student Information</h1>
            <p>Admin Only</p>
        </header>

        <nav style="margin-bottom: 20px;">
            <?php if ($student_id): ?>
                <a href="student_profile.php?id=<?= urlencode($student_id) ?>" class="btn btn-primary">Back to Profile</a>
            <?php endif; ?>
            <a href="student_management_dashboard.php" class="btn btn-info">Back to Dashboard</a>
        </nav>
        
        <?php
        if ($success) {
            echo '<div class="success-message">✅ ' . htmlspecialchars($success) . '</div>';
        }
        if ($error) {
            echo '<div class="error-message">❌ ' . htmlspecialchars($error) . '</div>';
        }
        ?>

        <?php if ($current_data): ?>
            <form action="update_student_info.php?id=<?= urlencode($student_id) ?>" method="POST">
                <h2>Update Record for ID: <?= htmlspecialchars($current_data['student_id']) ?></h2>
                <input type="hidden" name="student_id" value="<?= htmlspecialchars($current_data['student_id']) ?>"> 
                
                <div class="form-group">
                    <label for="full_names_surname">Full Name</label>
                    <input type="text" id="full_names_surname" name="full_names_surname" value="<?= htmlspecialchars($current_data['full_names_surname']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($current_data['email']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" id="dob" name="dob" value="<?= htmlspecialchars($current_data['dob']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="course_of_study">Course of Study</label>
                    <input type="text" id="course_of_study" name="course_of_study" value="<?= htmlspecialchars($current_data['course_of_study']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="enrollment_date">Enrollment Date</label>
                    <input type="date" id="enrollment_date" name="enrollment_date" value="<?= htmlspecialchars($current_data['enrollment_date']) ?>" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>