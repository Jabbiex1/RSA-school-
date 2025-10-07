<?php
session_start();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connect to the database
    $conn = new mysqli("localhost", "root", "", "royal_academy");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get and sanitize inputs
    $studentID = trim($conn->real_escape_string($_POST['student_id']));
    $parentPhone = trim($conn->real_escape_string($_POST['parent_phone']));

    // Check for match in parents table
    $sql = "SELECT * FROM parents WHERE student_id = '$studentID' AND parent_phone = '$parentPhone'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {
        $_SESSION['parent_student_id'] = $studentID;
        $_SESSION['parent_phone'] = $parentPhone;
        header("Location: parentdashboard.php");
        exit();
    } else {
        $error = "Invalid Student ID or Phone Number.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Parent Login - Royal Science Academy</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
<style>
  :root {
    --primary: #004aad;
    --primary-dark: #00317a;
    --accent: #ff9900;
    --white: #ffffff;
    --bg-light: #f7f9fc;
    --error: #d32f2f;
    --shadow: rgba(0, 74, 173, 0.15);
  }

  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }

  body, html {
    height: 100%;
    font-family: 'Poppins', sans-serif;
    background: var(--bg-light);
  }

  .container {
    display: flex;
    height: 100vh;
    overflow: hidden;
  }

  .left-panel {
    flex: 1.2;
    background: linear-gradient(135deg, var(--primary-dark), var(--primary));
    color: var(--white);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 2rem;
    position: relative;
  }

  .left-panel img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
    margin-bottom: 1.5rem;
  }

  .left-panel h1 {
    font-size: 2rem;
    font-weight: 800;
    text-align: center;
    line-height: 1.2;
    z-index: 2;
  }

  .blob1, .blob2 {
    position: absolute;
    border-radius: 50%;
    filter: blur(100px);
    opacity: 0.3;
    z-index: 0;
    animation: float 8s ease-in-out infinite;
  }

  .blob1 {
    width: 220px;
    height: 220px;
    background: #66a3ff;
    top: 15%;
    left: 10%;
    animation-delay: 0s;
  }

  .blob2 {
    width: 280px;
    height: 280px;
    background: #002f80;
    bottom: 10%;
    right: 15%;
    animation-delay: 4s;
  }

  @keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
  }

  .right-panel {
    flex: 1;
    background: var(--white);
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 2rem;
  }

  .login-box {
    width: 100%;
    max-width: 380px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 6px 24px rgba(0, 74, 173, 0.1);
    padding: 2.5rem;
    animation: fadeIn 0.6s ease;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .login-box h2 {
    text-align: center;
    color: var(--primary);
    font-size: 2rem;
    margin-bottom: 2rem;
    font-weight: 700;
  }

  input[type="text"] {
    width: 100%;
    padding: 12px 14px;
    margin-bottom: 1.2rem;
    border: 1.5px solid #ccc;
    border-radius: 10px;
    font-size: 1rem;
    transition: all 0.3s ease;
  }

  input[type="text"]:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 6px rgba(0, 74, 173, 0.4);
  }

  button {
    width: 100%;
    padding: 12px;
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--white);
    background: var(--primary);
    border: none;
    border-radius: 30px;
    cursor: pointer;
    transition: background 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 5px 15px rgba(0, 74, 173, 0.4);
  }

  button:hover {
    background: var(--primary-dark);
    box-shadow: 0 6px 20px rgba(0, 74, 173, 0.6);
  }

  .error {
    color: var(--error);
    font-weight: 600;
    margin-bottom: 1rem;
    text-align: center;
  }

  .back-icon {
    position: fixed;
    bottom: 20px;
    left: 20px;
    background-color: var(--primary);
    color: var(--white);
    padding: 8px 14px;
    border-radius: 22px;
    text-decoration: none;
    font-weight: bold;
    font-size: 1rem;
    box-shadow: 0 4px 12px rgba(0, 74, 173, 0.5);
    transition: background-color 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
    z-index: 10;
  }

  .back-icon:hover {
    background-color: var(--primary-dark);
  }

  @media (max-width: 900px) {
    .container {
      flex-direction: column;
    }

    .left-panel {
      display: none;
    }

    .right-panel {
      flex: none;
      width: 100%;
    }

    .back-icon {
      bottom: 10px;
      left: 10px;
    }
  }
</style>
</head>
<body>
  <div class="container" role="main" aria-label="Parent login section">
    <div class="left-panel" aria-hidden="true">
      <img src="Royal logo.jpeg" alt="Royal Science Academy Logo" />
      <h1>Royal Science Academy</h1>
      <div class="blob1"></div>
      <div class="blob2"></div>
    </div>

    <div class="right-panel">
      <div class="login-box">
        <h2>Parent Login</h2>
        <form method="POST" action="" novalidate>
          <input type="text" name="student_id" placeholder="Enter Student ID" required aria-required="true" autocomplete="off" />
          <input type="text" name="parent_phone" placeholder="Enter Parent Phone Number" required aria-required="true" autocomplete="off" />
          <button type="submit" aria-label="Login as parent">Login</button>
        </form>
        <?php if (isset($error)) : ?>
          <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <a href="portal-login.html" class="back-icon">&#8592;</a>
</body>
</html>
