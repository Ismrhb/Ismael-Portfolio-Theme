<?php
$data = $args['data'];
if (!$data['images']) {
    return;
}
?>
<section data-module="carousel">
    <?php if ($data['title']) : ?>
        <h2 class="carousel-title"><?= esc_html($data['title']) ?></h2>
    <?php endif; ?>
    <div class="carousel-track" tabindex="0" aria-label="<?= esc_attr($data['title'] ?: 'Bilder') ?>">
        <?php foreach ($data['images'] as $image) : ?>
            <button class="carousel-item" type="button">
                <?php get_template_part('frontend/views/partials/image', null, [
                    'image' => $image,
                    'class' => 'carousel-image',
                    'sizes' => '(max-width: 768px) 70vw, 30vw',
                ]) ?>
            </button>
        <?php endforeach; ?>
    </div>
</section>
