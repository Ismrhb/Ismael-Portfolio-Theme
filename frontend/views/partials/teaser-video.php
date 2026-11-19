<?php
// -> Titelvideo über dem Titelbild: stumm, in Schlaufe, startet erst wenn sichtbar (main.js)
$project = $args['project'];
if (empty($project['video'])) {
    return;
}
$poster = $project['highlight_image']['src'] ?? '';
?>
<video
    class="teaser-video <?= esc_attr($args['class'] ?? '') ?>"
    muted
    loop
    playsinline
    preload="none"
    aria-hidden="true"
    <?= $poster ? 'poster="' . esc_url($poster) . '"' : '' ?>
    data-teaser-video
>
    <source src="<?= esc_url($project['video']) ?>" type="video/mp4">
</video>
