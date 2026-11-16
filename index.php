<?php get_header() ?>
<?php get_template_part('frontend/views/layout/header') ?>
<main data-page="page">
    <section data-module="title">
        <h1 class="title">Seite nicht gefunden</h1>
        <p class="lead"><a href="<?= home_url('/') ?>">Zurück zur Startseite</a></p>
    </section>
</main>
<?php get_template_part('frontend/views/layout/footer') ?>
<?php get_footer() ?>
