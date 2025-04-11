<?php
require_once 'includes/header.php';
require_once '../includes/functions.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Fetch achievement data
    try {
        $stmt = $pdo->prepare("SELECT * FROM achievements WHERE id = ?");
        $stmt->execute([$id]);
        $achievement = $stmt->fetch();
        
        if (!$achievement) {
            // Log achievement not found error
            log_activity(
                'Achievement Not Found',
                'Attempted to edit non-existent achievement: ' . json_encode([
                    'achievement_id' => $id
                ]),
                $_SESSION['admin_id']
            );
            $_SESSION['error'] = "Data prestasi tidak ditemukan!";
            header("Location: achievements.php");
            exit();
        }
    } catch(PDOException $e) {
        // Log database error
        log_activity(
            'Achievement Fetch Failed',
            'Failed to fetch achievement data: ' . json_encode([
                'achievement_id' => $id,
                'error' => $e->getMessage()
            ]),
            $_SESSION['admin_id']
        );
        die("Error: " . $e->getMessage());
    }
} else {
    // Log invalid ID error
    log_activity(
        'Invalid Achievement ID',
        'Attempted to edit achievement with invalid ID',
        $_SESSION['admin_id']
    );
    $_SESSION['error'] = "ID tidak valid!";
    header("Location: achievements.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $date = $_POST['date'];
    
    // Handle image upload
    $image = $achievement['image']; // Keep existing image
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            // Create unique filename
            $newname = uniqid() . '.' . $filetype;
            $upload_path = '../assets/uploads/achievements/' . $newname;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                // Delete old image if exists
                if ($achievement['image'] && file_exists('../assets/uploads/achievements/' . $achievement['image'])) {
                    unlink('../assets/uploads/achievements/' . $achievement['image']);
                    // Log old image deletion
                    log_activity(
                        'Achievement Image Deleted',
                        'Deleted old image: ' . json_encode([
                            'achievement_id' => $id,
                            'old_image' => $achievement['image']
                        ]),
                        $_SESSION['admin_id']
                    );
                }
                $image = $newname;
                // Log successful image update
                log_activity(
                    'Achievement Image Updated',
                    'Updated image for achievement: ' . json_encode([
                        'achievement_id' => $id,
                        'title' => $title,
                        'old_image' => $achievement['image'],
                        'new_image' => $newname
                    ]),
                    $_SESSION['admin_id']
                );
            } else {
                // Log upload failure
                log_activity(
                    'Achievement Image Update Failed',
                    'Failed to update image: ' . json_encode([
                        'achievement_id' => $id,
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
                'Achievement Image Invalid Type',
                'Invalid image type for update: ' . json_encode([
                    'achievement_id' => $id,
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
        $stmt = $pdo->prepare("UPDATE achievements SET title = ?, description = ?, category = ?, date = ?, image = ? WHERE id = ?");
        if ($stmt->execute([$title, $description, $category, $date, $image, $id])) {
            // Log successful achievement update
            log_activity(
                'Achievement Updated',
                'Updated achievement: ' . json_encode([
                    'achievement_id' => $id,
                    'title' => $title,
                    'category' => $category,
                    'date' => $date,
                    'image_changed' => $image !== $achievement['image']
                ]),
                $_SESSION['admin_id']
            );
            
            $_SESSION['success'] = "Prestasi berhasil diperbarui!";
            header("Location: achievements.php");
            exit();
        }
    } catch(PDOException $e) {
        // Log database error
        log_activity(
            'Achievement Update Failed',
            'Failed to update achievement: ' . json_encode([
                'achievement_id' => $id,
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
                <h1>Edit Prestasi</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="achievements.php">Prestasi</a></li>
                    <li class="breadcrumb-item active">Edit Prestasi</li>
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
                        <h3 class="card-title">Form Edit Prestasi</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="title">Judul Prestasi</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($achievement['title']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Deskripsi</label>
                                <textarea class="form-control editor" id="description" name="description" rows="5" required><?php echo htmlspecialchars($achievement['description']); ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="category">Kategori</label>
                                <select class="form-control" id="category" name="category" required>
                                    <option value="Akademik" <?php echo $achievement['category'] == 'Akademik' ? 'selected' : ''; ?>>Akademik</option>
                                    <option value="Olahraga" <?php echo $achievement['category'] == 'Olahraga' ? 'selected' : ''; ?>>Olahraga</option>
                                    <option value="Seni" <?php echo $achievement['category'] == 'Seni' ? 'selected' : ''; ?>>Seni</option>
                                    <option value="Keagamaan" <?php echo $achievement['category'] == 'Keagamaan' ? 'selected' : ''; ?>>Keagamaan</option>
                                    <option value="Lainnya" <?php echo $achievement['category'] == 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="date">Tanggal</label>
                                <input type="date" class="form-control" id="date" name="date" value="<?php echo $achievement['date']; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="image">Gambar</label>
                                <?php if ($achievement['image']): ?>
                                    <div class="mb-2">
                                        <img src="../assets/uploads/achievements/<?php echo $achievement['image']; ?>" alt="Current Image" class="img-thumbnail" style="max-width: 200px;">
                                    </div>
                                <?php endif; ?>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="image" name="image">
                                    <label class="custom-file-label" for="image">Pilih file untuk mengganti gambar</label>
                                </div>
                                <small class="form-text text-muted">Format yang diizinkan: JPG, JPEG, PNG, GIF</small>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                <a href="achievements.php" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
