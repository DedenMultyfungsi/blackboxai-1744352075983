<?php
require_once 'includes/config.php';
$page_title = "Prestasi - MTsN 2 Garut";
require_once 'includes/header.php';

// Fetch all achievements
try {
    $stmt = $pdo->query("SELECT * FROM achievements ORDER BY date DESC");
    $achievements = $stmt->fetchAll();
    
    // Get unique categories
    $categories = array_unique(array_column($achievements, 'category'));
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!-- Hero Section -->
<div class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 font-weight-bold mb-3">Prestasi MTsN 2 Garut</h1>
                <p class="lead mb-0">Berbagai pencapaian membanggakan dari siswa dan sekolah kami</p>
            </div>
        </div>
    </div>
</div>

<!-- Achievements Section -->
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

        <!-- Achievements Grid -->
        <div class="row achievement-grid">
            <?php foreach($achievements as $achievement): ?>
            <div class="col-md-6 col-lg-4 mb-4 achievement-item" data-category="<?php echo strtolower($achievement['category']); ?>">
                <div class="card h-100 shadow-sm hover-card">
                    <?php if($achievement['image']): ?>
                        <img src="assets/uploads/achievements/<?php echo htmlspecialchars($achievement['image']); ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($achievement['title']); ?>"
                             style="height: 200px; object-fit: cover;">
                    <?php endif; ?>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge badge-primary"><?php echo htmlspecialchars($achievement['category']); ?></span>
                            <small class="text-muted"><?php echo date('d M Y', strtotime($achievement['date'])); ?></small>
                        </div>
                        <h5 class="card-title"><?php echo htmlspecialchars($achievement['title']); ?></h5>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars(substr($achievement['description'], 0, 150))) . '...'; ?></p>
                        <button type="button" class="btn btn-link p-0 read-more" data-toggle="modal" data-target="#achievementModal<?php echo $achievement['id']; ?>">
                            Baca selengkapnya
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal for full description -->
            <div class="modal fade" id="achievementModal<?php echo $achievement['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?php echo htmlspecialchars($achievement['title']); ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <?php if($achievement['image']): ?>
                                <img src="assets/uploads/achievements/<?php echo htmlspecialchars($achievement['image']); ?>" 
                                     class="img-fluid mb-3" 
                                     alt="<?php echo htmlspecialchars($achievement['title']); ?>">
                            <?php endif; ?>
                            <p class="text-muted">
                                <i class="fas fa-calendar-alt"></i> <?php echo date('d M Y', strtotime($achievement['date'])); ?>
                                <span class="mx-2">|</span>
                                <i class="fas fa-tag"></i> <?php echo htmlspecialchars($achievement['category']); ?>
                            </p>
                            <div class="achievement-description">
                                <?php echo nl2br(htmlspecialchars($achievement['description'])); ?>
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
.hover-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}

.achievement-item {
    transition: opacity 0.4s ease, transform 0.4s ease;
}

.achievement-item.hidden {
    opacity: 0;
    transform: scale(0.8);
    display: none;
}

.badge {
    padding: 8px 12px;
    font-size: 0.8rem;
}

.filter-buttons .btn {
    margin: 0 5px;
    border-radius: 20px;
    padding: 8px 20px;
}

.modal-body img {
    max-height: 400px;
    width: 100%;
    object-fit: cover;
}

.achievement-description {
    line-height: 1.8;
    text-align: justify;
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

<!-- Custom JavaScript for filtering -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-buttons .btn');
    const achievementItems = document.querySelectorAll('.achievement-item');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter items
            achievementItems.forEach(item => {
                const category = item.getAttribute('data-category');
                if (filter === 'all' || filter === category) {
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
