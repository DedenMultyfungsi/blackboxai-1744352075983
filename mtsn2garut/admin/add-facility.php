<?php
require_once 'includes/header.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate input
    $errors = [];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    if (strlen($name) < 3) {
        log_activity(
            'Facility Validation Failed',
            'Name too short: ' . json_encode([
                'name' => $name,
                'min_length' => 3
            ]),
            $_SESSION['admin_id']
        );
        $errors[] = "Nama fasilitas minimal 3 karakter!";
    }

    if (empty($description)) {
        log_activity(
            'Facility Validation Failed',
            'Description empty: ' . json_encode([
                'name' => $name
            ]),
            $_SESSION['admin_id']
        );
        $errors[] = "Deskripsi tidak boleh kosong!";
    }

    if (!empty($errors)) {
        $_SESSION['error'] = implode("<br>", $errors);
        header("Location: add-facility.php");
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
            $upload_path = '../assets/uploads/facilities/' . $newname;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image = $newname;
                // Log successful image upload
                log_activity(
                    'Facility Image Uploaded',
                    'Uploaded image for facility: ' . json_encode([
                        'name' => $name,
                        'filename' => $newname,
                        'original_name' => $filename,
                        'type' => $filetype
                    ]),
                    $_SESSION['admin_id']
                );
            } else {
                // Log upload failure
                log_activity(
                    'Facility Image Upload Failed',
                    'Failed to upload image: ' . json_encode([
                        'name' => $name,
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
                'Facility Image Invalid Type',
                'Invalid image type uploaded: ' . json_encode([
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
        $stmt = $pdo->prepare("INSERT INTO facilities (name, description, image) VALUES (?, ?, ?)");
        if ($stmt->execute([$name, $description, $image])) {
            // Log successful facility creation
            log_activity(
                'Facility Created',
                'Created new facility: ' . json_encode([
                    'name' => $name,
                    'has_image' => !empty($image)
                ]),
                $_SESSION['admin_id']
            );
            
            $_SESSION['success'] = "Fasilitas berhasil ditambahkan!";
            header("Location: facilities.php");
            exit();
        }
    } catch(PDOException $e) {
        // Log database error
        log_activity(
            'Facility Creation Failed',
            'Failed to create facility: ' . json_encode([
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
                <h1>Tambah Fasilitas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="facilities.php">Fasilitas</a></li>
                    <li class="breadcrumb-item active">Tambah Fasilitas</li>
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
                        <h3 class="card-title">Form Tambah Fasilitas</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="name">Nama Fasilitas</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Deskripsi</label>
                                <textarea class="form-control editor" id="description" name="description" rows="5" required></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="image">Gambar</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="image" name="image" required>
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
