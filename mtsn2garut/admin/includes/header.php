<?php
session_start();
require_once '../../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get current user data
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $user = $stmt->fetch();
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel - MTsN 2 Garut</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Custom CSS -->
    <style>
    .main-sidebar { background-color: #0d6efd; }
    .main-sidebar .brand-link { border-bottom: 1px solid rgba(255,255,255,.1); }
    .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active { background-color: rgba(255,255,255,.1); }
    .nav-sidebar .nav-item>.nav-link { color: rgba(255,255,255,.8); }
    .nav-sidebar .nav-item>.nav-link:hover { color: #fff; }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="../../" class="nav-link">Lihat Website</a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-user"></i>
                    <?php echo htmlspecialchars($user['name']); ?>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="edit-profile.php" class="dropdown-item">
                        <i class="fas fa-user-edit mr-2"></i> Edit Profil
                    </a>
                    <a href="change-password.php" class="dropdown-item">
                        <i class="fas fa-key mr-2"></i> Ubah Password
                    </a>
                    <?php if($_SESSION['admin_role'] == 'admin'): ?>
                    <div class="dropdown-divider"></div>
                    <a href="settings.php" class="dropdown-item">
                        <i class="fas fa-cog mr-2"></i> Pengaturan
                    </a>
                    <?php endif; ?>
                    <div class="dropdown-divider"></div>
                    <a href="logout.php" class="dropdown-item text-danger">
                        <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                    </a>
                </div>
            </li>
        </ul>
    </nav>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="dashboard.php" class="brand-link text-center">
            <span class="brand-text font-weight-light"><b>MTsN 2</b> Garut</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="profile.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-school"></i>
                            <p>Profil Sekolah</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="teachers.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'teachers.php' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-chalkboard-teacher"></i>
                            <p>Data Guru</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="news.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'news.php' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-newspaper"></i>
                            <p>Berita</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="achievements.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'achievements.php' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-trophy"></i>
                            <p>Prestasi</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="facilities.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'facilities.php' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-building"></i>
                            <p>Fasilitas</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="gallery.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'gallery.php' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-images"></i>
                            <p>Galeri</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="messages.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-envelope"></i>
                            <p>Pesan</p>
                        </a>
                    </li>

                    <li class="nav-header">AKUN</li>
                    <li class="nav-item">
                        <a href="edit-profile.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'edit-profile.php' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-user-edit"></i>
                            <p>Edit Profil</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="change-password.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'change-password.php' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-key"></i>
                            <p>Ubah Password</p>
                        </a>
                    </li>

                    <?php if($_SESSION['admin_role'] == 'admin'): ?>
                    <li class="nav-header">PENGATURAN</li>
                    <li class="nav-item">
                        <a href="users.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Pengguna</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="settings.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>Pengaturan</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="activity-logs.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'activity-logs.php' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-history"></i>
                            <p>Log Aktivitas</p>
                        </a>
                    </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a href="logout.php" class="nav-link text-danger">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>Keluar</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
