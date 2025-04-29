<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="main.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <script>
        function validateForgotForm() {
            let email = document.forms["forgotPasswordForm"]["email"].value;
            if (email === "") {
                alert("Please enter your email address.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <h2>MINDGAMES</h2>
    <div class="login-container">

        

        <form class="login-form" name="forgotPasswordForm" action="forgot_password.php" method="POST" onsubmit="return validateForgotForm()">    
            <h3>Forgot Password</h3>
            <?php if (isset($_GET['success'])): ?>
            <div class="success-message">Your password has been emailed to you.</div>
        <?php else: ?>
            <label for="email">Enter your email address:</label>
            <input type="email" id="email" name="email"><br>

            <input type="submit" value="Reset Password">
            <?php endif; ?>
            <a href="index.php">Back to login</a>
        </form>
        
    </div>
</body>
</html>
