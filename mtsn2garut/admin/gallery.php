<?php
require_once 'includes/header.php';
require_once '../includes/functions.php';

try {
    $stmt = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC");
    $gallery_items = $stmt->fetchAll();
} catch(PDOException $e) {
    // Log database error
    log_activity(
        'Gallery Listing Failed',
        'Failed to fetch gallery list: ' . $e->getMessage(),
        $_SESSION['admin_id']
    );
    die("Error: " . $e->getMessage());
}

// Log gallery list access
log_activity(
    'Gallery List Viewed',
    'Accessed gallery management page',
    $_SESSION['admin_id']
);
?>

<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Manajemen Galeri</h1>
            </div>
            <div class="col-sm-6">
                <a href="add-gallery.php" class="btn btn-primary float-sm-right">
                    <i class="fas fa-plus"></i> Tambah Foto
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- Filter Buttons -->
        <div class="mb-4">
            <div class="btn-group filter-buttons" role="group">
                <button type="button" class="btn btn-outline-primary active" data-filter="all">Semua</button>
                <button type="button" class="btn btn-outline-primary" data-filter="kegiatan">Kegiatan</button>
                <button type="button" class="btn btn-outline-primary" data-filter="fasilitas">Fasilitas</button>
                <button type="button" class="btn btn-outline-primary" data-filter="prestasi">Prestasi</button>
                <button type="button" class="btn btn-outline-primary" data-filter="lainnya">Lainnya</button>
            </div>
        </div>

        <!-- Gallery Grid -->
        <div class="row">
            <?php foreach($gallery_items as $item): ?>
            <div class="col-md-4 col-sm-6 mb-4 gallery-item" data-category="<?php echo strtolower($item['category']); ?>">
                <div class="card h-100">
                    <div class="gallery-image">
                        <img src="../assets/uploads/gallery/<?php echo htmlspecialchars($item['image']); ?>"
                             class="card-img-top"
                             alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <div class="gallery-overlay">
                            <button type="button" 
                                    class="btn btn-light btn-sm" 
                                    data-toggle="modal" 
                                    data-target="#galleryModal<?php echo $item['id']; ?>">
                                <i class="fas fa-expand"></i>
                            </button>
                            <a href="edit-gallery.php?id=<?php echo $item['id']; ?>" 
                               class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="delete-gallery.php?id=<?php echo $item['id']; ?>" 
                               class="btn btn-danger btn-sm delete-confirm"
                               data-title="<?php echo htmlspecialchars($item['title']); ?>">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h5>
                        <p class="card-text">
                            <small class="text-muted">
                                <i class="fas fa-tag"></i> <?php echo htmlspecialchars($item['category']); ?>
                            </small>
                            <small class="text-muted float-right">
                                <i class="fas fa-calendar"></i> 
                                <?php echo date('d M Y', strtotime($item['created_at'])); ?>
                            </small>
                        </p>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="galleryModal<?php echo $item['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?php echo htmlspecialchars($item['title']); ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <img src="../assets/uploads/gallery/<?php echo htmlspecialchars($item['image']); ?>"
                                     class="img-fluid"
                                     alt="<?php echo htmlspecialchars($item['title']); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Custom CSS -->
<style>
.gallery-image {
    position: relative;
    overflow: hidden;
}

.gallery-image img {
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.gallery-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.gallery-image:hover img {
    transform: scale(1.1);
}

.gallery-image:hover .gallery-overlay {
    opacity: 1;
}

.filter-buttons {
    margin-bottom: 20px;
}

.filter-buttons .btn {
    padding: 8px 20px;
}

.gallery-item {
    transition: transform 0.3s ease, opacity 0.3s ease;
}

.gallery-item.hidden {
    display: none;
    opacity: 0;
    transform: scale(0.8);
}

.modal-body img {
    width: 100%;
}
</style>

<!-- Custom JavaScript -->
<script>
$(function() {
    // Filter functionality with logging
    $('.filter-buttons .btn').on('click', function() {
        const filter = $(this).data('filter');
        
        // Update active button
        $('.filter-buttons .btn').removeClass('active');
        $(this).addClass('active');

        // Log filter action
        $.post('', {
            action: 'log_filter',
            filter: filter
        });
        
        // Filter items
        if (filter === 'all') {
            $('.gallery-item').removeClass('hidden');
        } else {
            $('.gallery-item').each(function() {
                if ($(this).data('category') === filter) {
                    $(this).removeClass('hidden');
                } else {
                    $(this).addClass('hidden');
                }
            });
        }
    });

    // Delete confirmation with logging
    $('.delete-confirm').on('click', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        const title = $(this).data('title');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: `Akan menghapus foto "${title}"?`,
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
