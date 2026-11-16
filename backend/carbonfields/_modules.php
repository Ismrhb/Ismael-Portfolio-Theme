<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

// -> Konfiguration der verschiedenen Module
Container::make('post_meta', 'Module')
    ->where('post_type', 'IN', ['page', 'project'])
    ->add_fields([
        Field::make('complex', 'modules', 'Module')
            // -> intro (Startseite: Begrüssung links, ausgewählte Projekte rechts)
            ->add_fields('intro', 'Startseite Intro & Projekte', [
                Field::make('text', 'greeting', 'Begrüssung')->set_default_value('Ciao,')->set_width(25),
                Field::make('text', 'intro', 'Einleitung')->set_default_value('ich bin')->set_width(25),
                Field::make('text', 'name', 'Name')->set_default_value('Ismael')->set_width(25),
                Field::make('text', 'subtitle', 'Untertitel')->set_default_value('Foto- und Videograf')->set_width(25),
                Field::make('textarea', 'tagline', 'Satz unter dem Namen')
                    ->set_rows(2)
                    ->set_help_text('Für wen und was du fotografierst, z.B. «Events, Portraits und Hochzeiten in Bern und Umgebung.»'),
                Field::make('image', 'portrait', 'Porträt')->set_required()->set_width(33),
                Field::make('image', 'portrait_mobile', 'Porträt (Mobile)')
                    ->set_help_text('Optional')->set_width(33),
                Field::make('text', 'portrait_hover', 'Text beim Hovern über das Porträt')
                    ->set_default_value('Let’s talk')->set_width(33),
                Field::make('association', 'projects', 'Projekte')
                    ->set_types([['type' => 'post', 'post_type' => 'project']])
                    ->set_help_text('Reihenfolge per Drag & Drop festlegen.'),
                Field::make('text', 'cta_title', 'Abschluss unter den Projekten')
                    ->set_default_value('Lust auf ein gemeinsames Projekt?')
                    ->set_help_text('Darunter erscheinen der Button «Kontakt» und ein kleiner Link zur Sammlung.'),
            ])
            // -> hero_image (grosses Bild mit Begrüssung und Zitat, z.B. "Über mich")
            ->add_fields('hero_image', 'Bild-Hero mit Zitat', [
                Field::make('image', 'image', 'Bild')->set_required()->set_width(50),
                Field::make('image', 'image_mobile', 'Bild (Mobile)')->set_help_text('Optional')->set_width(50),
                Field::make('text', 'greeting', 'Begrüssung')->set_default_value('Ciao,')->set_width(33),
                Field::make('text', 'intro', 'Einleitung')->set_default_value('ich bin')->set_width(33),
                Field::make('text', 'name', 'Name')->set_default_value('Ismael')->set_width(33),
                Field::make('textarea', 'quote', 'Zitat')->set_rows(3),
            ])
            // -> story (Text und Bild abwechselnd links/rechts)
            ->add_fields('story', 'Text & Bild abwechselnd', [
                Field::make('complex', 'items', 'Einträge')
                    ->add_fields([
                        Field::make('text', 'title', 'Titel')->set_width(50),
                        Field::make('image', 'image', 'Bild')->set_width(50),
                        Field::make('textarea', 'text', 'Text')->set_rows(4),
                    ])
                    ->set_header_template('<%- title %>'),
            ])
            // -> title (Seitentitel mit optionalem Lead, bei Projekten inkl. Kategorie)
            ->add_fields('title', 'Titel & Lead', [
                Field::make('text', 'title', 'Titel')
                    ->set_help_text('Leer lassen, um den Titel der Seite / des Projekts zu verwenden.'),
                Field::make('textarea', 'lead', 'Lead-Text')
                    ->set_rows(3)
                    ->set_help_text('Max. 2–3 Sätze. Längeren Text bitte im Baustein «Text» erfassen.'),
            ])
            // -> facts (Stammdaten des Projekts in einer Zeile)
            ->add_fields('facts', 'Stammdaten', [
                Field::make('html', 'facts_info')
                    ->set_html('<p>Zeigt die Stammdaten des Projekts (Datum, Kunde, Aufwand, Inspiration) in einer Zeile an.</p>'),
            ])
            // -> video (YouTube-Video im Querformat)
            ->add_fields('video', 'Video (YouTube)', [
                Field::make('text', 'url', 'YouTube-Link')->set_required()->set_width(70),
                Field::make('checkbox', 'show_facts', 'Stammdaten links daneben anzeigen')->set_width(30),
            ])
            // -> reels (Videos im Hochformat, nebeneinander)
            ->add_fields('reels', 'Reels (Hochformat)', [
                Field::make('complex', 'items', 'Reels')
                    ->add_fields([
                        Field::make('text', 'url', 'YouTube-Link (Shorts)')->set_width(50),
                        Field::make('file', 'file', 'oder Video-Datei (MP4)')
                            ->set_type(['video'])->set_width(50),
                    ])
                    ->set_max(3),
                Field::make('checkbox', 'show_facts', 'Stammdaten links daneben anzeigen'),
            ])
            // -> text (Fliesstext)
            ->add_fields('text', 'Text', [
                Field::make('rich_text', 'text', 'Text'),
            ])
            // -> gallery (Bildergalerie mit frei wählbaren Grössen)
            ->add_fields('gallery', 'Bildergalerie', [
                Field::make('complex', 'items', 'Bilder')
                    ->add_fields([
                        Field::make('image', 'image', 'Bild')->set_required()->set_width(40),
                        Field::make('select', 'size', 'Breite')
                            ->set_options([
                                'quarter' => '1/4',
                                'half' => '1/2',
                                'three-quarter' => '3/4',
                                'full' => 'Ganze Breite',
                            ])
                            ->set_default_value('half')
                            ->set_width(30),
                        Field::make('checkbox', 'tall', 'Doppelte Höhe')->set_width(30),
                    ])
                    ->set_layout('tabbed-horizontal'),
            ])
            // -> carousel (Bilder nebeneinander, das ausgewählte wird grösser)
            ->add_fields('carousel', 'Bild-Karussell', [
                Field::make('text', 'title', 'Titel')->set_default_value('Behind the scenes'),
                Field::make('media_gallery', 'images', 'Bilder')->set_type(['image']),
            ])
            // -> collection (alle Projekte mit Kategorie-Filter)
            ->add_fields('collection', 'Sammlung (alle Projekte)', [
                Field::make('text', 'title', 'Titel')->set_default_value('Sammlung'),
            ])
            // -> contact (Kontaktformular als Baustein)
            ->add_fields('contact', 'Kontaktformular', [
                Field::make('checkbox', 'mobile_only', 'Nur auf dem Handy anzeigen')
                    ->set_default_value(true)
                    ->set_help_text('Auf dem Desktop öffnet sich das Formular über den Kontakt-Button.'),
                Field::make('image', 'image', 'Bild neben dem Formular')
                    ->set_help_text('Optional, z.B. dein Portrait. Wird nur auf dem Desktop neben dem Formular gezeigt.')
                    ->set_width(50),
                Field::make('textarea', 'image_text', 'Text auf dem Bild')
                    ->set_rows(2)
                    ->set_help_text('z.B. «Ich freue mich auf deine Nachricht.»')
                    ->set_width(50),
                Field::make('text', 'kicker', 'Zeile über dem Titel')->set_help_text('z.B. «Schritt 01»')->set_width(50),
                Field::make('text', 'submit', 'Button-Text')->set_default_value('Senden')->set_width(50),
            ])
            // -> cta (Abschluss: Frage + Kontakt-Button + Link zur Sammlung)
            ->add_fields('cta', 'Abschluss mit Kontakt', [
                Field::make('text', 'title', 'Titel')->set_default_value('Lust auf ein gemeinsames Projekt?'),
                Field::make('checkbox', 'show_collection', 'Kleinen Link zur Sammlung darunter anzeigen'),
            ])
            // -> offer_intro (Angebot: Einstieg mit Button und Bildcollage)
            ->add_fields('offer_intro', 'Angebot: Einstieg', [
                Field::make('text', 'kicker', 'Zeile über dem Titel')->set_default_value('Foto- und Videograf aus Bern'),
                Field::make('textarea', 'title', 'Titel')->set_rows(2)->set_default_value('Bilder von Momenten, die bleiben sollen.'),
                Field::make('textarea', 'text', 'Satz zum Nutzen')->set_rows(2),
                Field::make('text', 'button', 'Button-Text')->set_default_value('Anfrage starten')->set_width(50),
                Field::make('text', 'process_link', 'Zweiter Button (springt zum Ablauf)')
                    ->set_default_value('So läuft’s ab')
                    ->set_help_text('Leer lassen, um ihn auszublenden.')
                    ->set_width(50),
                Field::make('text', 'note', 'Kleiner Hinweis unter den Buttons')
                    ->set_help_text('z.B. «Antwort innert 24 Stunden · Unverbindlich & kostenlos»'),
                Field::make('media_gallery', 'images', 'Bildcollage (3 Bilder)')->set_type(['image']),
            ])
            // -> offers (Angebot als Karten)
            ->add_fields('offers', 'Angebot: Karten', [
                Field::make('text', 'title', 'Titel')->set_default_value('Mein Angebot'),
                Field::make('text', 'subtitle', 'Untertitel')->set_default_value('Drei Schwerpunkte – jedes Projekt individuell offeriert.'),
                Field::make('complex', 'items', 'Angebote')
                    ->add_fields([
                        Field::make('text', 'title', 'Titel')->set_width(50),
                        Field::make('image', 'image', 'Bild')->set_width(50),
                        Field::make('textarea', 'text', 'Kurztext')->set_rows(2),
                        Field::make('textarea', 'services', 'Leistungen (eine pro Zeile)')->set_rows(3)->set_width(50),
                        Field::make('text', 'price', 'Preis')->set_help_text('z.B. «ab 450 CHF» – «ab» wird kursiv gesetzt.')->set_width(25),
                        Field::make('select', 'occasion', 'Anlass im Formular vorwählen')
                            ->set_options(['' => 'Keiner', 'Event' => 'Event', 'Portrait' => 'Portrait', 'Hochzeit' => 'Hochzeit', 'Anderes' => 'Anderes'])
                            ->set_width(25),
                    ])
                    ->set_max(3)
                    ->set_header_template('<%- title %>'),
            ])
            // -> process (Ablauf in Phasen, Schritte werden durchnummeriert)
            ->add_fields('process', 'Angebot: Ablauf', [
                Field::make('text', 'title', 'Titel')->set_default_value('So läuft’s ab'),
                Field::make('text', 'subtitle', 'Untertitel')->set_default_value('Von deiner Anfrage bis zur fertigen Galerie.'),
                Field::make('complex', 'phases', 'Phasen')
                    ->add_fields([
                        Field::make('text', 'title', 'Phase')->set_help_text('«Phase 1 ·» wird automatisch davorgesetzt.'),
                        Field::make('complex', 'steps', 'Schritte')
                            ->add_fields([
                                Field::make('text', 'title', 'Schritt')->set_width(50),
                                Field::make('text', 'addition', 'Zusatz')->set_help_text('optional, z.B. «wenn nötig»')->set_width(50),
                                Field::make('textarea', 'text', 'Beschreibung')->set_rows(2),
                            ])
                            ->set_header_template('<%- title %>'),
                    ])
                    ->set_header_template('<%- title %>'),
            ])
            // -> testimonials (Kundenstimmen)
            ->add_fields('testimonials', 'Kundenstimmen', [
                Field::make('text', 'title', 'Titel')->set_default_value('Das sagen Kundinnen und Kunden'),
                Field::make('complex', 'items', 'Stimmen')
                    ->add_fields([
                        Field::make('textarea', 'quote', 'Zitat')->set_rows(3),
                        Field::make('text', 'name', 'Name')->set_width(50),
                        Field::make('text', 'occasion', 'Anlass')->set_width(50),
                    ])
                    ->set_header_template('<%- name %>'),
            ])
            // -> faq (Fragen und Antworten als Akkordeon)
            ->add_fields('faq', 'Häufige Fragen', [
                Field::make('text', 'title', 'Titel')->set_default_value('Fragen?'),
                Field::make('textarea', 'text', 'Text neben dem Titel')
                    ->set_rows(2)
                    ->set_default_value('Die häufigsten Antworten – alles andere klären wir gerne persönlich.'),
                Field::make('complex', 'items', 'Fragen')
                    ->add_fields([
                        Field::make('text', 'question', 'Frage'),
                        Field::make('textarea', 'answer', 'Antwort')->set_rows(3),
                    ])
                    ->set_header_template('<%- question %>'),
            ])
            // -> SETTINGS
            ->set_layout('tabbed-vertical')
    ]);

// -> Funktion, um die Module und die entsprechenden Templates auf der Seite zu laden
function render_modules (): void {
    $modules = carbon_get_the_post_meta('modules');
    if (empty($modules)) {
        return;
    }
    foreach ($modules as $module) {
        // -> Modifiziere die Daten des Moduls, falls nötig, basierend auf dem Modultyp
        switch ($module['_type']) {
            case 'intro':
                $module['portrait'] = get_responsive_media($module['portrait'], $module['portrait_mobile'], 'm');
                $module['collection_url'] = get_collection_url();
                $module['projects'] = array_map(
                    fn ($project) => get_project_teaser_by_id($project['id']),
                    $module['projects']
                );
                break;
            case 'hero_image':
                $module['image'] = get_responsive_media($module['image'], $module['image_mobile'], 'xl');
                break;
            case 'story':
                foreach ($module['items'] as $i => $item) {
                    $module['items'][$i]['image'] = $item['image'] ? get_media_by_id($item['image'], 'l') : false;
                }
                break;
            case 'title':
                $module['title'] = $module['title'] ?: get_the_title();
                $module['categories'] = get_post_type() === 'project' ? get_project_categories(get_the_ID()) : [];
                // -> Projekte: Bild der Startseite (sonst Titelbild) wird mobil zwischen Titel und Einleitung gezeigt
                $module['image'] = get_post_type() === 'project'
                    ? get_media_by_id(carbon_get_post_meta(get_the_ID(), 'highlight_image') ?: carbon_get_post_meta(get_the_ID(), 'teaser_image'), 'l')
                    : false;
                break;
            case 'facts':
                $module['facts'] = get_project_facts(get_the_ID());
                break;
            case 'video':
                $module['video_id'] = get_youtube_id($module['url']);
                $module['facts'] = $module['show_facts'] ? get_project_facts(get_the_ID()) : [];
                break;
            case 'reels':
                foreach ($module['items'] as $i => $item) {
                    $module['items'][$i]['video_id'] = get_youtube_id($item['url']);
                    $module['items'][$i]['file'] = $item['file'] ? wp_get_attachment_url($item['file']) : false;
                }
                $module['facts'] = $module['show_facts'] ? get_project_facts(get_the_ID()) : [];
                break;
            case 'gallery':
                foreach ($module['items'] as $i => $item) {
                    $module['items'][$i]['image'] = get_media_by_id($item['image'], 'l');
                }
                break;
            case 'carousel':
                $module['images'] = array_map(fn ($id) => get_media_by_id($id, 'm'), $module['images']);
                break;
            case 'collection':
                $module['projects'] = get_projects();
                $module['categories'] = get_used_project_categories();
                break;
            case 'cta':
                $module['collection_url'] = $module['show_collection'] ? get_collection_url() : '';
                break;
            case 'offer_intro':
                $module['images'] = array_map(fn ($id) => get_media_by_id($id, 'l'), array_slice($module['images'], 0, 3));
                break;
            case 'offers':
                foreach ($module['items'] as $i => $item) {
                    $module['items'][$i]['image'] = get_media_by_id($item['image'], 'm');
                    $module['items'][$i]['services'] = array_filter(array_map('trim', explode("\n", $item['services'])));
                    // -> "ab 450 CHF" in "ab" und den Preis aufteilen, damit "ab" kursiv gesetzt werden kann
                    preg_match('/^(ab)\s+(.+)$/iu', trim($item['price']), $price);
                    $module['items'][$i]['price_prefix'] = $price[1] ?? '';
                    $module['items'][$i]['price'] = $price[2] ?? trim($item['price']);
                }
                break;
            case 'contact':
                $module['image'] = get_media_by_id($module['image'] ?? 0, 'l');
                $module['phone'] = get_globals_contact();
                break;
        }
        // -> Laden des Templates basierend auf dem Modultyp und weitergeben der Daten
        get_template_part(
            'frontend/views/modules/' . $module['_type'],
            null,
            [
                'data' => $module
            ]
        );
    }
}
