<?php
$page_title = "404 - Halaman Tidak Ditemukan";
require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="error-page">
                <h1 class="error-code display-1 text-primary mb-4">404</h1>
                <h2 class="error-title mb-4">Halaman Tidak Ditemukan</h2>
                <p class="error-message lead mb-4">
                    Maaf, halaman yang Anda cari tidak dapat ditemukan. 
                    Halaman mungkin telah dipindahkan atau dihapus.
                </p>
                <div class="error-actions">
                    <a href="/" class="btn btn-primary btn-lg me-3">
                        <i class="fas fa-home me-2"></i> Kembali ke Beranda
                    </a>
                    <a href="/kontak.php" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-envelope me-2"></i> Hubungi Kami
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.error-page {
    padding: 40px;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 0 30px rgba(0,0,0,0.1);
}

.error-code {
    font-weight: 700;
    color: #0d6efd;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
}

.error-title {
    font-size: 2rem;
    color: #343a40;
}

.error-message {
    color: #6c757d;
    margin-bottom: 30px;
}

.error-actions {
    margin-top: 30px;
}

.btn {
    padding: 12px 30px;
    border-radius: 50px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
</style>

<?php require_once 'includes/footer.php'; ?>
