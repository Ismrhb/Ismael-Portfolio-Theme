<?php
$others = get_other_projects(get_the_ID());
?>
<main id="inhalt" tabindex="-1" data-page="project">
    <?php render_modules(); ?>
    <?php get_template_part('frontend/views/partials/more-projects', null, [
        'projects' => $others,
        'title' => 'Andere Projekte',
    ]) ?>
</main>
