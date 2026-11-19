<?php
$data = $args['data'];
?>
<section data-module="text" class="<?= !empty($data['is_long']) ? 'is-long' : '' ?>">
    <div class="text-content"><?= wpautop(wp_kses_post($data['text'])) ?></div>
</section>
