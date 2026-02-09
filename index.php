<?php
// index.php - Student Registration Form (Admin Only) - UI Enhanced
require_once 'database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - Admin</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Student Registration System</h1>
            <p>Admin Registration Form | Secure & User-Friendly</p>
        </header>

        <div class="nav-bar">
            <a href="student_management_dashboard.php" class="btn btn-primary"><i class="fas fa-list-ul"></i> Student Dashboard</a>
        </div>

        <?php
        //Displaying success/error messages in stylish boxes
        if (isset($_GET['status']) && $_GET['status'] == 'success') {
            echo '<div class="message-box success-message"><i class="fas fa-check-circle"></i> Student successfully registered!</div>';
        } elseif (isset($_GET['status']) && $_GET['status'] == 'error' && isset($_GET['msg'])) {
            echo '<div class="message-box error-message"><i class="fas fa-exclamation-triangle"></i> Registration Failed: ' . htmlspecialchars($_GET['msg']) . '</div>';
        }
        ?>

        <form action="student_registration.php" method="POST">
            <h2><i class="fas fa-user-plus"></i> Register New Student</h2>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="full_names_surname">Full Names and Surname</label>
                    <input type="text" id="full_names_surname" name="full_names_surname" placeholder="Enter full name and surname" required>
                </div>
                
                <div class="form-group">
                    <label for="student_id">Student ID</label>
                    <input type="text" id="student_id" name="student_id" placeholder="e.g., S20240001" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="student@gmail.com" required>
                </div>
                
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" id="dob" name="dob" required>
                </div>

                <div class="form-group">
                    <label for="course_of_study">Course of Study</label>
                    <input type="text" id="course_of_study" name="course_of_study" placeholder="e.g., Computer Science" required>
                </div>
                
                <div class="form-group">
                    <label for="enrollment_date">Enrollment Date</label>
                    <input type="date" id="enrollment_date" name="enrollment_date" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary" style="margin-top: 20px;"><i class="fas fa-save"></i> Register Student</button>
        </form>
    </div>
</body>
</html>