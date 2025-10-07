<?php
// Database configuration
$host = 'localhost';
$db = 'royal_academy';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle form submissions
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($_POST['action'] === 'add_student') {
    $id = $_POST['student_id'];
    $name = $_POST['student_name'];
    $class = $_POST['student_class'];
    $dob = $_POST['student_dob'];
    $parent_email = $_POST['parent_email'];
    $email = $_POST['email'] ?? null;
    $phone = $_POST['phone'] ?? null;
    $previous_school = $_POST['previous_school'] ?? null;

    $stmt = $pdo->prepare("
        INSERT INTO students
        (id, name, class, dob, parent_email, source, email, phone, previous_school)
        VALUES (?, ?, ?, ?, ?, 'manual', ?, ?, ?)
    ");
    $stmt->execute([$id, $name, $class, $dob, $parent_email, $email, $phone, $previous_school]);
}

        // Pending Admissions handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        // Approve Pending Student
        if ($action === 'approve_pending' && isset($_POST['pending_id'])) {
            $pending_id = $_POST['pending_id'];
            
            // Fetch pending student data
            $stmt = $pdo->prepare("SELECT * FROM pending_students WHERE id = ?");
            $stmt->execute([$pending_id]);
            $pending = $stmt->fetch();

            if ($pending) {
                // Insert into students table
                $insert = $pdo->prepare("INSERT INTO students (id, name, class, dob, source) VALUES (?, ?, ?, ?, 'pending')");
                $insert->execute([$pending['id'], $pending['name'], $pending['class'], $pending['dob']]);

                // Delete from pending_students
                $del = $pdo->prepare("DELETE FROM pending_students WHERE id = ?");
                $del->execute([$pending_id]);

                $message = "Pending student {$pending['name']} approved successfully!";
            }
        }

        // Decline Pending Student
        if ($action === 'decline_pending' && isset($_POST['pending_id'])) {
            $pending_id = $_POST['pending_id'];
            
            $stmt = $pdo->prepare("DELETE FROM pending_students WHERE id = ?");
            $stmt->execute([$pending_id]);
            
            $message = "Pending student declined and removed successfully!";
        }
    }
}

// Fetch pending students
$pendingStudents = $pdo->query("SELECT * FROM pending_students ORDER BY name ASC")->fetchAll();


        // Edit Student
        if ($action === 'edit_student' && isset($_POST['student_id'])) {
            $id = $_POST['student_id'];
            $name = $_POST['student_name'];
            $class = $_POST['student_class'];
            $parent_email = $_POST['parent_email'];

            $dob = $_POST['student_dob'];

            $stmt = $pdo->prepare("UPDATE students SET name = ?, class = ?, dob = ? WHERE id = ?");
            try {
                $stmt->execute([$name, $class, $dob, $id]);
                $message = "Student ID $id updated successfully!";
            } catch (PDOException $e) {
                $message = "Error updating student: " . $e->getMessage();
            }
        }

        // Delete Student
        if ($action === 'delete_student' && isset($_POST['student_id'])) {
            $delId = $_POST['student_id'];
            $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
            try {
                $stmt->execute([$delId]);
                $message = "Student ID $delId deleted successfully!";
            } catch (PDOException $e) {
                $message = "Error deleting student: " . $e->getMessage();
            }
        }

       $stmt = $pdo->prepare("INSERT INTO lecturers (name, email, class, password) VALUES (?, ?, ?, ?)");
try {
  
    $message = "Student added successfully!";
} catch (PDOException $e) {
    $message = "Error adding student: " . $e->getMessage();
}

// Add Teacher (with class)
if ($action === 'add_teacher') {
    // Collect values from the submitted form
    $name = isset($_POST['teacher_name']) ? trim($_POST['teacher_name']) : null;
    $email = isset($_POST['teacher_email']) ? trim($_POST['teacher_email']) : null;
    $class = isset($_POST['teacher_class']) ? trim($_POST['teacher_class']) : null;
    $password = isset($_POST['teacher_password']) ? password_hash($_POST['teacher_password'], PASSWORD_BCRYPT) : null;

    // Validate required fields
    if ($name && $email && $class && $password) {
        $stmt = $pdo->prepare("INSERT INTO lecturers (name, email, class, password) VALUES (?, ?, ?, ?)");
        try {
            $stmt->execute([$name, $email, $class, $password]);
            $message = "Teacher added successfully!";
        } catch (PDOException $e) {
            $message = "Error adding teacher: " . $e->getMessage();
        }
    } else {
        $message = "All fields are required!";
    }
}



        // Edit Teacher (with class)
        if ($action === 'edit_teacher' && isset($_POST['teacher_email'])) {
            $email = $_POST['teacher_email'];
            $name = $_POST['teacher_name'];
            $class = $_POST['teacher_class'];

            $stmt = $pdo->prepare("UPDATE lecturers SET name = ?, subject = ?, class = ? WHERE email = ?");
            try {
                $stmt->execute([$name, $subject, $class, $email]);
                $message = "Teacher $email updated successfully!";
            } catch (PDOException $e) {
                $message = "Error updating teacher: " . $e->getMessage();
            }
        }

        // Delete Teacher
        if ($action === 'delete_teacher' && isset($_POST['teacher_email'])) {
            $email = $_POST['teacher_email'];
            $stmt = $pdo->prepare("DELETE FROM lecturers WHERE email = ?");
            try {
                $stmt->execute([$email]);
                $message = "Teacher $email deleted successfully!";
            } catch (PDOException $e) {
                $message = "Error deleting teacher: " . $e->getMessage();
            }
        }

 // Add Parent
if ($action === 'add_parent') {
    $student_id = $_POST['parent_student_id'];
    $parent_phone = $_POST['parent_phone'];

    $stmt = $pdo->prepare("INSERT INTO parents (student_id, parent_phone) VALUES (?, ?)");
    try {
        $stmt->execute([$student_id, $parent_phone]);
        $message = "Parent added successfully!";
    } catch (PDOException $e) {
        $message = "Error adding parent: " . $e->getMessage();
    }
}

// Edit Parent
if ($action === 'edit_parent' && isset($_POST['parent_phone'])) {
    $phone = $_POST['parent_phone'];
    $student_id = $_POST['parent_student_id'];

    $stmt = $pdo->prepare("UPDATE parents SET student_id = ? WHERE parent_phone = ?");
    try {
        $stmt->execute([$student_id, $phone]);
        $message = "Parent with phone $phone updated successfully!";
    } catch (PDOException $e) {
        $message = "Error updating parent: " . $e->getMessage();
    }
}

// Delete Parent
if ($action === 'delete_parent' && isset($_POST['parent_phone'])) {
    $phone = $_POST['parent_phone'];
    $stmt = $pdo->prepare("DELETE FROM parents WHERE parent_phone = ?");
    try {
        $stmt->execute([$phone]);
        $message = "Parent with phone $phone deleted successfully!";
    } catch (PDOException $e) {
        $message = "Error deleting parent: " . $e->getMessage();
    }
}
    }
  }
// Fetch stats
$totalStudents = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$totalTeachers = $pdo->query("SELECT COUNT(*) FROM lecturers")->fetchColumn();
$totalParents = $pdo->query("SELECT COUNT(*) FROM parents")->fetchColumn();
try {
    $pendingStudentsStmt = $pdo->query("SELECT * FROM pending_students ORDER BY name ASC");
    $pendingStudents = $pendingStudentsStmt ? $pendingStudentsStmt->fetchAll() : [];
} catch (PDOException $e) {
    $pendingStudents = [];
}

// Fetch lists for display (include class in lecturers)
$students = $pdo->query("SELECT id, name, class, dob FROM students ORDER BY name ASC")->fetchAll();
$teachers = $pdo->query("SELECT name, subject, email, class FROM lecturers ORDER BY name ASC")->fetchAll();
$parents = $pdo->query("SELECT p.parent_phone, s.name AS student_name, s.id AS student_id FROM parents p LEFT JOIN students s ON p.student_id = s.id ORDER BY student_name ASC")->fetchAll();

?>
<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RSA Admin Dashboard</title>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<!-- Updated CSS -->
<style>
/* Reset & basics */
* {
  box-sizing: border-box;
}
body {
  margin: 0; 
  font-family: 'Inter', sans-serif;
  background: #f0f3f8;
  color: #0b2545;
}
a {
  text-decoration: none;
  color: inherit;
}

/* Header */
.header {
  background: linear-gradient(90deg, #002060, #004aad);
  color: white;
  padding: 15px 30px;
  font-size: 24px;
  font-weight: 700;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 6px 12px rgba(0,0,0,0.1);
  border-bottom-left-radius: 12px;
  border-bottom-right-radius: 12px;
}
.header button.logout-btn {
  background: #cc0000;
  border: none;
  color: white;
  padding: 8px 18px;
  font-weight: 700;
  border-radius: 12px;
  cursor: pointer;
  font-size: 16px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 10px rgba(204,0,0,0.7);
}
.header button.logout-btn:hover {
  background: #990000;
}

/* Layout */
.container {
  display: flex;
  min-height: calc(100vh - 60px);
}

/* Sidebar */
.sidebar {
  width: 260px;
  background: linear-gradient(180deg, #003080, #004aad);
  color: white;
  display: flex;
  flex-direction: column;
  padding: 30px 20px;
  border-radius: 12px 0 0 12px;
  box-shadow: 4px 0 20px rgba(0,0,0,0.1);
}
.sidebar h2 {
  font-weight: 900;
  font-size: 28px;
  letter-spacing: 0.1em;
  margin-bottom: 40px;
  text-align: center;
  color: #cce0ff;
}
.sidebar nav a {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 12px 18px;
  margin-bottom: 15px;
  border-radius: 12px;
  font-weight: 600;
  background: #003080;
  color: #cce0ff;
  transition: all 0.3s ease;
  box-shadow: inset 0 0 0 0 #fff;
}
.sidebar nav a:hover,
.sidebar nav a.active {
  background: #cce0ff;
  color: #004aad;
  font-weight: 700;
  box-shadow: 0 6px 12px rgba(0,74,173,0.4);
}
.sidebar nav a i {
  min-width: 20px;
  font-size: 18px;
}

/* Main Content */
.content {
  flex: 1;
  padding: 30px 40px;
  background: #f8faff;
  border-radius: 20px;
  box-shadow: 0 12px 25px rgba(0,74,173,0.1);
  overflow-y: auto;
}

/* Message */
.message {
  background: #dbe9ff;
  color: #003080;
  padding: 15px 20px;
  margin-bottom: 25px;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  font-weight: 600;
  border: 1.5px solid #004aad;
}

/* Stats Cards */
.stats-cards {
  display: flex;
  gap: 25px;
  margin-bottom: 30px;
}
.card {
  flex: 1;
  background: #e0ebff;
  border-radius: 20px;
  padding: 25px 30px;
  box-shadow: 8px 8px 15px rgba(0,74,173,0.15), -8px -8px 15px rgba(255,255,255,0.7);
  text-align: center;
  font-weight: 700;
  font-size: 20px;
  color: #003080;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.card:hover {
  transform: translateY(-5px);
  box-shadow: 12px 12px 25px rgba(0,74,173,0.25), -12px -12px 25px rgba(255,255,255,0.6);
}
.card .count {
  font-size: 42px;
  margin-top: 10px;
  color: #004aad;
}

/* Forms */
section.form-section {
  background: #f0f3f8;
  border-radius: 12px;
  padding: 30px;
  margin-bottom: 40px;
  box-shadow: 0 6px 18px rgba(0,74,173,0.1);
}
section.form-section h2 {
  margin-bottom: 20px;
  font-weight: 700;
  color: #003080;
}
form input, form select {
  width: 100%;
  padding: 14px 18px;
  margin: 10px 0;
  border-radius: 12px;
  border: 1.8px solid #66a3ff;
  font-size: 16px;
  font-weight: 500;
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
  background: #f8faff;
  color: #003080;
}
form input:focus, form select:focus {
  outline: none;
  border-color: #004aad;
  box-shadow: 0 0 6px 2px #66a3ff;
}
form button {
  background: linear-gradient(90deg, #003080, #004aad);
  color: white;
  font-weight: 700;
  border: none;
  cursor: pointer;
  margin-top: 10px;
  padding: 14px 24px;
  border-radius: 50px;
  font-size: 17px;
  transition: all 0.3s ease;
  box-shadow: 0 8px 15px rgba(0,74,173,0.6);
}
form button:hover {
  background: linear-gradient(90deg, #004aad, #002060);
  box-shadow: 0 12px 20px rgba(0,74,173,0.8);
}

/* Collapsible Sections */
.toggle-section {
  margin-bottom: 40px;
}
.toggle-button {
  display: inline-flex;
  align-items: center;
  background: #004aad;
  color: white;
  border: none;
  padding: 12px 24px;
  font-size: 16px;
  border-radius: 12px;
  cursor: pointer;
  font-weight: 700;
  transition: background 0.3s ease, box-shadow 0.3s ease;
  gap: 8px;
  box-shadow: 0 6px 15px rgba(0,74,173,0.6);
}
.toggle-button:hover {
  background: #003080;
  box-shadow: 0 8px 22px rgba(0,74,173,0.8);
}
.toggle-button i {
  transition: transform 0.3s ease;
}
.toggle-button.active i {
  transform: rotate(90deg);
}
.collapsible-content {
  margin-top: 15px;
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.4s ease;
  border-radius: 12px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}
.collapsible-content.active {
  max-height: 600px;
  overflow-y: auto;
}

/* Table */
table {
  width: 100%;
  border-collapse: collapse;
  background: #ffffff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 8px 20px rgba(0,0,0,0.05);
}
thead {
  background: #004aad;
  color: white;
  font-weight: 700;
}
th, td {
  padding: 14px 20px;
  text-align: left;
  border-bottom: 1px solid #d6e2ff;
}
tbody tr:hover {
  background: #e6f0ff;
}
td button {
  padding: 8px 14px;
  margin-right: 6px;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 14px;
}
td button.edit-btn {
  background-color: #004aad;
  color: white;
  box-shadow: 0 4px 10px rgba(0,74,173,0.6);
}
td button.edit-btn:hover {
  background-color: #003080;
}
td button.delete-btn {
  background-color: #cc0000;
  color: white;
  box-shadow: 0 4px 10px rgba(204,0,0,0.6);
}
td button.delete-btn:hover {
  background-color: #990000;
}

/* Responsive */
@media (max-width: 900px) {
  .container {
    flex-direction: column;
  }
  .sidebar {
    width: 100%;
    border-radius: 12px 12px 0 0;
    padding: 20px 15px;
    display: flex;
    justify-content: center;
  }
  .sidebar nav a {
    margin: 0 10px;
    padding: 10px 15px;
  }
  .content {
    padding: 20px 15px;
  }
  .stats-cards {
    flex-direction: column;
  }
}
:root {
  /* Bright Mode */
  --bg-color: #f0f3f8;
  --sidebar-bg: linear-gradient(180deg, #003080, #004aad);
  --header-bg: linear-gradient(90deg, #002060, #004aad);
  --text-color: #0b2545;
  --card-bg: #e0ebff;
  --form-bg: #f0f3f8;
  --table-bg: #ffffff;
  --button-bg: linear-gradient(90deg, #003080, #004aad);
  --button-hover: linear-gradient(90deg, #004aad, #002060);
}

body.dark-mode {
  --bg-color: #121821;
  --sidebar-bg: linear-gradient(180deg, #1f2a3a, #1a2230);
  --header-bg: linear-gradient(90deg, #0b1c38, #1a2230);
  --text-color: #e0e0e0;
  --card-bg: #1f2a3a;
  --form-bg: #1a2230;
  --table-bg: #1f2a3a;
  --table-text: #e0e0e0;
  --table-hover: #2a3a50;
  --button-bg: linear-gradient(90deg, #0b1c38, #1a2230);
  --button-hover: linear-gradient(90deg, #1a2230, #0b1c38);
  --input-bg: #1a2230;
  --input-border: #3a4a65;
}

body.dark-mode {
  background: var(--bg-color);
  color: var(--text-color);
}

body.dark-mode .header {
  background: var(--header-bg);
  box-shadow: 0 6px 12px rgba(0,0,0,0.5);
}

body.dark-mode .sidebar {
  background: var(--sidebar-bg);
  box-shadow: 4px 0 20px rgba(0,0,0,0.5);
}

body.dark-mode .card {
  background: var(--card-bg);
  box-shadow: 8px 8px 15px rgba(0,0,0,0.4), -8px -8px 15px rgba(255,255,255,0.05);
  color: var(--text-color);
}

body.dark-mode section.form-section {
  background: var(--form-bg);
  color: var(--text-color);
  box-shadow: 0 6px 18px rgba(0,0,0,0.4);
}

body.dark-mode table {
  background: var(--table-bg);
  color: var(--table-text);
  box-shadow: 0 8px 20px rgba(0,0,0,0.4);
}

body.dark-mode tbody tr:hover {
  background: var(--table-hover);
}

body.dark-mode form input, 
body.dark-mode form select {
  background: var(--input-bg);
  border: 1.5px solid var(--input-border);
  color: var(--text-color);
}

body.dark-mode form input:focus, 
body.dark-mode form select:focus {
  border-color: #66a3ff;
  box-shadow: 0 0 6px 2px #66a3ff;
}

body.dark-mode form button,
body.dark-mode .toggle-button {
  background: var(--button-bg);
  color: #e0e0e0;
  box-shadow: 0 8px 15px rgba(0,74,173,0.6);
}

body.dark-mode form button:hover,
body.dark-mode .toggle-button:hover {
  background: var(--button-hover);
  box-shadow: 0 12px 20px rgba(0,74,173,0.8);
}

body.dark-mode .sidebar nav a {
  background: #1a2230;
  color: #cce0ff;
}
body.dark-mode .sidebar nav a:hover,
body.dark-mode .sidebar nav a.active {
  background: #66a3ff;
  color: #1a2230;
  box-shadow: inset 0 0 10px 3px #004aad;
}


</style>

</head>
<body>

<div class="header">
  Royal Science Academy - Admin Dashboard
  <form method="POST" action="portal-login.html" style="margin:0;">
    <button type="submit" class="logout-btn">Logout</button>
  </form>
</div>
<button id="theme-toggle" style="
  margin-left: 15px;
  padding: 8px 16px;
  border-radius: 12px;
  border: none;
  cursor: pointer;
  font-weight: 700;
  background: #004aad;
  color: white;
  transition: 0.3s;
">
  Dark Mode
</button>


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

    <section id="dashboard" class="stats-cards">
      <div class="card">
        <div>Total Students</div>
        <div class="count"><?= $totalStudents ?></div>
      </div>
      <div class="card">
        <div>Total Teachers</div>
        <div class="count"><?= $totalTeachers ?></div>
      </div>
      <div class="card">
        <div>Total Parents</div>
        <div class="count"><?= $totalParents ?></div>
      </div>
    </section>

    <!-- Add Student -->
  <section id="add-student" class="form-section">
  <h2>Add Student</h2>
  <form method="post" autocomplete="off">
    <input type="hidden" name="action" value="add_student" />

    <input type="text" name="student_id" placeholder="Student ID" required />
    <input type="text" name="student_name" placeholder="Student Name" required />
    <input type="text" name="student_class" placeholder="Class" required />

    <label for="parent_email">Parent Email:</label>
    <input type="email" id="parent_email" name="parent_email" placeholder="Parent Email" required />

    <!-- New fields -->
    <label for="email">Student Email:</label>
    <input type="email" id="email" name="email" placeholder="Student Email" />

    <label for="phone">Phone:</label>
    <input type="text" id="phone" name="phone" placeholder="Phone Number" />

    <label for="previous_school">Previous School:</label>
    <input type="text" id="previous_school" name="previous_school" placeholder="Previous School" />

    <input type="date" name="student_dob" placeholder="Date of Birth" required />

    <button type="submit">Add Student</button>
  </form>
</section>


    <!-- Add Teacher -->
    <section id="add-teacher" class="form-section">
     <h2>Add Teacher</h2>
<form method="POST" action="admin.php">
    <input type="hidden" name="action" value="add_teacher">

    <div>
        <label for="teacher_name">Teacher Name:</label><br>
        <input type="text" id="teacher_name" name="teacher_name" required>
    </div>

    <div>
        <label for="teacher_email">Email:</label><br>
        <input type="email" id="teacher_email" name="teacher_email" required>
    </div>

    <div>
        <label for="teacher_class">Class:</label><br>
        <input type="text" id="teacher_class" name="teacher_class" required>
    </div>

    <div>
        <label for="teacher_password">Password:</label><br>
        <input type="password" id="teacher_password" name="teacher_password" required>
    </div>

    <div style="margin-top:10px;">
        <button type="submit">Add Teacher</button>
    </div>
</form>

    </section>

   <!-- Add Parent -->
<section id="add-parent" class="form-section">
  <h2>Add Parent</h2>
  <form method="post" autocomplete="off">
    <input type="hidden" name="action" value="add_parent" />
    
    <!-- Select Student -->
    <select name="parent_student_id" required>
      <option value="">Select Student</option>
      <?php foreach ($students as $s): ?>
        <option value="<?= htmlspecialchars($s['id']) ?>">
          <?= htmlspecialchars($s['name']) ?> (ID: <?= htmlspecialchars($s['id']) ?>)
        </option>
      <?php endforeach; ?>
    </select>

    <!-- Parent Phone -->
    <input type="text" name="parent_phone" placeholder="Parent Phone Number" required pattern="[0-9]{8,15}" title="Enter a valid phone number (8-15 digits)" />

    <button type="submit">Add Parent</button>
  </form>
</section>


    <!-- Student List -->
    <section class="toggle-section">
      <button class="toggle-button" onclick="toggleSection('student-list')">
        <i class="fas fa-caret-right"></i> Students List
      </button>
      <div id="student-list" class="collapsible-content">
        <table>
          <thead>
            <tr>
              <th>ID</th><th>Name</th><th>Class</th><th>Date of Birth</th><th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($students as $s): ?>
              <tr>
                <td><?= htmlspecialchars($s['id']) ?></td>
                <td><?= htmlspecialchars($s['name']) ?></td>
                <td><?= htmlspecialchars($s['class']) ?></td>
                <td><?= htmlspecialchars($s['dob']) ?></td>
                <td>
                  <form method="post" style="display:inline" onsubmit="return confirm('Delete student <?= htmlspecialchars($s['name']) ?>?');">
                    <input type="hidden" name="action" value="delete_student" />
                    <input type="hidden" name="student_id" value="<?= htmlspecialchars($s['id']) ?>" />
                    <button type="submit" class="delete-btn">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Teacher List -->
    <section class="toggle-section">
      <button class="toggle-button" onclick="toggleSection('teacher-list')">
        <i class="fas fa-caret-right"></i> Teachers List
      </button>
      <div id="teacher-list" class="collapsible-content">
        <table>
          <thead>
            <tr>
              <th>Name</th><th>Subject</th><th>Class</th><th>Email</th><th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($teachers as $t): ?>
              <tr>
                <td><?= htmlspecialchars($t['name']) ?></td>
                <td><?= htmlspecialchars($t['subject']) ?></td>
                <td><?= htmlspecialchars($t['class']) ?></td>
                <td><?= htmlspecialchars($t['email']) ?></td>
                <td>
                  <form method="post" style="display:inline" onsubmit="return confirm('Delete teacher <?= htmlspecialchars($t['name']) ?>?');">
                    <input type="hidden" name="action" value="delete_teacher" />
                    <input type="hidden" name="teacher_email" value="<?= htmlspecialchars($t['email']) ?>" />
                    <button type="submit" class="delete-btn">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Parents List -->
    <section class="toggle-section">
      <button class="toggle-button" onclick="toggleSection('parent-list')">
        <i class="fas fa-caret-right"></i> Parents List
      </button>
      <div id="parent-list" class="collapsible-content">
        <table>
          <thead>
            <tr>
              <th>Parent Number</th><th>Student Name</th><th>Student ID</th><th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($parents as $p): ?>
              <tr>
                <td><?= htmlspecialchars($p['parent_phone']) ?></td>
                <td><?= htmlspecialchars($p['student_name']) ?></td>
                <td><?= htmlspecialchars($p['student_id']) ?></td>
                <td>
                  <form method="post" style="display:inline" onsubmit="return confirm('Delete parent <?= htmlspecialchars($p['parent_phone']) ?>?');">
                    <input type="hidden" name="action" value="delete_parent" />
                    <input type="hidden" name="parent_phone" value="<?= htmlspecialchars($p['parent_phone']) ?>" />
                    <button type="submit" class="delete-btn">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
<!-- Pending Admissions -->
<section class="toggle-section">
  <button class="toggle-button" onclick="toggleSection('pending-list')">
    <i class="fas fa-caret-right"></i> Pending Admissions
  </button>
  <div id="pending-list" class="collapsible-content">
    <table>
      <thead>
        <tr>
          <th>ID</th><th>Name</th><th>Class</th><th>Date of Birth</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pendingStudents as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['id']) ?></td>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= htmlspecialchars($p['class']) ?></td>
            <td><?= htmlspecialchars($p['dob']) ?></td>
            <td>
              <form method="post" style="display:inline">
                <input type="hidden" name="action" value="approve_pending" />
                <input type="hidden" name="pending_id" value="<?= htmlspecialchars($p['id']) ?>" />
                <button type="submit" class="edit-btn">Approve</button>
              </form>
              <form method="post" style="display:inline" onsubmit="return confirm('Decline pending student <?= htmlspecialchars($p['name']) ?>?');">
                <input type="hidden" name="action" value="decline_pending" />
                <input type="hidden" name="pending_id" value="<?= htmlspecialchars($p['id']) ?>" />
                <button type="submit" class="delete-btn">Decline</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

  </main>
</div>
<script>
  function toggleSection(id) {
    const content = document.getElementById(id);
    const btn = content.previousElementSibling;
    content.classList.toggle('active');
    btn.classList.toggle('active');
  }
</script>
<script>
const toggleBtn = document.getElementById('theme-toggle');

toggleBtn.addEventListener('click', () => {
  document.body.classList.toggle('dark-mode');
  
  if(document.body.classList.contains('dark-mode')){
    toggleBtn.textContent = "Light Mode";
  } else {
    toggleBtn.textContent = "Dark Mode";
  }
});
</script>

</body>
</html>
