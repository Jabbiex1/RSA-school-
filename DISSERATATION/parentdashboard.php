<?php
session_start();

if (!isset($_SESSION['parent_student_id']) || !isset($_SESSION['parent_phone'])) {
    header("Location: parent-login.php");
    exit();
}

$studentID = $_SESSION['parent_student_id'];
$parentEmail = $_SESSION['parent_phone'];

$conn = new mysqli("localhost", "root", "", "royal_academy");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// Fetch student name and class
$studentName = $studentClass = 'Unknown';
$stmt = $conn->prepare("SELECT name, class FROM students WHERE id = ?");
$stmt->bind_param("s", $studentID);
$stmt->execute();
$stmt->bind_result($nameResult, $classResult);
if ($stmt->fetch()) { $studentName = $nameResult; $studentClass = $classResult; }
$stmt->close();

// Fetch terms
$terms = [];
$term_query = "SELECT DISTINCT terms FROM grades WHERE student_id = ? ORDER BY terms ASC";
$stmt = $conn->prepare($term_query);
$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) { $terms[] = $row['terms']; }
$stmt->close();

// Selected term
$selectedTerm = $_GET['term'] ?? ($terms[0] ?? '');
$grades = [];

if ($selectedTerm !== '') {
    $grade_query = "SELECT subject, assignment, test, exam FROM grades WHERE student_id = ? AND terms = ?";
    $stmt = $conn->prepare($grade_query);
    $stmt->bind_param("ss", $studentID, $selectedTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) { $grades[] = $row; }
    $stmt->close();
}

$conn->close();

function getLetterGrade($total){
    if($total>=90) return 'A';
    if($total>=80) return 'B';
    if($total>=70) return 'C';
    if($total>=60) return 'D';
    return 'F';
}

$academicYear = "2024/2025";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Parent Dashboard - Royal Science Academy</title>
<style>
/* Fonts & Root */
:root {
    --blue:#0a4d8c; --gold:#ffd700; --bg:#f4f6f9; --text:#333;
    --white:#fff; --shadow:rgba(0,0,0,0.1); --radius:10px;
}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Segoe UI',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex;flex-direction:column;}

/* Header */
header{
    display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;background:var(--white);padding:1rem 2rem;box-shadow:0 4px 12px var(--shadow);
}
.logo img{height:60px;border-radius:8px;}
.school-name{flex:1;text-align:center;font-size:1.8rem;font-weight:700;color:var(--blue);}
.motto{color:var(--gold);font-weight:700;text-align:right;white-space:nowrap;}
.logout-btn{background:var(--blue);color:var(--white);padding:8px 18px;border:none;border-radius:25px;font-weight:700;cursor:pointer;transition:0.3s;}
.logout-btn:hover{background:#084d7a;}

/* Main container */
main{max-width:1000px;margin:2rem auto;background:var(--white);padding:2rem;border-radius:var(--radius);box-shadow:0 8px 24px var(--shadow);}

/* Info cards */
.student-info{display:flex;justify-content:space-between;flex-wrap:wrap;margin-bottom:1.5rem;color:var(--blue);font-weight:600;}
.student-info div{margin:0.3rem 0;padding:0.5rem 1rem;background:#e6f0ff;border-radius:var(--radius);flex:1;text-align:center;min-width:140px;}

/* Form select */
form select{padding:0.4rem 0.8rem;font-size:1rem;border-radius:6px;border:1.5px solid var(--blue);font-weight:600;margin-bottom:1rem;}

/* Table */
.table-container{overflow-x:auto;}
table{width:100%;border-collapse:collapse;}
th,td{border:1px solid var(--blue);padding:8px;text-align:center;}
th{background:var(--blue);color:white;font-weight:700;}
td.subject{text-align:left;font-weight:600;color:var(--blue);}
tbody tr:nth-child(even){background:#e6f0ff;}
tbody tr:hover{background:#1e90ff;color:white;transition:0.3s;}
.grade-A{color:#2ecc71;font-weight:700;}
.grade-B{color:#3498db;font-weight:700;}
.grade-C{color:#f1c40f;font-weight:700;}
.grade-D{color:#e67e22;font-weight:700;}
.grade-F{color:#e74c3c;font-weight:700;}

/* Extra info */
.extra-info{margin-top:2rem;padding:1rem 1.5rem;background:#e6f0ff;border-radius:var(--radius);}
.extra-info h4{color:var(--blue);margin-bottom:0.5rem;}
.extra-info p{color:#333;font-size:0.95rem;line-height:1.4;}

/* Download button */
.download-btn{margin-top:1.5rem;background:#27ae60;color:white;border:none;padding:10px 25px;font-weight:700;border-radius:25px;cursor:pointer;box-shadow:0 4px 12px rgba(39,174,96,0.6);transition:0.3s;}
.download-btn:hover{background:#1e8449;transform:scale(1.05);}

/* Responsive */
@media(max-width:700px){
    header{flex-direction:column;align-items:center;gap:0.5rem;}
    .school-name,.motto{text-align:center;}
    .student-info{flex-direction:column;gap:0.5rem;}
    form select{width:100%;}
}
</style>
</head>
<body>

<header>
    <div class="logo"><img src="Royal logo.jpeg" alt="School Logo"></div>
    <div class="school-name">Royal Science Academy</div>
    <div class="motto">Innovate, Learn, Excel</div>
    <form method="GET" style="margin-left:1rem;">
       <a href="portal-login.html" class="logout-btn">Logout</a>

    </form>
</header>

<main>
    <div class="student-info">
        <div><strong>Parent Email:</strong><br><?= htmlspecialchars($parentEmail) ?></div>
        <div><strong>Student Name:</strong><br><?= htmlspecialchars($studentName) ?></div>
        <div><strong>Student ID:</strong><br><?= htmlspecialchars($studentID) ?></div>
        <div><strong>Class:</strong><br><?= htmlspecialchars($studentClass) ?></div>
        <div><strong>Academic Year:</strong><br><?= $academicYear ?></div>
    </div>

    <form method="GET">
        <label for="term">Select Term:</label>
        <select name="term" id="term" onchange="this.form.submit()">
            <?php foreach($terms as $term): ?>
                <option value="<?= htmlspecialchars($term) ?>" <?= ($term === $selectedTerm)?'selected':'' ?>><?= htmlspecialchars($term) ?></option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if(!empty($grades)): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Assignment</th>
                        <th>Test</th>
                        <th>Exam</th>
                        <th>Total</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($grades as $grade):
                        $assignment=$grade['assignment']; $test=$grade['test']; $exam=$grade['exam'];
                        $total=$assignment+$test+$exam;
                        $letter=getLetterGrade($total);
                    ?>
                    <tr>
                        <td class="subject"><?= htmlspecialchars($grade['subject']) ?></td>
                        <td><?= $assignment ?></td>
                        <td><?= $test ?></td>
                        <td><?= $exam ?></td>
                        <td><?= $total ?></td>
                        <td class="grade-<?= $letter ?>"><?= $letter ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <section class="extra-info">
            <h4>Additional Information</h4>
            <p><strong>Attendance:</strong> 92%</p>
            <p><strong>Teacher's Remarks:</strong> <?= htmlspecialchars($studentName) ?> is showing improvement in class participation and homework submission.</p>
            <p><strong>Next Parent-Teacher Meeting:</strong> 25th August 2025</p>
            <p><strong>Upcoming Events:</strong> Science Fair on 10th September 2025</p>
        </section>

        <button class="download-btn" onclick="downloadResult()">Download Grades</button>
    <?php else: ?>
        <p class="no-results">No grades found for the selected term.</p>
    <?php endif; ?>

</main>

<script>
function downloadResult(){
    const content = document.querySelector('main').innerHTML;
    const mywindow = window.open('', 'Print', 'height=700,width=900');
    mywindow.document.write('<html><head><title>Student Grades</title>');
    mywindow.document.write('<style>body{font-family:Segoe UI,sans-serif;} table{width:100%;border-collapse:collapse;} th,td{border:1px solid #004aad;padding:8px;text-align:center;} th{background:#004aad;color:white;} td.subject{text-align:left;font-weight:600;color:#004aad;} .grade-A{color:#2ecc71;font-weight:700;} .grade-B{color:#3498db;font-weight:700;} .grade-C{color:#f1c40f;font-weight:700;} .grade-D{color:#e67e22;font-weight:700;} .grade-F{color:#e74c3c;font-weight:700;}</style>');
    mywindow.document.write('</head><body>');
    mywindow.document.write(content);
    mywindow.document.write('</body></html>');
    mywindow.document.close(); mywindow.focus(); mywindow.print(); mywindow.close();
}
</script>

</body>
</html>
