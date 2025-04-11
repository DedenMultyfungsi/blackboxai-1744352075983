<?php
require_once 'includes/header.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author_id = $_SESSION['admin_id'];
    $status = $_POST['status'];
    
    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            // Create unique filename
            $newname = uniqid() . '.' . $filetype;
            $upload_path = '../assets/uploads/news/' . $newname;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image = $newname;
                // Log successful image upload
                log_activity(
                    'News Image Uploaded',
                    'Uploaded image for news: ' . json_encode([
                        'filename' => $newname,
                        'original_name' => $filename,
                        'type' => $filetype
                    ]),
                    $_SESSION['admin_id']
                );
            } else {
                // Log upload failure
                log_activity(
                    'News Image Upload Failed',
                    'Failed to upload image: ' . json_encode([
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
                'News Image Invalid Type',
                'Invalid image type uploaded: ' . json_encode([
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
        $stmt = $pdo->prepare("INSERT INTO news (title, content, image, author_id, status) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$title, $content, $image, $author_id, $status])) {
            // Log successful news creation
            log_activity(
                'News Created',
                'Created new news article: ' . json_encode([
                    'title' => $title,
                    'status' => $status,
                    'has_image' => !empty($image)
                ]),
                $_SESSION['admin_id']
            );
            
            $_SESSION['success'] = "Berita berhasil ditambahkan!";
            header("Location: news.php");
            exit();
        }
    } catch(PDOException $e) {
        // Log database error
        log_activity(
            'News Creation Failed',
            'Failed to create news: ' . json_encode([
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
                <h1>Tambah Berita</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="news.php">Berita</a></li>
                    <li class="breadcrumb-item active">Tambah Berita</li>
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
                        <h3 class="card-title">Form Tambah Berita</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="title">Judul Berita</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="content">Konten</label>
                                <textarea class="form-control editor" id="content" name="content" rows="10" required></textarea>
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
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="published">Published</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="news.php" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Additional Script for Image Preview -->
<script>
document.getElementById('image').addEventListener('change', function(e) {
    const preview = document.getElementById('preview-image');
    preview.style.display = 'block';
    preview.src = URL.createObjectURL(e.target.files[0]);
});
</script>

<?php require_once 'includes/footer.php'; ?>
