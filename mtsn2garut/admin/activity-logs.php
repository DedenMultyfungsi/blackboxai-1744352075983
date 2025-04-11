<?php
require_once 'includes/header.php';

// Check if user has admin privileges
if ($_SESSION['admin_role'] !== 'admin') {
    $_SESSION['error'] = "Anda tidak memiliki akses ke halaman ini!";
    header("Location: dashboard.php");
    exit();
}

// Handle log deletion if requested
if (isset($_POST['delete']) && isset($_POST['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM activity_log WHERE id = ?");
        if ($stmt->execute([$_POST['id']])) {
            $_SESSION['success'] = "Log berhasil dihapus.";
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
    header("Location: activity-logs.php");
    exit();
}

// Handle bulk deletion if requested
if (isset($_POST['bulk_delete']) && !empty($_POST['selected_logs'])) {
    try {
        $ids = implode(',', array_map('intval', $_POST['selected_logs']));
        $stmt = $pdo->prepare("DELETE FROM activity_log WHERE id IN ($ids)");
        if ($stmt->execute()) {
            $_SESSION['success'] = "Log terpilih berhasil dihapus.";
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
    header("Location: activity-logs.php");
    exit();
}

// Fetch logs with user information
try {
    $stmt = $pdo->query("
        SELECT l.*, u.name as user_name, u.username 
        FROM activity_log l 
        LEFT JOIN users u ON l.user_id = u.id 
        ORDER BY l.created_at DESC
    ");
    $logs = $stmt->fetchAll();
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Log Aktivitas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">Log Aktivitas</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Log Aktivitas</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-danger btn-sm" id="bulkDeleteBtn" disabled>
                                <i class="fas fa-trash"></i> Hapus Terpilih
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="logsForm" action="" method="post">
                            <table id="logsTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="30px">
                                            <input type="checkbox" id="checkAll">
                                        </th>
                                        <th>Waktu</th>
                                        <th>Pengguna</th>
                                        <th>Aksi</th>
                                        <th>Detail</th>
                                        <th>IP Address</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="selected_logs[]" value="<?php echo $log['id']; ?>" class="log-checkbox">
                                        </td>
                                        <td><?php echo format_date($log['created_at'], true); ?></td>
                                        <td>
                                            <?php if ($log['user_id']): ?>
                                                <?php echo htmlspecialchars($log['user_name']); ?>
                                                <small class="d-block text-muted"><?php echo htmlspecialchars($log['username']); ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">System</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($log['action']); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-link p-0" data-toggle="modal" data-target="#detailsModal<?php echo $log['id']; ?>">
                                                Lihat Detail
                                            </button>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($log['ip_address']); ?>
                                            <button type="button" class="btn btn-link p-0 ml-2" 
                                                    data-toggle="popover" 
                                                    data-trigger="hover"
                                                    title="User Agent"
                                                    data-content="<?php echo htmlspecialchars($log['user_agent']); ?>">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        </td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-danger btn-sm delete-log" 
                                                    data-id="<?php echo $log['id']; ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Details Modal -->
                                    <div class="modal fade" id="detailsModal<?php echo $log['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Detail Log</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <pre class="bg-light p-3 rounded"><?php echo htmlspecialchars($log['details']); ?></pre>
                                                    <hr>
                                                    <div class="text-muted">
                                                        <small>
                                                            <strong>User Agent:</strong><br>
                                                            <?php echo htmlspecialchars($log['user_agent']); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <input type="hidden" name="bulk_delete" value="1">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Delete Log Form -->
<form id="deleteLogForm" action="" method="post" style="display: none;">
    <input type="hidden" name="delete" value="1">
    <input type="hidden" name="id" id="delete_log_id">
</form>

<!-- Custom JavaScript -->
<script>
$(function () {
    // Initialize DataTable
    $("#logsTable").DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "order": [[1, "desc"]],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });

    // Initialize popovers
    $('[data-toggle="popover"]').popover();

    // Handle check all
    $('#checkAll').change(function() {
        $('.log-checkbox').prop('checked', $(this).prop('checked'));
        updateBulkDeleteButton();
    });

    // Handle individual checkboxes
    $('.log-checkbox').change(function() {
        updateBulkDeleteButton();
    });

    // Update bulk delete button state
    function updateBulkDeleteButton() {
        const checkedCount = $('.log-checkbox:checked').length;
        $('#bulkDeleteBtn').prop('disabled', checkedCount === 0);
    }

    // Handle bulk delete
    $('#bulkDeleteBtn').click(function() {
        if ($('.log-checkbox:checked').length === 0) return;

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Log yang terpilih akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#logsForm').submit();
            }
        });
    });

    // Handle individual delete
    $('.delete-log').click(function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Log ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#delete_log_id').val(id);
                $('#deleteLogForm').submit();
            }
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
