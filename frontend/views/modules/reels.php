<?php
$data = $args['data'];
?>
<section data-module="reels" class="<?= $data['facts'] ? 'has-facts' : '' ?>">
    <?php get_template_part('frontend/views/partials/facts', null, ['facts' => $data['facts'], 'class' => 'facts-aside']) ?>
    <div class="reels-list">
        <?php foreach ($data['items'] as $item) : ?>
            <div class="reel">
                <?php if ($item['video_id']) : ?>
                    <iframe
                        src="https://www.youtube-nocookie.com/embed/<?= esc_attr($item['video_id']) ?>?rel=0"
                        title="Reel"
                        loading="lazy"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen
                    ></iframe>
                <?php elseif ($item['file']) : ?>
                    <video src="<?= esc_url($item['file']) ?>" controls playsinline preload="metadata"></video>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>
