<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/content.php';
require_once __DIR__ . '/../inc/upload.php';

ensure_session();
verify_csrf();
require_admin();

$section = $_POST['section'] ?? '';

function setting_update(string $name, string $value): void {
    $stmt = db()->prepare('INSERT INTO settings (name, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = ?');
    $stmt->execute([$name, $value, $value]);
}

function redirect_with_flash(string $anchor): void {
    $_SESSION['flash'] = 'Zapisano zmiany.';
    header('Location: /admin/#' . ltrim($anchor, '#'));
    exit;
}

try {
    switch ($section) {
        case 'hero':
            setting_update('hero_headline', $_POST['hero_headline'] ?? '');
            setting_update('hero_subline', $_POST['hero_subline'] ?? '');
            redirect_with_flash($_POST['return_anchor'] ?? 'hero');
            break;

        case 'counter':
            setting_update('counter_enabled', isset($_POST['counter_enabled']) ? '1' : '0');
            setting_update('counter_value', $_POST['counter_value'] ?? '0');
            setting_update('counter_label', $_POST['counter_label'] ?? '');
            redirect_with_flash($_POST['return_anchor'] ?? 'counter');
            break;

        case 'contact':
            setting_update('instagram_url', $_POST['instagram_url'] ?? '');
            setting_update('instagram_handle', $_POST['instagram_handle'] ?? '');
            setting_update('messenger_url', $_POST['messenger_url'] ?? '');
            setting_update('delivery_area', $_POST['delivery_area'] ?? '');
            setting_update('delivery_reach', $_POST['delivery_reach'] ?? '');
            redirect_with_flash($_POST['return_anchor'] ?? 'contact');
            break;

        case 'items':
            $type = $_POST['type'] ?? '';
            $action = $_POST['item_action'] ?? '';
            $anchor = $_POST['return_anchor'] ?? $type;
            if ($action === 'delete') {
                $id = (int)($_POST['id'] ?? 0);
                $old = db()->prepare('SELECT image FROM items WHERE id = ?');
                $old->execute([$id]);
                $img = $old->fetchColumn();
                db()->prepare('DELETE FROM items WHERE id = ?')->execute([$id]);
                if ($img) delete_upload($img);
            } elseif ($action === 'add') {
                $stmt = db()->prepare('INSERT INTO items (type, name, description, sort_order) VALUES (?, ?, ?, ?)');
                $stmt->execute([
                    $type,
                    $_POST['name'] ?? '',
                    $_POST['description'] ?? '',
                    (int)($_POST['sort_order'] ?? 0),
                ]);
            } else {
                $stmt = db()->prepare('UPDATE items SET name = ?, description = ?, sort_order = ? WHERE id = ?');
                $stmt->execute([
                    $_POST['name'] ?? '',
                    $_POST['description'] ?? '',
                    (int)($_POST['sort_order'] ?? 0),
                    (int)($_POST['id'] ?? 0),
                ]);
            }
            redirect_with_flash($anchor);
            break;

        case 'testimonials':
            $action = $_POST['ta'] ?? '';
            if ($action === 'delete') {
                $id = (int)($_POST['id'] ?? 0);
                db()->prepare('DELETE FROM testimonials WHERE id = ?')->execute([$id]);
            } elseif ($action === 'add') {
                $stmt = db()->prepare('INSERT INTO testimonials (quote, author, sort_order) VALUES (?, ?, ?)');
                $stmt->execute([
                    $_POST['quote'] ?? '',
                    $_POST['author'] ?? '',
                    (int)($_POST['sort_order'] ?? 0),
                ]);
            } else {
                $stmt = db()->prepare('UPDATE testimonials SET quote = ?, author = ?, sort_order = ? WHERE id = ?');
                $stmt->execute([
                    $_POST['quote'] ?? '',
                    $_POST['author'] ?? '',
                    (int)($_POST['sort_order'] ?? 0),
                    (int)($_POST['id'] ?? 0),
                ]);
            }
            redirect_with_flash($_POST['return_anchor'] ?? 'testimonials');
            break;

        case 'faq':
            $action = $_POST['fa'] ?? '';
            if ($action === 'delete') {
                db()->prepare('DELETE FROM faq WHERE id = ?')->execute([(int)($_POST['id'] ?? 0)]);
            } elseif ($action === 'add') {
                $stmt = db()->prepare('INSERT INTO faq (question, answer, sort_order) VALUES (?, ?, ?)');
                $stmt->execute([
                    $_POST['question'] ?? '',
                    $_POST['answer'] ?? '',
                    (int)($_POST['sort_order'] ?? 0),
                ]);
            } else {
                $stmt = db()->prepare('UPDATE faq SET question = ?, answer = ?, sort_order = ? WHERE id = ?');
                $stmt->execute([
                    $_POST['question'] ?? '',
                    $_POST['answer'] ?? '',
                    (int)($_POST['sort_order'] ?? 0),
                    (int)($_POST['id'] ?? 0),
                ]);
            }
            redirect_with_flash($_POST['return_anchor'] ?? 'faq');
            break;

        case 'gallery':
            $action = $_POST['ga'] ?? '';
            if ($action === 'delete') {
                $id = (int)($_POST['id'] ?? 0);
                $old = db()->prepare('SELECT image FROM gallery WHERE id = ?');
                $old->execute([$id]);
                $img = $old->fetchColumn();
                db()->prepare('DELETE FROM gallery WHERE id = ?')->execute([$id]);
                if ($img) delete_upload($img);
            } elseif ($action === 'add') {
                $stmt = db()->prepare('INSERT INTO gallery (tag, image, sort_order) VALUES (?, NULL, ?)');
                $stmt->execute([
                    $_POST['tag'] ?? 'Wesele',
                    (int)($_POST['sort_order'] ?? 0),
                ]);
            } else {
                $stmt = db()->prepare('UPDATE gallery SET tag = ?, sort_order = ? WHERE id = ?');
                $stmt->execute([
                    $_POST['tag'] ?? '',
                    (int)($_POST['sort_order'] ?? 0),
                    (int)($_POST['id'] ?? 0),
                ]);
            }
            redirect_with_flash($_POST['return_anchor'] ?? 'gallery');
            break;
    }
} catch (Throwable $e) {
    error_log('admin/save.php: ' . $e->getMessage());
    http_response_code(500);
    exit('Błąd zapisu.');
}

header('Location: /admin/');
exit;
