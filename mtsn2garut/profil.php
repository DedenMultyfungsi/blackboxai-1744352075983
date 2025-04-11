<?php
require_once 'includes/config.php';
$page_title = "Profil - MTsN 2 Garut";
require_once 'includes/header.php';

// Fetch school profile
try {
    $stmt = $pdo->query("SELECT * FROM school_profile LIMIT 1");
    $profile = $stmt->fetch();
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!-- Hero Section -->
<div class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 font-weight-bold mb-3">Profil Sekolah</h1>
                <p class="lead mb-0">Mengenal lebih dekat MTsN 2 Garut</p>
            </div>
        </div>
    </div>
</div>

<!-- Profile Section -->
<section class="py-5">
    <div class="container">
        <!-- Vision & Mission -->
        <div class="row mb-5">
            <div class="col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-box bg-primary text-white me-3">
                                <i class="fas fa-eye"></i>
                            </div>
                            <h3 class="mb-0">Visi</h3>
                        </div>
                        <div class="vision-content">
                            <?php echo nl2br(htmlspecialchars($profile['vision'])); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-box bg-primary text-white me-3">
                                <i class="fas fa-bullseye"></i>
                            </div>
                            <h3 class="mb-0">Misi</h3>
                        </div>
                        <div class="mission-content">
                            <?php echo nl2br(htmlspecialchars($profile['mission'])); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- History -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-box bg-primary text-white me-3">
                                <i class="fas fa-history"></i>
                            </div>
                            <h3 class="mb-0">Sejarah</h3>
                        </div>
                        <div class="history-content">
                            <?php echo nl2br(htmlspecialchars($profile['history'])); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- School Information -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-box bg-primary text-white me-3">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <h3 class="mb-0">Informasi Sekolah</h3>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li class="mb-3">
                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                        <strong>Alamat:</strong><br>
                                        <?php echo nl2br(htmlspecialchars($profile['address'])); ?>
                                    </li>
                                    <li class="mb-3">
                                        <i class="fas fa-phone text-primary me-2"></i>
                                        <strong>Telepon:</strong><br>
                                        <?php echo htmlspecialchars($profile['phone']); ?>
                                    </li>
                                    <li class="mb-3">
                                        <i class="fas fa-envelope text-primary me-2"></i>
                                        <strong>Email:</strong><br>
                                        <?php echo htmlspecialchars($profile['email']); ?>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <div class="embed-responsive embed-responsive-16by9">
                                    <iframe 
                                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.2988878671586!2d107.9047873!3d-7.2145283!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zN8KwMTInNTIuMyJTIDEwN8KwNTQnMTcuMiJF!5e0!3m2!1sen!2sid!4v1635134567890!5m2!1sen!2sid" 
                                        width="100%" 
                                        height="300" 
                                        style="border:0;" 
                                        allowfullscreen="" 
                                        loading="lazy">
                                    </iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Custom CSS -->
<style>
.icon-box {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.icon-box i {
    font-size: 1.5rem;
}

.card {
    border-radius: 15px;
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
}

.vision-content, .mission-content {
    font-size: 1.1rem;
    line-height: 1.8;
}

.history-content {
    font-size: 1.1rem;
    line-height: 1.8;
    text-align: justify;
}

.bg-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0043a8 100%)!important;
}

@media (max-width: 768px) {
    .icon-box {
        width: 40px;
        height: 40px;
    }

    .icon-box i {
        font-size: 1.2rem;
    }

    h3 {
        font-size: 1.5rem;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>
