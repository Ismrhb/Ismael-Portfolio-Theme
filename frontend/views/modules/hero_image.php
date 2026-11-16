<?php
$data = $args['data'];
?>
<section data-module="hero-image">
    <?php get_template_part('frontend/views/partials/picture', null, [
        'media' => $data['image'],
        'class' => 'hero-image-picture',
        'eager' => true,
    ]) ?>
    <h1 class="hero-image-heading">
        <span class="greeting"><?= esc_html($data['greeting']) ?></span>
        <span class="intro"><?= esc_html($data['intro']) ?></span>
        <span class="name"><?= esc_html($data['name']) ?></span>
    </h1>
    <?php if ($data['quote']) : ?>
        <blockquote class="hero-image-quote"><?= nl2br(esc_html($data['quote'])) ?></blockquote>
    <?php endif; ?>
</section>
