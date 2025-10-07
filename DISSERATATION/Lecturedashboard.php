<?php
session_start();

// Check if teacher is logged in
if (!isset($_SESSION['teacher_class'])) {
    header('Location: teacher-login.php');
    exit;
}

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

$message = '';
$teacherClass = $_SESSION['teacher_class'];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Fetch students for this class
    $stmt = $pdo->prepare("SELECT id, name FROM students WHERE class = ?");
    $stmt->execute([$teacherClass]);
    $students = $stmt->fetchAll();

    // Fetch subjects dynamically
    $stmtSubjects = $pdo->prepare("SELECT subject_name FROM subjects WHERE class_name = ? ORDER BY subject_name ASC");
    $stmtSubjects->execute([$teacherClass]);
    $subjects = $stmtSubjects->fetchAll(PDO::FETCH_COLUMN);

    // Initialize selectedStudentId to avoid undefined variable
    $selectedStudentId = $_POST['student_id'] ?? null;
    $selectedTerm = $_POST['terms'] ?? 'First Term';
    $existingGrades = [];
    $existingPosition = '';
    $existingAverage = '';

    // Handle grade submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id']) && isset($_POST['terms'])) {
        $student_id = $_POST['student_id'];
        $term = $_POST['terms'];
        $position = trim($_POST['position'] ?? '');

        $stmtCheck = $pdo->prepare("SELECT * FROM students WHERE id = ? AND class = ?");
        $stmtCheck->execute([$student_id, $teacherClass]);
        $studentValid = $stmtCheck->fetch();

        if (!$studentValid) {
            $message = "Invalid student selected.";
        } else {
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM grades WHERE student_id = ? AND subject = ? AND terms = ?");
            $updateStmt = $pdo->prepare("UPDATE grades SET assignment = ?, test = ?, exam = ? WHERE student_id = ? AND subject = ? AND terms = ?");
            $insertStmt = $pdo->prepare("INSERT INTO grades (student_id, subject, assignment, test, exam, terms) VALUES (?, ?, ?, ?, ?, ?)");

            $totalScore = 0;
            $subjectsCount = count($subjects);

            foreach ($subjects as $subj) {
                $inputAssignment = 'assignment_' . strtolower(str_replace([' ', '&', '(', ')'], '', $subj));
                $inputTest = 'test_' . strtolower(str_replace([' ', '&', '(', ')'], '', $subj));
                $inputExam = 'exam_' . strtolower(str_replace([' ', '&', '(', ')'], '', $subj));

                $assignmentGrade = floatval(trim($_POST[$inputAssignment] ?? 0));
                $testGrade = floatval(trim($_POST[$inputTest] ?? 0));
                $examGrade = floatval(trim($_POST[$inputExam] ?? 0));

                if ($assignmentGrade !== 0 || $testGrade !== 0 || $examGrade !== 0) {
                    $checkStmt->execute([$student_id, $subj, $term]);
                    $exists = $checkStmt->fetchColumn();

                    if ($exists) {
                        $updateStmt->execute([$assignmentGrade, $testGrade, $examGrade, $student_id, $subj, $term]);
                    } else {
                        $insertStmt->execute([$student_id, $subj, $assignmentGrade, $testGrade, $examGrade, $term]);
                    }
                }

                // Add to total score
                $totalScore += $assignmentGrade + $testGrade + $examGrade;
            }

            // Calculate average: sum of all scores divided by number of subjects
            $average = $subjectsCount > 0 ? round($totalScore / $subjectsCount, 2) : 0;

            // Save average
            $stmtAvgCheck = $pdo->prepare("SELECT COUNT(*) FROM averages WHERE student_id = ? AND term = ?");
            $stmtAvgInsert = $pdo->prepare("INSERT INTO averages (student_id, term, average) VALUES (?, ?, ?)");
            $stmtAvgUpdate = $pdo->prepare("UPDATE averages SET average = ? WHERE student_id = ? AND term = ?");
            $stmtAvgCheck->execute([$student_id, $term]);
            $avgExists = $stmtAvgCheck->fetchColumn();
            if ($avgExists) {
                $stmtAvgUpdate->execute([$average, $student_id, $term]);
            } else {
                $stmtAvgInsert->execute([$student_id, $term, $average]);
            }

            // Handle position
            $stmtPosCheck = $pdo->prepare("SELECT COUNT(*) FROM positions WHERE student_id = ? AND terms = ?");
            $stmtPosInsert = $pdo->prepare("INSERT INTO positions (student_id, terms, position) VALUES (?, ?, ?)");
            $stmtPosUpdate = $pdo->prepare("UPDATE positions SET position = ? WHERE student_id = ? AND terms = ?");
            if ($position !== '') {
                $stmtPosCheck->execute([$student_id, $term]);
                $posExists = $stmtPosCheck->fetchColumn();
                if ($posExists) {
                    $stmtPosUpdate->execute([$position, $student_id, $term]);
                } else {
                    $stmtPosInsert->execute([$student_id, $term, $position]);
                }
            }

            $message = "Grades saved successfully for " . htmlspecialchars($studentValid['name']) . " for $term.";
        }
    }

    // Load existing grades, position, and average if student selected
    if ($selectedStudentId) {
        $stmtGrades = $pdo->prepare("SELECT subject, assignment, test, exam FROM grades WHERE student_id = ? AND terms = ?");
        $stmtGrades->execute([$selectedStudentId, $selectedTerm]);
        $gradesRaw = $stmtGrades->fetchAll();
        foreach ($gradesRaw as $row) {
            $existingGrades[$row['subject']] = [
                'assignment' => $row['assignment'],
                'test' => $row['test'],
                'exam' => $row['exam'],
            ];
        }

        $stmtPos = $pdo->prepare("SELECT position FROM positions WHERE student_id = ? AND terms = ?");
        $stmtPos->execute([$selectedStudentId, $selectedTerm]);
        $posRow = $stmtPos->fetch();
        if ($posRow) $existingPosition = $posRow['position'];

        $stmtAvg = $pdo->prepare("SELECT average FROM averages WHERE student_id = ? AND term = ?");
        $stmtAvg->execute([$selectedStudentId, $selectedTerm]);
        $avgRow = $stmtAvg->fetch();
        if ($avgRow) $existingAverage = $avgRow['average'];
    }

} catch (PDOException $e) {
    $message = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Teacher Dashboard - Royal Science Academy</title>
<style>
/* --- All your previous CSS remains unchanged --- */
:root {
    --primary: #004aad;
    --primary-light: #5a8de8;
    --accent: #f39c12;
    --white: #fff;
    --gray-light: #f8f9fb;
    --text-dark: #2c3e50;
    --error-red: #e74c3c;
    --success-green: #27ae60;
    --bg-light: #f0f4ff;
    --bg-dark: #1f1f2e;
    --text-light: #f0f4ff;
    --text-darkmode: #e0e0e0;
}

body { font-family:'Poppins',sans-serif; margin:0; padding:0; color:var(--text-dark); background:var(--bg-light); transition:all 0.3s; }
body.dark-mode { background:var(--bg-dark); color:var(--text-darkmode); }

header { display:flex; justify-content:space-between; align-items:center; padding:20px 30px; background:var(--primary); color:var(--white); border-bottom:4px solid var(--primary-light); }
header h1 { margin:0; font-size:1.8rem; font-weight:800; }
header .actions { display:flex; align-items:center; gap:15px; }
header button, .theme-toggle { cursor:pointer; border:none; padding:10px 20px; border-radius:25px; font-weight:700; transition:0.3s; }
header button { background: var(--accent); color:var(--white); box-shadow:0 5px 14px rgb(243 156 18 /0.7); }
header button:hover { background:#c87f03; }
.theme-toggle { background:#fff; color:var(--primary); }
.theme-toggle.dark-mode { background:#2c3e50; color:var(--white); }

main { max-width:900px; margin:40px auto 60px; padding:30px 40px; background: var(--white); border-radius:15px; box-shadow:0 10px 30px rgb(0 0 0 /0.1); transition:0.3s; }
body.dark-mode main { background:#2c2c3c; }

p.class-info { font-weight:700; font-size:1.15rem; text-align:center; color:var(--primary); margin-bottom:35px; }
.message { text-align:center; font-weight:700; font-size:1.1rem; margin-bottom:25px; color:var(--error-red); transition:opacity 1s; }
.message.success { color:var(--success-green); }

form#selectionForm { display:flex; gap:20px; justify-content:center; flex-wrap:wrap; margin-bottom:40px; }
form#selectionForm label { font-weight:700; color:var(--primary); }
form#selectionForm select { padding:8px 14px; border-radius:7px; border:1.5px solid var(--primary-light); font-size:1rem; min-width:200px; }
form#selectionForm select:hover, select:focus { border-color: var(--accent); outline:none; }

table { width:100%; border-collapse: separate; border-spacing:0; border-radius:12px; overflow:hidden; box-shadow:0 3px 15px rgb(0 0 0 /0.07); margin-bottom:30px; }
thead tr { background: var(--primary-light); color: var(--white); font-weight:700; }
th, td { padding:14px 18px; text-align:center; border-bottom:1px solid #ddd; }
td.subject-cell { text-align:left; font-weight:700; color: var(--primary); }
tbody tr:hover { background-color: var(--gray-light); transition:0.3s; }

input[type="text"] { width:90px; padding:8px 12px; border-radius:8px; border:1.5px solid #ccc; text-align:center; transition:0.3s; }
input[type="text"]:focus { border-color: var(--accent); box-shadow:0 0 8px rgb(243 156 18 /0.5); outline:none; }

#positionInput { width:150px; margin-bottom:25px; font-weight:600; }
button[type="submit"] { background: var(--accent); color: var(--white); border:none; padding:14px 38px; font-weight:800; font-size:1.1rem; border-radius:30px; cursor:pointer; box-shadow:0 6px 20px rgb(243 156 18 /0.9); transition:0.3s; }
button[type="submit"]:hover { background:#c87f03; transform:scale(1.05); }

body.dark-mode table thead tr { background:#5a8de8; }
body.dark-mode table tbody tr:hover { background:#444455; }
body.dark-mode input, body.dark-mode select { background:#3a3a4a; color:#e0e0e0; border:1.5px solid #5a8de8; }

@media(max-width:720px) {
    form#selectionForm { flex-direction:column; gap:15px; }
    input[type="text"], #positionInput { width:100%; }
}
</style>

<script>
window.onload = function() {
    const messageDiv = document.querySelector('.message.success');
    if (messageDiv) {
        setTimeout(()=>{ messageDiv.style.opacity='0'; setTimeout(()=> messageDiv.style.display='none',1000); },4000);
    }

    const toggleBtn = document.querySelector('.theme-toggle');
    toggleBtn.addEventListener('click', ()=>{
        document.body.classList.toggle('dark-mode');
        toggleBtn.classList.toggle('dark-mode');
        toggleBtn.textContent = document.body.classList.contains('dark-mode') ? 'Light Mode' : 'Dark Mode';
    });
};
</script>
</head>

<body>
<header>
    <h1>Teacher Dashboard</h1>
    <div class="actions">
        <button type="button" class="theme-toggle">Dark Mode</button>
        <form method="POST" action="teacher-logout.php">
            <button type="submit">Logout</button>
        </form>
    </div>
</header>

<main>
    <p class="class-info">Class: <strong><?= htmlspecialchars($teacherClass) ?></strong></p>

    <?php if ($message): ?>
    <div class="message <?= strpos($message,'successfully')!==false?'success':'' ?>">
        <?= htmlspecialchars($message) ?>
    </div>
    <?php endif; ?>

    <form id="selectionForm" method="POST">
        <label for="studentSelect">Select Student</label>
        <select name="student_id" id="studentSelect" required onchange="this.form.submit()">
            <option value="">-- Choose Student --</option>
            <?php foreach($students as $student): ?>
                <option value="<?= $student['id'] ?>" <?= ($selectedStudentId==$student['id'])?'selected':'' ?>>
                    <?= htmlspecialchars($student['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="termSelect">Select Term</label>
        <select name="terms" id="termSelect" required onchange="this.form.submit()">
            <?php
            $termsOptions=['First Term','Second Term','Third Term'];
            foreach($termsOptions as $termOption){
                $sel=($selectedTerm==$termOption)?'selected':'';
                echo "<option value=\"$termOption\" $sel>$termOption</option>";
            }
            ?>
        </select>
        <noscript><button type="submit">Load Grades</button></noscript>
    </form>

    <?php if($selectedStudentId): 
        $studentName = htmlspecialchars($students[array_search($selectedStudentId,array_column($students,'id'))]['name']);
    ?>
        <h2 style="text-align:center; margin-bottom:25px;">
            Enter Grades for <?= $studentName ?><br/>
            <small style="color:var(--primary-light); font-weight:600; font-size:1rem;"><?= htmlspecialchars($selectedTerm) ?></small>
        </h2>

        <form id="gradesForm" method="POST">
            <input type="hidden" name="student_id" value="<?= $selectedStudentId ?>"/>
            <input type="hidden" name="terms" value="<?= htmlspecialchars($selectedTerm) ?>"/>

            <table>
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Assignment</th>
                        <th>Test</th>
                        <th>Exam</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($subjects as $subj): 
                    $inputAssignment = 'assignment_' . strtolower(str_replace([' ', '&', '(', ')'], '', $subj));
                    $inputTest = 'test_' . strtolower(str_replace([' ', '&', '(', ')'], '', $subj));
                    $inputExam = 'exam_' . strtolower(str_replace([' ', '&', '(', ')'], '', $subj));
                ?>
                    <tr>
                        <td class="subject-cell"><?= htmlspecialchars($subj) ?></td>
                        <td><input type="text" name="<?= $inputAssignment ?>" value="<?= htmlspecialchars($existingGrades[$subj]['assignment'] ?? '') ?>" /></td>
                        <td><input type="text" name="<?= $inputTest ?>" value="<?= htmlspecialchars($existingGrades[$subj]['test'] ?? '') ?>" /></td>
                        <td><input type="text" name="<?= $inputExam ?>" value="<?= htmlspecialchars($existingGrades[$subj]['exam'] ?? '') ?>" /></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <label for="positionInput">Position</label>
            <input type="text" name="position" id="positionInput" value="<?= htmlspecialchars($existingPosition) ?>" placeholder="e.g. 1st"/>

            <label>Average: <?= $existingAverage !== '' ? $existingAverage : 'N/A' ?></label><br/><br/>

            <button type="submit">Save Grades</button>
        </form>
    <?php endif; ?>
</main>
</body>
</html>
