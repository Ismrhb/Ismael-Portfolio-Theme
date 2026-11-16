<?php
$collection_url = get_collection_url();
$next = get_next_project(get_the_ID());
?>
<main data-page="project">
    <?php if ($collection_url) : ?>
        <nav class="project-back" aria-label="Zurück">
            <a class="link-arrow" href="<?= esc_url($collection_url) ?>">Zurück zur Sammlung</a>
        </nav>
    <?php endif; ?>
    <?php render_modules(); ?>
    <?php if ($next) : ?>
        <nav class="project-next" aria-label="Weitere Projekte">
            <a class="project-next-link" href="<?= esc_url($next['url']) ?>">
                <span class="project-next-label">Nächstes Projekt</span>
                <span class="project-next-title"><?= esc_html($next['title']) ?></span>
            </a>
        </nav>
    <?php endif; ?>
</main>
