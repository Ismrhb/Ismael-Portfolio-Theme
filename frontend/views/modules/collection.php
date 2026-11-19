<?php
$data = $args['data'];
?>
<section data-module="collection">
    <div class="collection-header">
        <h1 class="collection-title"><?= esc_html($data['title']) ?></h1>
        <?php if (count($data['categories']) > 1) : ?>
            <?php // -> Filter standardmässig zu; das Symbol rechts blendet die Kategorien ein und aus ?>
            <div class="collection-tools">
                <div class="collection-filter" id="projekte-filter" role="group" aria-label="Projekte filtern" hidden>
                    <button class="tag" type="button" data-filter="" aria-pressed="true">Alle</button>
                    <?php foreach ($data['categories'] as $category) : ?>
                        <button class="tag" type="button" data-filter="<?= esc_attr($category['slug']) ?>" aria-pressed="false">
                            <?= esc_html($category['name']) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                <button class="filter-toggle" type="button" aria-expanded="false" aria-controls="projekte-filter" aria-label="Filter einblenden">
                    <?php get_template_part('frontend/views/partials/icon', null, ['name' => 'filter']) ?>
                </button>
            </div>
        <?php endif; ?>
    </div>
    <div class="collection-grid" data-pending>
        <?php // -> Die ersten drei Bilder sind sofort sichtbar und laden darum ohne Verzögerung ?>
        <?php foreach ($data['projects'] as $index => $project) : ?>
            <?php get_template_part('frontend/views/teaser/project', null, ['project' => $project, 'eager' => $index < 3]) ?>
        <?php endforeach; ?>
    </div>
</section>
