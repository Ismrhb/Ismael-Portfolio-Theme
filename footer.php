<?php get_template_part('frontend/views/layout/contact') ?>
<?php get_template_part('frontend/views/layout/lightbox') ?>
<?php if (carbon_get_theme_option('snow')) : ?>
    <?php // -> Saison: dezente Schneeflocken (main.js zeichnet sie, aus bei "Bewegung reduzieren") ?>
    <canvas class="snow" aria-hidden="true"></canvas>
<?php endif; ?>
<script src="<?php bloginfo('template_directory'); ?>/frontend/scripts/main.js?v=<?= wp_get_theme()->get('Version') ?>" type="module"></script>
<?php wp_footer(); ?>
</body>
</html>
