<?php
require_once __DIR__ . '/db.php';

function load_settings(): array {
    $settings = [];
    foreach (db()->query('SELECT name, value FROM settings') as $row) {
        $settings[$row['name']] = $row['value'];
    }
    return $settings;
}

function setting(string $key, string $default = ''): string {
    static $settings = null;
    if ($settings === null) $settings = load_settings();
    return $settings[$key] ?? $default;
}

function load_items(string $type): array {
    $stmt = db()->prepare('SELECT id, type, name, description, image, sort_order FROM items WHERE type = ? ORDER BY sort_order ASC, id ASC');
    $stmt->execute([$type]);
    return $stmt->fetchAll();
}

function load_gallery(): array {
    return db()->query('SELECT id, tag, image, sort_order FROM gallery ORDER BY tag, sort_order, id')->fetchAll();
}

function load_gallery_tags(): array {
    $stmt = db()->query('SELECT DISTINCT tag FROM gallery ORDER BY tag');
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function load_testimonials(): array {
    return db()->query('SELECT id, quote, author, sort_order FROM testimonials ORDER BY sort_order, id')->fetchAll();
}

function load_faq(): array {
    return db()->query('SELECT id, question, answer, sort_order FROM faq ORDER BY sort_order, id')->fetchAll();
}

function load_categories(): array {
    return db()->query('SELECT id, parent_id, name, image, show_images, sort_order FROM categories ORDER BY sort_order ASC, id ASC')->fetchAll();
}

function build_category_tree(array $flat, ?int $parentId = null): array {
    $branch = [];
    foreach ($flat as $row) {
        $rowParent = $row['parent_id'] === null ? null : (int)$row['parent_id'];
        if ($rowParent === $parentId) {
            $row['children'] = build_category_tree($flat, (int)$row['id']);
            $branch[] = $row;
        }
    }
    return $branch;
}
