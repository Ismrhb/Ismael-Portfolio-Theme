<?php
// -> Erwartet $args['image'] im Format von get_media_by_id()
$image = $args['image'] ?? false;
if (!$image) {
    return;
}
?>
<img
    class="<?= esc_attr($args['class'] ?? 'image') ?>"
    <?= !empty($args['transition']) ? 'style="view-transition-name: ' . esc_attr($args['transition']) . '"' : '' ?>
    src="<?= esc_url($image['src']) ?>"
    <?php if (!empty($image['srcset'])) : ?>srcset="<?= esc_attr($image['srcset']) ?>" sizes="<?= esc_attr($args['sizes'] ?? '100vw') ?>"<?php endif; ?>
    width="<?= (int) ($image['width'] ?? 0) ?>"
    height="<?= (int) ($image['height'] ?? 0) ?>"
    alt="<?= esc_attr($image['alt']) ?>"
    <?= !empty($args['eager']) ? '' : 'loading="lazy"' ?>
>
