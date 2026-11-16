<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

// -> Beschreibung und Vorschaubild pro Seite / Projekt (Google, WhatsApp, Instagram usw.)
Container::make('post_meta', 'SEO & Teilen')
    ->where('post_type', 'IN', ['page', 'project'])
    ->set_context('side')
    ->set_priority('low')
    ->add_fields([
        Field::make('textarea', 'seo_description', 'Beschreibung für Google')
            ->set_rows(4)
            ->set_attribute('maxLength', 160)
            ->set_help_text('140–160 Zeichen. Leer = Standard-Beschreibung aus «Globales».'),
        Field::make('image', 'seo_image', 'Vorschaubild beim Teilen')
            ->set_help_text('Optional, ideal 1200 × 630 px.'),
    ]);
