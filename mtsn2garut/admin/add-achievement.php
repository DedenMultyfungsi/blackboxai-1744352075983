<?php
require_once 'includes/header.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate input
    $errors = [];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $date = trim($_POST['date']);

    if (strlen($title) < 5) {
        log_activity(
            'Achievement Validation Failed',
            'Title too short: ' . json_encode([
                'title' => $title,
                'min_length' => 5
            ]),
            $_SESSION['admin_id']
        );
        $errors[] = "Judul prestasi minimal 5 karakter!";
    }

    if (empty($category)) {
        log_activity(
            'Achievement Validation Failed',
            'Category not selected',
            $_SESSION['admin_id']
        );
        $errors[] = "Kategori harus dipilih!";
    }

    if (!empty($errors)) {
        $_SESSION['error'] = implode("<br>", $errors);
        header("Location: add-achievement.php");
        exit();
    }
    
    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            // Create unique filename
            $newname = uniqid() . '.' . $filetype;
            $upload_path = '../assets/uploads/achievements/' . $newname;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image = $newname;
                // Log successful image upload
                log_activity(
                    'Achievement Image Uploaded',
                    'Uploaded image for achievement: ' . json_encode([
                        'title' => $title,
                        'filename' => $newname,
                        'original_name' => $filename,
                        'type' => $filetype
                    ]),
                    $_SESSION['admin_id']
                );
            } else {
                // Log upload failure
                log_activity(
                    'Achievement Image Upload Failed',
                    'Failed to upload image: ' . json_encode([
                        'title' => $title,
                        'original_name' => $filename,
                        'upload_path' => $upload_path,
                        'error' => error_get_last()
                    ]),
                    $_SESSION['admin_id']
                );
                $_SESSION['error'] = "Gagal mengupload gambar!";
            }
        } else {
            // Log invalid file type
            log_activity(
                'Achievement Image Invalid Type',
                'Invalid image type uploaded: ' . json_encode([
                    'title' => $title,
                    'filename' => $filename,
                    'type' => $filetype,
                    'allowed_types' => $allowed
                ]),
                $_SESSION['admin_id']
            );
            $_SESSION['error'] = "Format file tidak diizinkan!";
        }
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO achievements (title, description, category, date, image) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$title, $description, $category, $date, $image])) {
            // Log successful achievement creation
            log_activity(
                'Achievement Created',
                'Created new achievement: ' . json_encode([
                    'title' => $title,
                    'category' => $category,
                    'date' => $date,
                    'has_image' => !empty($image)
                ]),
                $_SESSION['admin_id']
            );
            
            $_SESSION['success'] = "Prestasi berhasil ditambahkan!";
            header("Location: achievements.php");
            exit();
        }
    } catch(PDOException $e) {
        // Log database error
        log_activity(
            'Achievement Creation Failed',
            'Failed to create achievement: ' . json_encode([
                'title' => $title,
                'error' => $e->getMessage()
            ]),
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
                <h1>Tambah Prestasi</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="achievements.php">Prestasi</a></li>
                    <li class="breadcrumb-item active">Tambah Prestasi</li>
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
                        <h3 class="card-title">Form Tambah Prestasi</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data" id="achievementForm">
                            <div class="form-group">
                                <label for="title">Judul Prestasi</label>
                                <input type="text" class="form-control" id="title" name="title" required minlength="5">
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Deskripsi</label>
                                <textarea class="form-control editor" id="description" name="description" rows="5" required></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="category">Kategori</label>
                                <select class="form-control" id="category" name="category" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="Akademik">Akademik</option>
                                    <option value="Olahraga">Olahraga</option>
                                    <option value="Seni">Seni</option>
                                    <option value="Keagamaan">Keagamaan</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="date">Tanggal</label>
                                <input type="date" class="form-control" id="date" name="date" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="image">Gambar</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="image" name="image">
                                    <label class="custom-file-label" for="image">Pilih file</label>
                                </div>
                                <small class="form-text text-muted">Format yang diizinkan: JPG, JPEG, PNG, GIF</small>
                            </div>
                            
                            <div class="form-group">
                                <label>Preview Gambar</label><br>
                                <img id="preview-image" src="#" alt="Preview" style="max-width: 300px; display: none;">
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="achievements.php" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Additional Script -->
<script>
// Image preview
document.getElementById('image').addEventListener('change', function(e) {
    const preview = document.getElementById('preview-image');
    preview.style.display = 'block';
    preview.src = URL.createObjectURL(e.target.files[0]);
    
    // Update file input label
    const fileName = e.target.files[0].name;
    e.target.nextElementSibling.textContent = fileName;
});

// Initialize CKEditor
ClassicEditor
    .create(document.querySelector('#description'))
    .catch(error => {
        console.error(error);
    });

// Form validation
document.getElementById('achievementForm').addEventListener('submit', function(e) {
    const title = document.getElementById('title').value.trim();
    const category = document.getElementById('category').value;
    let hasError = false;
    
    if (title.length < 5) {
        e.preventDefault();
        alert('Judul prestasi minimal 5 karakter!');
        hasError = true;
    }
    
    if (!category) {
        if (!hasError) {
            e.preventDefault();
            alert('Silakan pilih kategori!');
        }
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
