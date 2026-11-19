<?php
$data = $args['data'];
if (!$data['video_id']) {
    return;
}
?>
<section data-module="video">
    <div class="video-frame">
        <iframe
            src="https://www.youtube-nocookie.com/embed/<?= esc_attr($data['video_id']) ?>?rel=0"
            title="Video"
            loading="lazy"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen
        ></iframe>
    </div>
</section>
