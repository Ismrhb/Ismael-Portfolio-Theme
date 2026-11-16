<?php
$data = $args['data'];
if (empty($data['items'])) {
    return;
}
?>
<section data-module="faq">
    <div class="faq-header">
        <?php if ($data['title']) : ?>
            <h2 class="section-title"><?= esc_html($data['title']) ?></h2>
        <?php endif; ?>
        <?php if ($data['text']) : ?>
            <p class="faq-intro"><?= nl2br(esc_html($data['text'])) ?></p>
        <?php endif; ?>
    </div>
    <div class="faq-list">
        <?php foreach ($data['items'] as $i => $item) : ?>
            <details class="faq-item" name="faq" <?= $i === 0 ? 'open' : '' ?>>
                <summary class="faq-question"><?= esc_html($item['question']) ?></summary>
                <div class="faq-answer"><?= wpautop(esc_html($item['answer'])) ?></div>
            </details>
        <?php endforeach; ?>
    </div>
</section>
