<?php
require_once 'includes/header.php';
require_once '../includes/functions.php';

try {
    $stmt = $pdo->query("SELECT * FROM facilities ORDER BY name ASC");
    $facilities = $stmt->fetchAll();
} catch(PDOException $e) {
    // Log database error
    log_activity(
        'Facilities Listing Failed',
        'Failed to fetch facilities list: ' . $e->getMessage(),
        $_SESSION['admin_id']
    );
    die("Error: " . $e->getMessage());
}

// Log facilities list access
log_activity(
    'Facilities List Viewed',
    'Accessed facilities management page',
    $_SESSION['admin_id']
);
?>

<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Manajemen Fasilitas</h1>
            </div>
            <div class="col-sm-6">
                <a href="add-facility.php" class="btn btn-primary float-sm-right">Tambah Fasilitas</a>
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
                        <h3 class="card-title">Daftar Fasilitas</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($facilities as $facility): ?>
                            <div class="col-md-4 col-sm-6 mb-4">
                                <div class="card h-100">
                                    <?php if($facility['image']): ?>
                                        <img src="../assets/uploads/facilities/<?php echo htmlspecialchars($facility['image']); ?>" 
                                             class="card-img-top"
                                             alt="<?php echo htmlspecialchars($facility['name']); ?>"
                                             style="height: 200px; object-fit: cover;">
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($facility['name']); ?></h5>
                                        <p class="card-text"><?php echo nl2br(htmlspecialchars($facility['description'])); ?></p>
                                    </div>
                                    <div class="card-footer bg-white border-top-0">
                                        <div class="btn-group">
                                            <a href="edit-facility.php?id=<?php echo $facility['id']; ?>" 
                                               class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="delete-facility.php?id=<?php echo $facility['id']; ?>" 
                                               class="btn btn-danger btn-sm delete-confirm" 
                                               data-name="<?php echo htmlspecialchars($facility['name']); ?>">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Custom CSS -->
<style>
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}

.card-footer {
    background-color: transparent;
    padding-top: 0;
}

.btn-group {
    width: 100%;
}

.btn-group .btn {
    flex: 1;
}
</style>

<!-- Page specific script -->
<script>
$(function () {
    // Initialize delete confirmation
    $('.delete-confirm').on('click', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data fasilitas yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
