<?php
$data = $args['data'];
?>
<section data-module="gallery">
    <?php foreach ($data['items'] as $item) : ?>
        <figure class="gallery-item" data-size="<?= esc_attr($item['size']) ?>" <?= $item['tall'] ? 'data-tall' : '' ?>>
            <?php get_template_part('frontend/views/partials/image', null, [
                'image' => $item['image'],
                'class' => 'gallery-image',
                'sizes' => '(max-width: 768px) 100vw, 50vw',
            ]) ?>
        </figure>
    <?php endforeach; ?>
</section>
