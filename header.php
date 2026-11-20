<!DOCTYPE html>
<html lang="<?php bloginfo('language') ?>">
<head>
    <meta charset="<?php bloginfo('charset') ?>">
    <link rel="preload" href="<?= get_template_directory_uri() ?>/frontend/assets/fonts/dm-serif-display-latin-400-normal.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= get_template_directory_uri() ?>/frontend/assets/fonts/montserrat-latin-400-normal.woff2" as="font" type="font/woff2" crossorigin>
    <?php if (is_front_page()) : ?>
        <link rel="preload" href="<?= get_template_directory_uri() ?>/frontend/assets/fonts/montserrat-latin-300-normal.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="<?= get_template_directory_uri() ?>/frontend/assets/fonts/dm-serif-display-latin-400-italic.woff2" as="font" type="font/woff2" crossorigin>
        <?php // -> Tipp-Animation des Titels: nur beim ersten Besuch pro Sitzung; vor dem ersten Bild entscheiden, damit nichts aufblitzt ?>
        <script>
            try {
                if (!sessionStorage.getItem('ciao-seen') && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    document.documentElement.classList.add('is-typing');
                }
                sessionStorage.setItem('ciao-seen', '1');
            } catch (error) {}
        </script>
    <?php endif; ?>
    <link rel="stylesheet" href="<?= get_template_directory_uri() ?>/frontend/styles/main.css?v=<?= wp_get_theme()->get('Version') ?>">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover" />
    <title><?= esc_html(get_document_title_text()) ?></title>
    <?php wp_head(); ?>
</head>
<?php // -> Wechselnde Wörter für die Kontakt-Buttons (main.js) ?>
<body data-contact-words="<?= esc_attr(implode('|', get_globals_form()['words'])) ?>">
<a class="skip-link" href="#inhalt">Zum Inhalt springen</a>
