<?php
$data = $args['data'];
$has_intro = !empty($data['subtitle']) || !empty($data['lead']) || !empty($data['facts']);
?>
<section data-module="title">
    <h1 class="title"><?= esc_html($data['title']) ?></h1>
    <?php if ($data['categories']) : ?>
        <ul class="tags">
            <?php foreach ($data['categories'] as $category) : ?>
                <li class="tag"><?= esc_html($category['name']) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <?php get_template_part('frontend/views/partials/image', null, [
        'image' => $data['image'] ?? false,
        'class' => 'title-image',
        'transition' => 'project-image',
        'sizes' => '100vw',
        'eager' => true,
    ]) ?>
    <?php if ($has_intro) : ?>
        <?php // -> Untertitel und Einleitung links, Stammdaten rechts (Projekte) ?>
        <div class="title-intro <?= !empty($data['facts']) ? 'has-facts' : '' ?>">
            <div class="title-text">
                <?php if (!empty($data['subtitle'])) : ?>
                    <p class="subtitle"><?= esc_html($data['subtitle']) ?></p>
                <?php endif; ?>
                <?php if ($data['lead']) : ?>
                    <p class="lead"><?= nl2br(esc_html($data['lead'])) ?></p>
                <?php endif; ?>
            </div>
            <?php if (!empty($data['facts'])) : ?>
                <dl class="facts-table">
                    <?php foreach ($data['facts'] as $label => $value) : ?>
                        <dt><?= esc_html($label) ?></dt>
                        <dd><?= esc_html($value) ?></dd>
                    <?php endforeach; ?>
                </dl>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>
