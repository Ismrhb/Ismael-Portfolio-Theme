<?php
$data = $args['data'];
?>
<section data-module="title">
    <?php if ($data['categories']) : ?>
        <ul class="tags">
            <?php foreach ($data['categories'] as $category) : ?>
                <li class="tag"><?= esc_html($category['name']) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <h1 class="title"><?= esc_html($data['title']) ?></h1>
    <?php get_template_part('frontend/views/partials/image', null, [
        'image' => $data['image'] ?? false,
        'class' => 'title-image',
        'transition' => 'project-image-' . get_the_ID(),
        'sizes' => '100vw',
        'eager' => true,
    ]) ?>
    <?php if ($data['lead']) : ?>
        <p class="lead"><?= nl2br(esc_html($data['lead'])) ?></p>
    <?php endif; ?>
</section>
