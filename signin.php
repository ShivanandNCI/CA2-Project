<?php
// Set secure session cookie parameters
$cookieParams = session_get_cookie_params();
session_set_cookie_params([
    'lifetime' => $cookieParams['lifetime'],
    'path' => $cookieParams['path'],
    'domain' => $cookieParams['domain'],
    'secure' => true, // Ensure the cookie is sent over HTTPS
    'httponly' => true, // Prevent JavaScript access to session cookie
    'samesite' => 'Strict' // Prevent CSRF attacks
]);

session_start();
error_reporting(E_ALL);
include 'config.php';
use PHPMailer\PHPMailer\PHPMailer;
require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';

// Session timeout settings
$timeout_duration = 1800; // 30 minutes

// Check if the user is logged in and if the session has timed out
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    // Last request was more than 30 minutes ago
    session_unset(); // Unset $_SESSION variable for the run-time
    session_destroy(); // Destroy session data in storage
    header("Location: signin.php"); // Redirect to login page
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity time stamp

if (isset($_SESSION['user_id'])) {
    // auto redirect to index.php if user is admin
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT roles FROM users WHERE id='$user_id'";
    $query = mysqli_query($link, $sql);
    $data = mysqli_fetch_array($query);

    if ($data['roles'] == 'admin') {
        header("Location: index.php");
    } else {
        header("Location: user.php");
    }
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['login'])) {
        if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
            $email = mysqli_real_escape_string($link, $_POST['email']);
            $password = mysqli_real_escape_string($link, $_POST['password']);
            $_SESSION['email'] = $email;
            
            $sql = "SELECT * FROM users WHERE email='$email'";
            $query = mysqli_query($link, $sql);
            $data = mysqli_fetch_array($query);

            if ($data && password_verify($password, $data['password'])) {
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);

                $otp = rand(100000, 999999);
                $otp_expiry = date("Y-m-d H:i:s", strtotime("+3 minute"));
                $subject = "Your OTP for Login";
                $message = "Your OTP is: $otp";

                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'alab8438@gmail.com'; //host email 
                $mail->Password = 'cqdzxirjkwzabwre'; // app password of your host email
                $mail->Port = 587;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->isHTML(true);
                $mail->setFrom('alab8438@gmail.com', 'Computer Laboratory Management System'); //Sender's Email & Name
                $name = $data['name'] ?? 'User'; // Define the name variable
                $mail->addAddress($email, $name); //Receiver's Email and Name
                $mail->Subject = $subject;
                $mail->Body = $message;
               
                try {
                    $mail->send();
                } catch (Exception $e) {
                    echo "<script>alert('Mailer Error: {$mail->ErrorInfo}');</script>";
                }
                $sql1 = "UPDATE users SET otp='$otp', otp_expiry='$otp_expiry' WHERE id=" . $data['id'];
                $query1 = mysqli_query($link, $sql1);

                $_SESSION['temp_user'] = ['id' => $data['id'], 'otp' => $otp];
                $show_otp_form = true;
            } else {
                echo "<script>alert('Invalid Email or Password. Please try again.');</script>";
            }
        } else {
            echo "<script>alert('Invalid Captcha. Please try again.');</script>";
        }
    } elseif (isset($_POST['verify_otp'])) {
        $user_otp = filter_input(INPUT_POST, 'otp', FILTER_SANITIZE_NUMBER_INT);
        $stored_otp = $_SESSION['temp_user']['otp'];
        $user_id = $_SESSION['temp_user']['id'];

        $stmt = $link->prepare("SELECT * FROM users WHERE id=? AND otp=?");
        $stmt->bind_param("is", $user_id, $user_otp);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        if ($data) {
            $otp_expiry = strtotime($data['otp_expiry']);
            if ($otp_expiry >= time()) {
                $_SESSION['user_id'] = $data['id'];
                unset($_SESSION['temp_user']);

                if ($data['roles'] == 'admin') {
                    header("Location: index.php");
                } else {
                    header("Location: user.php");
                }
                exit();
            } else {
                echo "<script>alert('OTP has expired. Please try again.');</script>";
            }
        } else {
            echo "<script>alert('Invalid OTP. Please try again.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        function enablesubmitbtn() {
            document.getElementById("submit").disabled = false;
        }
    </script>
    <title>Login</title>
    <style type="text/css">
        body {
            background-image: url('background.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        #container {
            background-color: white;
            border: 1px solid black;
            width: 440px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        input[type=text], input[type=password], input[type=number] {
            width: 300px;
            height: 20px;
            padding: 10px;
        }
        label {
            font-size: 20px;
            font-weight: bold;
        }
        form {
            margin-left: 50px;
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
        input[type=submit], button {
            width: 70px;
            background-color: blue;
            border: 1px solid blue;
            color: white;
            font-weight: bold;
            padding: 7px;
            margin-left: 130px;
        }
        input[type=submit]:hover, button:hover {
            background-color: purple;
            cursor: pointer;
            border: 1px solid purple;
        }
    </style>
</head>
<body>
    <div id="container">
        <?php if (isset($show_otp_form) && $show_otp_form): ?>
            <h1>Two-Step Verification</h1>
            <p>Enter the 6 Digit OTP Code that has been sent <br> to your email address.</p>
            <form method="post" action="signin.php">
                <label style="font-weight: bold; font-size: 18px;" for="otp">Enter OTP Code:</label><br>
                <input type="number" name="otp" pattern="\d{6}" placeholder="Six-Digit OTP" required><br><br>
                <button type="submit" name="verify_otp">Verify OTP</button>
            </form>
        <?php else: ?>
            <form method="post" action="signin.php">
                <label for="email">Email</label><br>
                <input type="text" name="email" placeholder="Enter Your Email" required><br><br>
                <label for="password">Password:</label><br>
                <input type="password" name="password" placeholder="Enter Your Password" required><br><br>
                <!-- Google reCAPTCHA block -->
                <div class="g-recaptcha" data-sitekey="6Lcnn5EqAAAAAPPAlTqjznykTMTrj44vj5ZVxsXM" data-callback="enablesubmitbtn"></div>
                <input type="submit" id="submit" disabled="disabled" name="login" value="Login"><br><br>
                <label>Don't have an account? </label><a href="registration.php">Sign Up</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
