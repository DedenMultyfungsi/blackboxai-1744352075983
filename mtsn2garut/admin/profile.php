<?php
require_once 'includes/header.php';

// Fetch current profile data
try {
    $stmt = $pdo->query("SELECT * FROM school_profile LIMIT 1");
    $profile = $stmt->fetch();
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vision = $_POST['vision'];
    $mission = $_POST['mission'];
    $history = $_POST['history'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    
    try {
        if ($profile) {
            // Update existing profile
            $stmt = $pdo->prepare("UPDATE school_profile SET vision = ?, mission = ?, history = ?, address = ?, phone = ?, email = ? WHERE id = ?");
            if ($stmt->execute([$vision, $mission, $history, $address, $phone, $email, $profile['id']])) {
                $_SESSION['success'] = "Profil sekolah berhasil diperbarui!";
                header("Location: profile.php");
                exit();
            }
        } else {
            // Insert new profile
            $stmt = $pdo->prepare("INSERT INTO school_profile (vision, mission, history, address, phone, email) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$vision, $mission, $history, $address, $phone, $email])) {
                $_SESSION['success'] = "Profil sekolah berhasil disimpan!";
                header("Location: profile.php");
                exit();
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
                <h1>Profil Sekolah</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">Profil Sekolah</li>
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
                        <h3 class="card-title">Edit Profil Sekolah</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="form-group">
                                <label for="vision">Visi</label>
                                <textarea class="form-control editor" id="vision" name="vision" rows="5" required><?php echo htmlspecialchars($profile['vision'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="mission">Misi</label>
                                <textarea class="form-control editor" id="mission" name="mission" rows="8" required><?php echo htmlspecialchars($profile['mission'] ?? ''); ?></textarea>
                                <small class="form-text text-muted">Gunakan format list/bullet points untuk misi sekolah.</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="history">Sejarah</label>
                                <textarea class="form-control editor" id="history" name="history" rows="10" required><?php echo htmlspecialchars($profile['history'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="address">Alamat</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($profile['address'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Telepon</label>
                                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
document.addEventListener('DOMContentLoaded', function() {
    // Initialize CKEditor for rich text editing
    const editors = document.querySelectorAll('.editor');
    editors.forEach(editor => {
        ClassicEditor
            .create(editor, {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'undo', 'redo'],
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                    ]
                }
            })
            .catch(error => {
                console.error(error);
            });
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const phone = document.getElementById('phone').value;
        const email = document.getElementById('email').value;
        
        // Basic phone number validation
        if (!phone.match(/^[0-9()\-\s+]+$/)) {
            e.preventDefault();
            alert('Format nomor telepon tidak valid');
            return;
        }
        
        // Basic email validation
        if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            e.preventDefault();
            alert('Format email tidak valid');
            return;
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
