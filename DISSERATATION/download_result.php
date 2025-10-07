<?php
session_start();

if (!isset($_SESSION['studentID']) || !isset($_SESSION['class'])) {
    header("Location: student-login.php");
    exit();
}

require('fpdf.php'); // include FPDF library

$studentID = $_SESSION['studentID'];
$class = $_SESSION['class'];

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

    // Student info
    $stmt = $pdo->prepare("SELECT name FROM students WHERE id = ?");
    $stmt->execute([$studentID]);
    $student = $stmt->fetch();
    $studentName = $student['name'] ?? "Student";

    // Fetch grades for all terms
    $terms = ['First Term', 'Second Term', 'Third Term'];
    $gradesAll = [];
    $termAverages = [];

    foreach ($terms as $term) {
        $stmtGrades = $pdo->prepare("SELECT subject, assignment, test, exam FROM grades WHERE student_id = ? AND terms = ?");
        $stmtGrades->execute([$studentID, $term]);
        $grades = $stmtGrades->fetchAll();

        $totalTerm = 0;
        $countSubjects = 0;

        foreach ($grades as $row) {
            $assignment = is_numeric($row['assignment']) ? (float)$row['assignment'] : 0;
            $test = is_numeric($row['test']) ? (float)$row['test'] : 0;
            $exam = is_numeric($row['exam']) ? (float)$row['exam'] : 0;
            $total = $assignment + $test + $exam;

            $gradesAll[$term][] = [
                'subject' => $row['subject'],
                'assignment' => $assignment,
                'test' => $test,
                'exam' => $exam,
                'total' => $total
            ];

            $totalTerm += $total;
            $countSubjects++;
        }

        $termAverages[$term] = $countSubjects ? ($totalTerm / $countSubjects) : 0;
    }

    // Overall average (capped at 100)
    $overallAverage = number_format(min(array_sum($termAverages) / 3, 100), 2);

    // Overall position
    $stmtPos = $pdo->prepare("SELECT position FROM positions WHERE student_id = ? AND terms = 'Third Term'");
    $stmtPos->execute([$studentID]);
    $positionRow = $stmtPos->fetch();
    $overallPosition = $positionRow ? $positionRow['position'] : 'N/A';

} catch (PDOException $e) {
    die("Database error: ".$e->getMessage());
}

// Generate PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);

// Header
$pdf->Image('Royal logo.jpeg',10,10,25);
$pdf->Cell(0,10,'Royal Science Academy',0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,'Excellence in Education Since 1998',0,1,'C');
$pdf->Ln(10);

// Student info
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,6,"Student Name: $studentName",0,1);
$pdf->Cell(0,6,"Class: $class",0,1);
$pdf->Cell(0,6,"Academic Year: ".date('Y'),0,1);
$pdf->Ln(5);

// Table header
$pdf->SetFont('Arial','B',11);
$pdf->SetFillColor(52, 152, 219);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(50,8,'Subject',1,0,'C',true);
$pdf->Cell(30,8,'Assignment',1,0,'C',true);
$pdf->Cell(30,8,'Test',1,0,'C',true);
$pdf->Cell(30,8,'Exam',1,0,'C',true);
$pdf->Cell(30,8,'Total',1,0,'C',true);
$pdf->Cell(20,8,'Term',1,1,'C',true);

// Table content
$pdf->SetFont('Arial','',11);
$pdf->SetTextColor(0,0,0);
foreach ($terms as $term) {
    if(empty($gradesAll[$term])) continue;
    foreach ($gradesAll[$term] as $row) {
        $pdf->Cell(50,8,$row['subject'],1);
        $pdf->Cell(30,8,$row['assignment'],1,0,'C');
        $pdf->Cell(30,8,$row['test'],1,0,'C');
        $pdf->Cell(30,8,$row['exam'],1,0,'C');
        $pdf->Cell(30,8,$row['total'],1,0,'C');
        $pdf->Cell(20,8,$term,1,1,'C');
    }
}

// Overall summary
$pdf->Ln(5);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,6,"Term Averages: First: ".number_format($termAverages['First Term'],2).", Second: ".number_format($termAverages['Second Term'],2).", Third: ".number_format($termAverages['Third Term'],2),0,1);
$pdf->Cell(0,6,"Overall Average: $overallAverage",0,1);
$pdf->Cell(0,6,"Overall Position: $overallPosition",0,1);

// Signatures
$pdf->Ln(15);
$pdf->Cell(60,10,"Teacher's Signature",0,0,'C');
$pdf->Cell(60,10,"Principal's Signature",0,0,'C');
$pdf->Cell(60,10,"Parent's Signature",0,1,'C');

$pdf->Output('D','Result_'.$studentName.'.pdf');
