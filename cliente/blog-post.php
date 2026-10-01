<?php
$blogData = json_decode(file_get_contents(__DIR__ . '/../blog.json'), true);
$posts = array_values(array_filter($blogData['posts'] ?? [], function ($p) { return !empty($p['published']); }));
usort($posts, function ($a, $b) { return strcmp($b['date'] ?? '', $a['date'] ?? ''); });

$slug = $_GET['slug'] ?? '';
$post = null;
$postIndex = null;
foreach ($posts as $i => $p) {
    if ($p['id'] === $slug) { $post = $p; $postIndex = $i; break; }
}

if (!$post) {
    header('Location: /cliente/blog.php');
    exit;
}

$prevPost = $postIndex > 0 ? $posts[$postIndex - 1] : null;
$nextPost = $postIndex < count($posts) - 1 ? $posts[$postIndex + 1] : null;

$langs = ['es', 'en', 'it'];
$backLabel = ['es' => '← Volver al blog', 'en' => '← Back to the blog', 'it' => '← Torna al blog'];
$prevLabel = ['es' => 'Posteo anterior', 'en' => 'Previous post', 'it' => 'Post precedente'];
$nextLabel = ['es' => 'Posteo siguiente', 'en' => 'Next post', 'it' => 'Post successivo'];
include __DIR__ . '/../partials/blog-helpers.php';

$pageTitle = ($post['title']['es'] ?? 'Historia') . ' | Blog de Argentina 1810';
$pageDescription = $post['excerpt']['es'] ?? 'Historias desde Argentina.';
$i18nSrc = '/cliente/translations.json';
include __DIR__ . '/../partials/head.php';
?>
<body data-i18n-src="<?= htmlspecialchars($i18nSrc) ?>" class="nav-cliente-alt page-white-canvas page-hero-overlay">
<?php $activePage = 'blog'; $headerOverlay = true; include __DIR__ . '/../partials/nav-cliente.php'; ?>

<main id="contenido-principal">
    <div class="blog-post__media">
        <video autoplay muted loop playsinline aria-hidden="true">
            <source src="/assets/videos/paisajes-argentina.mp4" type="video/mp4">
        </video>
    </div>

    <section class="section container-a1810 blog-post">
        <?php foreach ($langs as $lang): ?>
            <a class="blog-post__back" data-lang="<?= $lang ?>" href="/cliente/blog.php"><?= htmlspecialchars($backLabel[$lang]) ?></a>
        <?php endforeach; ?>

        <?php if (!empty($post['category'])): ?>
            <p class="blog-card__category"><?= htmlspecialchars($post['category']) ?></p>
        <?php endif; ?>

        <?php
            $galleryPaths = array_map(function ($src) { return '/assets/imgs/' . $src; }, $post['gallery'] ?? []);
        ?>
        <div class="blog-post__aside" aria-hidden="true" data-gallery='<?= htmlspecialchars(json_encode($galleryPaths)) ?>'>
            <div class="blog-post__aside-frame"></div>
        </div>

        <?php foreach ($langs as $lang): ?>
            <div data-lang="<?= $lang ?>">
                <?php if (!empty($post['date'])): ?>
                    <p class="blog-card__date"><?= htmlspecialchars(formatBlogDate($post['date'], $lang, $monthNames)) ?></p>
                <?php endif; ?>
                <h1 class="blog-post__title"><?= htmlspecialchars($post['title'][$lang] ?? $post['title']['es'] ?? '') ?></h1>
                <?php if (!empty($post['excerpt'][$lang])): ?>
                    <p class="blog-post__lede"><?= htmlspecialchars($post['excerpt'][$lang]) ?></p>
                <?php endif; ?>
                <hr class="blog-post__divider">
                <div class="blog-post__body">
                    <?php
                        $paragraphs = array_values(array_filter(explode("\n", trim($post['body'][$lang] ?? $post['body']['es'] ?? '')), function ($p) { return trim($p) !== ''; }));
                        $midpoint = (int) ceil(count($paragraphs) / 2);
                    ?>
                    <?php foreach ($paragraphs as $i => $paragraph): ?>
                        <p><?= htmlspecialchars($paragraph) ?></p>
                        <?php if ($i === $midpoint - 1 && !empty($post['highlight'][$lang])): ?>
                            <blockquote class="blog-post__highlight"><?= htmlspecialchars($post['highlight'][$lang]) ?></blockquote>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if ($prevPost || $nextPost): ?>
            <nav class="blog-post__nav">
                <?php if ($prevPost): ?>
                    <a class="blog-post__nav-link blog-post__nav-link--prev" href="/cliente/blog-post.php?slug=<?= urlencode($prevPost['id']) ?>">
                        <span class="blog-post__nav-arrow">←</span>
                        <span class="blog-post__nav-text">
                            <?php foreach ($langs as $lang): ?>
                                <span data-lang="<?= $lang ?>">
                                    <span class="blog-post__nav-label"><?= htmlspecialchars($prevLabel[$lang]) ?></span>
                                    <span class="blog-post__nav-title"><?= htmlspecialchars($prevPost['title'][$lang] ?? $prevPost['title']['es'] ?? '') ?></span>
                                </span>
                            <?php endforeach; ?>
                        </span>
                    </a>
                <?php endif; ?>
                <?php if ($nextPost): ?>
                    <a class="blog-post__nav-link blog-post__nav-link--next" href="/cliente/blog-post.php?slug=<?= urlencode($nextPost['id']) ?>">
                        <span class="blog-post__nav-text">
                            <?php foreach ($langs as $lang): ?>
                                <span data-lang="<?= $lang ?>">
                                    <span class="blog-post__nav-label"><?= htmlspecialchars($nextLabel[$lang]) ?></span>
                                    <span class="blog-post__nav-title"><?= htmlspecialchars($nextPost['title'][$lang] ?? $nextPost['title']['es'] ?? '') ?></span>
                                </span>
                            <?php endforeach; ?>
                        </span>
                        <span class="blog-post__nav-arrow">→</span>
                    </a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    </section>
</main>

<?php $footerTrack = 'cliente'; include __DIR__ . '/../partials/footer.php'; ?>
<script src="/assets/js/site.js"></script>
<script src="/assets/js/i18n.js"></script>
</body>
</html>
