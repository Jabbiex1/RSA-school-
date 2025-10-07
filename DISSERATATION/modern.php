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

        // Add Student
        if ($action === 'add_student') {
            $id = $_POST['student_id'];

            // Check if student ID already exists
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE id = ?");
            $checkStmt->execute([$id]);

            if ($checkStmt->fetchColumn() > 0) {
                $message = "Student ID '$id' already exists. Please use a unique ID.";
            } else {
                $name = $_POST['student_name'];
                $class = $_POST['student_class'];
                $dob = $_POST['student_dob'];
                $source = 'manual';

                $stmt = $pdo->prepare("INSERT INTO students (id, name, class, dob, source) VALUES (?, ?, ?, ?, ?)");

                try {
                    $stmt->execute([$id, $name, $class, $dob, $source]);
                    $message = "Student added successfully!";
                } catch (PDOException $e) {
                    $message = "Error adding student: " . $e->getMessage();
                }
            }
        }

        // Edit Student
        if ($action === 'edit_student' && isset($_POST['student_id'])) {
            $id = $_POST['student_id'];
            $name = $_POST['student_name'];
            $class = $_POST['student_class'];
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

        // Add Teacher
        if ($action === 'add_teacher') {
            $name = $_POST['teacher_name'];
            $subject = $_POST['teacher_subject'];
            $email = $_POST['teacher_email'];
            $password = password_hash($_POST['teacher_password'], PASSWORD_BCRYPT);

            $stmt = $pdo->prepare("INSERT INTO lecturers (name, subject, email, password) VALUES (?, ?, ?, ?)");
            try {
                $stmt->execute([$name, $subject, $email, $password]);
                $message = "Teacher added successfully!";
            } catch (PDOException $e) {
                $message = "Error adding teacher: " . $e->getMessage();
            }
        }

        // Edit Teacher
        if ($action === 'edit_teacher' && isset($_POST['teacher_email'])) {
            $email = $_POST['teacher_email'];
            $name = $_POST['teacher_name'];
            $subject = $_POST['teacher_subject'];

            $stmt = $pdo->prepare("UPDATE lecturers SET name = ?, subject = ? WHERE email = ?");
            try {
                $stmt->execute([$name, $subject, $email]);
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
            $parent_email = $_POST['parent_email'];

            $stmt = $pdo->prepare("INSERT INTO parents (student_id, parent_email) VALUES (?, ?)");
            try {
                $stmt->execute([$student_id, $parent_email]);
                $message = "Parent added successfully!";
            } catch (PDOException $e) {
                $message = "Error adding parent: " . $e->getMessage();
            }
        }

        // Edit Parent
        if ($action === 'edit_parent' && isset($_POST['parent_email'])) {
            $email = $_POST['parent_email'];
            $student_id = $_POST['parent_student_id'];

            $stmt = $pdo->prepare("UPDATE parents SET student_id = ? WHERE parent_email = ?");
            try {
                $stmt->execute([$student_id, $email]);
                $message = "Parent $email updated successfully!";
            } catch (PDOException $e) {
                $message = "Error updating parent: " . $e->getMessage();
            }
        }

        // Delete Parent
        if ($action === 'delete_parent' && isset($_POST['parent_email'])) {
            $email = $_POST['parent_email'];
            $stmt = $pdo->prepare("DELETE FROM parents WHERE parent_email = ?");
            try {
                $stmt->execute([$email]);
                $message = "Parent $email deleted successfully!";
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

// Fetch lists for display
$students = $pdo->query("SELECT id, name, class, dob FROM students ORDER BY name ASC")->fetchAll();
$teachers = $pdo->query("SELECT name, subject, email FROM lecturers ORDER BY name ASC")->fetchAll();
$parents = $pdo->query("SELECT p.parent_email, s.name AS student_name, s.id AS student_id FROM parents p LEFT JOIN students s ON p.student_id = s.id ORDER BY student_name ASC")->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Dashboard - Royal Academy</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<style>
  /* Reset & basics */
  * {
    box-sizing: border-box;
  }
  body {
    margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f0f4f8;
    color: #333;
  }
  a {
    text-decoration: none;
    color: inherit;
  }
  /* Layout */
  .header {
    background: #004aad;
    color: white;
    padding: 15px 30px;
    font-size: 24px;
    font-weight: 700;
    letter-spacing: 0.05em;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 10px rgb(0 0 0 / 0.1);
  }
  .container {
    display: flex;
    min-height: calc(100vh - 60px);
  }
  .sidebar {
    width: 260px;
    background: #0b2545;
    color: white;
    display: flex;
    flex-direction: column;
    padding: 30px 20px;
  }
  .sidebar h2 {
    font-weight: 900;
    font-size: 28px;
    letter-spacing: 0.1em;
    margin-bottom: 40px;
    text-align: center;
    color: #1db954;
  }
  .sidebar nav a {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px 18px;
    margin-bottom: 15px;
    border-radius: 8px;
    transition: background 0.3s ease, color 0.3s ease;
    font-weight: 600;
  }
  .sidebar nav a:hover,
  .sidebar nav a.active {
    background: #1db954;
    color: #0b2545;
    font-weight: 700;
  }
  .sidebar nav a i {
    min-width: 20px;
    font-size: 18px;
  }
  .content {
    flex: 1;
    padding: 30px 40px;
    overflow-y: auto;
  }

  /* Message */
  .message {
    background: #d4edda;
    color: #155724;
    padding: 12px 20px;
    margin-bottom: 25px;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgb(0 0 0 / 0.1);
    font-weight: 600;
  }

  /* Stats Cards */
  .stats-cards {
    display: flex;
    gap: 25px;
    margin-bottom: 30px;
  }
  .card {
    flex: 1;
    background: white;
    padding: 25px 30px;
    border-radius: 10px;
    box-shadow: 0 8px 16px rgb(29 185 84 / 0.15);
    text-align: center;
    font-weight: 700;
    font-size: 20px;
    color: #0b2545;
    transition: transform 0.3s ease;
  }
  .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 20px rgb(29 185 84 / 0.25);
  }
  .card .count {
    font-size: 42px;
    margin-top: 10px;
    color: #1db954;
  }

  /* Forms */
  section.form-section {
    background: white;
    border-radius: 10px;
    padding: 30px;
    margin-bottom: 40px;
    box-shadow: 0 6px 18px rgb(0 0 0 / 0.05);
  }
  section.form-section h2 {
    margin-bottom: 20px;
    font-weight: 700;
    color: #004aad;
  }
  form input, form select, form button {
    width: 100%;
    padding: 14px 18px;
    margin: 10px 0;
    border-radius: 8px;
    border: 1.8px solid #ccc;
    font-size: 16px;
    font-weight: 500;
    transition: border-color 0.3s ease;
  }
  form input:focus, form select:focus {
    outline: none;
    border-color: #1db954;
  }
  form button {
    background: #1db954;
    color: white;
    font-weight: 700;
    border: none;
    cursor: pointer;
    margin-top: 10px;
    transition: background 0.3s ease;
  }
  form button:hover {
    background: #169c3f;
  }

  /* Collapsible Lists */
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
    border-radius: 8px;
    cursor: pointer;
    font-weight: 700;
    transition: background 0.3s ease;
    gap: 8px;
  }
  .toggle-button:hover {
    background: #003080;
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
    border-radius: 10px;
    box-shadow: 0 6px 20px rgb(0 0 0 / 0.1);
  }
  .collapsible-content.active {
    max-height: 600px; /* enough height for tables */
    overflow-y: auto;
  }

  /* Table */
  table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgb(0 0 0 / 0.05);
  }
  thead {
    background: #1db954;
    color: white;
    font-weight: 700;
  }
  th, td {
    padding: 14px 20px;
    text-align: left;
    border-bottom: 1px solid #eee;
  }
  tbody tr:hover {
    background: #f1fdf4;
  }
  td button {
    padding: 6px 12px;
    margin-right: 6px;
    border: none;
    border-radius: 5px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s ease;
  }
  td button.edit-btn {
    background-color: #004aad;
    color: white;
  }
  td button.edit-btn:hover {
    background-color: #003080;
  }
  td button.delete-btn {
    background-color: #dc3545;
    color: white;
  }
  td button.delete-btn:hover {
    background-color: #a71d2a;
  }

  /* Responsive */
  @media (max-width: 900px) {
    .container {
      flex-direction: column;
    }
    .sidebar {
      width: 100%;
      height: auto;
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
</style>
</head>
<body>
<div class="header">
  Royal Science Academy - Admin Dashboard
</div>
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
        <input type="date" name="student_dob" placeholder="Date of Birth" required />
        <button type="submit">Add Student</button>
      </form>
      <div class="toggle-section">
        <button class="toggle-button"><i class="fas fa-chevron-right"></i> Show Students</button>
        <div class="collapsible-content">
          <?php if (count($students) > 0): ?>
            <table>
              <thead>
                <tr>
                  <th>ID</th><th>Name</th><th>Class</th><th>DOB</th><th>Actions</th>
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
                      <button class="edit-btn" onclick="showEditStudentForm('<?= htmlspecialchars($s['id']) ?>', '<?= htmlspecialchars(addslashes($s['name'])) ?>', '<?= htmlspecialchars($s['class']) ?>', '<?= htmlspecialchars($s['dob']) ?>')">Edit</button>
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
          <?php else: ?>
            <p>No students found.</p>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- Edit Student Form (hidden by default) -->
    <section id="edit-student-section" class="form-section" style="display:none;">
      <h2>Edit Student</h2>
      <form method="post" autocomplete="off">
        <input type="hidden" name="action" value="edit_student" />
        <input type="text" name="student_id" id="edit_student_id" readonly />
        <input type="text" name="student_name" id="edit_student_name" placeholder="Student Name" required />
        <input type="text" name="student_class" id="edit_student_class" placeholder="Class" required />
        <input type="date" name="student_dob" id="edit_student_dob" placeholder="Date of Birth" required />
        <button type="submit">Update Student</button>
        <button type="button" onclick="hideEditStudentForm()" style="background:#aaa; margin-left:10px;">Cancel</button>
      </form>
    </section>

    <!-- Add Teacher -->
    <section id="add-teacher" class="form-section">
      <h2>Add Teacher</h2>
      <form method="post" autocomplete="off">
        <input type="hidden" name="action" value="add_teacher" />
        <input type="text" name="teacher_name" placeholder="Teacher Name" required />
        <input type="text" name="teacher_subject" placeholder="Subject" required />
        <input type="email" name="teacher_email" placeholder="Email" required />
        <input type="password" name="teacher_password" placeholder="Password" required />
        <button type="submit">Add Teacher</button>
      </form>
      <div class="toggle-section">
        <button class="toggle-button"><i class="fas fa-chevron-right"></i> Show Teachers</button>
        <div class="collapsible-content">
          <?php if (count($teachers) > 0): ?>
            <table>
              <thead>
                <tr>
                  <th>Name</th><th>Subject</th><th>Email</th><th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($teachers as $t): ?>
                  <tr>
                    <td><?= htmlspecialchars($t['name']) ?></td>
                    <td><?= htmlspecialchars($t['subject']) ?></td>
                    <td><?= htmlspecialchars($t['email']) ?></td>
                    <td>
                      <button class="edit-btn" onclick="showEditTeacherForm('<?= htmlspecialchars(addslashes($t['email'])) ?>', '<?= htmlspecialchars(addslashes($t['name'])) ?>', '<?= htmlspecialchars($t['subject']) ?>')">Edit</button>
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
          <?php else: ?>
            <p>No teachers found.</p>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- Edit Teacher Form (hidden) -->
    <section id="edit-teacher-section" class="form-section" style="display:none;">
      <h2>Edit Teacher</h2>
      <form method="post" autocomplete="off">
        <input type="hidden" name="action" value="edit_teacher" />
        <input type="email" name="teacher_email" id="edit_teacher_email" readonly />
        <input type="text" name="teacher_name" id="edit_teacher_name" placeholder="Teacher Name" required />
        <input type="text" name="teacher_subject" id="edit_teacher_subject" placeholder="Subject" required />
        <button type="submit">Update Teacher</button>
        <button type="button" onclick="hideEditTeacherForm()" style="background:#aaa; margin-left:10px;">Cancel</button>
      </form>
    </section>

    <!-- Add Parent -->
    <section id="add-parent" class="form-section">
      <h2>Add Parent</h2>
      <form method="post" autocomplete="off">
        <input type="hidden" name="action" value="add_parent" />
        <input type="text" name="parent_student_id" placeholder="Student ID" required />
        <input type="email" name="parent_email" placeholder="Parent Email" required />
        <button type="submit">Add Parent</button>
      </form>
      <div class="toggle-section">
        <button class="toggle-button"><i class="fas fa-chevron-right"></i> Show Parents</button>
        <div class="collapsible-content">
          <?php if (count($parents) > 0): ?>
            <table>
              <thead>
                <tr>
                  <th>Parent Email</th><th>Student Name</th><th>Student ID</th><th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($parents as $p): ?>
                  <tr>
                    <td><?= htmlspecialchars($p['parent_email']) ?></td>
                    <td><?= htmlspecialchars($p['student_name'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($p['student_id'] ?? 'N/A') ?></td>
                    <td>
                      <button class="edit-btn" onclick="showEditParentForm('<?= htmlspecialchars(addslashes($p['parent_email'])) ?>', '<?= htmlspecialchars($p['student_id'] ?? '') ?>')">Edit</button>
                      <form method="post" style="display:inline" onsubmit="return confirm('Delete parent <?= htmlspecialchars($p['parent_email']) ?>?');">
                        <input type="hidden" name="action" value="delete_parent" />
                        <input type="hidden" name="parent_email" value="<?= htmlspecialchars($p['parent_email']) ?>" />
                        <button type="submit" class="delete-btn">Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <p>No parents found.</p>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- Edit Parent Form (hidden) -->
    <section id="edit-parent-section" class="form-section" style="display:none;">
      <h2>Edit Parent</h2>
      <form method="post" autocomplete="off">
        <input type="hidden" name="action" value="edit_parent" />
        <input type="email" name="parent_email" id="edit_parent_email" readonly />
        <input type="text" name="parent_student_id" id="edit_parent_student_id" placeholder="Student ID" required />
        <button type="submit">Update Parent</button>
        <button type="button" onclick="hideEditParentForm()" style="background:#aaa; margin-left:10px;">Cancel</button>
      </form>
    </section>

  </main>
</div>

<script>
  // Toggle collapsible content
  document.querySelectorAll('.toggle-button').forEach(btn => {
    btn.addEventListener('click', () => {
      btn.classList.toggle('active');
      const content = btn.nextElementSibling;
      if (content.classList.contains('active')) {
        content.classList.remove('active');
      } else {
        content.classList.add('active');
      }
    });
  });

  // Show / Hide Edit Student Form
  function showEditStudentForm(id, name, studentClass, dob) {
    document.getElementById('edit-student-section').style.display = 'block';
    document.getElementById('edit_student_id').value = id;
    document.getElementById('edit_student_name').value = name;
    document.getElementById('edit_student_class').value = studentClass;
    document.getElementById('edit_student_dob').value = dob;
    document.getElementById('edit-student-section').scrollIntoView({behavior: 'smooth'});
  }
  function hideEditStudentForm() {
    document.getElementById('edit-student-section').style.display = 'none';
  }

  // Show / Hide Edit Teacher Form
  function showEditTeacherForm(email, name, subject) {
    document.getElementById('edit-teacher-section').style.display = 'block';
    document.getElementById('edit_teacher_email').value = email;
    document.getElementById('edit_teacher_name').value = name;
    document.getElementById('edit_teacher_subject').value = subject;
    document.getElementById('edit-teacher-section').scrollIntoView({behavior: 'smooth'});
  }
  function hideEditTeacherForm() {
    document.getElementById('edit-teacher-section').style.display = 'none';
  }

  // Show / Hide Edit Parent Form
  function showEditParentForm(email, studentId) {
    document.getElementById('edit-parent-section').style.display = 'block';
    document.getElementById('edit_parent_email').value = email;
    document.getElementById('edit_parent_student_id').value = studentId;
    document.getElementById('edit-parent-section').scrollIntoView({behavior: 'smooth'});
  }
  function hideEditParentForm() {
    document.getElementById('edit-parent-section').style.display = 'none';
  }
</script>
</body>
</html>
