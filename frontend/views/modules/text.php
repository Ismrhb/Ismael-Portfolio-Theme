<?php
$data = $args['data'];
?>
<section data-module="text">
    <div class="text-content"><?= wpautop(wp_kses_post($data['text'])) ?></div>
</section>
