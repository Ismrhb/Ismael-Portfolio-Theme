<?php
// -> Projekt-Teaser in der Projekte-Übersicht: Titelbild (oder Titelvideo) mit Kategorie, Titel beim Darüberfahren
$project = $args['project'];
$slugs = implode(' ', array_column($project['categories'], 'slug'));
?>
<article class="project-teaser" data-priority="<?= esc_attr($project['priority']) ?>" data-categories="<?= esc_attr($slugs) ?>" data-id="<?= (int) $project['id'] ?>" data-format="<?= esc_attr($project['highlight_format']) ?>" style="--focus: <?= esc_attr($project['focus']) ?>">
    <a class="project-teaser-link" href="<?= esc_url($project['url']) ?>">
        <?php get_template_part('frontend/views/partials/picture', null, [
            'media' => $project['teaser_image'],
            'alt' => $project['alt'],
            'class' => 'project-teaser-image',
            'sizes' => '(max-width: 768px) 100vw, 66vw',
            'eager' => !empty($args['eager']),
        ]) ?>
        <?php get_template_part('frontend/views/partials/teaser-video', null, ['project' => $project, 'class' => 'project-teaser-video']) ?>
        <?php if ($project['categories']) : ?>
            <span class="tag"><?= esc_html($project['categories'][0]['name']) ?></span>
        <?php endif; ?>
        <?php if ($project['title']) : ?>
            <h2 class="project-teaser-title"><?= esc_html($project['title']) ?></h2>
        <?php endif; ?>
    </a>
</article>
