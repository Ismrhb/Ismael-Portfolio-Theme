<?php
$data = $args['data'];
// -> Ohne Einträge keine leere Sektion ausgeben
if (empty($data['items'])) {
    return;
}
?>
<section data-module="story">
    <?php foreach ($data['items'] as $item) : ?>
        <div class="story-item">
            <?php if ($item['title']) : ?>
                <h2 class="story-title"><?= esc_html($item['title']) ?></h2>
            <?php endif; ?>
            <?php get_template_part('frontend/views/partials/image', null, [
                'image' => $item['image'],
                'class' => 'story-image',
                'sizes' => '(max-width: 768px) 100vw, 50vw',
            ]) ?>
            <p class="story-text"><?= nl2br(esc_html($item['text'])) ?></p>
        </div>
    <?php endforeach; ?>
</section>
