<?php
$data = $args['data'];
?>
<section data-module="intro">
    <div class="intro-aside">
        <div class="intro-text">
            <h1 class="intro-heading">
                <span class="greeting"><?= esc_html($data['greeting']) ?></span>
                <span class="intro"><?= esc_html($data['intro']) ?></span>
                <span class="name"><?= esc_html($data['name']) ?></span>
                <span class="subtitle"><?= esc_html($data['subtitle']) ?></span>
            </h1>
            <?php if (!empty($data['tagline'])) : ?>
                <p class="intro-tagline"><?= esc_html($data['tagline']) ?></p>
            <?php endif; ?>
        </div>
        <div class="intro-portrait-wrap">
            <button class="intro-portrait" type="button" data-contact-open aria-label="Kontaktformular öffnen">
                <?php get_template_part('frontend/views/partials/picture', null, [
                    'media' => $data['portrait'],
                    'class' => 'intro-portrait-image',
                    'sizes' => '(max-width: 768px) 100vw, 40vw',
                    'eager' => true,
                ]) ?>
                <?php // -> Erstes der wechselnden Wörter (Globales), sonst der feste Text; main.js lässt die Wörter wechseln ?>
                <span class="intro-portrait-hover" aria-hidden="true"><?= esc_html(get_globals_form()['words'][0] ?? $data['portrait_hover']) ?></span>
            </button>
            <?php // -> Mobil: Button auf dem Portrait führt zu den Projekten darunter ?>
            <a class="intro-portrait-projects" href="#projekte">Projekte</a>
        </div>
        <?php if ($data['collection_url']) : ?>
            <?php // -> Desktop: Link zur Projekte-Übersicht unter dem Portrait, weil das Projekt-Band rechts kein Ende hat ?>
            <a class="text-link intro-aside-link" href="<?= esc_url($data['collection_url']) ?>">Alle Projekte ansehen</a>
        <?php endif; ?>
    </div>
    <div class="intro-projects" id="projekte">
        <h2 class="visually-hidden">Ausgewählte Projekte</h2>
        <?php foreach ($data['projects'] as $project) : ?>
            <?php get_template_part('frontend/views/teaser/project-highlight', null, ['project' => $project]) ?>
        <?php endforeach; ?>
        <?php if ($data['collection_url']) : ?>
            <a class="button-block intro-all-projects" href="<?= esc_url($data['collection_url']) ?>">Alle Projekte ansehen</a>
        <?php endif; ?>
        <?php get_template_part('frontend/views/partials/cta', null, [
            'title' => $data['cta_title'] ?? 'Lust auf ein gemeinsames Projekt?',
            'collection_url' => $data['collection_url'],
            'collection_label' => 'oder alle Projekte ansehen',
        ]) ?>
    </div>
</section>
