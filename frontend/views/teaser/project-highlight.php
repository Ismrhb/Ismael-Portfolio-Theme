<?php
// -> Projekt auf der Startseite: Desktop mit Bild, Titel und Text; Mobile mit dem Titelbild der Projekte-Übersicht
$project = $args['project'];
$teaser = $project['teaser_image'];
$media = $project['highlight_image'] ? [
    'desktop' => $project['highlight_image'],
    'mobile' => $teaser ? ($teaser['mobile'] ?: $teaser['desktop']) : false,
] : false;
?>
<article class="project-highlight">
    <a class="project-highlight-link" href="<?= esc_url($project['url']) ?>">
        <div class="project-highlight-media">
            <?php get_template_part('frontend/views/partials/picture', null, [
                'media' => $media,
                'alt' => $project['alt'],
                'class' => 'project-highlight-image',
                'sizes' => '(max-width: 768px) 100vw, 50vw',
            ]) ?>
            <?php get_template_part('frontend/views/partials/teaser-video', null, ['project' => $project, 'class' => 'project-highlight-video']) ?>
        </div>
        <?php if ($project['title']) : ?>
            <h3 class="project-highlight-title"><?= esc_html($project['title']) ?></h3>
        <?php endif; ?>
        <?php if ($project['highlight_text']) : ?>
            <p class="project-highlight-text"><?= esc_html($project['highlight_text']) ?></p>
        <?php endif; ?>
    </a>
</article>
