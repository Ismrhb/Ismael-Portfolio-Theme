<?php
$data = $args['data'];
// -> Nur echte Stimmen mit Text anzeigen
$items = array_filter($data['items'] ?? [], fn ($item) => trim($item['quote']) !== '');
if (!$items) {
    return;
}
?>
<section data-module="testimonials">
    <?php if ($data['title']) : ?>
        <h2 class="section-title"><?= esc_html($data['title']) ?></h2>
    <?php endif; ?>
    <div class="testimonials-list">
        <?php foreach ($items as $item) : ?>
            <figure class="testimonial">
                <blockquote class="testimonial-quote"><?= nl2br(esc_html($item['quote'])) ?></blockquote>
                <figcaption class="testimonial-author">
                    <?= esc_html($item['name']) ?><?php if ($item['occasion']) : ?>, <span class="testimonial-occasion"><?= esc_html($item['occasion']) ?></span><?php endif; ?>
                </figcaption>
            </figure>
        <?php endforeach; ?>
    </div>
</section>
