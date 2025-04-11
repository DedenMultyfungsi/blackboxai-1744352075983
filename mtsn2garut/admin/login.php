<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            $_SESSION['admin_role'] = $user['role'];
            $_SESSION['admin_created'] = $user['created_at'];
            
            // Log successful login
            log_activity(
                'Login', 
                'Successful login to admin panel', 
                $user['id']
            );
            
            header("Location: dashboard.php");
            exit();
        } else {
            // Log failed login attempt
            log_activity(
                'Login Failed', 
                'Failed login attempt with username: ' . $username, 
                null
            );
            
            $error = "Username atau password salah!";
        }
    } catch(PDOException $e) {
        $error = "Terjadi kesalahan: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - MTsN 2 Garut</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- AdminLTE Theme style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    
    <style>
    .login-page {
        background: linear-gradient(135deg, #0d6efd 0%, #0043a8 100%);
    }
    .login-box {
        margin-top: 0;
    }
    .login-logo {
        margin-bottom: 25px;
    }
    .login-logo a {
        color: white;
    }
    .card {
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    .login-card-body {
        border-radius: 10px;
        padding: 30px;
    }
    .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    .btn-primary:hover {
        background-color: #0043a8;
        border-color: #0043a8;
    }
    .input-group-text {
        background-color: transparent;
        border-right: none;
    }
    .form-control {
        border-left: none;
    }
    .form-control:focus {
        border-color: #80bdff;
        box-shadow: none;
    }
    </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="<?php echo BASE_URL; ?>"><b>MTsN 2</b> Garut</a>
    </div>
    
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Masuk ke Panel Admin</p>

            <?php if(isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <form action="" method="post">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fas fa-user"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control" name="username" placeholder="Username" required>
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                    </div>
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember">
                            <label for="remember">
                                Ingat Saya
                            </label>
                        </div>
                    </div>
                    <div class="col-4">
                        <button type="submit" name="login" class="btn btn-primary btn-block">Masuk</button>
                    </div>
                </div>
            </form>

            <p class="mb-1 mt-3">
                <a href="forgot-password.php">Lupa password?</a>
            </p>
            <p class="mb-0">
                <a href="<?php echo BASE_URL; ?>" class="text-center">Kembali ke Beranda</a>
            </p>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
