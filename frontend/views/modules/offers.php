<?php
$data = $args['data'];
if (empty($data['items'])) {
    return;
}
?>
<section data-module="offers" id="angebot">
    <div class="section-header">
        <?php if ($data['title']) : ?>
            <h2 class="section-title"><?= esc_html($data['title']) ?></h2>
        <?php endif; ?>
        <?php if ($data['subtitle']) : ?>
            <p class="section-subtitle"><?= esc_html($data['subtitle']) ?></p>
        <?php endif; ?>
    </div>
    <div class="offers-list">
        <?php foreach ($data['items'] as $item) : ?>
            <article class="offer">
                <?php get_template_part('frontend/views/partials/image', null, [
                    'image' => $item['image'],
                    'class' => 'offer-image',
                    'sizes' => '(max-width: 768px) 100vw, 33vw',
                ]) ?>
                <div class="offer-body">
                    <h3 class="offer-title"><?= esc_html($item['title']) ?></h3>
                    <?php if ($item['text']) : ?>
                        <p class="offer-text"><?= esc_html($item['text']) ?></p>
                    <?php endif; ?>
                    <?php if ($item['services']) : ?>
                        <ul class="offer-services">
                            <?php foreach ($item['services'] as $service) : ?>
                                <li><?= esc_html($service) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <div class="offer-footer">
                        <?php if ($item['price']) : ?>
                            <p class="offer-price">
                                <?php if ($item['price_prefix']) : ?><em><?= esc_html($item['price_prefix']) ?></em><?php endif; ?>
                                <?= esc_html($item['price']) ?>
                            </p>
                        <?php endif; ?>
                        <button class="offer-link" type="button" data-contact-open <?= $item['occasion'] ? 'data-occasion="' . esc_attr($item['occasion']) . '"' : '' ?>>
                            Anfragen
                        </button>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
