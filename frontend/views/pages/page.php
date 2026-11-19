<main id="inhalt" tabindex="-1" data-page="page">
    <?php render_modules(); ?>
    <?php // -> Angebotsseite: zum Schluss alle Projekte als Band
    if (page_has_module('offers')) {
        get_template_part('frontend/views/partials/more-projects', null, [
            'projects' => get_projects(),
            'title' => 'Projekte',
        ]);
    } ?>
</main>
