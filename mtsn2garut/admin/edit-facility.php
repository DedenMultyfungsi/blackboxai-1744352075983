<?php
require_once 'includes/header.php';
require_once '../includes/functions.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Fetch facility data
    try {
        $stmt = $pdo->prepare("SELECT * FROM facilities WHERE id = ?");
        $stmt->execute([$id]);
        $facility = $stmt->fetch();
        
        if (!$facility) {
            // Log facility not found error
            log_activity(
                'Facility Not Found',
                'Attempted to edit non-existent facility: ' . json_encode([
                    'facility_id' => $id
                ]),
                $_SESSION['admin_id']
            );
            $_SESSION['error'] = "Data fasilitas tidak ditemukan!";
            header("Location: facilities.php");
            exit();
        }
    } catch(PDOException $e) {
        // Log database error
        log_activity(
            'Facility Fetch Failed',
            'Failed to fetch facility data: ' . json_encode([
                'facility_id' => $id,
                'error' => $e->getMessage()
            ]),
            $_SESSION['admin_id']
        );
        die("Error: " . $e->getMessage());
    }
} else {
    // Log invalid ID error
    log_activity(
        'Invalid Facility ID',
        'Attempted to edit facility with invalid ID',
        $_SESSION['admin_id']
    );
    $_SESSION['error'] = "ID tidak valid!";
    header("Location: facilities.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    
    // Handle image upload
    $image = $facility['image']; // Keep existing image
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            // Create unique filename
            $newname = uniqid() . '.' . $filetype;
            $upload_path = '../assets/uploads/facilities/' . $newname;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                // Delete old image if exists
                if ($facility['image'] && file_exists('../assets/uploads/facilities/' . $facility['image'])) {
                    unlink('../assets/uploads/facilities/' . $facility['image']);
                    // Log old image deletion
                    log_activity(
                        'Facility Image Deleted',
                        'Deleted old image: ' . json_encode([
                            'facility_id' => $id,
                            'old_image' => $facility['image']
                        ]),
                        $_SESSION['admin_id']
                    );
                }
                $image = $newname;
                // Log successful image update
                log_activity(
                    'Facility Image Updated',
                    'Updated image for facility: ' . json_encode([
                        'facility_id' => $id,
                        'name' => $name,
                        'old_image' => $facility['image'],
                        'new_image' => $newname
                    ]),
                    $_SESSION['admin_id']
                );
            } else {
                // Log upload failure
                log_activity(
                    'Facility Image Update Failed',
                    'Failed to update image: ' . json_encode([
                        'facility_id' => $id,
                        'name' => $name,
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
                'Facility Image Invalid Type',
                'Invalid image type for update: ' . json_encode([
                    'facility_id' => $id,
                    'name' => $name,
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
        $stmt = $pdo->prepare("UPDATE facilities SET name = ?, description = ?, image = ? WHERE id = ?");
        if ($stmt->execute([$name, $description, $image, $id])) {
            // Log successful facility update
            log_activity(
                'Facility Updated',
                'Updated facility: ' . json_encode([
                    'facility_id' => $id,
                    'name' => $name,
                    'image_changed' => $image !== $facility['image']
                ]),
                $_SESSION['admin_id']
            );
            
            $_SESSION['success'] = "Fasilitas berhasil diperbarui!";
            header("Location: facilities.php");
            exit();
        }
    } catch(PDOException $e) {
        // Log database error
        log_activity(
            'Facility Update Failed',
            'Failed to update facility: ' . json_encode([
                'facility_id' => $id,
                'name' => $name,
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
                <h1>Edit Fasilitas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="facilities.php">Fasilitas</a></li>
                    <li class="breadcrumb-item active">Edit Fasilitas</li>
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
                        <h3 class="card-title">Form Edit Fasilitas</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="name">Nama Fasilitas</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($facility['name']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Deskripsi</label>
                                <textarea class="form-control editor" id="description" name="description" rows="5" required><?php echo htmlspecialchars($facility['description']); ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="image">Gambar</label>
                                <?php if ($facility['image']): ?>
                                    <div class="mb-2">
                                        <img src="../assets/uploads/facilities/<?php echo $facility['image']; ?>" 
                                             alt="Current Image" 
                                             class="img-thumbnail" 
                                             style="max-width: 200px;">
                                    </div>
                                <?php endif; ?>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="image" name="image">
                                    <label class="custom-file-label" for="image">Pilih file untuk mengganti gambar</label>
                                </div>
                                <small class="form-text text-muted">Format yang diizinkan: JPG, JPEG, PNG, GIF</small>
                            </div>
                            
                            <div class="form-group">
                                <label>Preview Gambar Baru</label><br>
                                <img id="preview-image" src="#" alt="Preview" style="max-width: 300px; display: none;">
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                <a href="facilities.php" class="btn btn-secondary">Batal</a>
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
    preview.style.display = 'block';
    preview.src = URL.createObjectURL(e.target.files[0]);
});

// Initialize CKEditor
ClassicEditor
    .create(document.querySelector('#description'))
    .catch(error => {
        console.error(error);
    });
</script>

<?php require_once 'includes/footer.php'; ?>
