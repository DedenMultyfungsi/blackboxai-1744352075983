<?php
require_once 'includes/header.php';
require_once '../includes/functions.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // Get teacher details before deletion for logging
        $stmt = $pdo->prepare("SELECT name, photo, position FROM teachers WHERE id = ?");
        $stmt->execute([$id]);
        $teacher = $stmt->fetch();
        
        if (!$teacher) {
            // Log attempt to delete non-existent teacher
            log_activity(
                'Teacher Not Found',
                'Attempted to delete non-existent teacher: ' . json_encode([
                    'teacher_id' => $id
                ]),
                $_SESSION['admin_id']
            );
            $_SESSION['error'] = "Data guru tidak ditemukan!";
            header("Location: teachers.php");
            exit();
        }
        
        // Begin transaction
        $pdo->beginTransaction();
        
        // Delete the teacher
        $stmt = $pdo->prepare("DELETE FROM teachers WHERE id = ?");
        if ($stmt->execute([$id])) {
            // If teacher had a photo, delete it
            if (!empty($teacher['photo'])) {
                $photo_path = '../assets/uploads/teachers/' . $teacher['photo'];
                if (file_exists($photo_path)) {
                    unlink($photo_path);
                    
                    // Log photo deletion
                    log_activity(
                        'Teacher Photo Deleted',
                        'Deleted photo file: ' . json_encode([
                            'teacher_id' => $id,
                            'photo' => $teacher['photo'],
                            'path' => $photo_path
                        ]),
                        $_SESSION['admin_id']
                    );
                }
            }
            
            // Log successful teacher deletion
            log_activity(
                'Teacher Deleted',
                'Deleted teacher: ' . json_encode([
                    'teacher_id' => $id,
                    'name' => $teacher['name'],
                    'position' => $teacher['position'],
                    'had_photo' => !empty($teacher['photo'])
                ]),
                $_SESSION['admin_id']
            );
            
            // Commit transaction
            $pdo->commit();
            
            $_SESSION['success'] = "Data guru berhasil dihapus!";
        }
    } catch(PDOException $e) {
        // Rollback transaction
        $pdo->rollBack();
        
        // Log deletion failure
        log_activity(
            'Teacher Deletion Failed',
            'Failed to delete teacher: ' . json_encode([
                'teacher_id' => $id,
                'name' => $teacher['name'] ?? 'unknown',
                'error' => $e->getMessage()
            ]),
            $_SESSION['admin_id']
        );
        
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
} else {
    // Log invalid deletion attempt
    log_activity(
        'Invalid Teacher Deletion',
        'Attempted to delete teacher without ID',
        $_SESSION['admin_id']
    );
    $_SESSION['error'] = "ID guru tidak valid!";
}

header("Location: teachers.php");
exit();
?>
