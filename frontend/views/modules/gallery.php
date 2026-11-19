<?php
$data = $args['data'];
?>
<?php // -> Bildreihen: gleiche Höhe pro Reihe, Breite nach Seitenverhältnis (--ratio) ?>
<section data-module="gallery" class="is-rows">
    <?php foreach ($data['rows'] as $row) : ?>
        <div class="gallery-row">
            <?php foreach ($row as $item) : ?>
                <?php if (!empty($item['text'])) : ?>
                    <div class="gallery-item gallery-text" style="--ratio: <?= esc_attr($item['ratio']) ?>">
                        <?= wpautop(wp_kses_post($item['text'])) ?>
                    </div>
                    <?php continue; ?>
                <?php endif; ?>
                <figure class="gallery-item" style="--ratio: <?= esc_attr($item['ratio']) ?>">
                    <?php get_template_part('frontend/views/partials/image', null, [
                        'image' => $item['image'],
                        'class' => 'gallery-image',
                        'sizes' => '(max-width: 768px) 100vw, ' . ($item['vw'] ?? 50) . 'vw',
                    ]) ?>
                </figure>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</section>
