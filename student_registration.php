<?php
require_once 'database.php';
$mysqli = connectDB();

//checking to see if the form was/is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    //collects and sanitises user inputs (Form Handling with PHP, using variables)
    $full_names_surname = trim($_POST['full_names_surname'] ?? '');
    $student_id = trim($_POST['student_id'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $course_of_study = trim($_POST['course_of_study'] ?? '');
    $enrollment_date = trim($_POST['enrollment_date'] ?? '');

    $errors = [];

    //validating inputs using string functions and conditionals (considering security)
    if (empty($full_names_surname) || empty($student_id) || empty($email) || empty($dob) || empty($course_of_study) || empty($enrollment_date)) {
        $errors[] = "All fields are required.";
    }
    
    //student ID format validation (e.g., starts with 'S', followed by 8 digits)
    if (!preg_match('/^S\d{8}$/', $student_id)) {
        $errors[] = "Student ID must start with 'S' and be followed by exactly 8 digits (e.g., S20240001).";
    }

    //basic email format checking
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    //date format validation
    if (!strtotime($dob) || !strtotime($enrollment_date)) {
        $errors[] = "Invalid date format.";
    }

    //checking if a student ID or email already exists
    if (empty($errors)) {
        $check_sql = "SELECT student_id FROM students WHERE student_id = ? OR email = ?";
        if ($stmt = $mysqli->prepare($check_sql)) {
            $stmt->bind_param("ss", $student_id, $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errors[] = "A student with this ID or Email already exists.";
            }
            $stmt->close();
        } else {
            error_log("Error preparing ID/Email check: " . $mysqli->error);
            $errors[] = "System error during validation check.";
        }
    }

    //store submitted data in MySQL using prepared statements (CRUD)
    if (empty($errors)) {
        //SQL injection prevention by using prepared statements
        $sql = "INSERT INTO students (full_names_surname, student_id, email, dob, course_of_study, enrollment_date) VALUES (?, ?, ?, ?, ?, ?)";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("ssssss", $full_names_surname, $student_id, $email, $dob, $course_of_study, $enrollment_date);
            
            if ($stmt->execute()) {
                //upon success - redirects back to index.php with a success message
                $stmt->close();
                closeDB();
                header("Location: index.php?status=success");
                exit();
            } else {
                //PHP error handling
                error_log("Error executing INSERT: " . $stmt->error);
                $errors[] = "Database insertion failed. Please check server logs.";
            }
            $stmt->close();
        } else {
            error_log("Error preparing INSERT statement: " . $mysqli->error);
            $errors[] = "System error preparing database query.";
        }
    }
    
    //shows the error messages with proper handling
    if (!empty($errors)) {
        // Concatenate errors into a single message for URL redirect
        $error_msg = urlencode(implode(" ", $errors));
        closeDB();
        header("Location: index.php?status=error&msg={$error_msg}");
        exit();
    }
} else {
    //if it is accessed directly without POST data
    closeDB();
    header("Location: index.php");
    exit();
}
?>