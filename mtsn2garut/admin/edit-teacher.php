<?php
require_once 'includes/header.php';
require_once '../includes/functions.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Fetch teacher data
    try {
        $stmt = $pdo->prepare("SELECT * FROM teachers WHERE id = ?");
        $stmt->execute([$id]);
        $teacher = $stmt->fetch();
        
        if (!$teacher) {
            // Log teacher not found error
            log_activity(
                'Teacher Not Found',
                'Attempted to edit non-existent teacher: ' . json_encode([
                    'teacher_id' => $id
                ]),
                $_SESSION['admin_id']
            );
            $_SESSION['error'] = "Data guru tidak ditemukan!";
            header("Location: teachers.php");
            exit();
        }
    } catch(PDOException $e) {
        // Log database error
        log_activity(
            'Teacher Fetch Failed',
            'Failed to fetch teacher data: ' . json_encode([
                'teacher_id' => $id,
                'error' => $e->getMessage()
            ]),
            $_SESSION['admin_id']
        );
        die("Error: " . $e->getMessage());
    }
} else {
    // Log invalid ID error
    log_activity(
        'Invalid Teacher ID',
        'Attempted to edit teacher with invalid ID',
        $_SESSION['admin_id']
    );
    $_SESSION['error'] = "ID tidak valid!";
    header("Location: teachers.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nip = $_POST['nip'];
    $name = $_POST['name'];
    $position = $_POST['position'];
    $subject = $_POST['subject'];
    $education = $_POST['education'];
    $status = $_POST['status'];
    
    // Handle image upload
    $photo = $teacher['photo']; // Keep existing photo
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['photo']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            // Create unique filename
            $newname = uniqid() . '.' . $filetype;
            $upload_path = '../assets/uploads/teachers/' . $newname;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                // Delete old photo if exists
                if ($teacher['photo'] && file_exists('../assets/uploads/teachers/' . $teacher['photo'])) {
                    unlink('../assets/uploads/teachers/' . $teacher['photo']);
                    // Log old photo deletion
                    log_activity(
                        'Teacher Photo Deleted',
                        'Deleted old photo: ' . json_encode([
                            'teacher_id' => $id,
                            'old_photo' => $teacher['photo']
                        ]),
                        $_SESSION['admin_id']
                    );
                }
                $photo = $newname;
                // Log successful photo update
                log_activity(
                    'Teacher Photo Updated',
                    'Updated photo for teacher: ' . json_encode([
                        'teacher_id' => $id,
                        'name' => $name,
                        'old_photo' => $teacher['photo'],
                        'new_photo' => $newname
                    ]),
                    $_SESSION['admin_id']
                );
            } else {
                // Log upload failure
                log_activity(
                    'Teacher Photo Update Failed',
                    'Failed to update photo: ' . json_encode([
                        'teacher_id' => $id,
                        'name' => $name,
                        'original_name' => $filename,
                        'error' => error_get_last()
                    ]),
                    $_SESSION['admin_id']
                );
                $_SESSION['error'] = "Gagal mengupload foto!";
            }
        } else {
            // Log invalid file type
            log_activity(
                'Teacher Photo Invalid Type',
                'Invalid photo type for update: ' . json_encode([
                    'teacher_id' => $id,
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
        $stmt = $pdo->prepare("UPDATE teachers SET nip = ?, name = ?, photo = ?, position = ?, subject = ?, education = ?, status = ? WHERE id = ?");
        if ($stmt->execute([$nip, $name, $photo, $position, $subject, $education, $status, $id])) {
            // Log successful teacher update
            log_activity(
                'Teacher Updated',
                'Updated teacher data: ' . json_encode([
                    'teacher_id' => $id,
                    'name' => $name,
                    'nip' => $nip,
                    'position' => $position,
                    'subject' => $subject,
                    'status' => $status,
                    'photo_changed' => $photo !== $teacher['photo']
                ]),
                $_SESSION['admin_id']
            );
            
            $_SESSION['success'] = "Data guru berhasil diperbarui!";
            header("Location: teachers.php");
            exit();
        }
    } catch(PDOException $e) {
        // Log database error
        log_activity(
            'Teacher Update Failed',
            'Failed to update teacher: ' . json_encode([
                'teacher_id' => $id,
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
                <h1>Edit Data Guru</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="teachers.php">Guru</a></li>
                    <li class="breadcrumb-item active">Edit Guru</li>
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
                        <h3 class="card-title">Form Edit Data Guru</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="nip">NIP</label>
                                <input type="text" class="form-control" id="nip" name="nip" value="<?php echo htmlspecialchars($teacher['nip']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="name">Nama</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($teacher['name']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="position">Jabatan</label>
                                <input type="text" class="form-control" id="position" name="position" value="<?php echo htmlspecialchars($teacher['position']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="subject">Mata Pelajaran</label>
                                <input type="text" class="form-control" id="subject" name="subject" value="<?php echo htmlspecialchars($teacher['subject']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="education">Pendidikan</label>
                                <textarea class="form-control" id="education" name="education" rows="3" required><?php echo htmlspecialchars($teacher['education']); ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="photo">Foto</label>
                                <?php if ($teacher['photo']): ?>
                                    <div class="mb-2">
                                        <img src="../assets/uploads/teachers/<?php echo $teacher['photo']; ?>" alt="Current Photo" class="img-thumbnail" style="max-width: 200px;">
                                    </div>
                                <?php endif; ?>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="photo" name="photo">
                                    <label class="custom-file-label" for="photo">Pilih file untuk mengganti foto</label>
                                </div>
                                <small class="form-text text-muted">Format yang diizinkan: JPG, JPEG, PNG, GIF</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="active" <?php echo $teacher['status'] == 'active' ? 'selected' : ''; ?>>Aktif</option>
                                    <option value="inactive" <?php echo $teacher['status'] == 'inactive' ? 'selected' : ''; ?>>Tidak Aktif</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                <a href="teachers.php" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
