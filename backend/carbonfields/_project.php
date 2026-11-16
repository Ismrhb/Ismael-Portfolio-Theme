<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

// -> Teaser-Bilder und Texte, die in der Sammlung und auf der Startseite verwendet werden
Container::make('post_meta', 'Vorschau')
    ->where('post_type', '=', 'project')
    ->set_context('side')
    ->add_fields([
        Field::make('image', 'teaser_image', 'Titelbild Sammlung')
            ->set_help_text('Fertig gestaltetes Bild mit Titel. Wird in der Sammlung und auf der Startseite (Mobile) angezeigt.')
            ->set_required(),
        Field::make('image', 'teaser_image_mobile', 'Titelbild Sammlung (Mobile)')
            ->set_help_text('Optional. Wird auf dem Handy anstelle des Titelbilds angezeigt.'),
        Field::make('select', 'teaser_size', 'Priorität in der Sammlung')
            ->set_options([
                'auto' => 'Normal',
                'wide' => 'Hoch – bekommt das breiteste Feld',
                'narrow' => 'Niedrig – eher schmal',
            ])
            ->set_help_text('Die Anordnung wird bei jedem Besuch neu gemischt. Die Priorität bestimmt, wer in seiner Reihe breiter wird.'),
        Field::make('image', 'highlight_image', 'Bild Startseite (ohne Text)')
            ->set_help_text('Optional. Wird auf der Startseite (Desktop) angezeigt. Ohne Bild wird das Titelbild verwendet.'),
        Field::make('textarea', 'highlight_text', 'Kurzbeschreibung Startseite')->set_rows(4),
    ]);

// -> Stammdaten des Projekts, werden im Baustein "Stammdaten" oder neben Videos angezeigt
Container::make('post_meta', 'Stammdaten')
    ->where('post_type', '=', 'project')
    ->add_fields([
        Field::make('text', 'fact_date', 'Datum')->set_width(25),
        Field::make('text', 'fact_client', 'Kunde')->set_width(25),
        Field::make('text', 'fact_effort', 'Aufwand')->set_width(25),
        Field::make('text', 'fact_inspiration', 'Inspiration')->set_width(25),
    ]);
