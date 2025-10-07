<?php
session_start();

if (!isset($_SESSION['studentID']) || !isset($_SESSION['class'])) {
    header("Location: student-login.php");
    exit();
}

$studentID = htmlspecialchars($_SESSION['studentID']);
$class = htmlspecialchars($_SESSION['class']);
$academicYear = "2024/2025"; // Academic year format

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: student-login.php");
    exit();
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

function getLetterGrade($total) {
    if ($total >= 90) return 'A';
    if ($total >= 80) return 'B';
    if ($total >= 70) return 'C';
    if ($total >= 60) return 'D';
    return 'F';
}

$termSelected = $_POST['term'] ?? 'First Term';
$termsOrder = ['First Term', 'Second Term', 'Third Term'];
$termsToShow = [];
foreach ($termsOrder as $t) {
    $termsToShow[] = $t;
    if ($t === $termSelected) break;
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Student name
    $stmt = $pdo->prepare("SELECT name FROM students WHERE id = ?");
    $stmt->execute([$studentID]);
    $student = $stmt->fetch();
    $studentName = $student['name'] ?? 'Student Name';

    // Fetch grades & calculate term averages dynamically
    $grades = [];
    $termAverages = [];
    foreach ($termsToShow as $term) {
        $stmtGrades = $pdo->prepare("SELECT subject, assignment, test, exam FROM grades WHERE student_id = ? AND terms = ?");
        $stmtGrades->execute([$studentID, $term]);
        $rows = $stmtGrades->fetchAll();

        $termTotal = 0;
        $subjectCount = count($rows);

        foreach ($rows as $row) {
            $assignment = is_numeric($row['assignment']) ? (float)$row['assignment'] : 0;
            $test = is_numeric($row['test']) ? (float)$row['test'] : 0;
            $exam = is_numeric($row['exam']) ? (float)$row['exam'] : 0;
            $total = $assignment + $test + $exam;

            $grades[$row['subject']][$term] = [
                'assignment' => $assignment,
                'test' => $test,
                'exam' => $exam,
                'total' => $total,
                'letter' => getLetterGrade($total)
            ];

            $termTotal += $total;
        }

        // Calculate term average
        $termAverages[$term] = $subjectCount > 0 ? round($termTotal / $subjectCount, 2) : 0;
    }

    // Calculate overall average for all three terms
    $overallAverage = null;
    if (in_array('Third Term', $termsToShow)) {
        $sumOfAverages = 0;
        foreach ($termsOrder as $t) {
            $sumOfAverages += $termAverages[$t] ?? 0;
        }
        $overallAverage = round($sumOfAverages / 3, 2);
    }

    // Overall position
    $stmtPos = $pdo->prepare("SELECT position FROM positions WHERE student_id = ?");
    $stmtPos->execute([$studentID]);
    $positionRow = $stmtPos->fetch();
    $overallPosition = $positionRow ? $positionRow['position'] : 'N/A';

} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Result - Royal Science Academy</title>
<style>
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f4f8; margin: 0; padding: 0; font-size: 14px; }
header { display: flex; justify-content: space-between; align-items: center; background-color: #fff; padding: 0.5rem 1rem; box-shadow: 0 4px 12px rgba(0,0,0,0.1); flex-wrap: wrap; }
.logo img { height: 60px; border-radius: 8px; }
.school-name { text-align: center; flex: 1; font-size: 1.4rem; font-weight: 700; color: #004aad; }
.motto { color: #1e90ff; font-size: 1.2rem; font-weight: 700; text-align: right; white-space: nowrap; }
.logout-btn { background: #2980b9; color:#fff; padding:8px 20px; border:none; border-radius:25px; cursor:pointer; font-weight:700; transition: 0.3s; }
.logout-btn:hover { background:#1f618d; }

main { max-width: 1000px; margin: 1rem auto; background: #fff; padding: 1rem 1.5rem; border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,0.15); }
.student-info { display: flex; justify-content: space-between; flex-wrap: wrap; margin-bottom: 1rem; font-weight: 600; color: #004aad; }
.student-info div { margin: 0.3rem 0; }
form select { padding: 0.3rem 0.6rem; font-size: 0.9rem; border-radius: 6px; border: 1.5px solid #004aad; font-weight: 600; margin-bottom: 1rem; }

.table-container { overflow-x: auto; margin-bottom: 20px; }
table { width: 100%; border-collapse: collapse; font-size: 0.85rem; margin-bottom: 15px; }
th, td { border: 1px solid #004aad; padding: 6px; text-align: center; }
th { background-color: #004aad; color: white; font-weight: 700; }
td.subject { text-align: left; font-weight: 600; color: #004aad; }
.grade-A { color: #2ecc71; font-weight: 700; }
.grade-B { color: #3498db; font-weight: 700; }
.grade-C { color: #f1c40f; font-weight: 700; }
.grade-D { color: #e67e22; font-weight: 700; }
.grade-F { color: #e74c3c; font-weight: 700; }

.signatures { display: flex; justify-content: space-between; margin-top: 1.5rem; flex-wrap: wrap; gap: 0.5rem; }
.signature-box { flex: 1; min-width: 150px; border-top: 1px solid #004aad; padding-top: 0.3rem; text-align: center; font-weight: 600; }

.comment-date { margin-top: 1rem; display: flex; justify-content: space-between; flex-wrap: wrap; }
.comment, .date { flex: 1; min-width: 150px; margin: 0.3rem 0; font-weight: 600; }

.download-btn { margin-top: 1rem; display: block; background-color: #27ae60; color: #fff; border: none;
    padding: 10px 30px; font-weight: 700; border-radius: 25px; cursor: pointer; font-size: 0.95rem; }
.download-btn:hover { background-color: #1e8449; }
</style>
</head>
<body>
<header>
    <div class="logo">
        <img src="Royal logo.jpeg" alt="School Logo">
    </div>
    <div class="school-name">Royal Science Academy</div>
    <div class="motto">Innovate, Learn, Excel</div>
    <form method="GET">
        <button type="submit" name="logout" class="logout-btn">Logout</button>
    </form>
</header>

<main id="resultContent">
    <div class="student-info">
        <div><strong>Student Name:</strong> <?= htmlspecialchars($studentName) ?></div>
        <div><strong>Class:</strong> <?= htmlspecialchars($class) ?></div>
        <div><strong>Academic Year:</strong> <?= $academicYear ?></div>
    </div>

    <form method="POST">
        <label>Select Term:</label>
        <select name="term" onchange="this.form.submit()">
            <?php foreach(['First Term','Second Term','Third Term'] as $t): ?>
            <option value="<?= $t ?>" <?= $termSelected==$t?'selected':'' ?>><?= $t ?></option>
            <?php endforeach; ?>
        </select>
    </form>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Subject</th>
                    <?php foreach ($termsToShow as $term): ?>
                    <th>Assignment</th><th>Test</th><th>Exam</th><th>Total</th><th>Grade</th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach($grades as $subject => $termData): ?>
                <tr>
                    <td class="subject"><?= htmlspecialchars($subject) ?></td>
                    <?php foreach($termsToShow as $term): 
                        if(isset($termData[$term])): $g = $termData[$term]; ?>
                    <td><?= $g['assignment'] ?></td>
                    <td><?= $g['test'] ?></td>
                    <td><?= $g['exam'] ?></td>
                    <td><?= $g['total'] ?></td>
                    <td class="grade grade-<?= $g['letter'] ?>"><?= $g['letter'] ?></td>
                    <?php else: ?>
                    <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
                    <?php endif; endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Term Averages Table -->
    <?php if (!empty($termAverages)): ?>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Term</th>
                    <?php foreach ($termsToShow as $term): ?>
                    <th><?= htmlspecialchars($term) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Average</td>
                    <?php foreach ($termsToShow as $term): ?>
                        <td><?= isset($termAverages[$term]) ? $termAverages[$term] : '-' ?></td>
                    <?php endforeach; ?>
                </tr>
                <?php if ($overallAverage !== null): ?>
                <tr>
                    <td>Overall Average</td>
                    <td colspan="<?= count($termsToShow) ?>"><?= $overallAverage ?></td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <div class="signatures">
        <div class="signature-box">Teacher</div>
        <div class="signature-box">Principal</div>
        <div class="signature-box">Parent</div>
    </div>

    <div class="comment-date">
        <div class="comment"><strong>Comment:</strong> ___________________________</div>
        <div class="date"><strong>Date:</strong> <?= date("d/m/Y") ?></div>
    </div>

    <button class="download-btn" onclick="downloadResult()">Download Result</button>
</main>

<script>
function downloadResult() {
    const content = document.getElementById('resultContent').innerHTML;
    const mywindow = window.open('', 'Print', 'height=700,width=900');
    mywindow.document.write('<html><head><title>Student Result</title>');
    mywindow.document.write('<style>body{font-family:Segoe UI,Tahoma,Geneva,Verdana,sans-serif;font-size:14px;}table{width:100%;border-collapse:collapse;}th,td{border:1px solid #004aad;padding:6px;text-align:center;}th{background:#004aad;color:#fff;}td.subject{text-align:left;font-weight:600;color:#004aad;}.grade-A{color:#2ecc71;font-weight:700;}.grade-B{color:#3498db;font-weight:700;}.grade-C{color:#f1c40f;font-weight:700;}.grade-D{color:#e67e22;font-weight:700;}.grade-F{color:#e74c3c;font-weight:700;}</style>');
    mywindow.document.write('</head><body>');
    mywindow.document.write(content);
    mywindow.document.write('</body></html>');
    mywindow.document.close();
    mywindow.focus();
    mywindow.print();
    mywindow.close();
}
</script>
</body>
</html>
