<?php
require_once 'includes/config.php';
$page_title = "Kontak - MTsN 2 Garut";
require_once 'includes/header.php';

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $email, $subject, $message])) {
            $success = "Pesan Anda telah berhasil dikirim. Kami akan segera menghubungi Anda.";
        } else {
            $error = "Gagal mengirim pesan. Silakan coba lagi.";
        }
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!-- Hero Section -->
<div class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 font-weight-bold mb-3">Hubungi Kami</h1>
                <p class="lead mb-0">Silakan hubungi kami untuk informasi lebih lanjut</p>
            </div>
        </div>
    </div>
</div>

<!-- Contact Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Contact Information -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h4 class="mb-4">Informasi Kontak</h4>
                        <div class="contact-info">
                            <div class="d-flex mb-3">
                                <div class="icon-box">
                                    <i class="fas fa-map-marker-alt text-primary"></i>
                                </div>
                                <div class="info-box">
                                    <h5>Alamat</h5>
                                    <p class="mb-0">Jl. Sekolah No. 123, Garut</p>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <div class="icon-box">
                                    <i class="fas fa-phone text-primary"></i>
                                </div>
                                <div class="info-box">
                                    <h5>Telepon</h5>
                                    <p class="mb-0">(0262) 123456</p>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <div class="icon-box">
                                    <i class="fas fa-envelope text-primary"></i>
                                </div>
                                <div class="info-box">
                                    <h5>Email</h5>
                                    <p class="mb-0">info@mtsn2garut.sch.id</p>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="icon-box">
                                    <i class="fas fa-clock text-primary"></i>
                                </div>
                                <div class="info-box">
                                    <h5>Jam Operasional</h5>
                                    <p class="mb-0">Senin - Jumat: 07:00 - 15:00</p>
                                    <p class="mb-0">Sabtu: 07:00 - 12:00</p>
                                </div>
                            </div>
                        </div>

                        <h4 class="mt-4 mb-3">Sosial Media</h4>
                        <div class="social-links">
                            <a href="#" class="btn btn-outline-primary me-2"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="btn btn-outline-primary me-2"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="btn btn-outline-primary me-2"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="btn btn-outline-primary"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h4 class="mb-4">Kirim Pesan</h4>
                        
                        <?php if($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo $success; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="" method="post" id="contactForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="name">Nama Lengkap</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="subject">Subjek</label>
                                <input type="text" class="form-control" id="subject" name="subject" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="message">Pesan</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Kirim Pesan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.2988878671586!2d107.9047873!3d-7.2145283!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zN8KwMTInNTIuMyJTIDEwN8KwNTQnMTcuMiJF!5e0!3m2!1sen!2sid!4v1635134567890!5m2!1sen!2sid" 
                            width="100%" 
                            height="450" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Custom CSS -->
<style>
.icon-box {
    width: 40px;
    height: 40px;
    background: rgba(13, 110, 253, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
}

.icon-box i {
    font-size: 1.2rem;
}

.info-box h5 {
    font-size: 1rem;
    margin-bottom: 5px;
}

.info-box p {
    color: #6c757d;
}

.social-links .btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.social-links .btn:hover {
    transform: translateY(-3px);
}

.card {
    border-radius: 15px;
}

.form-control {
    border-radius: 8px;
    padding: 0.75rem 1rem;
}

.form-control:focus {
    box-shadow: none;
    border-color: #0d6efd;
}

.btn-primary {
    padding: 0.75rem 2rem;
    border-radius: 8px;
}

@media (max-width: 768px) {
    .contact-info .d-flex {
        margin-bottom: 1.5rem;
    }
}
</style>

<!-- Form Validation Script -->
<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const subject = document.getElementById('subject').value;
    const message = document.getElementById('message').value;
    
    if (name.length < 3) {
        e.preventDefault();
        alert('Nama harus minimal 3 karakter');
        return;
    }
    
    if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        e.preventDefault();
        alert('Format email tidak valid');
        return;
    }
    
    if (subject.length < 5) {
        e.preventDefault();
        alert('Subjek harus minimal 5 karakter');
        return;
    }
    
    if (message.length < 10) {
        e.preventDefault();
        alert('Pesan harus minimal 10 karakter');
        return;
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
