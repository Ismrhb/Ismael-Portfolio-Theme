<?php
$data = $args['data'];
$classes = trim(($data['mobile_only'] ? 'mobile-only' : '') . ($data['image'] ? ' has-image' : ''));
?>
<section data-module="contact" class="<?= esc_attr($classes) ?>">
    <?php if ($data['image']) : ?>
        <figure class="contact-media">
            <?php get_template_part('frontend/views/partials/image', null, [
                'image' => $data['image'],
                'class' => 'contact-image',
                'sizes' => '40vw',
            ]) ?>
            <?php if ($data['image_text']) : ?>
                <figcaption class="contact-image-text"><?= nl2br(esc_html($data['image_text'])) ?></figcaption>
            <?php endif; ?>
        </figure>
    <?php endif; ?>
    <?php get_template_part('frontend/views/partials/contact-form', null, [
        'id' => 'module',
        'kicker' => $data['kicker'] ?? '',
        'submit' => $data['submit'] ?? '',
        'phone' => $data['image'] ? $data['phone'] : false,
    ]) ?>
</section>
