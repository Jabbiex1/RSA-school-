<?php
// Start session
session_start();

// DB connection
$host = 'localhost';
$db = 'royal_academy';
$user = 'root';
$pass = ''; // Set this if you use a MySQL password

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Handle login
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();
    if (password_verify($password, $admin['password'])) {
      $_SESSION['admin'] = $admin['username'];
      header("Location: admin.php"); // Redirect to admin panel
      exit;
    } else {
      $login_error = "Invalid password.";
    }
  } else {
    $login_error = "Admin not found.";
  }
  $stmt->close();
}
?>
<?php
// (PHP login handling remains the same, omitted here for brevity)
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Login - Royal Science Academy</title>
  <style>
    :root {
      --primary: #004aad;
      --primary-dark: #002e7a;
      --white: #ffffff;
      --font: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    * {
      box-sizing: border-box;
    }

    body, html {
      margin: 0;
      padding: 0;
      height: 100%;
      width: 100%;
      font-family: var(--font);
      background: #f0f4fc;
      overflow: hidden;
    }

    .container {
      display: flex;
      height: 100vh;
      width: 100vw;
      /* fill entire screen */
    }

    .left-panel, .right-panel {
      flex: 1; /* equal width */
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 3rem 4rem;
      /* make sure content is centered inside */
    }

    .left-panel {
      background: linear-gradient(135deg, var(--primary-dark), var(--primary));
      color: var(--white);
      text-align: center;
      position: relative;
    }

    .left-panel img {
      width: 140px;
      height: 140px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 1.5rem;
      box-shadow: 0 8px 20px rgba(0,0,0,0.25);
    }

    .left-panel h1 {
      font-size: 2.8rem;
      font-weight: 900;
      margin: 0 0 1rem 0;
      line-height: 1.1;
    }

    .left-panel p {
      font-size: 1.1rem;
      line-height: 1.4;
      max-width: 320px;
      margin: 0 auto;
      opacity: 0.85;
    }

    .right-panel {
      background-color: var(--white);
      box-shadow: 0 8px 30px rgba(0,74,173,0.1);
      animation: fadeInUp 0.8s ease forwards;
      transform: translateY(40px);
      opacity: 0;
      border-radius: 0 0 0 30px; /* subtle rounded corner on left bottom */
    }

    @keyframes fadeInUp {
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .login-container {
      width: 100%;
      max-width: 360px;
      margin: 0 auto;
    }

    h2 {
      margin-bottom: 24px;
      color: var(--primary);
      font-weight: 700;
      font-size: 2rem;
    }

    label {
      display: block;
      margin: 15px 0 6px;
      font-weight: 600;
      text-align: left;
      color: var(--primary);
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 12px;
      border-radius: 8px;
      border: 1.5px solid #ccc;
      font-size: 16px;
      transition: border-color 0.3s ease;
    }

    input[type="text"]:focus,
    input[type="password"]:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 8px rgba(0, 74, 173, 0.3);
    }

    button {
      margin-top: 28px;
      width: 100%;
      padding: 14px;
      border: none;
      border-radius: 12px;
      background-color: var(--primary);
      color: var(--white);
      font-size: 1.2rem;
      font-weight: 700;
      cursor: pointer;
      box-shadow: 0 6px 15px rgba(0,74,173,0.4);
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    button:hover {
      background-color: var(--primary-dark);
      box-shadow: 0 8px 22px rgba(0,74,173,0.5);
    }

    .error {
      color: #e53935;
      margin-top: 14px;
      font-weight: 600;
      font-size: 0.95rem;
      text-align: center;
    }

    .back-icon {
      position: fixed;
      bottom: 20px;
      left: 20px;
      background-color: var(--primary);
      color: var(--white);
      font-weight: 700;
      font-size: 1.1rem;
      padding: 8px 16px;
      border-radius: 30px;
      text-decoration: none;
      box-shadow: 0 5px 15px rgba(0,74,173,0.6);
      user-select: none;
      transition: background-color 0.3s ease;
      display: flex;
      align-items: center;
      gap: 8px;
      z-index: 1000;
    }

    .back-icon:hover {
      background-color: var(--primary-dark);
    }

    /* Responsive */
    @media (max-width: 950px) {
      .container {
        flex-direction: column;
        height: auto;
      }

      .left-panel, .right-panel {
        width: 100%;
        padding: 3rem 2rem;
        border-radius: 0;
      }
    }
  </style>
</head>
<body>
  <div class="container" role="main" aria-label="Admin Login Panel">
    <section class="left-panel" aria-hidden="true">
      <img src="Royal logo.jpeg" alt="Royal Science Academy Logo" />
      <h1>Royal Science Academy</h1>
      <p>Welcome to the Royal Science Academy administration portal.<br />
         Please login to manage the school data and system settings.</p>
    </section>

    <section class="right-panel">
      <div class="login-container">
        <h2>Admin Login</h2>
        <form method="POST" action="" novalidate>
          <label for="username">Username</label>
          <input type="text" id="username" name="username" required autocomplete="off" />

          <label for="password">Password</label>
          <input type="password" id="password" name="password" required autocomplete="off" />

          <button type="submit">Login</button>
          <?php if ($login_error): ?>
            <div class="error"><?php echo htmlspecialchars($login_error); ?></div>
          <?php endif; ?>
        </form>
      </div>
    </section>
  </div>

 <a href="portal-login.html" class="back-icon" title="Go Back">&#8592;</a>
  </a>
</body>
</html>
