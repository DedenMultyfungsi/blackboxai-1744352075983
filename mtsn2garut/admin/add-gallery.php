<?php
require_once 'includes/header.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate input
    $errors = [];
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);

    if (strlen($title) < 3) {
        log_activity(
            'Gallery Validation Failed',
            'Title too short: ' . json_encode([
                'title' => $title,
                'min_length' => 3
            ]),
            $_SESSION['admin_id']
        );
        $errors[] = "Judul foto minimal 3 karakter!";
    }

    if (empty($category)) {
        log_activity(
            'Gallery Validation Failed',
            'Category not selected: ' . json_encode([
                'title' => $title
            ]),
            $_SESSION['admin_id']
        );
        $errors[] = "Kategori harus dipilih!";
    }

    if (!empty($errors)) {
        $_SESSION['error'] = implode("<br>", $errors);
        header("Location: add-gallery.php");
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
            $upload_path = '../assets/uploads/gallery/' . $newname;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image = $newname;
                // Log successful image upload
                log_activity(
                    'Gallery Image Uploaded',
                    'Uploaded image for gallery: ' . json_encode([
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
                    'Gallery Image Upload Failed',
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
                'Gallery Image Invalid Type',
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
        $stmt = $pdo->prepare("INSERT INTO gallery (title, image, category) VALUES (?, ?, ?)");
        if ($stmt->execute([$title, $image, $category])) {
            // Log successful gallery creation
            log_activity(
                'Gallery Image Created',
                'Added new image to gallery: ' . json_encode([
                    'title' => $title,
                    'category' => $category,
                    'filename' => $image
                ]),
                $_SESSION['admin_id']
            );
            
            $_SESSION['success'] = "Foto berhasil ditambahkan ke galeri!";
            header("Location: gallery.php");
            exit();
        }
    } catch(PDOException $e) {
        // Log database error
        log_activity(
            'Gallery Creation Failed',
            'Failed to add image to gallery: ' . json_encode([
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
                <h1>Tambah Foto Galeri</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="gallery.php">Galeri</a></li>
                    <li class="breadcrumb-item active">Tambah Foto</li>
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
                        <h3 class="card-title">Form Tambah Foto</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="title">Judul Foto</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="category">Kategori</label>
                                <select class="form-control" id="category" name="category" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="Kegiatan">Kegiatan</option>
                                    <option value="Fasilitas">Fasilitas</option>
                                    <option value="Prestasi">Prestasi</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="image">Foto</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="image" name="image" required>
                                    <label class="custom-file-label" for="image">Pilih file</label>
                                </div>
                                <small class="form-text text-muted">Format yang diizinkan: JPG, JPEG, PNG, GIF. Ukuran maksimal: 2MB</small>
                            </div>
                            
                            <div class="form-group">
                                <label>Preview Foto</label><br>
                                <img id="preview-image" src="#" alt="Preview" style="max-width: 300px; display: none;">
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="gallery.php" class="btn btn-secondary">Batal</a>
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
// Preview image before upload
document.getElementById('image').addEventListener('change', function(e) {
    const preview = document.getElementById('preview-image');
    const file = this.files[0];
    const reader = new FileReader();

    reader.onload = function(e) {
        preview.style.display = 'block';
        preview.src = e.target.result;
    }

    if (file) {
        reader.readAsDataURL(file);
        // Update file input label
        document.querySelector('.custom-file-label').textContent = file.name;
    }
});

// File size validation
document.querySelector('form').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('image');
    const file = fileInput.files[0];
    const maxSize = 2 * 1024 * 1024; // 2MB

    if (file && file.size > maxSize) {
        e.preventDefault();
        // Log file size validation error
        $.post('', {
            action: 'log_validation',
            error: 'File size exceeded',
            size: file.size,
            max_size: maxSize,
            filename: file.name
        });
        
        Swal.fire({
            icon: 'error',
            title: 'File terlalu besar',
            text: 'Ukuran file maksimal adalah 2MB',
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
