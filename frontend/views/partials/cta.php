<?php
// -> Abschluss mit Frage, Kontakt-Button und optionalem leisem Link zur Sammlung
$title = $args['title'] ?? '';
$url = $args['collection_url'] ?? '';
?>
<div class="cta">
    <?php if ($title) : ?>
        <p class="cta-title"><?= esc_html($title) ?></p>
    <?php endif; ?>
    <button class="button-contact" type="button" data-contact-open>Kontakt</button>
    <?php if ($url) : ?>
        <a class="cta-link" href="<?= esc_url($url) ?>"><?= esc_html($args['collection_label'] ?? 'oder zuerst die Sammlung ansehen') ?></a>
    <?php endif; ?>
</div>
