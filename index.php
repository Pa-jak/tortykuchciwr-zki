<?php
if (!file_exists(__DIR__ . '/config.php')) {
    echo '<!doctype html><html lang="pl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Konfiguracja — Torty Kuchciwróżki</title></head><body><main style="font-family:system-ui,-apple-system,sans-serif;max-width:600px;margin:4rem auto;padding:2rem;text-align:center;border:1px solid oklch(85% 0.02 340);border-radius:18px;box-shadow:0 4px 16px oklch(40% 0.05 340 / 0.08)"><h1>Torty Kuchciwróżki</h1><p>Skopiuj <code>config.sample.php</code> jako <code>config.php</code> i uzupełnij dane bazy danych.</p></main></body></html>';
    exit;
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/content.php';

$siteUrl = rtrim(SITE_URL, '/');

$heroHeadline = nl2br(e(setting('hero_headline')));
$heroSubline = e(setting('hero_subline'));
$counterEnabled = setting('counter_enabled') === '1';
$counterValue = e(setting('counter_value'));
$counterLabel = e(setting('counter_label'));
$instagramUrl = e(setting('instagram_url'));
$messengerUrl = e(setting('messenger_url'));
$instagramHandle = e(setting('instagram_handle'));
$deliveryArea = e(setting('delivery_area'));
$deliveryReach = e(setting('delivery_reach'));
$seoTitle = e(setting('seo_title'));
$seoDesc = e(setting('seo_description'));

$desserts = load_items('dessert');
$cakes = load_items('cake');
$team = load_items('team');
$gallery = load_gallery();
$galleryTags = load_gallery_tags();
$testimonials = load_testimonials();
$faq = load_faq();

function mediaUrl(?string $file): string {
    if (!$file) return '';
    return rtrim(SITE_URL, '/') . '/uploads/' . rawurlencode($file);
}

function itemImage(array $item): void {
    $name = $item['name'] ?? '';
    if (!empty($item['image'])) {
        echo '<img src="' . e(mediaUrl($item['image'])) . '" alt="' . e($name) . '" loading="lazy">';
    } else {
        echo '<div class="placeholder" aria-label="' . e($name) . '"><span>' . e(mb_substr($name, 0, 1)) . '</span></div>';
    }
}

function galleryImage(array $g): void {
    $tag = $g['tag'] ?? '';
    if (!empty($g['image'])) {
        echo '<img src="' . e(mediaUrl($g['image'])) . '" alt="' . e($tag) . '" loading="lazy">';
    } else {
        echo '<div class="placeholder" aria-label="' . e($tag) . '"><span>' . e($tag) . '</span></div>';
    }
}
?><!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $seoTitle; ?></title>
    <meta name="description" content="<?php echo $seoDesc; ?>">
    <link rel="canonical" href="<?php echo $siteUrl; ?>/">
    <meta property="og:title" content="<?php echo $seoTitle; ?>">
    <meta property="og:description" content="<?php echo $seoDesc; ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo $siteUrl; ?>/">
    <meta property="og:image" content="<?php echo $siteUrl; ?>/assets/logo.jpg">
    <meta property="og:locale" content="pl_PL">
    <meta name="twitter:card" content="summary">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <script type="application/ld+json">
<?php
$ld = [
    '@context' => 'https://schema.org',
    '@type' => 'Bakery',
    'name' => 'Torty Kuchciwróżki',
    'url' => $siteUrl . '/',
    'image' => $siteUrl . '/assets/logo.jpg',
    'servesCuisine' => 'Torty i desery',
    'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'Warka'],
    'areaServed' => ['Warka', 'Radom', 'Warszawa', 'Grójec'],
    'sameAs' => [setting('instagram_url', '')],
    'priceRange' => '$$',
];
echo json_encode($ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
?>
    </script>
</head>
<body>
    <header class="hero">
        <div class="sparkles" aria-hidden="true"><span></span><span></span><span></span></div>
        <div class="hero-inner">
            <img src="/assets/logo.jpg" alt="Torty Kuchciwróżki" class="hero-logo">
            <h1><?php echo $heroHeadline; ?></h1>
            <p class="hero-sub"><?php echo $heroSubline; ?></p>
            <div class="hero-actions">
                <a href="<?php echo $instagramUrl; ?>" target="_blank" rel="noopener" class="btn btn-primary">Napisz do nas</a>
                <a href="<?php echo $messengerUrl; ?>" target="_blank" rel="noopener" class="btn btn-outline">Messenger</a>
            </div>
        </div>
    </header>

<?php if ($counterEnabled): ?>
    <div class="counter-bar">
        <strong><?php echo $counterValue; ?>+</strong> <?php echo $counterLabel; ?>
    </div>
<?php endif; ?>

    <main>
        <section class="section" id="slodkie-stoly">
            <p class="overline">Oferta</p>
            <h2>Słodkie stoły</h2>
            <p class="section-intro">Słodki stół to serce przyjęcia — komponujemy go tak, by zachwycał okiem i smakiem.</p>
            <div class="grid cards dessert-grid">
                <?php foreach ($desserts as $item): ?>
                <article class="card">
                    <div class="card-media ratio-4-3">
                        <?php itemImage($item); ?>
                    </div>
                    <div class="card-body">
                        <h3><?php echo e($item['name']); ?></h3>
                        <p><?php echo e($item['description']); ?></p>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="section section-alt" id="torty">
            <p class="overline">Oferta</p>
            <h2>Torty</h2>
            <p class="section-intro">Każdy tort projektujemy indywidualnie — smak, wielkość i zdobienia dopasowujemy do Waszych potrzeb i sezonu. <strong>Wycena zawsze ustalana indywidualnie</strong> po rozmowie.</p>
            <div class="grid cards cake-grid">
                <?php foreach ($cakes as $item): ?>
                <article class="card">
                    <div class="card-media ratio-5-4">
                        <?php itemImage($item); ?>
                    </div>
                    <div class="card-body">
                        <h3><?php echo e($item['name']); ?></h3>
                        <p><?php echo e($item['description']); ?></p>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="section" id="galeria">
            <p class="overline">Galeria</p>
            <h2>Nasze realizacje</h2>
            <div class="filters" role="group" aria-label="Filtry galerii">
                <button class="filter-btn active" data-filter="all">Wszystkie</button>
                <?php foreach ($galleryTags as $tag): ?>
                <button class="filter-btn" data-filter="<?php echo e($tag); ?>"><?php echo e($tag); ?></button>
                <?php endforeach; ?>
            </div>
            <div class="gallery-grid" id="gallery">
                <?php foreach ($gallery as $idx => $g): ?>
                <button class="gallery-item" data-tag="<?php echo e($g['tag']); ?>" data-index="<?php echo $idx; ?>" type="button" aria-label="Zobacz zdjęcie <?php echo e($g['tag']); ?>">
                    <?php galleryImage($g); ?>
                </button>
                <?php endforeach; ?>
            </div>
            <p class="gallery-more"><a href="<?php echo $instagramUrl; ?>" target="_blank" rel="noopener">Więcej na Instagramie →</a></p>
        </section>

        <section class="section section-alt" id="o-nas">
            <p class="overline">O nas</p>
            <h2>Poznajcie Kuchciwróżki</h2>
            <div class="team-list">
                <?php foreach ($team as $member): ?>
                <article class="team-card">
                    <div class="team-photo">
                        <?php itemImage($member); ?>
                    </div>
                    <h3><?php echo e($member['name']); ?></h3>
                    <p><?php echo e($member['description']); ?></p>
                </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="section" id="opinie" data-carousel>
            <p class="overline">Opinie</p>
            <h2>Co mówią o nas klienci</h2>
            <div class="carousel" aria-roledescription="carousel">
                <button class="carousel-arrow carousel-prev" type="button" aria-label="Poprzednia opinia">‹</button>
                <div class="carousel-track">
                    <?php foreach ($testimonials as $t): ?>
                    <figure class="testimonial">
                        <blockquote>“<?php echo e($t['quote']); ?>”</blockquote>
                        <figcaption><?php echo e($t['author']); ?></figcaption>
                    </figure>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-arrow carousel-next" type="button" aria-label="Następna opinia">›</button>
            </div>
            <div class="carousel-dots" role="tablist"></div>
        </section>

        <section class="section section-alt" id="jak-to-dziala">
            <p class="overline">Jak to działa</p>
            <h2>Od pomysłu do stołu</h2>
            <ol class="steps">
                <li>
                    <span class="step-nr">1</span>
                    <h3>Kontakt</h3>
                    <p>Napisz do nas na Instagramie lub Messengerze i opowiedz o swojej uroczystości.</p>
                </li>
                <li>
                    <span class="step-nr">2</span>
                    <h3>Ustalenie szczegółów</h3>
                    <p>Dobieramy smaki, wielkość i styl zdobień do Waszego budżetu.</p>
                </li>
                <li>
                    <span class="step-nr">3</span>
                    <h3>Wykonanie</h3>
                    <p>Tworzymy tort i słodki stół ręcznie, z dbałością o detal.</p>
                </li>
                <li>
                    <span class="step-nr">4</span>
                    <h3>Odbiór / dostawa</h3>
                    <p>Odbiór osobisty w Warce lub dostawa na miejsce uroczystości.</p>
                </li>
            </ol>
        </section>

        <section class="section" id="faq">
            <p class="overline">FAQ</p>
            <h2>Najczęściej zadawane pytania</h2>
            <div class="faq-list">
                <?php foreach ($faq as $q): ?>
                <details class="faq-item">
                    <summary><?php echo e($q['question']); ?></summary>
                    <p><?php echo e($q['answer']); ?></p>
                </details>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <footer class="footer" id="kontakt">
        <div class="footer-inner">
            <img src="/assets/logo.jpg" alt="" class="footer-logo" loading="lazy">
            <h2>Napiszmy razem Waszą słodką historię</h2>
            <div class="footer-actions">
                <a href="<?php echo $instagramUrl; ?>" target="_blank" rel="noopener" class="btn btn-primary"><?php echo $instagramHandle; ?></a>
                <a href="<?php echo $messengerUrl; ?>" target="_blank" rel="noopener" class="btn btn-outline">Messenger</a>
            </div>
            <p class="footer-line"><strong>Obszar działania:</strong> <?php echo $deliveryArea; ?></p>
            <p class="footer-line"><strong>Dojazd:</strong> <?php echo $deliveryReach; ?></p>
            <p class="admin-link"><a href="/admin/">Panel administracyjny</a></p>
        </div>
    </footer>

    <div class="lightbox" id="lightbox" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Podgląd zdjęcia">
        <button class="lightbox-close" type="button" aria-label="Zamknij">×</button>
        <button class="lightbox-arrow lightbox-prev" type="button" aria-label="Poprzednie zdjęcie">‹</button>
        <div class="lightbox-stage"></div>
        <button class="lightbox-arrow lightbox-next" type="button" aria-label="Następne zdjęcie">›</button>
    </div>

    <script src="/assets/js/main.js" defer></script>
</body>
</html>
