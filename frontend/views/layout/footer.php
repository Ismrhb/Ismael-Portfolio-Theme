<?php
$main_menu = get_menu_by_slug('main');
$legal_menu = get_menu_by_slug('legals');
$contact = get_globals_contact();
$socials = get_globals_socials();
?>
<?php // -> Abschluss mit Kontakt am Ende jeder Seite, nur mobil und nur wenn die Seite keinen eigenen hat ?>
<div class="page-cta">
    <?php get_template_part('frontend/views/partials/cta', null, ['title' => 'Lust auf ein gemeinsames Projekt?']) ?>
</div>
<footer data-layout="footer">
    <a class="logo" href="<?= home_url('/') ?>">
        <span class="logo-name">Ismael Rehab</span>
        <span class="logo-subtitle">Foto - Videograf</span>
    </a>
    <button class="button-contact" type="button" data-contact-open>Kontakt</button>
    <?php if ($main_menu) : ?>
        <ul class="footer-menu footer-menu-main">
            <?php foreach ($main_menu as $item) : ?>
                <li><a href="<?= esc_url($item['url']) ?>"><?= esc_html($item['title']) ?></a></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <?php if ($legal_menu) : ?>
        <ul class="footer-menu footer-menu-legals">
            <?php foreach ($legal_menu as $item) : ?>
                <li><a href="<?= esc_url($item['url']) ?>"><?= esc_html($item['title']) ?></a></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <?php if ($socials) : ?>
        <ul class="socials">
            <?php foreach ($socials as $key => $social) : ?>
                <li>
                    <a class="social" href="<?= esc_url($social['url']) ?>" target="_blank" rel="noopener" aria-label="<?= esc_attr($social['title']) ?>">
                        <?php get_template_part('frontend/views/partials/icon', null, ['name' => $key]) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <?php // -> Ohne Strasse und Ort keine Adresse ausgeben (der Name steht schon im Logo) ?>
    <?php if ($contact['street'] || $contact['city']) : ?>
        <address class="address">
            <?php if ($contact['name']) : ?><?= esc_html($contact['name']) ?><br><?php endif; ?>
            <?php if ($contact['street']) : ?><?= esc_html($contact['street']) ?><br><?php endif; ?>
            <?= esc_html($contact['city']) ?>
        </address>
    <?php endif; ?>
    <p class="contact-details">
        <?php if ($contact['phone']) : ?><a href="<?= esc_attr($contact['phone_link']) ?>"><?= esc_html($contact['phone']) ?></a><br><?php endif; ?>
        <?php if ($contact['email']) : ?><a href="mailto:<?= esc_attr($contact['email']) ?>"><?= esc_html($contact['email']) ?></a><?php endif; ?>
    </p>
</footer>
