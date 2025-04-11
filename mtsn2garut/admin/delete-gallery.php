<?php
require_once 'includes/header.php';
require_once '../includes/functions.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // Get gallery item details before deletion for logging
        $stmt = $pdo->prepare("SELECT title, image, category FROM gallery WHERE id = ?");
        $stmt->execute([$id]);
        $gallery = $stmt->fetch();
        
        if (!$gallery) {
            // Log attempt to delete non-existent gallery item
            log_activity(
                'Gallery Item Not Found',
                'Attempted to delete non-existent gallery item: ' . json_encode([
                    'gallery_id' => $id
                ]),
                $_SESSION['admin_id']
            );
            $_SESSION['error'] = "Data galeri tidak ditemukan!";
            header("Location: gallery.php");
            exit();
        }
        
        // Begin transaction
        $pdo->beginTransaction();
        
        // Delete the gallery item
        $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
        if ($stmt->execute([$id])) {
            // If gallery item had an image, delete it
            if (!empty($gallery['image'])) {
                $image_path = '../assets/uploads/gallery/' . $gallery['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                    
                    // Log image deletion
                    log_activity(
                        'Gallery Image Deleted',
                        'Deleted image file: ' . json_encode([
                            'gallery_id' => $id,
                            'image' => $gallery['image'],
                            'path' => $image_path
                        ]),
                        $_SESSION['admin_id']
                    );
                }
            }
            
            // Log successful gallery item deletion
            log_activity(
                'Gallery Item Deleted',
                'Deleted gallery item: ' . json_encode([
                    'gallery_id' => $id,
                    'title' => $gallery['title'],
                    'category' => $gallery['category'],
                    'had_image' => !empty($gallery['image'])
                ]),
                $_SESSION['admin_id']
            );
            
            // Commit transaction
            $pdo->commit();
            
            $_SESSION['success'] = "Foto berhasil dihapus dari galeri!";
        }
    } catch(PDOException $e) {
        // Rollback transaction
        $pdo->rollBack();
        
        // Log deletion failure
        log_activity(
            'Gallery Deletion Failed',
            'Failed to delete gallery item: ' . json_encode([
                'gallery_id' => $id,
                'title' => $gallery['title'] ?? 'unknown',
                'error' => $e->getMessage()
            ]),
            $_SESSION['admin_id']
        );
        
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
} else {
    // Log invalid deletion attempt
    log_activity(
        'Invalid Gallery Deletion',
        'Attempted to delete gallery item without ID',
        $_SESSION['admin_id']
    );
    $_SESSION['error'] = "ID galeri tidak valid!";
}

header("Location: gallery.php");
exit();
?>
