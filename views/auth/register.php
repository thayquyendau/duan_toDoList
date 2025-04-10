<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .signup-container {
            max-width: 400px;
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }
        .form-control {
            padding-left: 40px;
        }
        .input-group-text {
            background: none;
            border: none;
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }
        .signup-btn {
            background: #764ba2;
            border: none;
            transition: 0.3s;
        }
        .signup-btn:hover {
            background: #5a3d87;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2 class="text-center mb-4">Create an Account</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" onsubmit="return validatePassword()">
            <div class="mb-3 position-relative">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
            </div>
            <div class="mb-3 position-relative">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
            </div>
            <div class="mb-3 position-relative">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
            </div>
            <div class="mb-3 position-relative">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
            </div>
            <button type="submit" class="btn signup-btn w-100 text-white">Sign Up</button>
        </form>

        <div class="text-center mt-3">
            <small>Already have an account? <a href="?action=login" class="text-primary">Login</a></small>
        </div>
    </div>

    <script>
        function validatePassword() {
            var password = document.getElementById("password").value;
            var confirm_password = document.getElementById("confirm_password").value;
            if (password !== confirm_password) {
                alert("Passwords do not match!");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
