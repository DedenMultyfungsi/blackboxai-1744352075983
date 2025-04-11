<?php
require_once 'includes/header.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    try {
        // Get current user's password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        $user = $stmt->fetch();
        
        // Verify current password
        if (!password_verify($current_password, $user['password'])) {
            // Log invalid current password attempt
            log_activity(
                'Password Change Failed',
                'Invalid current password provided during password change attempt',
                $_SESSION['admin_id']
            );
            $_SESSION['error'] = "Password saat ini tidak sesuai!";
        }
        // Check if new passwords match
        elseif ($new_password !== $confirm_password) {
            // Log password mismatch
            log_activity(
                'Password Change Failed',
                'New password and confirmation did not match',
                $_SESSION['admin_id']
            );
            $_SESSION['error'] = "Password baru dan konfirmasi password tidak cocok!";
        }
        // Check password length
        elseif (strlen($new_password) < 6) {
            // Log invalid password length
            log_activity(
                'Password Change Failed',
                'New password does not meet minimum length requirement',
                $_SESSION['admin_id']
            );
            $_SESSION['error'] = "Password baru minimal 6 karakter!";
        }
        else {
            // Update password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            
            if ($stmt->execute([$hashed_password, $_SESSION['admin_id']])) {
                // Log successful password change
                log_activity(
                    'Password Changed',
                    'User successfully changed their password',
                    $_SESSION['admin_id']
                );
                
                $_SESSION['success'] = "Password berhasil diubah!";
                header("Location: dashboard.php");
                exit();
            } else {
                // Log failed password change
                log_activity(
                    'Password Change Failed',
                    'Failed attempt to change password',
                    $_SESSION['admin_id']
                );
                
                $_SESSION['error'] = "Gagal mengubah password!";
            }
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}
?>

<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Ubah Password</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">Ubah Password</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Form Ubah Password</h3>
                    </div>
                    <div class="card-body">
                        <?php if(isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php 
                                    echo $_SESSION['error'];
                                    unset($_SESSION['error']);
                                ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <form action="" method="post" id="changePasswordForm">
                            <div class="form-group">
                                <label for="current_password">Password Saat Ini</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="new_password">Password Baru</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Minimal 6 karakter</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm_password">Konfirmasi Password Baru</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirm_password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Ubah Password</button>
                                <a href="dashboard.php" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Password Tips -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Tips Keamanan Password</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success mr-2"></i>
                                Gunakan minimal 6 karakter
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success mr-2"></i>
                                Kombinasikan huruf besar dan kecil
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success mr-2"></i>
                                Sertakan angka dan karakter khusus
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success mr-2"></i>
                                Hindari informasi pribadi seperti tanggal lahir
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success mr-2"></i>
                                Jangan gunakan password yang sama dengan akun lain
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Custom JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Form validation
    document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        if (newPassword.length < 6) {
            e.preventDefault();
            alert('Password baru minimal 6 karakter!');
            return;
        }
        
        if (newPassword !== confirmPassword) {
            e.preventDefault();
            alert('Password baru dan konfirmasi password tidak cocok!');
            return;
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
