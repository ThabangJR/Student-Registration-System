<?php
//database connection constants
define('DB_SERVER', '');
define('DB_USERNAME', 'oot'); 
define('DB_PASSWORD', ''); 
define('DB_NAME', '');

//defining academic status constants (for use in student_profile.php)
define('STATUS_ACTIVE', 'Active 🟢');
define('STATUS_PENDING', 'Pending Enrollment 🟡');
define('STATUS_GRADUATED', 'Graduated 🎓');

//global connection variable
$mysqli = null;

//establishing database connection
function connectDB() {
    global $mysqli;
    if ($mysqli) {
        return $mysqli; // Already connected
    }
    
    //PHP error andling to manage exceptions 
    try {
        $mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
        
        if ($mysqli->connect_error) {
            //logs the error but show a generic message to the user
            error_log("Failed to connect to MySQL: " . $mysqli->connect_error);
            die("SYSTEM ERROR: Could not connect to the database. Please try again later.");
        }
        
        return $mysqli;
    } catch (Exception $e) {
        //catch any other general exceptions
        error_log("A general database error occurred: " . $e->getMessage());
        die("An unexpected system error occurred.");
    }
}

//helper function to safely close the connection
function closeDB() {
    global $mysqli;
    if ($mysqli) {
        $mysqli->close();
        $mysqli = null;
    }
}

//function to retrieve a single student record
function getStudentById($id) {
    $mysqli = connectDB();
    //use prepared statements for security
    $sql = "SELECT * FROM students WHERE student_id = ?";
    
    if ($stmt = $mysqli->prepare($sql)) {

        $stmt->bind_param("s", $id);
        
      
        if ($stmt->execute()) {
            $result = $stmt->get_result();
           
            $student = $result->fetch_assoc();
            $stmt->close();
            return $student; //returns the student data
        } else {
            error_log("Error executing getStudentById: " . $stmt->error);
        }
        $stmt->close();
    } else {
        error_log("Error preparing getStudentById statement: " . $mysqli->error);
    }
    return null;
}
?>