<?php // -> Bilder der Projektseite gross ansehen und durchklicken (Desktop: Zurück/Weiter, Pfeiltasten; Handy: wischen) ?>
<dialog data-layout="lightbox" aria-label="Bildansicht">
    <button class="lightbox-close" type="button" data-lightbox-close aria-label="Schliessen">
        <?php get_template_part('frontend/views/partials/icon', null, ['name' => 'close']) ?>
    </button>
    <div class="lightbox-track" tabindex="0"></div>
    <div class="lightbox-nav">
        <button class="lightbox-step" type="button" data-lightbox-step="-1">Zurück</button>
        <span class="lightbox-count" aria-live="polite"></span>
        <button class="lightbox-step" type="button" data-lightbox-step="1">Weiter</button>
    </div>
</dialog>
