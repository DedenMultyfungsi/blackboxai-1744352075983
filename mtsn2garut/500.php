<?php
$page_title = "500 - Kesalahan Server";
require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="error-page">
                <h1 class="error-code display-1 text-warning mb-4">500</h1>
                <h2 class="error-title mb-4">Kesalahan Server Internal</h2>
                <p class="error-message lead mb-4">
                    Mohon maaf, telah terjadi kesalahan pada server kami. 
                    Tim teknis kami telah diberitahu dan sedang bekerja untuk memperbaiki masalah ini. 
                    Silakan coba lagi beberapa saat lagi.
                </p>
                <div class="error-actions">
                    <a href="javascript:window.location.reload()" class="btn btn-warning btn-lg me-3">
                        <i class="fas fa-sync-alt me-2"></i> Muat Ulang Halaman
                    </a>
                    <a href="/" class="btn btn-outline-warning btn-lg">
                        <i class="fas fa-home me-2"></i> Kembali ke Beranda
                    </a>
                </div>

                <div class="error-details mt-5">
                    <div class="alert alert-light" role="alert">
                        <h5 class="alert-heading mb-3">
                            <i class="fas fa-info-circle me-2"></i> Informasi Tambahan
                        </h5>
                        <p class="mb-2">Jika masalah terus berlanjut, Anda dapat:</p>
                        <ul class="list-unstyled text-start">
                            <li><i class="fas fa-check me-2"></i> Membersihkan cache browser Anda</li>
                            <li><i class="fas fa-check me-2"></i> Mencoba mengakses halaman beberapa menit lagi</li>
                            <li><i class="fas fa-check me-2"></i> Menghubungi administrator website</li>
                        </ul>
                    </div>
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
    color: #ffc107;
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

.btn-outline-warning {
    color: #ffc107;
    border-color: #ffc107;
}

.btn-outline-warning:hover {
    background-color: #ffc107;
    color: #000;
}

.error-details {
    text-align: left;
}

.error-details .alert {
    border: 1px solid rgba(0,0,0,0.1);
    background-color: #f8f9fa;
}

.error-details ul li {
    margin-bottom: 10px;
    color: #6c757d;
}

.error-details ul li i {
    color: #28a745;
}

@media (max-width: 768px) {
    .error-actions .btn {
        display: block;
        width: 100%;
        margin-bottom: 15px;
    }
    
    .error-actions .btn:last-child {
        margin-bottom: 0;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>
