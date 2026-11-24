<?php
$others = get_other_projects(get_the_ID());
$cta = get_project_cta();
?>
<main id="inhalt" tabindex="-1" data-page="project">
    <?php render_modules(); ?>
    <?php if ($cta) : ?>
        <?php // -> Abschluss mit Kontakt-Button unter jedem Projekt, vor «Andere Projekte» ?>
        <section data-module="cta">
            <?php get_template_part('frontend/views/partials/cta', null, $cta) ?>
        </section>
    <?php endif; ?>
    <?php get_template_part('frontend/views/partials/more-projects', null, [
        'projects' => $others,
        'title' => 'Andere Projekte',
    ]) ?>
</main>
