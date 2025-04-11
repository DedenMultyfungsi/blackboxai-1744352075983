<?php
require_once 'includes/config.php';
$page_title = "Galeri - MTsN 2 Garut";
require_once 'includes/header.php';

// Fetch all gallery items
try {
    $stmt = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC");
    $gallery_items = $stmt->fetchAll();
    
    // Get unique categories
    $categories = array_unique(array_column($gallery_items, 'category'));
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!-- Add Lightbox2 CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">

<!-- Hero Section -->
<div class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 font-weight-bold mb-3">Galeri Foto</h1>
                <p class="lead mb-0">Dokumentasi kegiatan dan momen berkesan di MTsN 2 Garut</p>
            </div>
        </div>
    </div>
</div>

<!-- Gallery Section -->
<section class="py-5">
    <div class="container">
        <!-- Filter Buttons -->
        <div class="text-center mb-5">
            <div class="btn-group filter-buttons" role="group">
                <button type="button" class="btn btn-outline-primary active" data-filter="all">Semua</button>
                <?php foreach($categories as $category): ?>
                    <button type="button" class="btn btn-outline-primary" data-filter="<?php echo strtolower($category); ?>">
                        <?php echo htmlspecialchars($category); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Gallery Grid -->
        <div class="row gallery-grid">
            <?php foreach($gallery_items as $item): ?>
            <div class="col-md-4 col-sm-6 mb-4 gallery-item" data-category="<?php echo strtolower($item['category']); ?>">
                <div class="gallery-card">
                    <a href="assets/uploads/gallery/<?php echo htmlspecialchars($item['image']); ?>" 
                       data-lightbox="gallery" 
                       data-title="<?php echo htmlspecialchars($item['title']); ?>">
                        <div class="gallery-image">
                            <img src="assets/uploads/gallery/<?php echo htmlspecialchars($item['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['title']); ?>"
                                 class="img-fluid">
                            <div class="gallery-overlay">
                                <i class="fas fa-search-plus"></i>
                            </div>
                        </div>
                        <div class="gallery-info">
                            <h5><?php echo htmlspecialchars($item['title']); ?></h5>
                            <span class="badge badge-primary"><?php echo htmlspecialchars($item['category']); ?></span>
                            <small class="text-muted d-block mt-1">
                                <?php echo date('d M Y', strtotime($item['created_at'])); ?>
                            </small>
                        </div>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Custom CSS -->
<style>
.gallery-card {
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 3px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.gallery-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.gallery-image {
    position: relative;
    padding-top: 75%; /* 4:3 Aspect Ratio */
    overflow: hidden;
}

.gallery-image img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.gallery-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.gallery-overlay i {
    color: white;
    font-size: 2rem;
}

.gallery-card:hover .gallery-image img {
    transform: scale(1.1);
}

.gallery-card:hover .gallery-overlay {
    opacity: 1;
}

.gallery-info {
    padding: 1rem;
}

.gallery-info h5 {
    margin: 0 0 0.5rem;
    font-size: 1.1rem;
    color: var(--dark-color);
}

.filter-buttons {
    margin-bottom: 2rem;
}

.filter-buttons .btn {
    border-radius: 25px;
    padding: 8px 20px;
    margin: 0 5px;
}

.badge {
    padding: 5px 10px;
    border-radius: 15px;
}

.gallery-item {
    transition: opacity 0.4s ease, transform 0.4s ease;
}

.gallery-item.hidden {
    opacity: 0;
    transform: scale(0.8);
    display: none;
}

/* Lightbox Customization */
.lb-data .lb-caption {
    font-size: 1.1rem;
    font-weight: bold;
}

.lb-data .lb-number {
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .filter-buttons {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
    }
    
    .filter-buttons .btn {
        margin: 5px;
    }
}
</style>

<!-- Add Lightbox2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

<!-- Custom JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Lightbox Configuration
    lightbox.option({
        'resizeDuration': 200,
        'wrapAround': true,
        'albumLabel': "Foto %1 dari %2"
    });

    // Filter functionality
    const filterButtons = document.querySelectorAll('.filter-buttons .btn');
    const galleryItems = document.querySelectorAll('.gallery-item');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter items
            galleryItems.forEach(item => {
                if (filter === 'all' || item.getAttribute('data-category') === filter) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
