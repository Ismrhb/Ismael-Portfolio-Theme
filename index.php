<?php get_header() ?>
<?php get_template_part('frontend/views/layout/header') ?>
<?php // -> Fehlerseite (404): kurz, mit etwas Witz, und gleich wieder zu den Projekten ?>
<main id="inhalt" tabindex="-1" data-page="page" class="is-404">
    <section data-module="title">
        <p class="kicker">Fehler 404</p>
        <h1 class="title">Dieses Bild ist leider verwackelt.</h1>
        <p class="lead">Die Seite gibt es nicht (mehr). Vielleicht findest du bei meinen Projekten, was du gesucht hast.</p>
        <div class="error-actions">
            <a class="button-contact" href="<?= esc_url(get_collection_url() ?: home_url('/')) ?>">Zu den Projekten</a>
            <a class="text-link" href="<?= esc_url(home_url('/')) ?>">Zur Startseite</a>
        </div>
    </section>
    <?php get_template_part('frontend/views/partials/more-projects', null, [
        'projects' => get_projects(),
        'title' => 'Projekte',
    ]) ?>
</main>
<?php get_template_part('frontend/views/layout/footer') ?>
<?php get_footer() ?>
