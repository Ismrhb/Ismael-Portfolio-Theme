<?php
$form = get_globals_form();
$id = $args['id'] ?? 'form';
?>
<form class="contact-form" method="post" action="<?= esc_url(admin_url('admin-ajax.php')) ?>" data-contact-form novalidate>
    <?php if (!empty($args['kicker'])) : ?>
        <p class="kicker"><?= esc_html($args['kicker']) ?></p>
    <?php endif; ?>
    <h2 class="contact-title"><?= esc_html($form['title']) ?></h2>
    <input type="hidden" name="action" value="contact_form">
    <?php wp_nonce_field('contact_form', 'contact_nonce', false) ?>
    <div class="contact-honeypot" aria-hidden="true">
        <label for="<?= $id ?>-website">Website</label>
        <input id="<?= $id ?>-website" type="text" name="website" tabindex="-1" autocomplete="off">
    </div>
    <div class="contact-field contact-field-half">
        <label class="contact-label" for="<?= $id ?>-name">Name</label>
        <input id="<?= $id ?>-name" type="text" name="name" autocomplete="name" required aria-describedby="<?= $id ?>-name-error">
        <p class="contact-error" id="<?= $id ?>-name-error"></p>
    </div>
    <div class="contact-field contact-field-half">
        <label class="contact-label" for="<?= $id ?>-contact">E-Mail oder Telefon</label>
        <input id="<?= $id ?>-contact" type="text" name="contact" autocomplete="email" required data-validate="contact" aria-describedby="<?= $id ?>-contact-error">
        <p class="contact-error" id="<?= $id ?>-contact-error"></p>
    </div>
    <div class="contact-row">
        <div class="contact-field">
            <label class="contact-label" for="<?= $id ?>-occasion">Anlass <span class="contact-optional">(optional)</span></label>
            <select id="<?= $id ?>-occasion" name="occasion">
                <option value="">Bitte wählen</option>
                <?php foreach ($form['occasions'] as $occasion) : ?>
                    <option value="<?= esc_attr($occasion) ?>"><?= esc_html($occasion) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="contact-field">
            <label class="contact-label" for="<?= $id ?>-date">Datum <span class="contact-optional">(optional)</span></label>
            <input id="<?= $id ?>-date" type="date" name="date">
        </div>
    </div>
    <div class="contact-field">
        <label class="contact-label" for="<?= $id ?>-message">Wie kann ich dir helfen?</label>
        <textarea id="<?= $id ?>-message" name="message" rows="4" required aria-describedby="<?= $id ?>-message-error"></textarea>
        <p class="contact-error" id="<?= $id ?>-message-error"></p>
    </div>
    <div class="contact-actions">
        <button class="button-submit" type="submit"><?= esc_html($args['submit'] ?? '' ?: 'Senden') ?></button>
        <?php if (!empty($args['phone']['phone'])) : ?>
            <p class="contact-direct">Lieber direkt? <a href="<?= esc_attr($args['phone']['phone_link']) ?>"><?= esc_html($args['phone']['phone']) ?></a></p>
        <?php endif; ?>
    </div>
    <p class="contact-privacy">
        Mit dem Absenden akzeptierst du die
        <?php if ($form['privacy_url']) : ?>
            <a href="<?= esc_url($form['privacy_url']) ?>">Datenschutzerklärung</a>.
        <?php else : ?>
            Datenschutzerklärung.
        <?php endif; ?>
    </p>
    <p class="contact-status" role="status" aria-live="polite"></p>
</form>
