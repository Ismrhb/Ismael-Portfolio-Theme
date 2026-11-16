<?php // -> M15: Bilder der Projektseite gross ansehen (mobil), Inhalt wird per JavaScript befüllt ?>
<dialog data-layout="lightbox" aria-label="Bildansicht">
    <button class="lightbox-close" type="button" data-lightbox-close aria-label="Schliessen">
        <?php get_template_part('frontend/views/partials/icon', null, ['name' => 'close']) ?>
    </button>
    <div class="lightbox-track" tabindex="0"></div>
</dialog>
