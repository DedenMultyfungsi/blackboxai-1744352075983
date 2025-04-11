<?php
$page_title = "Beranda";
require_once 'includes/header.php';
?>

<!-- Hero Section with Carousel -->
<div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="assets/images/slider1.jpg" class="d-block w-100" alt="MTsN 2 Garut">
            <div class="carousel-caption">
                <h1>Selamat Datang di MTsN 2 Garut</h1>
                <p>Mendidik Generasi Berakhlak Mulia dan Berprestasi</p>
            </div>
        </div>
        <div class="carousel-item">
            <img src="assets/images/slider2.jpg" class="d-block w-100" alt="Fasilitas">
            <div class="carousel-caption">
                <h1>Fasilitas Modern</h1>
                <p>Mendukung Pembelajaran yang Berkualitas</p>
            </div>
        </div>
        <div class="carousel-item">
            <img src="assets/images/slider3.jpg" class="d-block w-100" alt="Prestasi">
            <div class="carousel-caption">
                <h1>Prestasi Gemilang</h1>
                <p>Mengukir Prestasi di Berbagai Bidang</p>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<!-- Welcome Section -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="mb-4">Selamat Datang di MTsN 2 Garut</h2>
                <p class="lead mb-4">Madrasah Tsanawiyah Negeri 2 Garut merupakan sekolah menengah pertama yang mengintegrasikan pendidikan umum dengan nilai-nilai keislaman.</p>
                <p class="mb-4">Kami berkomitmen untuk mengembangkan potensi peserta didik menjadi manusia yang beriman dan bertakwa kepada Allah SWT, berakhlak mulia, sehat, berilmu, cakap, kreatif, mandiri, dan menjadi warga negara yang demokratis serta bertanggung jawab.</p>
                <a href="<?php echo BASE_URL; ?>/profile/sejarah" class="btn btn-primary">Pelajari Lebih Lanjut</a>
            </div>
            <div class="col-lg-6">
                <img src="assets/images/school-building.jpg" alt="MTsN 2 Garut" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <div class="stat-item">
                    <i class="fas fa-users fa-3x mb-3 text-primary"></i>
                    <h3 class="counter">1200+</h3>
                    <p>Siswa Aktif</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-item">
                    <i class="fas fa-chalkboard-teacher fa-3x mb-3 text-primary"></i>
                    <h3 class="counter">50+</h3>
                    <p>Guru & Staff</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-item">
                    <i class="fas fa-medal fa-3x mb-3 text-primary"></i>
                    <h3 class="counter">100+</h3>
                    <p>Prestasi</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-item">
                    <i class="fas fa-graduation-cap fa-3x mb-3 text-primary"></i>
                    <h3 class="counter">5000+</h3>
                    <p>Alumni</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- News & Announcements Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Berita & Pengumuman Terbaru</h2>
        <div class="row">
            <!-- News cards will be dynamically populated from database -->
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="assets/images/news1.jpg" class="card-img-top" alt="News 1">
                    <div class="card-body">
                        <h5 class="card-title">Prestasi Gemilang di Olimpiade Sains</h5>
                        <p class="card-text">Tim Olimpiade Sains MTsN 2 Garut berhasil meraih medali emas dalam Kompetisi Sains Madrasah tingkat Provinsi...</p>
                        <a href="#" class="btn btn-outline-primary">Baca Selengkapnya</a>
                    </div>
                    <div class="card-footer text-muted">
                        <small>2 hari yang lalu</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="assets/images/news2.jpg" class="card-img-top" alt="News 2">
                    <div class="card-body">
                        <h5 class="card-title">Workshop Pengembangan Karakter</h5>
                        <p class="card-text">MTsN 2 Garut mengadakan workshop pengembangan karakter bagi siswa dengan menghadirkan pembicara nasional...</p>
                        <a href="#" class="btn btn-outline-primary">Baca Selengkapnya</a>
                    </div>
                    <div class="card-footer text-muted">
                        <small>4 hari yang lalu</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="assets/images/news3.jpg" class="card-img-top" alt="News 3">
                    <div class="card-body">
                        <h5 class="card-title">Pembukaan PPDB 2024/2025</h5>
                        <p class="card-text">MTsN 2 Garut membuka pendaftaran peserta didik baru tahun ajaran 2024/2025. Pendaftaran dapat dilakukan secara online...</p>
                        <a href="#" class="btn btn-outline-primary">Baca Selengkapnya</a>
                    </div>
                    <div class="card-footer text-muted">
                        <small>1 minggu yang lalu</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="<?php echo BASE_URL; ?>/berita" class="btn btn-primary">Lihat Semua Berita</a>
        </div>
    </div>
</section>

<!-- Facilities Preview Section -->
<section class="bg-light py-5">
    <div class="container">
        <h2 class="text-center mb-5">Fasilitas Unggulan</h2>
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="text-center">
                    <i class="fas fa-microscope fa-3x mb-3 text-primary"></i>
                    <h5>Laboratorium Sains</h5>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="text-center">
                    <i class="fas fa-desktop fa-3x mb-3 text-primary"></i>
                    <h5>Lab Komputer</h5>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="text-center">
                    <i class="fas fa-mosque fa-3x mb-3 text-primary"></i>
                    <h5>Masjid</h5>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="text-center">
                    <i class="fas fa-book-reader fa-3x mb-3 text-primary"></i>
                    <h5>Perpustakaan</h5>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="<?php echo BASE_URL; ?>/fasilitas" class="btn btn-primary">Lihat Semua Fasilitas</a>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h2 class="mb-4">Bergabunglah dengan MTsN 2 Garut</h2>
        <p class="lead mb-4">Daftarkan putra-putri Anda untuk masa depan yang lebih cerah</p>
        <a href="<?php echo BASE_URL; ?>/ppdb" class="btn btn-light btn-lg">Daftar Sekarang</a>
    </div>
</section>

<!-- Custom CSS for this page -->
<style>
.carousel-item {
    height: 600px;
}

.carousel-item img {
    object-fit: cover;
    height: 100%;
}

.carousel-caption {
    background: rgba(0, 0, 0, 0.5);
    padding: 20px;
    border-radius: 10px;
}

.stat-item {
    padding: 20px;
    border-radius: 10px;
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-5px);
}

.counter {
    font-size: 2.5rem;
    font-weight: bold;
    color: #0d6efd;
}

.card {
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
}

.card-img-top {
    height: 200px;
    object-fit: cover;
}
</style>

<?php require_once 'includes/footer.php'; ?>
