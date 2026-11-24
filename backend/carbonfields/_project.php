<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

// -> Titelbild und Texte, die in der Projekte-Übersicht und auf der Startseite verwendet werden
Container::make('post_meta', 'Vorschau')
    ->where('post_type', '=', 'project')
    ->set_context('side')
    ->add_fields([
        Field::make('image', 'highlight_image', 'Titelbild')
            ->set_help_text('Ein Foto ohne Text. Wird in der Projekte-Übersicht, auf der Startseite und oben auf der Projektseite (Handy) gezeigt. Den Titel blendet die Website selbst ein.'),
        Field::make('file', 'teaser_video', 'Titelvideo (optional)')
            ->set_type(['video'])
            ->set_help_text('MP4 ohne Ton, 5–8 Sekunden als Schlaufe, ca. 1280 px breit, möglichst unter 3 MB. Läuft in der Projekte-Übersicht und auf der Startseite anstelle des Titelbilds; das Titelbild dient als Vorschau.'),
        Field::make('select', 'teaser_size', 'Priorität in der Projekte-Übersicht')
            ->set_options([
                'auto' => 'Normal',
                'wide' => 'Hoch – bekommt das breiteste Feld',
                'narrow' => 'Niedrig – eher schmal',
            ])
            ->set_help_text('Die Anordnung wird bei jedem Besuch neu gemischt. Die Priorität bestimmt, wer in seiner Reihe breiter wird.'),
        Field::make('textarea', 'highlight_text', 'Kurzbeschreibung Startseite')->set_rows(4),
        Field::make('select', 'highlight_format', 'Bildformat')
            ->set_options([
                '' => 'Flexibel',
                'portrait' => 'Hochformat',
                'landscape' => 'Querformat',
            ])
            ->set_help_text('Gilt für die Startseite und die Projekte-Übersicht. Flexibel = das Raster entscheidet.'),
    ]);

// -> Stammdaten des Projekts, werden neben der Einleitung (Baustein "Titel & Einleitung") angezeigt
Container::make('post_meta', 'Stammdaten')
    ->where('post_type', '=', 'project')
    ->add_fields([
        Field::make('text', 'fact_date', 'Datum')->set_help_text('z.B. «Mai 2026»')->set_width(33),
        Field::make('text', 'fact_client', 'Kunde')->set_width(33),
        Field::make('text', 'fact_service', 'Leistung')->set_help_text('Was du gemacht hast, z.B. «Konzept, Kamera, Schnitt»')->set_width(33),
        Field::make('text', 'fact_effort', 'Aufwand')->set_help_text('z.B. «6 Stunden» oder «5 Drehtage»')->set_width(50),
        Field::make('text', 'fact_inspiration', 'Inspiration')->set_width(50),
    ]);
