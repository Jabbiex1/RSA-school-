<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>School Admission Form</title>
    <style>
        /* Reset & base styling */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #74ebd5 0%, #ACB6E5 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .form-container {
            background: #ffffffcc; /* Slight transparency */
            padding: 40px 35px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 550px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .form-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.25);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #0b2545;
            font-weight: 700;
            font-size: 26px;
        }
        label { 
            display: block; 
            margin-top: 15px; 
            color: #0b2545; 
            font-weight: 600; 
            font-size: 14px;
        }
        label .required { color: red; margin-left: 2px; }
        input, select {
            width: 100%;
            padding: 14px 16px;
            margin-top: 5px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 15px;
            transition: border 0.3s ease, box-shadow 0.3s ease;
        }
        input:focus, select:focus {
            border: 2px solid #007BFF;
            box-shadow: 0 0 5px rgba(0,123,255,0.5);
            outline: none;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            gap: 10px;
            flex-wrap: wrap;
        }
        button {
            padding: 15px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            flex: 1 1 48%;
        }
        button.submit-btn {
            background: linear-gradient(90deg, #007BFF, #00c6ff);
            color: #fff;
        }
        button.submit-btn:hover {
            background: linear-gradient(90deg, #0056b3, #00a3cc);
        }
        button.back-btn {
            background: #6c757d;
            color: #fff;
        }
        button.back-btn:hover {
            background: #5a6268;
        }
        .message {
            margin-top: 20px;
            padding: 14px;
            border-radius: 10px;
            text-align: center;
            font-weight: 500;
            font-size: 14px;
        }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }

        /* Responsive adjustments */
        @media (max-width: 600px) {
            .form-container {
                padding: 30px 20px;
            }
            h2 {
                font-size: 22px;
            }
            button {
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body>

<?php
$message = "";
$message_class = "";
$full_name = $dob = $gender = $email = $phone = $previous_school = $applying_class = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dsn = "mysql:host=localhost;dbname=royal_academy;charset=utf8";
    $user = "root";
    $pass = "";

    try {
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Sanitize inputs
        $full_name = trim($_POST['full_name']);
        $dob = $_POST['dob'];
        $gender = $_POST['gender'];
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $previous_school = trim($_POST['previous_school']);
        $applying_class = trim($_POST['applying_class']);

        // Validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid email format.";
            $message_class = "error";
        } elseif (!preg_match("/^[0-9]{7,15}$/", $phone)) {
            $message = "Phone number should contain only digits (7-15 digits).";
            $message_class = "error";
        } else {
            // Check duplicate in pending_students
            $stmt = $pdo->prepare("SELECT * FROM pending_students WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $message = "An application with this email already exists in pending list.";
                $message_class = "error";
            } else {
                // Insert into pending_students
                $stmt = $pdo->prepare("
                    INSERT INTO pending_students (name, dob, class, email, phone, previous_school)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$full_name, $dob, $applying_class, $email, $phone, $previous_school]);

                $message = "Application submitted successfully! Your admission is pending approval.";
                $message_class = "success";

                // Clear form
                $full_name = $dob = $gender = $email = $phone = $previous_school = $applying_class = "";
            }
        }
    } catch (PDOException $e) {
        $message = "Database error: " . $e->getMessage();
        $message_class = "error";
    }
}
?>

<div class="form-container">
    <h2>Admission Form</h2>

    <?php if($message != ""): ?>
        <div class="message <?php echo $message_class; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <label>Full Name<span class="required">*</span>:</label>
        <input type="text" name="full_name" value="<?php echo htmlspecialchars($full_name ?? ''); ?>" required>

        <label>Date of Birth<span class="required">*</span>:</label>
        <input type="date" name="dob" value="<?php echo htmlspecialchars($dob ?? ''); ?>" required>

        <label>Gender<span class="required">*</span>:</label>
        <select name="gender" required>
            <option value="">Select</option>
            <option value="Male" <?php if(($gender ?? '')=="Male") echo "selected"; ?>>Male</option>
            <option value="Female" <?php if(($gender ?? '')=="Female") echo "selected"; ?>>Female</option>
        </select>

        <label>Email<span class="required">*</span>:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>

        <label>Phone<span class="required">*</span>:</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($phone ?? ''); ?>" required>

        <label>Previous School<span class="required">*</span>:</label>
        <input type="text" name="previous_school" value="<?php echo htmlspecialchars($previous_school ?? ''); ?>" required>

        <label>Class Applying For<span class="required">*</span>:</label>
        <input type="text" name="applying_class" value="<?php echo htmlspecialchars($applying_class ?? ''); ?>" required>

        <div class="button-group">
            <button type="submit" class="submit-btn">Submit Application</button>
            <button type="button" class="back-btn" onclick="window.location.href='home.html';">Back to Home</button>
        </div>
    </form>
</div>

</body>
</html>
