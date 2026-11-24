<?php
$others = get_other_projects(get_the_ID());
$contact = get_project_contact_module();
?>
<main id="inhalt" tabindex="-1" data-page="project">
    <?php render_modules(); ?>
    <?php if ($contact) : ?>
        <?php // -> Kontaktformular unter jedem Projekt, vor «Andere Projekte» ?>
        <?php get_template_part('frontend/views/modules/contact', null, ['data' => $contact]) ?>
    <?php endif; ?>
    <?php get_template_part('frontend/views/partials/more-projects', null, [
        'projects' => $others,
        'title' => 'Andere Projekte',
    ]) ?>
</main>
