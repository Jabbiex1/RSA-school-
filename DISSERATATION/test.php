<?php
// Database configuration
$host = 'sql106.infinityfree.com';
$db   = 'if0_39814001_royal_academy';
$user = 'if0_39814001';
$pass = 'zcrfMswTaMBZ';
$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $pdo->exec("SET NAMES 'utf8'");
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$message = "";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // --- STUDENTS ---
    if ($action === 'add_student') {
        $id = $_POST['student_id'];
        $name = $_POST['student_name'];
        $class = $_POST['student_class'];
        $dob = $_POST['student_dob'];
        $parent_email = $_POST['parent_email'];
        $email = $_POST['email'] ?? null;
        $phone = $_POST['phone'] ?? null;
        $previous_school = $_POST['previous_school'] ?? null;

        $stmt = $pdo->prepare("INSERT INTO students (id, name, class, dob, parent_email, source, email, phone, previous_school) VALUES (?, ?, ?, ?, ?, 'manual', ?, ?, ?)");
        try {
            $stmt->execute([$id, $name, $class, $dob, $parent_email, $email, $phone, $previous_school]);
            $message = "Student added successfully!";
        } catch (PDOException $e) {
            $message = "Error adding student: " . $e->getMessage();
        }
    }

    if ($action === 'edit_student' && isset($_POST['student_id'])) {
        $id = $_POST['student_id'];
        $name = $_POST['student_name'];
        $class = $_POST['student_class'];
        $dob = $_POST['student_dob'];

        $stmt = $pdo->prepare("UPDATE students SET name=?, class=?, dob=? WHERE id=?");
        try {
            $stmt->execute([$name, $class, $dob, $id]);
            $message = "Student updated successfully!";
        } catch (PDOException $e) {
            $message = "Error updating student: " . $e->getMessage();
        }
    }

    if ($action === 'delete_student' && isset($_POST['student_id'])) {
        $id = $_POST['student_id'];
        $stmt = $pdo->prepare("DELETE FROM students WHERE id=?");
        try {
            $stmt->execute([$id]);
            $message = "Student deleted successfully!";
        } catch (PDOException $e) {
            $message = "Error deleting student: " . $e->getMessage();
        }
    }

    // --- TEACHERS ---
    if ($action === 'add_teacher') {
        $name = $_POST['teacher_name'] ?? null;
        $email = $_POST['teacher_email'] ?? null;
        $class = $_POST['teacher_class'] ?? null;
        $subject = $_POST['teacher_subject'] ?? null;
        $password = $_POST['teacher_password'] ?? null;

        if ($name && $email && $class && $subject && $password) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO lecturers (name, email, class, subject, password) VALUES (?, ?, ?, ?, ?)");
            try {
                $stmt->execute([$name, $email, $class, $subject, $hash]);
                $message = "Teacher added successfully!";
            } catch (PDOException $e) {
                $message = "Error adding teacher: " . $e->getMessage();
            }
        } else {
            $message = "All teacher fields are required!";
        }
    }

    if ($action === 'edit_teacher' && isset($_POST['teacher_email'])) {
        $email = $_POST['teacher_email'];
        $name = $_POST['teacher_name'];
        $class = $_POST['teacher_class'];
        $subject = $_POST['teacher_subject'];

        $stmt = $pdo->prepare("UPDATE lecturers SET name=?, class=?, subject=? WHERE email=?");
        try {
            $stmt->execute([$name, $class, $subject, $email]);
            $message = "Teacher updated successfully!";
        } catch (PDOException $e) {
            $message = "Error updating teacher: " . $e->getMessage();
        }
    }

    if ($action === 'delete_teacher' && isset($_POST['teacher_email'])) {
        $email = $_POST['teacher_email'];
        $stmt = $pdo->prepare("DELETE FROM lecturers WHERE email=?");
        try {
            $stmt->execute([$email]);
            $message = "Teacher deleted successfully!";
        } catch (PDOException $e) {
            $message = "Error deleting teacher: " . $e->getMessage();
        }
    }

    // --- PARENTS ---
    if ($action === 'add_parent') {
        $student_id = $_POST['parent_student_id'];
        $phone = $_POST['parent_phone'];
        $stmt = $pdo->prepare("INSERT INTO parents (student_id, parent_phone) VALUES (?, ?)");
        try {
            $stmt->execute([$student_id, $phone]);
            $message = "Parent added successfully!";
        } catch (PDOException $e) {
            $message = "Error adding parent: " . $e->getMessage();
        }
    }

    if ($action === 'edit_parent') {
        $student_id = $_POST['parent_student_id'];
        $phone = $_POST['parent_phone'];
        $stmt = $pdo->prepare("UPDATE parents SET student_id=? WHERE parent_phone=?");
        try {
            $stmt->execute([$student_id, $phone]);
            $message = "Parent updated successfully!";
        } catch (PDOException $e) {
            $message = "Error updating parent: " . $e->getMessage();
        }
    }

    if ($action === 'delete_parent') {
        $phone = $_POST['parent_phone'];
        $stmt = $pdo->prepare("DELETE FROM parents WHERE parent_phone=?");
        try {
            $stmt->execute([$phone]);
            $message = "Parent deleted successfully!";
        } catch (PDOException $e) {
            $message = "Error deleting parent: " . $e->getMessage();
        }
    }

    // --- PENDING STUDENTS ---
    if ($action === 'approve_pending' && isset($_POST['pending_id'])) {
        $id = $_POST['pending_id'];
        $stmt = $pdo->prepare("SELECT * FROM pending_students WHERE id=?");
        $stmt->execute([$id]);
        $pending = $stmt->fetch();
        if ($pending) {
            $insert = $pdo->prepare("INSERT INTO students (id, name, class, dob, source) VALUES (?, ?, ?, ?, 'pending')");
            $insert->execute([$pending['id'], $pending['name'], $pending['class'], $pending['dob']]);
            $del = $pdo->prepare("DELETE FROM pending_students WHERE id=?");
            $del->execute([$id]);
            $message = "Pending student approved successfully!";
        }
    }

    if ($action === 'decline_pending' && isset($_POST['pending_id'])) {
        $id = $_POST['pending_id'];
        $stmt = $pdo->prepare("DELETE FROM pending_students WHERE id=?");
        $stmt->execute([$id]);
        $message = "Pending student declined successfully!";
    }
}

// Fetch data for dashboard
$totalStudents = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$totalTeachers = $pdo->query("SELECT COUNT(*) FROM lecturers")->fetchColumn();
$totalParents = $pdo->query("SELECT COUNT(*) FROM parents")->fetchColumn();
$students = $pdo->query("SELECT * FROM students ORDER BY name ASC")->fetchAll();
$teachers = $pdo->query("SELECT * FROM lecturers ORDER BY name ASC")->fetchAll();
$parents = $pdo->query("SELECT p.parent_phone, s.name AS student_name, s.id AS student_id FROM parents p LEFT JOIN students s ON p.student_id=s.id ORDER BY student_name ASC")->fetchAll();
$pendingStudents = $pdo->query("SELECT * FROM pending_students ORDER BY name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RSA Admin Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<style>
/* Modern responsive design styles here (same as your previous CSS) */
/* Keep all the CSS from your last modern redesign for full responsiveness and dark mode */
</style>
</head>
<body>
<div class="header">
  Royal Science Academy - Admin Dashboard
  <form method="POST" action="portal-login.html" style="margin:0;">
    <button type="submit" class="logout-btn">Logout</button>
  </form>
</div>
<button id="theme-toggle">Dark Mode</button>

<div class="container">
<aside class="sidebar">
<h2>RSA Admin</h2>
<nav>
<a href="#dashboard" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
<a href="#add-student"><i class="fas fa-user-graduate"></i> Add Student</a>
<a href="#add-teacher"><i class="fas fa-chalkboard-teacher"></i> Add Teacher</a>
<a href="#add-parent"><i class="fas fa-users"></i> Add Parent</a>
</nav>
</aside>
<main class="content">

<?php if($message): ?>
<div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<!-- Stats Cards -->
<section id="dashboard" class="stats-cards">
<div class="card">Total Students: <?= $totalStudents ?></div>
<div class="card">Total Teachers: <?= $totalTeachers ?></div>
<div class="card">Total Parents: <?= $totalParents ?></div>
<div class="card">Pending Students: <?= count($pendingStudents) ?></div>
</section>

<!-- Pending Students -->
<section>
<h2>Pending Students</h2>
<table>
<tr><th>ID</th><th>Name</th><th>Class</th><th>DOB</th><th>Actions</th></tr>
<?php foreach($pendingStudents as $p): ?>
<tr>
<td><?= $p['id'] ?></td>
<td><?= $p['name'] ?></td>
<td><?= $p['class'] ?></td>
<td><?= $p['dob'] ?></td>
<td>
<form method="POST" style="display:inline;">
<input type="hidden" name="pending_id" value="<?= $p['id'] ?>">
<button name="action" value="approve_pending">Approve</button>
<button name="action" value="decline_pending">Decline</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</table>
</section>

<!-- Add Student -->
<section id="add-student">
<h2>Add Student</h2>
<form method="POST">
<input type="hidden" name="action" value="add_student">
<input type="text" name="student_id" placeholder="Student ID (90200XXXX)" required>
<input type="text" name="student_name" placeholder="Full Name" required>
<input type="text" name="student_class" placeholder="Class" required>
<input type="date" name="student_dob" placeholder="Date of Birth" required>
<input type="email" name="parent_email" placeholder="Parent Email" required>
<input type="email" name="email" placeholder="Student Email">
<input type="text" name="phone" placeholder="Phone">
<input type="text" name="previous_school" placeholder="Previous School">
<button type="submit">Add Student</button>
</form>
</section>

<!-- Add Teacher -->
<section id="add-teacher">
<h2>Add Teacher</h2>
<form method="POST">
<input type="hidden" name="action" value="add_teacher">
<input type="text" name="teacher_name" placeholder="Full Name" required>
<input type="email" name="teacher_email" placeholder="Email" required>
<input type="text" name="teacher_class" placeholder="Class" required>
<input type="text" name="teacher_subject" placeholder="Subject" required>
<input type="password" name="teacher_password" placeholder="Password" required>
<button type="submit">Add Teacher</button>
</form>
</section>

<!-- Add Parent -->
<section id="add-parent">
<h2>Add Parent</h2>
<form method="POST">
<input type="hidden" name="action" value="add_parent">
<input type="text" name="parent_student_id" placeholder="Student ID" required>
<input type="text" name="parent_phone" placeholder="Parent Phone" required>
<button type="submit">Add Parent</button>
</form>
</section>

<!-- Existing Data Tables -->
<section>
<h2>All Students</h2>
<table>
<tr><th>ID</th><th>Name</th><th>Class</th><th>DOB</th><th>Actions</th></tr>
<?php foreach($students as $s): ?>
<tr>
<td><?= $s['id'] ?></td>
<td><?= $s['name'] ?></td>
<td><?= $s['class'] ?></td>
<td><?= $s['dob'] ?></td>
<td>
<form method="POST" style="display:inline;">
<input type="hidden" name="student_id" value="<?= $s['id'] ?>">
<button name="action" value="delete_student">Delete</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</table>

<h2>All Teachers</h2>
<table>
<tr><th>Name</th><th>Email</th><th>Class</th><th>Subject</th><th>Actions</th></tr>
<?php foreach($teachers as $t): ?>
<tr>
<td><?= $t['name'] ?></td>
<td><?= $t['email'] ?></td>
<td><?= $t['class'] ?></td>
<td><?= $t['subject'] ?></td>
<td>
<form method="POST" style="display:inline;">
<input type="hidden" name="teacher_email" value="<?= $t['email'] ?>">
<button name="action" value="delete_teacher">Delete</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</table>

<h2>All Parents</h2>
<table>
<tr><th>Student Name</th><th>Student ID</th><th>Parent Phone</th><th>Actions</th></tr>
<?php foreach($parents as $p): ?>
<tr>
<td><?= $p['student_name'] ?></td>
<td><?= $p['student_id'] ?></td>
<td><?= $p['parent_phone'] ?></td>
<td>
<form method="POST" style="display:inline;">
<input type="hidden" name="parent_phone" value="<?= $p['parent_phone'] ?>">
<button name="action" value="delete_parent">Delete</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</table>

</main>
</div>

<script>
// Dark mode toggle
const toggle = document.getElementById('theme-toggle');
toggle.addEventListener('click', () => {
document.body.classList.toggle('dark-mode');
});
</script>
</body>
</html>
