<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "royal_academy";

$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentID = trim($_POST['studentID'] ?? '');
    $class = trim($_POST['class'] ?? '');

    if (empty($studentID) || empty($class)) {
        $errorMsg = "Please fill in all required fields.";
    } else {
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Database connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("SELECT id, class FROM students WHERE id = ? AND class = ?");
        $stmt->bind_param("ss", $studentID, $class);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $_SESSION['studentID'] = $studentID;
            $_SESSION['class'] = $class;
            header("Location: studentdashboard.php");
            exit();
        } else {
            $errorMsg = "Invalid Student ID or Class.";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Student Login - Royal Science Academy</title>
  <style>
    :root {
      --primary: #004aad;
      --white: #ffffff;
      --light-gray: #f7faff;
      --error: #d9534f;
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      display: flex;
      height: 100vh;
    }

    .split-left {
      flex: 1;
      background: linear-gradient(145deg, #004aad, #003380);
      color: var(--white);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 40px 20px;
    }

    .split-left img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      margin-bottom: 20px;
    }

    .split-left h1 {
      font-size: 1.8rem;
      margin: 0;
      text-align: center;
    }

    .split-left p {
      margin-top: 10px;
      font-size: 1rem;
      text-align: center;
      opacity: 0.9;
    }

    .split-right {
      flex: 1;
      background-color: var(--light-gray);
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px 20px;
    }

    .login-box {
      background: var(--white);
      padding: 35px 30px;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
    }

    .login-box h2 {
      color: var(--primary);
      margin-bottom: 25px;
      font-size: 1.6rem;
      text-align: center;
    }

    label {
      display: block;
      margin-bottom: 6px;
      color: #222;
      font-weight: 600;
    }

    input {
      width: 100%;
      padding: 12px 14px;
      border: 1.5px solid #ccc;
      border-radius: 8px;
      margin-bottom: 20px;
      font-size: 1rem;
    }

    input:focus {
      border-color: var(--primary);
      outline: none;
      box-shadow: 0 0 5px rgba(0, 74, 173, 0.4);
    }

    .error-msg {
      color: var(--error);
      font-size: 0.9rem;
      margin-top: -10px;
      margin-bottom: 15px;
      font-weight: 600;
    }

    button {
      width: 100%;
      padding: 12px;
      background-color: var(--primary);
      color: var(--white);
      border: none;
      border-radius: 10px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #003380;
    }

    .back-icon {
      position: fixed;
      bottom: 15px;
      left: 15px;
      background-color: var(--primary);
      color: var(--white);
      font-weight: bold;
      font-size: 1.1rem;
      padding: 6px 12px;
      border-radius: 50px;
      text-decoration: none;
      box-shadow: 0 4px 10px rgba(0, 74, 173, 0.4);
    }

    .back-icon:hover {
      background-color: #00285f;
    }

    @media (max-width: 768px) {
      body {
        flex-direction: column;
      }

      .split-left, .split-right {
        flex: unset;
        width: 100%;
        height: auto;
      }

      .split-left {
        padding: 30px 20px;
      }
    }
  </style>
</head>
<body>
  <div class="split-left">
    <img src="Royal logo.jpeg" alt="Royal Science Academy Logo" />
    <h1>Royal Science Academy</h1>
    <p>Welcome to your personalized student portal. Login to access your academic dashboard.</p>
  </div>

  <div class="split-right">
    <div class="login-box">
      <h2>Student Login</h2>
      <form method="POST" action="student-login.php" novalidate>
        <label for="studentID">Student ID</label>
        <input type="text" id="studentID" name="studentID" placeholder="Enter your Student ID" required value="<?php echo htmlspecialchars($_POST['studentID'] ?? '') ?>" />

        <label for="class">Class</label>
        <input type="text" id="class" name="class" placeholder="Enter your Class" required value="<?php echo htmlspecialchars($_POST['class'] ?? '') ?>" />

        <div class="error-msg"><?php echo $errorMsg ?></div>

        <button type="submit">Login</button>
      </form>
    </div>
  </div>

  <a href="portal-login.html" class="back-icon" title="Go Back">&#8592;</a>
</body>
</html>
