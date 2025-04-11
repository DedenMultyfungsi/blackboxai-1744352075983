<?php
require_once 'includes/header.php';
require_once '../includes/functions.php';

// Handle message view logging
if (isset($_POST['action']) && $_POST['action'] === 'log_view' && isset($_POST['message_id'])) {
    $message_id = $_POST['message_id'];
    try {
        // Get message details for logging
        $stmt = $pdo->prepare("SELECT subject, email FROM messages WHERE id = ?");
        $stmt->execute([$message_id]);
        $message = $stmt->fetch();
        
        // Log message view
        log_activity(
            'Message Viewed',
            'Viewed message: ' . json_encode([
                'message_id' => $message_id,
                'subject' => $message['subject'],
                'from' => $message['email']
            ]),
            $_SESSION['admin_id']
        );
    } catch(PDOException $e) {
        // Log error silently
        log_activity(
            'Message View Logging Failed',
            'Failed to log message view: ' . json_encode([
                'message_id' => $message_id,
                'error' => $e->getMessage()
            ]),
            $_SESSION['admin_id']
        );
    }
    exit;
}

// Fetch messages from database
try {
    $stmt = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC");
    $messages = $stmt->fetchAll();
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Mark message as read
if (isset($_GET['mark_read']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $stmt = $pdo->prepare("UPDATE messages SET status = 'read' WHERE id = ?");
        $stmt->execute([$id]);
        
        // Log message status update
        log_activity(
            'Message Marked Read',
            'Marked message as read: ' . json_encode([
                'message_id' => $id
            ]),
            $_SESSION['admin_id']
        );
        
        $_SESSION['success'] = "Pesan ditandai sebagai telah dibaca.";
        header("Location: messages.php");
        exit();
    } catch(PDOException $e) {
        // Log error
        log_activity(
            'Message Status Update Failed',
            'Failed to mark message as read: ' . json_encode([
                'message_id' => $id,
                'error' => $e->getMessage()
            ]),
            $_SESSION['admin_id']
        );
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}

// Delete message
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
        // Get message details before deletion for logging
        $stmt_get = $pdo->prepare("SELECT subject, email FROM messages WHERE id = ?");
        $stmt_get->execute([$id]);
        $message = $stmt_get->fetch();
        
        $stmt->execute([$id]);
        
        // Log message deletion
        log_activity(
            'Message Deleted',
            'Deleted message: ' . json_encode([
                'message_id' => $id,
                'subject' => $message['subject'],
                'email' => $message['email']
            ]),
            $_SESSION['admin_id']
        );
        
        $_SESSION['success'] = "Pesan berhasil dihapus.";
        header("Location: messages.php");
        exit();
    } catch(PDOException $e) {
        // Log error
        log_activity(
            'Message Deletion Failed',
            'Failed to delete message: ' . json_encode([
                'message_id' => $id,
                'error' => $e->getMessage()
            ]),
            $_SESSION['admin_id']
        );
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}
?>

<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Manajemen Pesan</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">Pesan</li>
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
                        <h3 class="card-title">Daftar Pesan Masuk</h3>
                    </div>
                    <div class="card-body">
                        <table id="messagesTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Subjek</th>
                                    <th>Pesan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($messages as $message): ?>
                                <tr class="<?php echo $message['status'] == 'unread' ? 'table-warning' : ''; ?>">
                                    <td>
                                        <?php if ($message['status'] == 'unread'): ?>
                                            <span class="badge badge-warning">Belum Dibaca</span>
                                        <?php else: ?>
                                            <span class="badge badge-success">Sudah Dibaca</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d M Y H:i', strtotime($message['created_at'])); ?></td>
                                    <td><?php echo htmlspecialchars($message['name']); ?></td>
                                    <td>
                                        <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>">
                                            <?php echo htmlspecialchars($message['email']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($message['subject']); ?></td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-link p-0" 
                                                data-toggle="modal" 
                                                data-target="#messageModal<?php echo $message['id']; ?>">
                                            Lihat Pesan
                                        </button>
                                    </td>
                                    <td>
                                        <?php if ($message['status'] == 'unread'): ?>
                                            <a href="?mark_read=1&id=<?php echo $message['id']; ?>" 
                                               class="btn btn-success btn-sm">
                                                <i class="fas fa-check"></i> Tandai Dibaca
                                            </a>
                                        <?php endif; ?>
                                        <a href="?delete=1&id=<?php echo $message['id']; ?>" 
                                           class="btn btn-danger btn-sm delete-confirm">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>

                                <!-- Message Modal -->
                                <div class="modal fade" id="messageModal<?php echo $message['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Pesan dari <?php echo htmlspecialchars($message['name']); ?></h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Subjek:</strong> <?php echo htmlspecialchars($message['subject']); ?></p>
                                                <p><strong>Email:</strong> <?php echo htmlspecialchars($message['email']); ?></p>
                                                <p><strong>Tanggal:</strong> <?php echo date('d M Y H:i', strtotime($message['created_at'])); ?></p>
                                                <hr>
                                                <p><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                                            </div>
                                            <div class="modal-footer">
                                                <?php
                                                // Log message view when modal is opened
                                                if ($message['status'] == 'unread') {
                                                    log_activity(
                                                        'Message Viewed',
                                                        'Viewed message: ' . json_encode([
                                                            'message_id' => $message['id'],
                                                            'subject' => $message['subject'],
                                                            'from' => $message['email']
                                                        ]),
                                                        $_SESSION['admin_id']
                                                    );
                                                }
                                                ?>
                                                <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>" 
                                                   class="btn btn-primary">
                                                    <i class="fas fa-reply"></i> Balas Email
                                                </a>
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Page specific script -->
<script>
$(function () {
    const table = $("#messagesTable").DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "order": [[0, "desc"], [1, "desc"]],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });

    // Log message view when modal is opened
    $('.modal').on('show.bs.modal', function (e) {
        const messageId = $(this).attr('id').replace('messageModal', '');
        
        if ($(this).find('.badge-warning').length) {
            fetch('messages.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'action': 'log_view',
                    'message_id': messageId
                })
            });
        }
    });

    // Delete confirmation
    $('.delete-confirm').on('click', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Pesan yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
