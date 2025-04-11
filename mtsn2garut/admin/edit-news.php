<?php
require_once 'includes/header.php';
require_once '../includes/functions.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch news from the database
    try {
        $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
        $stmt->execute([$id]);
        $news = $stmt->fetch();
    } catch(PDOException $e) {
        die("Error: " . $e->getMessage());
    }

    if (!$news) {
        // Log news not found error
        log_activity(
            'News Not Found',
            'Attempted to edit non-existent news: ' . json_encode([
                'news_id' => $id
            ]),
            $_SESSION['admin_id']
        );
        $_SESSION['error'] = "Berita tidak ditemukan!";
        header("Location: news.php");
        exit();
    }
} else {
    // Log invalid ID error
    log_activity(
        'Invalid News ID',
        'Attempted to edit news with invalid ID',
        $_SESSION['admin_id']
    );
    $_SESSION['error'] = "ID berita tidak valid!";
    header("Location: news.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $status = $_POST['status'];
    
    // Handle image upload
    $image = $news['image']; // Keep existing image
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            // Create unique filename
            $newname = uniqid() . '.' . $filetype;
            $upload_path = '../assets/uploads/news/' . $newname;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image = $newname; // Update image if new one is uploaded
                // Log successful image update
                log_activity(
                    'News Image Updated',
                    'Updated image for news: ' . json_encode([
                        'news_id' => $id,
                        'old_image' => $news['image'],
                        'new_image' => $newname,
                        'original_name' => $filename
                    ]),
                    $_SESSION['admin_id']
                );
            } else {
                // Log upload failure
                log_activity(
                    'News Image Update Failed',
                    'Failed to update image: ' . json_encode([
                        'news_id' => $id,
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
                'Invalid image type for update: ' . json_encode([
                    'news_id' => $id,
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
        $stmt = $pdo->prepare("UPDATE news SET title = ?, content = ?, image = ?, status = ? WHERE id = ?");
        if ($stmt->execute([$title, $content, $image, $status, $id])) {
            // Log successful news update
            log_activity(
                'News Updated',
                'Updated news article: ' . json_encode([
                    'news_id' => $id,
                    'title' => $title,
                    'status' => $status,
                    'image_changed' => $image !== $news['image']
                ]),
                $_SESSION['admin_id']
            );
            
            $_SESSION['success'] = "Berita berhasil diperbarui!";
            header("Location: news.php");
            exit();
        }
    } catch(PDOException $e) {
        // Log database error
        log_activity(
            'News Update Failed',
            'Failed to update news: ' . json_encode([
                'news_id' => $id,
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
                <h1>Edit Berita</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="news.php">Berita</a></li>
                    <li class="breadcrumb-item active">Edit Berita</li>
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
                        <h3 class="card-title">Form Edit Berita</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="title">Judul Berita</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($news['title']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="content">Konten</label>
                                <textarea class="form-control editor" id="content" name="content" rows="10" required><?php echo htmlspecialchars($news['content']); ?></textarea>
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
                                <img id="preview-image" src="<?php echo '../assets/uploads/news/' . $news['image']; ?>" alt="Preview" style="max-width: 300px;">
                            </div>
                            
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="published" <?php echo $news['status'] == 'published' ? 'selected' : ''; ?>>Published</option>
                                    <option value="draft" <?php echo $news['status'] == 'draft' ? 'selected' : ''; ?>>Draft</option>
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
