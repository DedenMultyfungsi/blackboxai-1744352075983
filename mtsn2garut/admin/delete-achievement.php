<?php
require_once 'includes/header.php';
require_once '../includes/functions.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // Get achievement details before deletion for logging
        $stmt = $pdo->prepare("SELECT title, image, category FROM achievements WHERE id = ?");
        $stmt->execute([$id]);
        $achievement = $stmt->fetch();
        
        if (!$achievement) {
            // Log attempt to delete non-existent achievement
            log_activity(
                'Achievement Not Found',
                'Attempted to delete non-existent achievement: ' . json_encode([
                    'achievement_id' => $id
                ]),
                $_SESSION['admin_id']
            );
            $_SESSION['error'] = "Data prestasi tidak ditemukan!";
            header("Location: achievements.php");
            exit();
        }
        
        // Begin transaction
        $pdo->beginTransaction();
        
        // Delete the achievement
        $stmt = $pdo->prepare("DELETE FROM achievements WHERE id = ?");
        if ($stmt->execute([$id])) {
            // If achievement had an image, delete it
            if (!empty($achievement['image'])) {
                $image_path = '../assets/uploads/achievements/' . $achievement['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                    
                    // Log image deletion
                    log_activity(
                        'Achievement Image Deleted',
                        'Deleted image file: ' . json_encode([
                            'achievement_id' => $id,
                            'image' => $achievement['image'],
                            'path' => $image_path
                        ]),
                        $_SESSION['admin_id']
                    );
                }
            }
            
            // Log successful achievement deletion
            log_activity(
                'Achievement Deleted',
                'Deleted achievement: ' . json_encode([
                    'achievement_id' => $id,
                    'title' => $achievement['title'],
                    'category' => $achievement['category'],
                    'had_image' => !empty($achievement['image'])
                ]),
                $_SESSION['admin_id']
            );
            
            // Commit transaction
            $pdo->commit();
            
            $_SESSION['success'] = "Prestasi berhasil dihapus!";
        }
    } catch(PDOException $e) {
        // Rollback transaction
        $pdo->rollBack();
        
        // Log deletion failure
        log_activity(
            'Achievement Deletion Failed',
            'Failed to delete achievement: ' . json_encode([
                'achievement_id' => $id,
                'title' => $achievement['title'] ?? 'unknown',
                'error' => $e->getMessage()
            ]),
            $_SESSION['admin_id']
        );
        
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
} else {
    // Log invalid deletion attempt
    log_activity(
        'Invalid Achievement Deletion',
        'Attempted to delete achievement without ID',
        $_SESSION['admin_id']
    );
    $_SESSION['error'] = "ID prestasi tidak valid!";
}

header("Location: achievements.php");
exit();
?>
