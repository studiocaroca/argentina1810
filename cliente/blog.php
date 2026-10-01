<?php
$pageTitle = 'Historias desde Argentina | Blog de Argentina 1810';
$pageDescription = 'Guías, relatos y recomendaciones para descubrir Argentina como locales.';
$i18nSrc = '/cliente/translations.json';
include __DIR__ . '/../partials/head.php';

$blogData = json_decode(file_get_contents(__DIR__ . '/../blog.json'), true);
$posts = array_values(array_filter($blogData['posts'] ?? [], function ($p) { return !empty($p['published']); }));
usort($posts, function ($a, $b) { return strcmp($b['date'] ?? '', $a['date'] ?? ''); });
$langs = ['es', 'en', 'it'];
$ctaLabel = ['es' => 'Leer nota completa', 'en' => 'Read full post', 'it' => 'Leggi tutto'];
$photoPlaceholder = ['es' => 'Fotos — muy pronto', 'en' => 'Photos — coming soon', 'it' => 'Foto — in arrivo a breve'];
include __DIR__ . '/../partials/blog-helpers.php';
?>
<body data-i18n-src="<?= htmlspecialchars($i18nSrc) ?>" class="nav-cliente-alt page-white-canvas page-blog-flat">
<?php $activePage = 'blog'; include __DIR__ . '/../partials/nav-cliente.php'; ?>

<main id="contenido-principal">
    <section class="section container-a1810">
        <h1 class="section-title" data-section="blog" data-translate="title">Historias desde Argentina</h1>
        <p class="section-lede" data-section="blog" data-translate="lede">Relatos, guías y recomendaciones para descubrir el país como lo conocemos nosotros.</p>

        <?php if (empty($posts)): ?>
            <div class="blog-empty">
                <p data-section="blog" data-translate="empty_state">Muy pronto vas a encontrar acá relatos, guías y recomendaciones. Mientras tanto, contanos qué te gustaría leer.</p>
            </div>
        <?php else: ?>
            <div class="blog-grid">
                <?php foreach ($posts as $post): ?>
                    <a class="blog-card" href="/cliente/blog-post.php?slug=<?= urlencode($post['id']) ?>">
                        <div class="blog-card__media">
                            <?php if (!empty($post['cover'])): ?>
                                <img src="/assets/imgs/<?= htmlspecialchars($post['cover']) ?>" alt="" loading="lazy">
                            <?php else: ?>
                                <?php foreach ($langs as $lang): ?>
                                    <span class="blog-card__placeholder" data-lang="<?= $lang ?>"><?= htmlspecialchars($photoPlaceholder[$lang]) ?></span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="blog-card__body">
                            <?php if (!empty($post['category'])): ?>
                                <p class="blog-card__category"><?= htmlspecialchars($post['category']) ?></p>
                            <?php endif; ?>
                            <?php foreach ($langs as $lang): ?>
                                <div data-lang="<?= $lang ?>">
                                    <?php if (!empty($post['date'])): ?>
                                        <p class="blog-card__date"><?= htmlspecialchars(formatBlogDate($post['date'], $lang, $monthNames)) ?></p>
                                    <?php endif; ?>
                                    <h2 class="blog-card__title"><?= htmlspecialchars($post['title'][$lang] ?? $post['title']['es'] ?? '') ?></h2>
                                    <p class="blog-card__excerpt"><?= htmlspecialchars($post['excerpt'][$lang] ?? $post['excerpt']['es'] ?? '') ?></p>
                                    <span class="blog-card__cta"><?= htmlspecialchars($ctaLabel[$lang]) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php $footerTrack = 'cliente'; include __DIR__ . '/../partials/footer.php'; ?>
<script src="/assets/js/site.js"></script>
<script src="/assets/js/i18n.js"></script>
</body>
</html>
