<dialog data-layout="contact" aria-label="Kontaktformular">
    <button class="contact-close" type="button" data-contact-close aria-label="Schliessen">
        <?php get_template_part('frontend/views/partials/icon', null, ['name' => 'close']) ?>
    </button>
    <?php get_template_part('frontend/views/partials/contact-form', null, ['id' => 'overlay']) ?>
</dialog>
