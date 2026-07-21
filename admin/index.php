<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/content.php';

ensure_session();

$loginError = '';
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    verify_csrf();
    if (try_login($_POST['password'] ?? '')) {
        header('Location: /admin/');
        exit;
    }
    if (rate_lockout_remaining() > 0) {
        $loginError = 'Zbyt wiele prób. Spróbuj ponownie za kilka minut.';
    } else {
        $loginError = 'Nieprawidłowe hasło.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['logout'])) {
    verify_csrf();
    logout();
    header('Location: /admin/');
    exit;
}

$loggedIn = is_logged_in();

if ($loggedIn) {
    $settings = load_settings();
    $team = load_items('team');
    $testimonials = load_testimonials();
    $faq = load_faq();
    $gallery = load_gallery();
    $galleryTags = ['Wesele', 'Komunia', 'Chrzciny', 'Urodziny'];
    $categoryTree = build_category_tree(load_categories());
}

function admin_thumb(?string $file): string {
    if (!$file) return '<span class="admin-thumb-placeholder">brak</span>';
    return '<img src="/uploads/' . e($file) . '" alt="" class="admin-thumb">';
}

function setting_val(string $key, string $default = ''): string {
    return e($GLOBALS['settings'][$key] ?? $default);
}
?><!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel administracyjny — Torty Kuchciwróżki</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<?php if (!$loggedIn): ?>
    <div class="admin-login-wrap">
        <div class="admin-login-card">
            <img src="/assets/logo.jpg" alt="Torty Kuchciwróżki" class="admin-login-logo">
            <h1>Panel administracyjny</h1>
            <?php if ($loginError): ?><p class="admin-error"><?php echo e($loginError); ?></p><?php endif; ?>
            <form method="post" action="/admin/" class="admin-login-form">
                <?php echo csrf_input(); ?>
                <input type="hidden" name="action" value="login">
                <label for="password">Hasło</label>
                <input type="password" id="password" name="password" required autofocus>
                <button type="submit" class="btn btn-primary">Zaloguj</button>
            </form>
        </div>
    </div>
<?php else: ?>
    <header class="admin-header">
        <div class="admin-header-inner">
            <h1>Panel administracyjny</h1>
            <form method="post" action="/admin/?logout=1" class="admin-logout-form">
                <?php echo csrf_input(); ?>
                <button type="submit" class="btn btn-outline">Wyloguj</button>
            </form>
        </div>
    </header>

    <main class="admin-main">
        <?php if ($flash): ?><div class="admin-flash" role="status"><?php echo e($flash); ?></div><?php endif; ?>

        <section class="admin-section" id="hero">
            <h2>Hero</h2>
            <form action="save.php" method="post" class="admin-form">
                <?php echo csrf_input(); ?>
                <input type="hidden" name="section" value="hero">
                <input type="hidden" name="return_anchor" value="hero">
                <p>
                    <label for="hero_headline">Nagłówek H1</label>
                    <textarea id="hero_headline" name="hero_headline" rows="3"><?php echo setting_val('hero_headline'); ?></textarea>
                </p>
                <p>
                    <label for="hero_subline">Podtytuł</label>
                    <textarea id="hero_subline" name="hero_subline" rows="3"><?php echo setting_val('hero_subline'); ?></textarea>
                </p>
                <p><button type="submit" class="btn btn-primary">Zapisz</button></p>
            </form>
        </section>

        <section class="admin-section" id="counter">
            <h2>Licznik</h2>
            <form action="save.php" method="post" class="admin-form">
                <?php echo csrf_input(); ?>
                <input type="hidden" name="section" value="counter">
                <input type="hidden" name="return_anchor" value="counter">
                <p>
                    <label><input type="checkbox" name="counter_enabled" value="1" <?php if (setting_val('counter_enabled') === '1') echo 'checked'; ?>> Włącz licznik</label>
                </p>
                <p>
                    <label for="counter_value">Wartość</label>
                    <input type="number" id="counter_value" name="counter_value" value="<?php echo setting_val('counter_value'); ?>">
                </p>
                <p>
                    <label for="counter_label">Etykieta</label>
                    <input type="text" id="counter_label" name="counter_label" value="<?php echo setting_val('counter_label'); ?>">
                </p>
                <p><button type="submit" class="btn btn-primary">Zapisz</button></p>
            </form>
        </section>

        <?php
        function render_items_table(array $items, string $type, string $anchor): void {
            $addAnchor = $anchor . '-add';
            ?>
            <section class="admin-section" id="<?php echo e($anchor); ?>">
                <h2><?php echo e(ucfirst($type === 'dessert' ? 'Słodkie stoły' : ($type === 'cake' ? 'Torty' : 'O nas'))); ?></h2>
                <table class="admin-table">
                    <thead>
                        <tr><th>Zdjęcie</th><th>Dane</th><th>Zmień zdjęcie</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo admin_thumb($item['image']); ?></td>
                            <td>
                                <form action="save.php" method="post" class="admin-inline-form">
                                    <?php echo csrf_input(); ?>
                                    <input type="hidden" name="section" value="items">
                                    <input type="hidden" name="type" value="<?php echo e($type); ?>">
                                    <input type="hidden" name="return_anchor" value="<?php echo e($anchor); ?>">
                                    <input type="hidden" name="id" value="<?php echo (int)$item['id']; ?>">
                                    <input type="hidden" name="item_action" value="update">
                                    <p><input type="text" name="name" value="<?php echo e($item['name']); ?>" placeholder="Nazwa" required></p>
                                    <p><textarea name="description" rows="3" placeholder="Opis"><?php echo e($item['description']); ?></textarea></p>
                                    <p>
                                        <label>Kolejność <input type="number" name="sort_order" value="<?php echo (int)$item['sort_order']; ?>" style="width:4rem"></label>
                                    </p>
                                    <p>
                                        <button type="submit" class="btn btn-primary">Zapisz</button>
                                        <button type="submit" name="item_action" value="delete" class="btn btn-danger" onclick="return confirm('Usunąć pozycję?')">Usuń</button>
                                    </p>
                                </form>
                            </td>
                            <td>
                                <form action="upload.php" method="post" enctype="multipart/form-data" class="admin-upload-form">
                                    <?php echo csrf_input(); ?>
                                    <input type="hidden" name="entity" value="item">
                                    <input type="hidden" name="id" value="<?php echo (int)$item['id']; ?>">
                                    <input type="hidden" name="return_anchor" value="<?php echo e($anchor); ?>">
                                    <p><input type="file" name="image" accept="image/jpeg,image/png,image/webp" required></p>
                                    <p><button type="submit" class="btn btn-secondary">Wgraj</button></p>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="admin-add-box" id="<?php echo e($addAnchor); ?>">
                    <h3>+ Dodaj</h3>
                    <form action="save.php" method="post" class="admin-form">
                        <?php echo csrf_input(); ?>
                        <input type="hidden" name="section" value="items">
                        <input type="hidden" name="type" value="<?php echo e($type); ?>">
                        <input type="hidden" name="item_action" value="add">
                        <input type="hidden" name="return_anchor" value="<?php echo e($anchor); ?>">
                        <p><input type="text" name="name" placeholder="Nazwa" required></p>
                        <p><textarea name="description" rows="2" placeholder="Opis"></textarea></p>
                        <p><label>Kolejność <input type="number" name="sort_order" value="0"></label></p>
                        <p><button type="submit" class="btn btn-primary">Dodaj</button></p>
                    </form>
                </div>
        </section>

        <?php
        }

        function render_category_node(array $node, int $depth = 0): void {
            ?>
            <div class="admin-cat-node" style="margin-left: <?php echo $depth * 1.5; ?>rem">
                <div class="admin-cat-row">
                    <?php echo admin_thumb($node['image']); ?>
                    <form action="save.php" method="post" class="admin-inline-form admin-cat-form">
                        <?php echo csrf_input(); ?>
                        <input type="hidden" name="section" value="categories">
                        <input type="hidden" name="return_anchor" value="oferta">
                        <input type="hidden" name="id" value="<?php echo (int)$node['id']; ?>">
                        <input type="hidden" name="cat_action" value="update">
                        <input type="text" name="name" value="<?php echo e($node['name']); ?>" required>
                        <label>Kolejność <input type="number" name="sort_order" value="<?php echo (int)$node['sort_order']; ?>" style="width:4rem"></label>
                        <?php if ($depth === 0): ?>
                        <label><input type="checkbox" name="show_images" value="1" <?php if (!empty($node['show_images'])) echo 'checked'; ?>> Zdjęcia w tej gałęzi</label>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary">Zapisz</button>
                        <button type="submit" name="cat_action" value="delete" class="btn btn-danger" onclick="return confirm('Usunąć tę pozycję razem z podkategoriami?')">Usuń</button>
                    </form>
                    <form action="upload.php" method="post" enctype="multipart/form-data" class="admin-upload-form">
                        <?php echo csrf_input(); ?>
                        <input type="hidden" name="entity" value="category">
                        <input type="hidden" name="id" value="<?php echo (int)$node['id']; ?>">
                        <input type="hidden" name="return_anchor" value="oferta">
                        <input type="file" name="image" accept="image/jpeg,image/png,image/webp" required>
                        <button type="submit" class="btn btn-secondary">Wgraj</button>
                    </form>
                </div>
                <?php foreach ($node['children'] as $child): render_category_node($child, $depth + 1); endforeach; ?>
                <form action="save.php" method="post" class="admin-inline-form admin-cat-add">
                    <?php echo csrf_input(); ?>
                    <input type="hidden" name="section" value="categories">
                    <input type="hidden" name="return_anchor" value="oferta">
                    <input type="hidden" name="parent_id" value="<?php echo (int)$node['id']; ?>">
                    <input type="hidden" name="cat_action" value="add">
                    <input type="text" name="name" placeholder="+ Dodaj podkategorię / pozycję" required>
                    <input type="number" name="sort_order" value="0" style="width:4rem" title="Kolejność">
                    <button type="submit" class="btn btn-secondary">Dodaj</button>
                </form>
            </div>
            <?php
        }
        ?>

        <section class="admin-section" id="oferta">
            <h2>Oferta (drzewo kategorii)</h2>
            <p style="color:var(--muted);margin-bottom:1rem">Dodawaj podkategorie i pozycje na dowolnym poziomie. Usunięcie węzła kasuje też wszystko pod nim. Zdjęcia wgrywasz przy pozycjach-liściach w gałęziach z włączoną opcją "Zdjęcia w tej gałęzi".</p>
            <?php foreach ($categoryTree as $branch): render_category_node($branch, 0); ?>
            <hr style="margin:1.5rem 0;border:none;border-top:1px solid var(--bg-alt)">
            <?php endforeach; ?>
            <div class="admin-add-box">
                <h3>+ Dodaj nową gałąź główną</h3>
                <form action="save.php" method="post" class="admin-form">
                    <?php echo csrf_input(); ?>
                    <input type="hidden" name="section" value="categories">
                    <input type="hidden" name="return_anchor" value="oferta">
                    <input type="hidden" name="cat_action" value="add">
                    <p><input type="text" name="name" placeholder="Nazwa" required></p>
                    <p><label>Kolejność <input type="number" name="sort_order" value="0"></label></p>
                    <p><label><input type="checkbox" name="show_images" value="1"> Zdjęcia w tej gałęzi</label></p>
                    <p><button type="submit" class="btn btn-primary">Dodaj</button></p>
                </form>
            </div>
        </section>

        <?php
        render_items_table($team, 'team', 'o-nas');
        ?>

        <section class="admin-section" id="testimonials">
            <h2>Opinie</h2>
            <table class="admin-table">
                <thead><tr><th>Treść</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($testimonials as $t): ?>
                    <tr>
                        <td>
                            <form action="save.php" method="post" class="admin-inline-form">
                                <?php echo csrf_input(); ?>
                                <input type="hidden" name="section" value="testimonials">
                                <input type="hidden" name="return_anchor" value="testimonials">
                                <input type="hidden" name="id" value="<?php echo (int)$t['id']; ?>">
                                <p><textarea name="quote" rows="3" placeholder="Cytat" required><?php echo e($t['quote']); ?></textarea></p>
                                <p><input type="text" name="author" value="<?php echo e($t['author']); ?>" placeholder="Autor" required></p>
                                <p><label>Kolejność <input type="number" name="sort_order" value="<?php echo (int)$t['sort_order']; ?>" style="width:4rem"></label></p>
                                <p>
                                    <button type="submit" class="btn btn-primary">Zapisz</button>
                                    <button type="submit" name="ta" value="delete" class="btn btn-danger" onclick="return confirm('Usunąć opinię?')">Usuń</button>
                                </p>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div class="admin-add-box">
                <h3>+ Dodaj</h3>
                <form action="save.php" method="post" class="admin-form">
                    <?php echo csrf_input(); ?>
                    <input type="hidden" name="section" value="testimonials">
                    <input type="hidden" name="ta" value="add">
                    <input type="hidden" name="return_anchor" value="testimonials">
                    <p><textarea name="quote" rows="2" placeholder="Cytat" required></textarea></p>
                    <p><input type="text" name="author" placeholder="Autor" required></p>
                    <p><label>Kolejność <input type="number" name="sort_order" value="0"></label></p>
                    <p><button type="submit" class="btn btn-primary">Dodaj</button></p>
                </form>
            </div>
        </section>

        <section class="admin-section" id="faq">
            <h2>FAQ</h2>
            <table class="admin-table">
                <thead><tr><th>Pytanie / odpowiedź</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($faq as $q): ?>
                    <tr>
                        <td>
                            <form action="save.php" method="post" class="admin-inline-form">
                                <?php echo csrf_input(); ?>
                                <input type="hidden" name="section" value="faq">
                                <input type="hidden" name="return_anchor" value="faq">
                                <input type="hidden" name="id" value="<?php echo (int)$q['id']; ?>">
                                <p><input type="text" name="question" value="<?php echo e($q['question']); ?>" placeholder="Pytanie" required></p>
                                <p><textarea name="answer" rows="3" placeholder="Odpowiedź" required><?php echo e($q['answer']); ?></textarea></p>
                                <p><label>Kolejność <input type="number" name="sort_order" value="<?php echo (int)$q['sort_order']; ?>" style="width:4rem"></label></p>
                                <p>
                                    <button type="submit" class="btn btn-primary">Zapisz</button>
                                    <button type="submit" name="fa" value="delete" class="btn btn-danger" onclick="return confirm('Usunąć pytanie?')">Usuń</button>
                                </p>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div class="admin-add-box">
                <h3>+ Dodaj</h3>
                <form action="save.php" method="post" class="admin-form">
                    <?php echo csrf_input(); ?>
                    <input type="hidden" name="section" value="faq">
                    <input type="hidden" name="fa" value="add">
                    <input type="hidden" name="return_anchor" value="faq">
                    <p><input type="text" name="question" placeholder="Pytanie" required></p>
                    <p><textarea name="answer" rows="2" placeholder="Odpowiedź" required></textarea></p>
                    <p><label>Kolejność <input type="number" name="sort_order" value="0"></label></p>
                    <p><button type="submit" class="btn btn-primary">Dodaj</button></p>
                </form>
            </div>
        </section>

        <section class="admin-section" id="gallery">
            <h2>Galeria</h2>
            <table class="admin-table">
                <thead><tr><th>Zdjęcie</th><th>Dane</th><th>Zmień zdjęcie</th></tr></thead>
                <tbody>
                <?php foreach ($gallery as $g): ?>
                    <tr>
                        <td><?php echo admin_thumb($g['image']); ?></td>
                        <td>
                            <form action="save.php" method="post" class="admin-inline-form">
                                <?php echo csrf_input(); ?>
                                <input type="hidden" name="section" value="gallery">
                                <input type="hidden" name="return_anchor" value="gallery">
                                <input type="hidden" name="id" value="<?php echo (int)$g['id']; ?>">
                                <p>
                                    <label>Tag
                                        <select name="tag">
                                            <?php foreach ($GLOBALS['galleryTags'] as $tag): ?>
                                            <option value="<?php echo e($tag); ?>" <?php if (($g['tag'] ?? '') === $tag) echo 'selected'; ?>><?php echo e($tag); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </label>
                                </p>
                                <p><label>Kolejność <input type="number" name="sort_order" value="<?php echo (int)$g['sort_order']; ?>" style="width:4rem"></label></p>
                                <p>
                                    <button type="submit" class="btn btn-primary">Zapisz</button>
                                    <button type="submit" name="ga" value="delete" class="btn btn-danger" onclick="return confirm('Usunąć zdjęcie?')">Usuń</button>
                                </p>
                            </form>
                        </td>
                        <td>
                            <form action="upload.php" method="post" enctype="multipart/form-data" class="admin-upload-form">
                                <?php echo csrf_input(); ?>
                                <input type="hidden" name="entity" value="gallery">
                                <input type="hidden" name="id" value="<?php echo (int)$g['id']; ?>">
                                <input type="hidden" name="return_anchor" value="gallery">
                                <p><input type="file" name="image" accept="image/jpeg,image/png,image/webp" required></p>
                                <p><button type="submit" class="btn btn-secondary">Wgraj</button></p>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div class="admin-add-box">
                <h3>+ Dodaj zdjęcie</h3>
                <form action="save.php" method="post" class="admin-form">
                    <?php echo csrf_input(); ?>
                    <input type="hidden" name="section" value="gallery">
                    <input type="hidden" name="ga" value="add">
                    <input type="hidden" name="return_anchor" value="gallery">
                    <p>
                        <label>Tag
                            <select name="tag">
                                <?php foreach ($galleryTags as $tag): ?>
                                <option value="<?php echo e($tag); ?>"><?php echo e($tag); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </p>
                    <p><label>Kolejność <input type="number" name="sort_order" value="0"></label></p>
                    <p><button type="submit" class="btn btn-primary">Dodaj</button></p>
                </form>
            </div>
        </section>

        <section class="admin-section" id="contact">
            <h2>Kontakt</h2>
            <form action="save.php" method="post" class="admin-form">
                <?php echo csrf_input(); ?>
                <input type="hidden" name="section" value="contact">
                <input type="hidden" name="return_anchor" value="contact">
                <p><label for="instagram_url">Instagram URL</label><input type="url" id="instagram_url" name="instagram_url" value="<?php echo setting_val('instagram_url'); ?>"></p>
                <p><label for="instagram_handle">Instagram handle</label><input type="text" id="instagram_handle" name="instagram_handle" value="<?php echo setting_val('instagram_handle'); ?>"></p>
                <p><label for="messenger_url">Messenger URL</label><input type="url" id="messenger_url" name="messenger_url" value="<?php echo setting_val('messenger_url'); ?>"></p>
                <p><label for="delivery_area">Obszar działania</label><input type="text" id="delivery_area" name="delivery_area" value="<?php echo setting_val('delivery_area'); ?>"></p>
                <p><label for="delivery_reach">Dojazd</label><input type="text" id="delivery_reach" name="delivery_reach" value="<?php echo setting_val('delivery_reach'); ?>"></p>
                <p><button type="submit" class="btn btn-primary">Zapisz</button></p>
            </form>
        </section>
    </main>

    <script src="/assets/js/admin.js?v=1" defer></script>
<?php endif; ?>
</body>
</html>
