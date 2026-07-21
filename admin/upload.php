<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/content.php';
require_once __DIR__ . '/../inc/upload.php';

ensure_session();
verify_csrf();
require_admin();

$entity = $_POST['entity'] ?? 'item';
$id = (int)($_POST['id'] ?? 0);
$anchor = $_POST['return_anchor'] ?? ($entity === 'gallery' ? 'gallery' : 'oferta');

try {
    $file = $_FILES['image'] ?? null;
    if ($file && $file['error'] === UPLOAD_ERR_OK) {
        if ($entity === 'gallery') {
            $old = db()->prepare('SELECT image FROM gallery WHERE id = ?');
            $old->execute([$id]);
            $oldFile = $old->fetchColumn();
            $newFile = handle_upload($file, $oldFile ? (string)$oldFile : null);
            if ($newFile) {
                $stmt = db()->prepare('UPDATE gallery SET image = ? WHERE id = ?');
                $stmt->execute([$newFile, $id]);
            }
        } elseif ($entity === 'category') {
            $old = db()->prepare('SELECT image FROM categories WHERE id = ?');
            $old->execute([$id]);
            $oldFile = $old->fetchColumn();
            $newFile = handle_upload($file, $oldFile ? (string)$oldFile : null);
            if ($newFile) {
                $stmt = db()->prepare('UPDATE categories SET image = ? WHERE id = ?');
                $stmt->execute([$newFile, $id]);
            }
        } else {
            $old = db()->prepare('SELECT image FROM items WHERE id = ?');
            $old->execute([$id]);
            $oldFile = $old->fetchColumn();
            $newFile = handle_upload($file, $oldFile ? (string)$oldFile : null);
            if ($newFile) {
                $stmt = db()->prepare('UPDATE items SET image = ? WHERE id = ?');
                $stmt->execute([$newFile, $id]);
            }
        }
    }
    $_SESSION['flash'] = 'Zapisano zmiany.';
} catch (Throwable $e) {
    error_log('admin/upload.php: ' . $e->getMessage());
    $_SESSION['flash'] = 'Błąd wgrywania pliku.';
}

header('Location: /admin/#' . ltrim($anchor, '#'));
exit;
