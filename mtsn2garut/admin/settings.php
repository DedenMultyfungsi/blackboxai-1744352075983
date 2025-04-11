<?php
require_once 'includes/header.php';
require_once '../includes/functions.php';

// Check if user has admin privileges
if ($_SESSION['admin_role'] !== 'admin') {
    $_SESSION['error'] = "Anda tidak memiliki akses ke halaman ini!";
    header("Location: dashboard.php");
    exit();
}

// Fetch current settings
try {
    $stmt = $pdo->query("SELECT * FROM settings");
    $settings = [];
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Handle validation logging
if (isset($_POST['action']) && $_POST['action'] === 'log_validation') {
    log_activity(
        'Settings Validation Failed',
        json_encode([
            'field' => $_POST['field'],
            'message' => $_POST['message'],
            'value' => $_POST['value']
        ]),
        $_SESSION['admin_id']
    );
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['action'])) {
    try {
        // Begin transaction
        $pdo->beginTransaction();
        
        // Update settings
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) 
                              ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        
        // Website Settings
        $settings_updates = [];
        $stmt->execute(['site_name', $_POST['site_name']]);
        $settings_updates['site_name'] = $_POST['site_name'];
        
        $stmt->execute(['site_description', $_POST['site_description']]);
        $settings_updates['site_description'] = $_POST['site_description'];
        
        $stmt->execute(['meta_keywords', $_POST['meta_keywords']]);
        $settings_updates['meta_keywords'] = $_POST['meta_keywords'];
        
        // Social Media Links
        $stmt->execute(['facebook_url', $_POST['facebook_url']]);
        $settings_updates['facebook_url'] = $_POST['facebook_url'];
        
        $stmt->execute(['instagram_url', $_POST['instagram_url']]);
        $settings_updates['instagram_url'] = $_POST['instagram_url'];
        
        $stmt->execute(['twitter_url', $_POST['twitter_url']]);
        $settings_updates['twitter_url'] = $_POST['twitter_url'];
        
        $stmt->execute(['youtube_url', $_POST['youtube_url']]);
        $settings_updates['youtube_url'] = $_POST['youtube_url'];
        
        // Contact Information
        $stmt->execute(['contact_email', $_POST['contact_email']]);
        $settings_updates['contact_email'] = $_POST['contact_email'];
        
        $stmt->execute(['contact_phone', $_POST['contact_phone']]);
        $settings_updates['contact_phone'] = $_POST['contact_phone'];
        
        $stmt->execute(['contact_address', $_POST['contact_address']]);
        $settings_updates['contact_address'] = $_POST['contact_address'];
        
        $stmt->execute(['maps_embed_code', $_POST['maps_embed_code']]);
        $settings_updates['maps_embed_code'] = 'maps_embed_code_updated';
        
        // Handle logo upload
        if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['site_logo']['name'];
            $filetype = pathinfo($filename, PATHINFO_EXTENSION);
            
            if (in_array(strtolower($filetype), $allowed)) {
                $newname = 'logo.' . $filetype;
                $upload_path = '../assets/images/' . $newname;
                
                if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $upload_path)) {
                    $stmt->execute(['site_logo', $newname]);
                    $settings_updates['site_logo'] = $newname;
                    
                    // Log successful logo update
                    log_activity(
                        'Logo Updated',
                        'Website logo updated: ' . $newname,
                        $_SESSION['admin_id']
                    );
                } else {
                    // Log upload failure
                    log_activity(
                        'Logo Update Failed',
                        'Failed to upload logo: ' . json_encode([
                            'filename' => $filename,
                            'upload_path' => $upload_path,
                            'error' => error_get_last()
                        ]),
                        $_SESSION['admin_id']
                    );
                }
            } else {
                // Log invalid file type
                log_activity(
                    'Settings Validation Failed',
                    'Invalid logo file type: ' . json_encode([
                        'filename' => $filename,
                        'type' => $filetype,
                        'allowed_types' => $allowed
                    ]),
                    $_SESSION['admin_id']
                );
            }
        }

        // Log settings update
        log_activity(
            'Settings Updated',
            'Updated website settings: ' . json_encode($settings_updates),
            $_SESSION['admin_id']
        );
        
        // Commit transaction
        $pdo->commit();
        
        $_SESSION['success'] = "Pengaturan berhasil disimpan!";
        header("Location: settings.php");
        exit();
        
    } catch(PDOException $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        
        // Log the error
        log_activity(
            'Settings Update Failed',
            'Error updating settings: ' . $e->getMessage(),
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
                <h1>Pengaturan Website</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">Pengaturan</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pengaturan Umum</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            <!-- Website Settings -->
                            <h5 class="mb-3">Pengaturan Website</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="site_name">Nama Website</label>
                                        <input type="text" class="form-control" id="site_name" name="site_name" 
                                               value="<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="site_logo">Logo Website</label>
                                        <?php if(isset($settings['site_logo'])): ?>
                                            <div class="mb-2">
                                                <img src="../assets/images/<?php echo $settings['site_logo']; ?>" 
                                                     alt="Current Logo" 
                                                     style="max-height: 50px;">
                                            </div>
                                        <?php endif; ?>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="site_logo" name="site_logo">
                                            <label class="custom-file-label" for="site_logo">Pilih file</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="site_description">Deskripsi Website</label>
                                <textarea class="form-control" id="site_description" name="site_description" rows="3"><?php echo htmlspecialchars($settings['site_description'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="meta_keywords">Meta Keywords</label>
                                <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" 
                                       value="<?php echo htmlspecialchars($settings['meta_keywords'] ?? ''); ?>"
                                       placeholder="Kata kunci dipisahkan dengan koma">
                            </div>
                            
                            <hr>
                            
                            <!-- Social Media Links -->
                            <h5 class="mb-3">Sosial Media</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="facebook_url">Facebook URL</label>
                                        <input type="url" class="form-control" id="facebook_url" name="facebook_url" 
                                               value="<?php echo htmlspecialchars($settings['facebook_url'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="instagram_url">Instagram URL</label>
                                        <input type="url" class="form-control" id="instagram_url" name="instagram_url" 
                                               value="<?php echo htmlspecialchars($settings['instagram_url'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="twitter_url">Twitter URL</label>
                                        <input type="url" class="form-control" id="twitter_url" name="twitter_url" 
                                               value="<?php echo htmlspecialchars($settings['twitter_url'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="youtube_url">YouTube URL</label>
                                        <input type="url" class="form-control" id="youtube_url" name="youtube_url" 
                                               value="<?php echo htmlspecialchars($settings['youtube_url'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <!-- Contact Information -->
                            <h5 class="mb-3">Informasi Kontak</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contact_email">Email</label>
                                        <input type="email" class="form-control" id="contact_email" name="contact_email" 
                                               value="<?php echo htmlspecialchars($settings['contact_email'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contact_phone">Telepon</label>
                                        <input type="text" class="form-control" id="contact_phone" name="contact_phone" 
                                               value="<?php echo htmlspecialchars($settings['contact_phone'] ?? ''); ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="contact_address">Alamat</label>
                                <textarea class="form-control" id="contact_address" name="contact_address" rows="3" required><?php echo htmlspecialchars($settings['contact_address'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="maps_embed_code">Google Maps Embed Code</label>
                                <textarea class="form-control" id="maps_embed_code" name="maps_embed_code" rows="3"><?php echo htmlspecialchars($settings['maps_embed_code'] ?? ''); ?></textarea>
                                <small class="form-text text-muted">Masukkan kode embed Google Maps untuk menampilkan peta lokasi sekolah.</small>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Custom JavaScript -->
<script>
// Update file input label with selected filename
document.querySelector('.custom-file-input').addEventListener('change', function(e) {
    var fileName = e.target.files[0].name;
    var nextSibling = e.target.nextElementSibling;
    nextSibling.innerText = fileName;
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const phone = document.getElementById('contact_phone').value;
    const email = document.getElementById('contact_email').value;
    
    // Basic phone number validation
    if (!phone.match(/^[0-9()\-\s+]+$/)) {
        e.preventDefault();
        // Log validation error
        fetch('settings.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'action': 'log_validation',
                'field': 'contact_phone',
                'message': 'Invalid phone number format',
                'value': phone
            })
        });
        alert('Format nomor telepon tidak valid');
        return;
    }
    
    // Basic email validation
    if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        e.preventDefault();
        // Log validation error
        fetch('settings.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'action': 'log_validation',
                'field': 'contact_email',
                'message': 'Invalid email format',
                'value': email
            })
        });
        alert('Format email tidak valid');
        return;
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
