<?php
$data = $args['data'];
?>
<section data-module="collection">
    <div class="collection-header">
        <h1 class="collection-title"><?= esc_html($data['title']) ?></h1>
        <?php if (count($data['categories']) > 1) : ?>
            <div class="collection-filter" role="group" aria-label="Projekte filtern">
                <button class="tag" type="button" data-filter="" aria-pressed="true">Alle</button>
                <?php foreach ($data['categories'] as $category) : ?>
                    <button class="tag" type="button" data-filter="<?= esc_attr($category['slug']) ?>" aria-pressed="false">
                        <?= esc_html($category['name']) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="collection-grid">
        <?php foreach ($data['projects'] as $project) : ?>
            <?php get_template_part('frontend/views/teaser/project', null, ['project' => $project]) ?>
        <?php endforeach; ?>
    </div>
</section>
