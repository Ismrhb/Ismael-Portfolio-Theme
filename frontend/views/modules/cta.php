<?php
$data = $args['data'];
?>
<section data-module="cta">
    <?php get_template_part('frontend/views/partials/cta', null, [
        'title' => $data['title'],
        'collection_url' => $data['collection_url'],
    ]) ?>
</section>
