<?php
$data = $args['data'];
if (empty($data['phases'])) {
    return;
}
$step = 0;
?>
<section data-module="process" id="ablauf">
    <div class="process-inner">
        <div class="section-header">
            <?php if ($data['title']) : ?>
                <h2 class="section-title"><?= esc_html($data['title']) ?></h2>
            <?php endif; ?>
            <?php if ($data['subtitle']) : ?>
                <p class="section-subtitle"><?= esc_html($data['subtitle']) ?></p>
            <?php endif; ?>
        </div>
        <div class="process-phases" style="--columns: <?= min(count($data['phases']), 4) ?>">
            <?php foreach ($data['phases'] as $i => $phase) : ?>
                <div class="process-phase">
                    <h3 class="process-phase-title">Phase <?= $i + 1 ?> · <?= esc_html($phase['title']) ?></h3>
                    <ol class="process-steps" start="<?= $step + 1 ?>">
                        <?php foreach ($phase['steps'] as $item) : ?>
                            <?php $step++; ?>
                            <li class="process-step">
                                <span class="process-step-number" aria-hidden="true"><?= sprintf('%02d', $step) ?></span>
                                <p class="process-step-title">
                                    <?= esc_html($item['title']) ?>
                                    <?php if ($item['addition']) : ?><em class="process-step-addition"><?= esc_html($item['addition']) ?></em><?php endif; ?>
                                </p>
                                <?php if ($item['text']) : ?>
                                    <p class="process-step-text"><?= esc_html($item['text']) ?></p>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
