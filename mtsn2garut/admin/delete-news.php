<?php
require_once 'includes/header.php';
require_once '../includes/functions.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // Get news details before deletion for logging
        $stmt = $pdo->prepare("SELECT title, image FROM news WHERE id = ?");
        $stmt->execute([$id]);
        $news = $stmt->fetch();
        
        if (!$news) {
            // Log attempt to delete non-existent news
            log_activity(
                'News Not Found',
                'Attempted to delete non-existent news: ' . json_encode([
                    'news_id' => $id
                ]),
                $_SESSION['admin_id']
            );
            $_SESSION['error'] = "Berita tidak ditemukan!";
            header("Location: news.php");
            exit();
        }
        
        // Begin transaction
        $pdo->beginTransaction();
        
        // Delete the news
        $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
        if ($stmt->execute([$id])) {
            // If news had an image, delete it
            if (!empty($news['image'])) {
                $image_path = '../assets/uploads/news/' . $news['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                    
                    // Log image deletion
                    log_activity(
                        'News Image Deleted',
                        'Deleted image file: ' . json_encode([
                            'news_id' => $id,
                            'image' => $news['image'],
                            'path' => $image_path
                        ]),
                        $_SESSION['admin_id']
                    );
                }
            }
            
            // Log successful news deletion
            log_activity(
                'News Deleted',
                'Deleted news article: ' . json_encode([
                    'news_id' => $id,
                    'title' => $news['title'],
                    'had_image' => !empty($news['image'])
                ]),
                $_SESSION['admin_id']
            );
            
            // Commit transaction
            $pdo->commit();
            
            $_SESSION['success'] = "Berita berhasil dihapus!";
        }
    } catch(PDOException $e) {
        // Rollback transaction
        $pdo->rollBack();
        
        // Log deletion failure
        log_activity(
            'News Deletion Failed',
            'Failed to delete news: ' . json_encode([
                'news_id' => $id,
                'error' => $e->getMessage()
            ]),
            $_SESSION['admin_id']
        );
        
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
} else {
    // Log invalid deletion attempt
    log_activity(
        'Invalid News Deletion',
        'Attempted to delete news without ID',
        $_SESSION['admin_id']
    );
    $_SESSION['error'] = "ID berita tidak valid!";
}

header("Location: news.php");
exit();
?>
