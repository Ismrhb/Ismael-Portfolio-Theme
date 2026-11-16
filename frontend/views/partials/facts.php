<?php
$facts = $args['facts'] ?? [];
if (!$facts) {
    return;
}
?>
<dl class="facts <?= esc_attr($args['class'] ?? '') ?>">
    <?php foreach ($facts as $label => $value) : ?>
        <div class="fact">
            <dt class="fact-label"><?= esc_html($label) ?></dt>
            <dd class="fact-value"><?= esc_html($value) ?></dd>
        </div>
    <?php endforeach; ?>
</dl>
