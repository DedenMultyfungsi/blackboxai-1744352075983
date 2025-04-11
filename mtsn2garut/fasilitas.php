<?php
require_once 'includes/config.php';
$page_title = "Fasilitas - MTsN 2 Garut";
require_once 'includes/header.php';

// Fetch all facilities
try {
    $stmt = $pdo->query("SELECT * FROM facilities ORDER BY name ASC");
    $facilities = $stmt->fetchAll();
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!-- Hero Section -->
<div class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 font-weight-bold mb-3">Fasilitas Sekolah</h1>
                <p class="lead mb-0">Berbagai fasilitas modern untuk mendukung kegiatan belajar mengajar</p>
            </div>
        </div>
    </div>
</div>

<!-- Facilities Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <?php foreach($facilities as $facility): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 facility-card shadow-sm">
                    <?php if($facility['image']): ?>
                    <div class="facility-image">
                        <img src="assets/uploads/facilities/<?php echo htmlspecialchars($facility['image']); ?>"
                             class="card-img-top"
                             alt="<?php echo htmlspecialchars($facility['name']); ?>">
                        <div class="facility-overlay">
                            <button type="button" 
                                    class="btn btn-light" 
                                    data-toggle="modal" 
                                    data-target="#facilityModal<?php echo $facility['id']; ?>">
                                <i class="fas fa-expand-alt"></i> Lihat Detail
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($facility['name']); ?></h5>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars(substr($facility['description'], 0, 100))); ?>...</p>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="facilityModal<?php echo $facility['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?php echo htmlspecialchars($facility['name']); ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <?php if($facility['image']): ?>
                            <img src="assets/uploads/facilities/<?php echo htmlspecialchars($facility['image']); ?>"
                                 class="img-fluid mb-4 rounded"
                                 alt="<?php echo htmlspecialchars($facility['name']); ?>">
                            <?php endif; ?>
                            <div class="facility-description">
                                <?php echo nl2br(htmlspecialchars($facility['description'])); ?>
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
.facility-card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.facility-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}

.facility-image {
    position: relative;
    overflow: hidden;
}

.facility-image img {
    height: 250px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.facility-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.facility-card:hover .facility-image img {
    transform: scale(1.1);
}

.facility-card:hover .facility-overlay {
    opacity: 1;
}

.facility-description {
    line-height: 1.8;
    text-align: justify;
}

.modal-content {
    border-radius: 15px;
    overflow: hidden;
}

.modal-body img {
    width: 100%;
    max-height: 400px;
    object-fit: cover;
}

@media (max-width: 768px) {
    .facility-overlay {
        opacity: 1;
        background: rgba(0, 0, 0, 0.3);
    }
}

.bg-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0043a8 100%)!important;
}

.btn-light {
    border-radius: 25px;
    padding: 8px 20px;
    font-weight: 500;
}

.btn-light i {
    margin-right: 5px;
}
</style>

<!-- Custom JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Add animation class to cards when they come into view
    const cards = document.querySelectorAll('.facility-card');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1
    });
    
    cards.forEach(card => {
        observer.observe(card);
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
