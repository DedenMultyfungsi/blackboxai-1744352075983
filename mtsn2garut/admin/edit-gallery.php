<?php
require_once 'includes/header.php';
require_once '../includes/functions.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Fetch gallery item data
    try {
        $stmt = $pdo->prepare("SELECT * FROM gallery WHERE id = ?");
        $stmt->execute([$id]);
        $gallery = $stmt->fetch();
        
        if (!$gallery) {
            // Log gallery not found error
            log_activity(
                'Gallery Item Not Found',
                'Attempted to edit non-existent gallery item: ' . json_encode([
                    'gallery_id' => $id
                ]),
                $_SESSION['admin_id']
            );
            $_SESSION['error'] = "Data galeri tidak ditemukan!";
            header("Location: gallery.php");
            exit();
        }
    } catch(PDOException $e) {
        // Log database error
        log_activity(
            'Gallery Fetch Failed',
            'Failed to fetch gallery data: ' . json_encode([
                'gallery_id' => $id,
                'error' => $e->getMessage()
            ]),
            $_SESSION['admin_id']
        );
        die("Error: " . $e->getMessage());
    }
} else {
    // Log invalid ID error
    log_activity(
        'Invalid Gallery ID',
        'Attempted to edit gallery item with invalid ID',
        $_SESSION['admin_id']
    );
    $_SESSION['error'] = "ID tidak valid!";
    header("Location: gallery.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $category = $_POST['category'];
    
    // Handle image upload
    $image = $gallery['image']; // Keep existing image
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            // Create unique filename
            $newname = uniqid() . '.' . $filetype;
            $upload_path = '../assets/uploads/gallery/' . $newname;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                // Delete old image if exists
                if ($gallery['image'] && file_exists('../assets/uploads/gallery/' . $gallery['image'])) {
                    unlink('../assets/uploads/gallery/' . $gallery['image']);
                    // Log old image deletion
                    log_activity(
                        'Gallery Image Deleted',
                        'Deleted old image: ' . json_encode([
                            'gallery_id' => $id,
                            'old_image' => $gallery['image']
                        ]),
                        $_SESSION['admin_id']
                    );
                }
                $image = $newname;
                // Log successful image update
                log_activity(
                    'Gallery Image Updated',
                    'Updated image for gallery item: ' . json_encode([
                        'gallery_id' => $id,
                        'title' => $title,
                        'old_image' => $gallery['image'],
                        'new_image' => $newname
                    ]),
                    $_SESSION['admin_id']
                );
            } else {
                // Log upload failure
                log_activity(
                    'Gallery Image Update Failed',
                    'Failed to update image: ' . json_encode([
                        'gallery_id' => $id,
                        'title' => $title,
                        'original_name' => $filename,
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
                'Invalid image type for update: ' . json_encode([
                    'gallery_id' => $id,
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
        $stmt = $pdo->prepare("UPDATE gallery SET title = ?, image = ?, category = ? WHERE id = ?");
        if ($stmt->execute([$title, $image, $category, $id])) {
            // Log successful gallery update
            log_activity(
                'Gallery Item Updated',
                'Updated gallery item: ' . json_encode([
                    'gallery_id' => $id,
                    'title' => $title,
                    'category' => $category,
                    'image_changed' => $image !== $gallery['image']
                ]),
                $_SESSION['admin_id']
            );
            
            $_SESSION['success'] = "Data galeri berhasil diperbarui!";
            header("Location: gallery.php");
            exit();
        }
    } catch(PDOException $e) {
        // Log database error
        log_activity(
            'Gallery Update Failed',
            'Failed to update gallery item: ' . json_encode([
                'gallery_id' => $id,
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
                <h1>Edit Foto Galeri</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="gallery.php">Galeri</a></li>
                    <li class="breadcrumb-item active">Edit Foto</li>
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
                        <h3 class="card-title">Form Edit Foto</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="title">Judul Foto</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($gallery['title']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="category">Kategori</label>
                                <select class="form-control" id="category" name="category" required>
                                    <option value="Kegiatan" <?php echo $gallery['category'] == 'Kegiatan' ? 'selected' : ''; ?>>Kegiatan</option>
                                    <option value="Fasilitas" <?php echo $gallery['category'] == 'Fasilitas' ? 'selected' : ''; ?>>Fasilitas</option>
                                    <option value="Prestasi" <?php echo $gallery['category'] == 'Prestasi' ? 'selected' : ''; ?>>Prestasi</option>
                                    <option value="Lainnya" <?php echo $gallery['category'] == 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Foto Saat Ini</label>
                                <?php if ($gallery['image']): ?>
                                    <div class="mb-2">
                                        <img src="../assets/uploads/gallery/<?php echo $gallery['image']; ?>" 
                                             alt="Current Image" 
                                             class="img-thumbnail" 
                                             style="max-width: 200px;">
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="form-group">
                                <label for="image">Ganti Foto</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="image" name="image">
                                    <label class="custom-file-label" for="image">Pilih file untuk mengganti foto</label>
                                </div>
                                <small class="form-text text-muted">Format yang diizinkan: JPG, JPEG, PNG, GIF. Ukuran maksimal: 2MB</small>
                            </div>
                            
                            <div class="form-group">
                                <label>Preview Foto Baru</label><br>
                                <img id="preview-image" src="#" alt="Preview" style="max-width: 300px; display: none;">
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        const maxSize = 2 * 1024 * 1024; // 2MB

        if (file.size > maxSize) {
            e.preventDefault();
            // Log file size validation error
            $.post('', {
                action: 'log_validation',
                error: 'File size exceeded',
                size: file.size,
                max_size: maxSize,
                filename: file.name,
                gallery_id: <?php echo $id; ?>
            });
            
            Swal.fire({
                icon: 'error',
                title: 'File terlalu besar',
                text: 'Ukuran file maksimal adalah 2MB',
            });
        }
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
