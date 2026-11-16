<?php
// -> Projekt-Teaser in der Sammlung: fertig gestaltetes Titelbild mit Kategorie
$project = $args['project'];
$slugs = implode(' ', array_column($project['categories'], 'slug'));
?>
<article class="project-teaser" data-priority="<?= esc_attr($project['priority']) ?>" data-categories="<?= esc_attr($slugs) ?>" data-id="<?= (int) $project['id'] ?>">
    <a class="project-teaser-link" href="<?= esc_url($project['url']) ?>">
        <?php get_template_part('frontend/views/partials/picture', null, [
            'media' => $project['teaser_image'],
            'alt' => $project['alt'],
            'class' => 'project-teaser-image',
            'transition' => 'project-image-' . $project['id'],
            'sizes' => '(max-width: 768px) 100vw, 66vw',
        ]) ?>
        <?php if ($project['categories']) : ?>
            <span class="tag"><?= esc_html($project['categories'][0]['name']) ?></span>
        <?php endif; ?>
        <?php if ($project['title']) : ?>
            <h2 class="visually-hidden"><?= esc_html($project['title']) ?></h2>
        <?php endif; ?>
    </a>
</article>
