<?php
require_once 'includes/config.php';
$page_title = "Guru - MTsN 2 Garut";
require_once 'includes/header.php';

// Fetch all active teachers
try {
    $stmt = $pdo->query("SELECT * FROM teachers WHERE status = 'active' ORDER BY name ASC");
    $teachers = $stmt->fetchAll();
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!-- Hero Section -->
<div class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 font-weight-bold mb-3">Tenaga Pendidik</h1>
                <p class="lead mb-0">Guru-guru berkualitas dan berpengalaman di MTsN 2 Garut</p>
            </div>
        </div>
    </div>
</div>

<!-- Teachers Section -->
<section class="py-5">
    <div class="container">
        <!-- Search and Filter -->
        <div class="row mb-4">
            <div class="col-md-6">
                <input type="text" id="searchTeacher" class="form-control" placeholder="Cari guru...">
            </div>
            <div class="col-md-6">
                <select id="filterSubject" class="form-control">
                    <option value="">Semua Mata Pelajaran</option>
                    <?php
                    $subjects = array_unique(array_column($teachers, 'subject'));
                    foreach($subjects as $subject) {
                        echo "<option value='" . htmlspecialchars($subject) . "'>" . htmlspecialchars($subject) . "</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <!-- Teachers Grid -->
        <div class="row" id="teachersGrid">
            <?php foreach($teachers as $teacher): ?>
            <div class="col-md-6 col-lg-4 mb-4 teacher-card" 
                 data-name="<?php echo strtolower(htmlspecialchars($teacher['name'])); ?>"
                 data-subject="<?php echo strtolower(htmlspecialchars($teacher['subject'])); ?>">
                <div class="card h-100 shadow-sm hover-shadow">
                    <div class="text-center pt-4">
                        <?php if($teacher['photo']): ?>
                            <img src="assets/uploads/teachers/<?php echo htmlspecialchars($teacher['photo']); ?>" 
                                 alt="<?php echo htmlspecialchars($teacher['name']); ?>" 
                                 class="rounded-circle mb-3" 
                                 style="width: 150px; height: 150px; object-fit: cover;">
                        <?php else: ?>
                            <img src="assets/images/default-avatar.png" 
                                 alt="Default Avatar" 
                                 class="rounded-circle mb-3" 
                                 style="width: 150px; height: 150px; object-fit: cover;">
                        <?php endif; ?>
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title mb-1"><?php echo htmlspecialchars($teacher['name']); ?></h5>
                        <p class="text-muted small mb-2"><?php echo htmlspecialchars($teacher['nip']); ?></p>
                        <p class="badge bg-primary mb-2"><?php echo htmlspecialchars($teacher['subject']); ?></p>
                        <p class="card-text mb-2"><?php echo htmlspecialchars($teacher['position']); ?></p>
                        <p class="card-text small text-muted"><?php echo htmlspecialchars($teacher['education']); ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Custom CSS -->
<style>
.hover-shadow {
    transition: all 0.3s ease;
}

.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}

.teacher-card .card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
}

.badge {
    padding: 8px 15px;
    border-radius: 20px;
}

.bg-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0043a8 100%)!important;
}
</style>

<!-- Custom JavaScript for Search and Filter -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchTeacher');
    const filterSubject = document.getElementById('filterSubject');
    const teacherCards = document.querySelectorAll('.teacher-card');

    function filterTeachers() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedSubject = filterSubject.value.toLowerCase();

        teacherCards.forEach(card => {
            const teacherName = card.dataset.name;
            const teacherSubject = card.dataset.subject;
            
            const matchesSearch = teacherName.includes(searchTerm);
            const matchesSubject = !selectedSubject || teacherSubject === selectedSubject;

            if (matchesSearch && matchesSubject) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterTeachers);
    filterSubject.addEventListener('change', filterTeachers);
});
</script>

<?php require_once 'includes/footer.php'; ?>
