<?php
require_once 'includes/header.php';
require_once '../includes/functions.php';

// Fetch achievements from database
try {
    $stmt = $pdo->query("SELECT * FROM achievements ORDER BY date DESC");
    $achievements = $stmt->fetchAll();
} catch(PDOException $e) {
    // Log database error
    log_activity(
        'Achievements Listing Failed',
        'Failed to fetch achievements list: ' . $e->getMessage(),
        $_SESSION['admin_id']
    );
    die("Error: " . $e->getMessage());
}

// Log achievements list access
log_activity(
    'Achievements List Viewed',
    'Accessed achievements management page',
    $_SESSION['admin_id']
);
?>

<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Manajemen Prestasi</h1>
            </div>
            <div class="col-sm-6">
                <a href="add-achievement.php" class="btn btn-primary float-sm-right">Tambah Prestasi</a>
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
                        <h3 class="card-title">Daftar Prestasi</h3>
                    </div>
                    <div class="card-body">
                        <table id="achievementsTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">ID</th>
                                    <th width="20%">Judul</th>
                                    <th width="25%">Deskripsi</th>
                                    <th width="15%">Kategori</th>
                                    <th width="15%">Tanggal</th>
                                    <th width="10%">Gambar</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($achievements as $achievement): ?>
                                <tr>
                                    <td><?php echo $achievement['id']; ?></td>
                                    <td><?php echo htmlspecialchars($achievement['title']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($achievement['description'], 0, 100)) . '...'; ?></td>
                                    <td><?php echo htmlspecialchars($achievement['category']); ?></td>
                                    <td><?php echo date('d-m-Y', strtotime($achievement['date'])); ?></td>
                                    <td>
                                        <?php if($achievement['image']): ?>
                                            <img src="../assets/uploads/achievements/<?php echo $achievement['image']; ?>" 
                                                 alt="Achievement Image" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 50px;">
                                        <?php else: ?>
                                            <span class="text-muted">No image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="edit-achievement.php?id=<?php echo $achievement['id']; ?>" 
                                               class="btn btn-warning btn-sm" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete-achievement.php?id=<?php echo $achievement['id']; ?>" 
                                               class="btn btn-danger btn-sm delete-confirm" 
                                               title="Delete"
                                               data-title="<?php echo htmlspecialchars($achievement['title']); ?>">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
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
    $("#achievementsTable").DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print"],
        "order": [[4, "desc"]], // Sort by date column descending
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    }).buttons().container().appendTo('#achievementsTable_wrapper .col-md-6:eq(0)');

    // Delete confirmation
    $('.delete-confirm').on('click', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        const title = $(this).data('title');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: `Akan menghapus prestasi "${title}"?`,
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

    // Log export actions
    $('.dt-button').on('click', function() {
        const exportType = $(this).text().toLowerCase();
        log_activity(
            'Achievements Export',
            'Exported achievements list as ' + exportType,
            <?php echo $_SESSION['admin_id']; ?>
        );
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
