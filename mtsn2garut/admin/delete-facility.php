<?php
require_once 'includes/header.php';
require_once '../includes/functions.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // Get facility details before deletion for logging
        $stmt = $pdo->prepare("SELECT name, image FROM facilities WHERE id = ?");
        $stmt->execute([$id]);
        $facility = $stmt->fetch();
        
        if (!$facility) {
            // Log attempt to delete non-existent facility
            log_activity(
                'Facility Not Found',
                'Attempted to delete non-existent facility: ' . json_encode([
                    'facility_id' => $id
                ]),
                $_SESSION['admin_id']
            );
            $_SESSION['error'] = "Data fasilitas tidak ditemukan!";
            header("Location: facilities.php");
            exit();
        }
        
        // Begin transaction
        $pdo->beginTransaction();
        
        // Delete the facility
        $stmt = $pdo->prepare("DELETE FROM facilities WHERE id = ?");
        if ($stmt->execute([$id])) {
            // If facility had an image, delete it
            if (!empty($facility['image'])) {
                $image_path = '../assets/uploads/facilities/' . $facility['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                    
                    // Log image deletion
                    log_activity(
                        'Facility Image Deleted',
                        'Deleted image file: ' . json_encode([
                            'facility_id' => $id,
                            'image' => $facility['image'],
                            'path' => $image_path
                        ]),
                        $_SESSION['admin_id']
                    );
                }
            }
            
            // Log successful facility deletion
            log_activity(
                'Facility Deleted',
                'Deleted facility: ' . json_encode([
                    'facility_id' => $id,
                    'name' => $facility['name']
                ]),
                $_SESSION['admin_id']
            );
            
            // Commit transaction
            $pdo->commit();
            
            $_SESSION['success'] = "Fasilitas berhasil dihapus!";
        }
    } catch(PDOException $e) {
        // Rollback transaction
        $pdo->rollBack();
        
        // Log deletion failure
        log_activity(
            'Facility Deletion Failed',
            'Failed to delete facility: ' . json_encode([
                'facility_id' => $id,
                'name' => $facility['name'] ?? 'unknown',
                'error' => $e->getMessage()
            ]),
            $_SESSION['admin_id']
        );
        
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
} else {
    // Log invalid deletion attempt
    log_activity(
        'Invalid Facility Deletion',
        'Attempted to delete facility without ID',
        $_SESSION['admin_id']
    );
    $_SESSION['error'] = "ID fasilitas tidak valid!";
}

header("Location: facilities.php");
exit();
?>
