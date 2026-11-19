<?php
// -> Projekte als endloses Band (Projektseiten: "Andere Projekte", Angebotsseite: "Projekte")
if (empty($args['projects'])) {
    return;
}
?>
<?php // -> Klick auf ein seitliches Bild holt es in die Mitte, Klick auf das mittlere öffnet es ?>
<nav class="more-projects" aria-label="<?= esc_attr($args['title']) ?>">
    <h2 class="more-projects-title"><?= esc_html($args['title']) ?></h2>
    <div class="more-projects-track" tabindex="0">
        <?php foreach ($args['projects'] as $project) : ?>
            <a class="more-project" href="<?= esc_url($project['url']) ?>">
                <?php get_template_part('frontend/views/partials/picture', null, [
                    'media' => $project['teaser_image'],
                    'alt' => $project['alt'],
                    'class' => 'more-project-image',
                    'sizes' => '(max-width: 768px) 56vw, 30vw',
                ]) ?>
                <span class="more-project-name"><?= esc_html($project['title']) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</nav>
