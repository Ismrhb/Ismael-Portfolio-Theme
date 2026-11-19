<?php
$data = $args['data'];
if (!$data['image']) {
    return;
}
// -> Bildgrösse passend zur Breite des Bausteins
$sizes = [
    'full' => '100vw',
    'two_thirds' => '(max-width: 768px) 100vw, 66vw',
    'half' => '(max-width: 768px) 100vw, 50vw',
    'third' => '(max-width: 768px) 100vw, 33vw',
];
?>
<section data-module="image">
    <?php get_template_part('frontend/views/partials/image', null, [
        'image' => $data['image'],
        'class' => 'image-module-image',
        'sizes' => $sizes[$data['width'] ?? 'full'] ?? '100vw',
    ]) ?>
</section>
