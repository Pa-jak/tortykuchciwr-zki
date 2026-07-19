<?php
require_once __DIR__ . '/config.php';
header('Content-Type: application/xml; charset=utf-8');
$url = rtrim(SITE_URL, '/');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc><?php echo htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>/</loc>
        <priority>1.0</priority>
    </url>
</urlset>
