<?php
require_once 'includes/header.php';
require_once '../includes/functions.php';

// Handle validation logging
if (isset($_POST['action']) && $_POST['action'] === 'log_validation') {
    log_activity(
        'Teacher Validation Failed',
        json_encode([
            'field' => $_POST['field'],
            'message' => $_POST['message'],
            'value' => $_POST['value']
        ]),
        $_SESSION['admin_id']
    );
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['action'])) {
    $nip = trim($_POST['nip']);
    $name = trim($_POST['name']);
    $position = trim($_POST['position']);
    $subject = trim($_POST['subject']);
    $education = trim($_POST['education']);
    $status = trim($_POST['status']);

    // Server-side validation
    $errors = [];
    
    if (!preg_match('/^\d{18}$/', $nip)) {
        log_activity(
            'Teacher Validation Failed',
            'Invalid NIP format: ' . json_encode([
                'nip' => $nip,
                'pattern' => '18 digits'
            ]),
            $_SESSION['admin_id']
        );
        $errors[] = "NIP harus terdiri dari 18 digit angka!";
    }

    if (strlen($name) < 3) {
        log_activity(
            'Teacher Validation Failed',
            'Name too short: ' . json_encode([
                'name' => $name,
                'min_length' => 3
            ]),
            $_SESSION['admin_id']
        );
        $errors[] = "Nama minimal 3 karakter!";
    }

    if (!empty($errors)) {
        $_SESSION['error'] = implode("<br>", $errors);
        header("Location: add-teacher.php");
        exit();
    }
    
    // Handle image upload
    $photo = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['photo']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            // Create unique filename
            $newname = uniqid() . '.' . $filetype;
            $upload_path = '../assets/uploads/teachers/' . $newname;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                $photo = $newname;
                // Log successful photo upload
                log_activity(
                    'Teacher Photo Uploaded',
                    'Uploaded photo for teacher: ' . json_encode([
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
                    'Teacher Photo Upload Failed',
                    'Failed to upload photo: ' . json_encode([
                        'name' => $name,
                        'original_name' => $filename,
                        'upload_path' => $upload_path,
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
                'Invalid photo type uploaded: ' . json_encode([
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
        $stmt = $pdo->prepare("INSERT INTO teachers (nip, name, photo, position, subject, education, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$nip, $name, $photo, $position, $subject, $education, $status])) {
            // Log successful teacher creation
            log_activity(
                'Teacher Created',
                'Created new teacher: ' . json_encode([
                    'nip' => $nip,
                    'name' => $name,
                    'position' => $position,
                    'subject' => $subject,
                    'status' => $status,
                    'has_photo' => !empty($photo)
                ]),
                $_SESSION['admin_id']
            );
            
            $_SESSION['success'] = "Guru berhasil ditambahkan!";
            header("Location: teachers.php");
            exit();
        }
    } catch(PDOException $e) {
        // Log database error
        log_activity(
            'Teacher Creation Failed',
            'Failed to create teacher: ' . json_encode([
                'nip' => $nip,
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
                <h1>Tambah Guru</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="teachers.php">Guru</a></li>
                    <li class="breadcrumb-item active">Tambah Guru</li>
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
                        <h3 class="card-title">Form Tambah Guru</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="nip">NIP</label>
                                <input type="text" class="form-control" id="nip" name="nip" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="name">Nama</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="position">Jabatan</label>
                                <input type="text" class="form-control" id="position" name="position" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="subject">Mata Pelajaran</label>
                                <input type="text" class="form-control" id="subject" name="subject" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="education">Pendidikan</label>
                                <textarea class="form-control" id="education" name="education" rows="3" required></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="photo">Foto</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="photo" name="photo">
                                    <label class="custom-file-label" for="photo">Pilih file</label>
                                </div>
                                <small class="form-text text-muted">Format yang diizinkan: JPG, JPEG, PNG, GIF</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Tidak Aktif</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="teachers.php" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Additional Script for Form Validation -->
<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const nip = document.getElementById('nip').value;
    
    // Basic NIP validation
    if (!/^\d{18}$/.test(nip)) {
        e.preventDefault();
        // Log validation error
        fetch('', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'action': 'log_validation',
                'field': 'nip',
                'message': 'Invalid NIP format',
                'value': nip
            })
        });
        alert('NIP harus terdiri dari 18 digit angka!');
        return;
    }
});

// Update file input label with selected filename
document.querySelector('.custom-file-input').addEventListener('change', function(e) {
    const fileName = e.target.files[0].name;
    e.target.nextElementSibling.textContent = fileName;
});
</script>

<?php require_once 'includes/footer.php'; ?>
