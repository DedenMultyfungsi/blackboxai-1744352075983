<?php
require_once 'includes/header.php';
require_once '../includes/functions.php';

// Fetch news from the database
try {
    $stmt = $pdo->query("SELECT * FROM news ORDER BY created_at DESC");
    $news = $stmt->fetchAll();
} catch(PDOException $e) {
    // Log database error
    log_activity(
        'News Listing Failed',
        'Failed to fetch news list: ' . $e->getMessage(),
        $_SESSION['admin_id']
    );
    die("Error: " . $e->getMessage());
}

// Log news list access
log_activity(
    'News List Viewed',
    'Accessed news management page',
    $_SESSION['admin_id']
);
?>

<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Manajemen Berita</h1>
            </div>
            <div class="col-sm-6">
                <a href="add-news.php" class="btn btn-primary float-sm-right">Tambah Berita</a>
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
                        <h3 class="card-title">Daftar Berita</h3>
                    </div>
                    <div class="card-body">
                        <table id="newsTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Judul</th>
                                    <th>Konten</th>
                                    <th>Penulis</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($news as $item): ?>
                                <tr>
                                    <td><?php echo $item['id']; ?></td>
                                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($item['content'], 0, 50)) . '...'; ?></td>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo date('d-m-Y', strtotime($item['created_at'])); ?></td>
                                    <td>
                                        <a href="edit-news.php?id=<?php echo $item['id']; ?>" 
                                           class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="delete-news.php?id=<?php echo $item['id']; ?>" 
                                           class="btn btn-danger btn-sm delete-confirm"
                                           data-title="<?php echo htmlspecialchars($item['title']); ?>">
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

<!-- Custom JavaScript -->
<script>
$(function() {
    // Initialize DataTable
    $("#newsTable").DataTable({
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
        const title = $(this).data('title');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: `Akan menghapus berita "${title}"?`,
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
