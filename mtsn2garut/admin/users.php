<?php
require_once 'includes/header.php';
require_once '../includes/functions.php';

// Check if user has admin privileges
if ($_SESSION['admin_role'] !== 'admin') {
    $_SESSION['error'] = "Anda tidak memiliki akses ke halaman ini!";
    header("Location: dashboard.php");
    exit();
}

// Handle user actions
if (isset($_POST['action'])) {
    $id = $_POST['user_id'] ?? null;
    
    switch ($_POST['action']) {
        case 'add':
            $username = $_POST['username'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $name = $_POST['name'];
            $role = $_POST['role'];
            
            try {
                $stmt = $pdo->prepare("INSERT INTO users (username, password, name, role) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$username, $password, $name, $role])) {
                    // Log user creation
                    log_activity(
                        'User Created',
                        'Created new user: ' . json_encode([
                            'username' => $username,
                            'name' => $name,
                            'role' => $role
                        ]),
                        $_SESSION['admin_id']
                    );
                    $_SESSION['success'] = "Pengguna baru berhasil ditambahkan!";
                }
            } catch(PDOException $e) {
                // Log the error
                log_activity(
                    'User Creation Failed',
                    'Failed to create user: ' . json_encode([
                        'username' => $username,
                        'name' => $name,
                        'role' => $role,
                        'error' => $e->getMessage()
                    ]),
                    $_SESSION['admin_id']
                );
                $_SESSION['error'] = "Error: " . $e->getMessage();
            }
            break;
            
        case 'edit':
            $name = $_POST['name'];
            $role = $_POST['role'];
            
            try {
                if (!empty($_POST['password'])) {
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET name = ?, role = ?, password = ? WHERE id = ?");
                    $result = $stmt->execute([$name, $role, $password, $id]);
                    $log_details = ['password_changed' => true];
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET name = ?, role = ? WHERE id = ?");
                    $result = $stmt->execute([$name, $role, $id]);
                    $log_details = ['password_changed' => false];
                }
                
                if ($result) {
                    // Log user update
                    $log_details = array_merge($log_details, [
                        'user_id' => $id,
                        'name' => $name,
                        'role' => $role
                    ]);
                    log_activity(
                        'User Updated',
                        'Updated user information: ' . json_encode($log_details),
                        $_SESSION['admin_id']
                    );
                    $_SESSION['success'] = "Data pengguna berhasil diperbarui!";
                }
            } catch(PDOException $e) {
                // Log the error
                log_activity(
                    'User Update Failed',
                    'Failed to update user: ' . json_encode([
                        'user_id' => $id,
                        'name' => $name,
                        'role' => $role,
                        'password_changed' => !empty($_POST['password']),
                        'error' => $e->getMessage()
                    ]),
                    $_SESSION['admin_id']
                );
                $_SESSION['error'] = "Error: " . $e->getMessage();
            }
            break;
            
        case 'delete':
            try {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND id != ?");
                if ($stmt->execute([$id, $_SESSION['admin_id']])) {
                    // Log user deletion
                    log_activity(
                        'User Deleted',
                        'Deleted user with ID: ' . $id,
                        $_SESSION['admin_id']
                    );
                    $_SESSION['success'] = "Pengguna berhasil dihapus!";
                }
            } catch(PDOException $e) {
                // Log the error
                log_activity(
                    'User Deletion Failed',
                    'Failed to delete user: ' . json_encode([
                        'user_id' => $id,
                        'error' => $e->getMessage()
                    ]),
                    $_SESSION['admin_id']
                );
                $_SESSION['error'] = "Error: " . $e->getMessage();
            }
            break;
    }
    
    header("Location: users.php");
    exit();
}

// Fetch all users
try {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY name ASC");
    $users = $stmt->fetchAll();
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Manajemen Pengguna</h1>
            </div>
            <div class="col-sm-6">
                <button type="button" class="btn btn-primary float-sm-right" data-toggle="modal" data-target="#addUserModal">
                    <i class="fas fa-user-plus"></i> Tambah Pengguna
                </button>
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
                        <h3 class="card-title">Daftar Pengguna</h3>
                    </div>
                    <div class="card-body">
                        <table id="usersTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Nama</th>
                                    <th>Role</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $user['role'] == 'admin' ? 'danger' : 'info'; ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d M Y H:i', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-warning btn-sm" 
                                                data-toggle="modal" 
                                                data-target="#editUserModal" 
                                                data-id="<?php echo $user['id']; ?>"
                                                data-username="<?php echo htmlspecialchars($user['username']); ?>"
                                                data-name="<?php echo htmlspecialchars($user['name']); ?>"
                                                data-role="<?php echo $user['role']; ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <?php if ($user['id'] != $_SESSION['admin_id']): ?>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm delete-user" 
                                                data-id="<?php echo $user['id']; ?>"
                                                data-name="<?php echo htmlspecialchars($user['name']); ?>">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Pengguna</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Pengguna</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="user_id" id="edit_user_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" id="edit_username" disabled>
                    </div>
                    <div class="form-group">
                        <label for="edit_password">Password Baru (kosongkan jika tidak ingin mengubah)</label>
                        <input type="password" class="form-control" id="edit_password" name="password">
                    </div>
                    <div class="form-group">
                        <label for="edit_name">Nama Lengkap</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_role">Role</label>
                        <select class="form-control" id="edit_role" name="role" required>
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete User Form -->
<form id="deleteUserForm" action="" method="post" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="user_id" id="delete_user_id">
</form>

<!-- Custom JavaScript -->
<script>
$(function () {
    // Initialize DataTable
    $("#usersTable").DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });

    // Edit User Modal
    $('#editUserModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const modal = $(this);
        
        modal.find('#edit_user_id').val(button.data('id'));
        modal.find('#edit_username').val(button.data('username'));
        modal.find('#edit_name').val(button.data('name'));
        modal.find('#edit_role').val(button.data('role'));
    });

    // Delete User
    $('.delete-user').click(function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: `Akan menghapus pengguna "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#delete_user_id').val(id);
                $('#deleteUserForm').submit();
            }
        });
    });

    // Form Validation
    $('form').submit(function(e) {
        const password = $(this).find('input[name="password"]');
        const username = $(this).find('#username');
        
        if (username.length && username.val().length < 4) {
            e.preventDefault();
            // Log validation error
            log_activity(
                'User Validation Failed',
                'Username too short (min 4 characters)',
                $_SESSION['admin_id']
            );
            alert('Username minimal 4 karakter!');
            return;
        }
        
        if (password.length && password.val() !== '' && password.val().length < 6) {
            e.preventDefault();
            // Log validation error
            log_activity(
                'User Validation Failed',
                'Password too short (min 6 characters)',
                $_SESSION['admin_id']
            );
            alert('Password minimal 6 karakter!');
            return;
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
