<?php
require_once 'includes/header.php';
require_once '../includes/functions.php';

// Fetch current user's data
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $user = $stmt->fetch();
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    
    try {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
        if ($stmt->execute([$name, $email, $phone, $_SESSION['admin_id']])) {
            // Log successful profile update
            log_activity(
                'Profile Updated',
                'User updated their profile information: ' . json_encode([
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone
                ]),
                $_SESSION['admin_id']
            );
            
            $_SESSION['success'] = "Profil berhasil diperbarui!";
            $_SESSION['admin_name'] = $name; // Update session name
            header("Location: edit-profile.php");
            exit();
        } else {
            // Log failed profile update
            log_activity(
                'Profile Update Failed',
                'Failed to update profile information',
                $_SESSION['admin_id']
            );
        }
    } catch(PDOException $e) {
        // Log database error
        log_activity(
            'Profile Update Error',
            'Database error during profile update: ' . $e->getMessage(),
            $_SESSION['admin_id']
        );
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}
?>

<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Profil</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">Edit Profil</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Profile Information -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Profil</h3>
                    </div>
                    <div class="card-body">
                        <?php if(isset($_SESSION['success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php 
                                    echo $_SESSION['success'];
                                    unset($_SESSION['success']);
                                ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

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

                        <form action="" method="post" id="profileForm">
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                                <small class="form-text text-muted">Username tidak dapat diubah</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="name">Nama Lengkap</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Nomor Telepon</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label>Role</label>
                                <input type="text" class="form-control" value="<?php echo ucfirst($user['role']); ?>" disabled>
                            </div>
                            
                            <div class="form-group">
                                <label>Tanggal Bergabung</label>
                                <input type="text" class="form-control" value="<?php echo date('d M Y H:i', strtotime($user['created_at'])); ?>" disabled>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                <a href="change-password.php" class="btn btn-warning">Ubah Password</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Profile Tips -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Akun</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info"></i> Tips Keamanan Akun</h5>
                            <ul class="mb-0">
                                <li>Pastikan email yang Anda gunakan aktif</li>
                                <li>Gunakan password yang kuat dan unik</li>
                                <li>Jangan bagikan informasi akun Anda kepada siapapun</li>
                                <li>Ganti password Anda secara berkala</li>
                            </ul>
                        </div>

                        <div class="alert alert-warning">
                            <h5><i class="icon fas fa-exclamation-triangle"></i> Perhatian</h5>
                            <p class="mb-0">
                                Jika Anda mengalami masalah dengan akun Anda atau lupa password, 
                                silakan hubungi administrator sistem.
                            </p>
                        </div>

                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Informasi Login Terakhir</h6>
                                <p class="card-text">
                                    <strong>IP Address:</strong> <?php echo $_SERVER['REMOTE_ADDR']; ?><br>
                                    <strong>Browser:</strong> <?php echo $_SERVER['HTTP_USER_AGENT']; ?><br>
                                    <strong>Waktu:</strong> <?php echo date('d M Y H:i:s'); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Custom JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const phone = document.getElementById('phone').value;
        
        if (name.length < 3) {
            e.preventDefault();
            alert('Nama lengkap minimal 3 karakter!');
            return;
        }
        
        if (email && !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            e.preventDefault();
            alert('Format email tidak valid!');
            return;
        }
        
        if (phone && !phone.match(/^[0-9()\-\s+]+$/)) {
            e.preventDefault();
            alert('Format nomor telepon tidak valid!');
            return;
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
