<?php
$data = $args['data'];
if (!$data['facts']) {
    return;
}
?>
<section data-module="facts">
    <?php get_template_part('frontend/views/partials/facts', null, ['facts' => $data['facts'], 'class' => 'facts-row']) ?>
</section>
