<?php
require_once 'includes/header.php';
require_once '../includes/functions.php';

// Fetch teachers from the database
try {
    $stmt = $pdo->query("SELECT * FROM teachers ORDER BY name ASC");
    $teachers = $stmt->fetchAll();
} catch(PDOException $e) {
    // Log database error
    log_activity(
        'Teachers Listing Failed',
        'Failed to fetch teachers list: ' . $e->getMessage(),
        $_SESSION['admin_id']
    );
    die("Error: " . $e->getMessage());
}

// Log teachers list access
log_activity(
    'Teachers List Viewed',
    'Accessed teachers management page',
    $_SESSION['admin_id']
);
?>

<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Manajemen Guru</h1>
            </div>
            <div class="col-sm-6">
                <a href="add-teacher.php" class="btn btn-primary float-sm-right">Tambah Guru</a>
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
                        <h3 class="card-title">Daftar Guru</h3>
                    </div>
                    <div class="card-body">
                        <table id="teachersTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>NIP</th>
                                    <th>Nama</th>
                                    <th>Jabatan</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($teachers as $teacher): ?>
                                <tr>
                                    <td><?php echo $teacher['id']; ?></td>
                                    <td><?php echo htmlspecialchars($teacher['nip']); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['name']); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['position']); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['subject']); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['status']); ?></td>
                                    <td>
                                        <a href="edit-teacher.php?id=<?php echo $teacher['id']; ?>" 
                                           class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="delete-teacher.php?id=<?php echo $teacher['id']; ?>" 
                                           class="btn btn-danger btn-sm delete-confirm"
                                           data-name="<?php echo htmlspecialchars($teacher['name']); ?>">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Page specific script -->
<script>
$(function () {
    $("#teachersTable").DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });

    // Delete confirmation
    $('.delete-confirm').on('click', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        const name = $(this).data('name');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: `Akan menghapus data guru "${name}"?`,
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
