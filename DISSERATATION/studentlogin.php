<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database config
$host = 'localhost';
$dbName = 'royal_academy';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// Check if this is an API call (has action param)
$action = $_GET['action'] ?? null;

if ($action === 'register' || $action === 'login') {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=$charset", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdo->exec("USE royal_academy");

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if ($action === 'register') {
        // Check required fields including new ones: class, dob
        if (!isset($data['username'], $data['email'], $data['password'], $data['class'], $data['dob'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input']);
            exit;
        }

        $username = trim($data['username']);
        $email = strtolower(trim($data['email']));
        $password = $data['password'];
        $class = trim($data['class']);
        $dob = trim($data['dob']); // Expecting 'YYYY-MM-DD' format

        // Check if email exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM registered_students WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            http_response_code(409);
            echo json_encode(['error' => 'Email already registered']);
            exit;
        }

        // Generate unique student ID starting with 90200 + 4 digits
        do {
            $studentID = "90200" . random_int(1000, 9999);
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM registered_students WHERE student_id = ?");
            $stmt->execute([$studentID]);
        } while ($stmt->fetchColumn() > 0);

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Insert student with new fields class and dob
        $stmt = $pdo->prepare("INSERT INTO registered_students (student_id, name, email, password, class, dob) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$studentID, $username, $email, $passwordHash, $class, $dob]);

        echo json_encode(['message' => 'Registration successful', 'studentID' => $studentID]);
        exit;

    } elseif ($action === 'login') {
        if (!isset($data['studentID'], $data['email'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Student ID and Email are required']);
            exit;
        }

        $studentID = trim($data['studentID']);
        $email = strtolower(trim($data['email']));

        $stmt = $pdo->prepare("SELECT student_id, name, email FROM registered_students WHERE student_id = ? AND email = ?");
        $stmt->execute([$studentID, $email]);
        $user = $stmt->fetch();

        if ($user) {
            echo json_encode(['message' => 'Login successful', 'username' => $user['name'], 'studentID' => $user['student_id']]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid Student ID or Email']);
        }
        exit;
    }
}

// If no action param, serve the HTML page:

header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Student Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    /* Your existing CSS unchanged */
    * {
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background-color: #e9f1fc;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
      padding: 20px;
    }

    .container {
      background-color: #ffffff;
      padding: 30px;
      width: 370px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0, 102, 204, 0.2);
    }

    h2 {
      text-align: center;
      color: #004080;
      margin-bottom: 15px;
    }

    input {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border: 1px solid #cce0ff;
      border-radius: 6px;
      font-size: 15px;
    }

    button {
      width: 100%;
      padding: 10px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 6px;
      margin-top: 10px;
      font-weight: bold;
      cursor: pointer;
      font-size: 16px;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #0056b3;
    }

    .toggle-btn {
      background: none;
      border: none;
      color: #007bff;
      cursor: pointer;
      margin-top: 10px;
      text-align: center;
      display: block;
      font-size: 14px;
    }

    .form {
      display: none;
    }

    .form.active {
      display: block;
    }

    .error {
      color: red;
      font-size: 13px;
      text-align: left;
      margin-top: 5px;
      min-height: 18px;
    }

    .success {
      color: green;
      font-size: 13px;
      text-align: center;
      min-height: 18px;
    }

    #logoutBtn {
      background-color: #cc0000;
      margin-top: 20px;
      font-weight: normal;
      display: none;
    }

    @media (max-width: 400px) {
      .container {
        width: 95%;
        padding: 20px;
      }
    }
  </style>
</head>
<body>

<div class="container">
  <div id="loginForm" class="form active">
    <h2>Student Login</h2>
    <input type="text" id="loginID" placeholder="Student ID" autocomplete="username" />
    <input type="email" id="loginEmail" placeholder="Email" autocomplete="email" />
    <button onclick="login()">Login</button>
    <button class="toggle-btn" onclick="toggleForms()">Don't have an account? Register</button>
    <p class="error" id="loginError"></p>
  </div>

  <div id="registerForm" class="form">
    <h2>Student Register</h2>
    <input type="text" id="regUsername" placeholder="Full Name" autocomplete="username" />
    <input type="text" id="regClass" placeholder="Class (e.g. Grade 10)" />
    <input type="date" id="regDOB" placeholder="Date of Birth" />
    <input type="email" id="regEmail" placeholder="Email" autocomplete="email" />
    <input type="password" id="regPassword" placeholder="Password" autocomplete="new-password" />
    <input type="password" id="regConfirmPassword" placeholder="Confirm Password" autocomplete="new-password" />
    <button onclick="register()">Register</button>
    <button class="toggle-btn" onclick="toggleForms()">Already have an account? Login</button>
    <p class="error" id="regError"></p>
    <p class="success" id="regSuccess"></p>
  </div>

  <button id="logoutBtn" onclick="logout()">Logout</button>
</div>

<script>
  function toggleForms() {
    const loginForm = document.getElementById("loginForm");
    const registerForm = document.getElementById("registerForm");
    const loginError = document.getElementById("loginError");
    const regError = document.getElementById("regError");
    const regSuccess = document.getElementById("regSuccess");

    loginError.textContent = "";
    regError.textContent = "";
    regSuccess.textContent = "";

    loginForm.classList.toggle("active");
    registerForm.classList.toggle("active");
  }

  function validateRegister(username, className, dob, email, password, confirmPassword) {
    if (username.length < 3) return "Name must be at least 3 characters.";
    if (className.length < 1) return "Class is required.";
    if (!dob) return "Date of Birth is required.";
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) return "Invalid email address.";
    if (password.length < 6) return "Password must be at least 6 characters.";
    if (!/[a-zA-Z]/.test(password)) return "Password must contain at least one letter.";
    if (!/\d/.test(password)) return "Password must contain at least one number.";
    if (password !== confirmPassword) return "Passwords do not match.";
    return "";
  }

  async function register() {
    const username = document.getElementById("regUsername").value.trim();
    const className = document.getElementById("regClass").value.trim();
    const dob = document.getElementById("regDOB").value;
    const email = document.getElementById("regEmail").value.trim().toLowerCase();
    const password = document.getElementById("regPassword").value;
    const confirmPassword = document.getElementById("regConfirmPassword").value;
    const regError = document.getElementById("regError");
    const regSuccess = document.getElementById("regSuccess");

    regError.textContent = "";
    regSuccess.textContent = "";

    const validationError = validateRegister(username, className, dob, email, password, confirmPassword);
    if (validationError) {
      regError.textContent = validationError;
      return;
    }

    try {
      const res = await fetch('?action=register', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, class: className, dob, email, password }),
      });
      const result = await res.json();

      if (res.ok) {
        regSuccess.textContent = `Registration successful! Your Student ID is ${result.studentID}`;

        // Clear inputs
        document.getElementById("regUsername").value = "";
        document.getElementById("regClass").value = "";
        document.getElementById("regDOB").value = "";
        document.getElementById("regEmail").value = "";
        document.getElementById("regPassword").value = "";
        document.getElementById("regConfirmPassword").value = "";
      } else {
        regError.textContent = result.error || "Registration failed";
      }
    } catch (err) {
      console.log(err)
      regError.textContent = "Network error: " + err.message;
    }
  }

  async function login() {
    const studentID = document.getElementById("loginID").value.trim();
    const email = document.getElementById("loginEmail").value.trim().toLowerCase();
    const loginError = document.getElementById("loginError");

    loginError.textContent = "";

    if (!studentID || !email) {
      loginError.textContent = "Please enter Student ID and Email.";
      return;
    }

    try {
      const res = await fetch('?action=login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ studentID, email }),
      });
      const result = await res.json();

      if (res.ok) {
        alert(`Login successful! Welcome, ${result.username}`);
        localStorage.setItem("loggedInUserID", result.studentID);
        window.location.href = "studentdashboard.php";
      } else {
        loginError.textContent = result.error || "Login failed";
      }
    } catch (err) {
      loginError.textContent = "Network error: " + err.message;
    }
  }

  function logout() {
    localStorage.removeItem("loggedInUserID");
    alert("You have been logged out.");
    window.location.reload();
  }
</script>

</body>
</html>
