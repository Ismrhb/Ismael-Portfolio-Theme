<?php
// -> Erwartet $args['media'] im Format von get_responsive_media()
$media = $args['media'] ?? false;
if (!$media) {
    return;
}
$desktop = $media['desktop'];
$mobile = $media['mobile'];
$alt = $args['alt'] ?? $desktop['alt'];
?>
<picture class="<?= esc_attr($args['class'] ?? 'image') ?>"<?= !empty($args['transition']) ? ' style="view-transition-name: ' . esc_attr($args['transition']) . '"' : '' ?>>
    <?php if ($mobile) : ?>
        <source media="(max-width: 768px)" srcset="<?= esc_attr($mobile['srcset'] ?: $mobile['src']) ?>" sizes="100vw">
    <?php endif; ?>
    <img
        src="<?= esc_url($desktop['src']) ?>"
        <?php if ($desktop['srcset']) : ?>srcset="<?= esc_attr($desktop['srcset']) ?>" sizes="<?= esc_attr($args['sizes'] ?? '100vw') ?>"<?php endif; ?>
        width="<?= (int) $desktop['width'] ?>"
        height="<?= (int) $desktop['height'] ?>"
        alt="<?= esc_attr($alt) ?>"
        <?= !empty($args['eager']) ? 'fetchpriority="high"' : 'loading="lazy"' ?>
    >
</picture>
