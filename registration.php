<?php
session_start();
include 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format');</script>";
        exit();
    }

    // Validate password (example: at least 8 characters, including at least one number and one special character)
    if (!preg_match('/^(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        echo "<script>alert('Password must be at least 8 characters long and include at least one number and one special character'); window.location.href='registration.php';</script>";
        exit();
    }

    // Check if passwords match
    if ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match'); window.location.href='registration.php';</script>";
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password
    
    // Use prepared statements to prevent SQL injection
    $stmt = $link->prepare("INSERT INTO users (roles, username, email, password) VALUES (?, ?, ?, ?)");
    $role = 'user';
    $stmt->bind_param("ssss", $role, $username, $email, $hashedPassword);

    if ($stmt->execute()) {
        ?>
        <script>
            alert("Registration Successful.");
            function navigateToPage() {
                window.location.href = 'signin.php';
            }
            window.onload = function() {
                navigateToPage();
            }
        </script>
        <?php 
    } else {
        echo "<script>alert('Registration Failed. Try Again');</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration</title>
    <style type="text/css">
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        #container {
            border: 1px solid black;
            width: 450px;
            padding: 20px;
        }
        form {
            margin-left: 50px;
        }
        input[type=text], input[type=password] {
            width: 300px;
            height: 20px;
            padding: 10px;
        }
        label {
            font-size: 20px;
            font-weight: bold;
        }
        a {
            text-decoration: none;
            font-weight: bold;
            font-size: 21px;
            color: blue;
        }
        a:hover {
            cursor: pointer;
            color: purple;
        }
        input[type=submit] {
            width: 70px;
            background-color: blue;
            border: 1px solid blue;
            color: white;
            font-weight: bold;
            padding: 7px;
            margin-left: 130px;
        }
        input[type=submit]:hover {
            background-color: purple;
            cursor: pointer;
            border: 1px solid purple;
        }
    </style>
</head>
<body>
    <div id="container">
        <form method="post" action="registration.php">
            <label for="username">Username:</label><br>
            <input type="text" name="username" placeholder="Enter Username" required><br><br>

            <label for="email">Email:</label><br>
            <input type="text" name="email" placeholder="Enter Your Email" required><br><br>

            <label for="password">Password:</label><br>
            <input type="password" name="password" placeholder="Enter Password" required><br><br>

            <label for="confirm_password">Confirm Password:</label><br>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required><br><br>

            <input type="submit" name="register" value="Register"><br><br>
            <label>Already have an account? </label><a href="signin.php">Login</a>
        </form>
    </div>
</body>
</html>