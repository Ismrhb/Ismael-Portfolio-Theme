<?php
$data = $args['data'];
?>
<section data-module="offer-intro">
    <div class="offer-intro-inner">
        <div class="offer-intro-text">
            <?php if ($data['kicker']) : ?>
                <p class="kicker"><?= esc_html($data['kicker']) ?></p>
            <?php endif; ?>
            <h1 class="offer-intro-title"><?= nl2br(esc_html($data['title'])) ?></h1>
            <?php if ($data['text']) : ?>
                <p class="offer-intro-lead"><?= nl2br(esc_html($data['text'])) ?></p>
            <?php endif; ?>
            <div class="offer-intro-actions">
                <?php if ($data['button']) : ?>
                    <button class="button-contact" type="button" data-contact-open><?= esc_html($data['button']) ?></button>
                <?php endif; ?>
                <?php if ($data['process_link']) : ?>
                    <a class="text-link" href="#ablauf"><?= esc_html($data['process_link']) ?></a>
                <?php endif; ?>
            </div>
            <?php if ($data['note']) : ?>
                <p class="offer-intro-note"><?= esc_html($data['note']) ?></p>
            <?php endif; ?>
        </div>
        <?php if ($data['images']) : ?>
            <div class="offer-intro-collage">
                <?php foreach ($data['images'] as $image) : ?>
                    <?php get_template_part('frontend/views/partials/image', null, [
                        'image' => $image,
                        'class' => 'offer-intro-image',
                        'sizes' => '(max-width: 768px) 50vw, 25vw',
                    ]) ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
