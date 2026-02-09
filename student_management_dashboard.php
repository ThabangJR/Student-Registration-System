<?php
require_once 'database.php';
$mysqli = connectDB();
$search_term = $_GET['search'] ?? '';
$sort_by = $_GET['sort'] ?? 'student_id';
$sort_order = $_GET['order'] ?? 'ASC';

//building the SQL query (Read Operation)
$sql = "SELECT student_id, full_names_surname, email, course_of_study FROM students";
$params = [];
$types = '';

if (!empty($search_term)) {
    $sql .= " WHERE full_names_surname LIKE ? OR student_id LIKE ? OR email LIKE ?";
    $like_term = "%" . $search_term . "%";
    $params = [$like_term, $like_term, $like_term];
    $types = 'sss';
}

$valid_sorts = ['student_id', 'full_names_surname', 'course_of_study'];
$sort_order = strtoupper($sort_order) == 'DESC' ? 'DESC' : 'ASC';
if (in_array($sort_by, $valid_sorts)) {
    $sql .= " ORDER BY " . $sort_by . " " . $sort_order;
} else {
    $sql .= " ORDER BY student_id ASC";
}

$students = [];
if ($stmt = $mysqli->prepare($sql)) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
    } else {
        error_log("Error executing dashboard query: " . $stmt->error);
    }
    $stmt->close();
} else {
    error_log("Error preparing dashboard statement: " . $mysqli->error);
}
closeDB();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Student Management Dashboard</h1>
            <p>Admin View | Comprehensive Student List</p>
        </header>

        <div class="nav-bar">
            <a href="index.php" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Register New Student</a>
        </div>

        <?php
        if (isset($_GET['msg'])) {
            $msg = htmlspecialchars($_GET['msg']);
            $class = isset($_GET['status']) && $_GET['status'] == 'success' ? 'success-message' : 'error-message';
            $icon = $class == 'success-message' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
            echo "<div class='message-box $class'><i class='$icon'></i> $msg</div>";
        }
        ?>

        <form action="student_management_dashboard.php" method="GET" style="margin-bottom: 25px;">
            <div style="display: flex; gap: 15px; align-items: flex-end;">
                <div class="form-group" style="flex-grow: 1; margin-bottom: 0;">
                    <label for="search">Search</label>
                    <input type="text" id="search" name="search" placeholder="Name, ID, or Email" value="<?= htmlspecialchars($search_term) ?>">
                </div>
                
                <div class="form-group" style="width: 150px; margin-bottom: 0;">
                    <label for="sort">Sort By</label>
                    <select id="sort" name="sort">
                        <option value="student_id" <?= $sort_by == 'student_id' ? 'selected' : '' ?>>ID</option>
                        <option value="full_names_surname" <?= $sort_by == 'full_names_surname' ? 'selected' : '' ?>>Name</option>
                        <option value="course_of_study" <?= $sort_by == 'course_of_study' ? 'selected' : '' ?>>Course</option>
                    </select>
                </div>
                
                <div class="form-group" style="width: 120px; margin-bottom: 0;">
                    <label for="order">Order</label>
                    <select id="order" name="order">
                        <option value="ASC" <?= $sort_order == 'ASC' ? 'selected' : '' ?>>ASC</option>
                        <option value="DESC" <?= $sort_order == 'DESC' ? 'selected' : '' ?>>DESC</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-info" style="margin: 0;"><i class="fas fa-filter"></i> Apply</button>
            </div>
        </form>

        <?php if (!empty($students)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Full Names and Surname</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= htmlspecialchars($student['student_id']) ?></td>
                            <td><?= htmlspecialchars($student['full_names_surname']) ?></td>
                            <td><?= htmlspecialchars($student['email']) ?></td>
                            <td><?= htmlspecialchars($student['course_of_study']) ?></td>
                            <td class="action-links">
                                <a href="student_profile.php?id=<?= urlencode($student['student_id']) ?>" title="View Profile"><i class="fas fa-eye"></i> View</a>
                                <a href="update_student_info.php?id=<?= urlencode($student['student_id']) ?>" title="Update Record"><i class="fas fa-edit"></i> Update</a>
                                <a href="javascript:void(0)" 
                                   onclick="confirmDelete('<?= urlencode($student['student_id']) ?>', '<?= htmlspecialchars($student['full_names_surname']) ?>')" 
                                   title="Delete Record" style="color: var(--danger-color);"><i class="fas fa-trash-alt"></i> Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; padding: 20px; border: 1px solid #ccc; border-radius: 4px;">No student records found. Start by registering a new student.</p>
        <?php endif; ?>
    </div>

    <script>
    function confirmDelete(studentId, studentName) {
        if (confirm(`Are you sure you want to delete the record for ${studentName} (${studentId})? This action cannot be undone.`)) {
            window.location.href = 'delete_function.php?id=' + studentId;
        }
    }
    </script>
</body>
</html>