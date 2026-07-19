<?php
require_once __DIR__ . '/db.php';

function delete_upload(?string $filename): void {
    if (!$filename) return;
    $base = basename($filename);
    $path = __DIR__ . '/../uploads/' . $base;
    $realBase = realpath(__DIR__ . '/../uploads');
    $real = realpath($path);
    if ($real !== false && $realBase !== false && strpos($real, $realBase . DIRECTORY_SEPARATOR) === 0) {
        @unlink($real);
    }
}

function handle_upload(array $file, ?string $oldFile = null): string|false {
    $handlers = [
        'image/jpeg' => [
            'create' => 'imagecreatefromjpeg',
            'save' => function ($img, string $p) { imagejpeg($img, $p, 82); },
            'ext' => 'jpg',
        ],
        'image/png' => [
            'create' => 'imagecreatefrompng',
            'save' => function ($img, string $p) { imagepng($img, $p, 6); },
            'ext' => 'png',
        ],
        'image/webp' => [
            'create' => 'imagecreatefromwebp',
            'save' => function ($img, string $p) { imagewebp($img, $p, 82); },
            'ext' => 'webp',
        ],
    ];

    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) return false;
    if ($file['size'] > 8 * 1024 * 1024) return false;

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!isset($handlers[$mime])) return false;

    $info = @getimagesize($file['tmp_name']);
    if ($info === false) return false;

    $cfg = $handlers[$mime];
    $ext = $cfg['ext'];
    $name = bin2hex(random_bytes(8)) . '.' . $ext;
    $uploadsDir = realpath(__DIR__ . '/../uploads');
    if ($uploadsDir === false) return false;
    $dest = $uploadsDir . DIRECTORY_SEPARATOR . $name;

    $newFile = false;
    if ($info[0] > 1600) {
        $orig = $cfg['create']($file['tmp_name']);
        if (!$orig) return false;
        $w = (int)$info[0];
        $h = (int)$info[1];
        $nw = 1600;
        $nh = (int)round($h * ($nw / $w));
        $dst = imagecreatetruecolor($nw, $nh);
        if ($mime === 'image/png' || $mime === 'image/webp') {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
        }
        imagecopyresampled($dst, $orig, 0, 0, 0, 0, $nw, $nh, $w, $h);
        imagedestroy($orig);
        $cfg['save']($dst, $dest);
        imagedestroy($dst);
        $newFile = true;
    } else {
        if (!move_uploaded_file($file['tmp_name'], $dest)) return false;
        $newFile = true;
    }

    if ($newFile && $oldFile) {
        delete_upload($oldFile);
    }

    return $newFile ? $name : false;
}
