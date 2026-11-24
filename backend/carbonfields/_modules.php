<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

// -> Breite eines Bausteins: aufeinanderfolgende schmale Bausteine stehen in einer Reihe nebeneinander (Handy: untereinander)
function module_width_field(): Carbon_Fields\Field\Field
{
    return Field::make('select', 'width', 'Breite')
        ->set_options([
            'full' => 'Ganze Breite',
            'two_thirds' => 'Zwei Drittel',
            'half' => 'Hälfte',
            'third' => 'Ein Drittel',
        ])
        ->set_default_value('full')
        ->set_help_text('Schmale Bausteine direkt hintereinander stehen nebeneinander, bis die Reihe voll ist.');
}

// -> Konfiguration der verschiedenen Module
Container::make('post_meta', 'Module')
    ->where('post_type', 'IN', ['page', 'project'])
    ->add_fields([
        Field::make('complex', 'modules', 'Module')
            // -> title (Seitentitel mit optionalem Lead, bei Projekten inkl. Kategorie)
            ->add_fields('title', 'Inhalt · Titel & Einleitung', [
                Field::make('text', 'title', 'Titel')
                    ->set_help_text('Leer lassen, um den Titel der Seite / des Projekts zu verwenden.'),
                Field::make('text', 'subtitle', 'Untertitel')
                    ->set_help_text('Optional, ein kurzer Satz, z.B. «100 Jahre Verner Panton». Steht bei Projekten über der Einleitung.'),
                Field::make('textarea', 'lead', 'Einleitung')
                    ->set_rows(3)
                    ->set_help_text('Max. 3–4 Sätze. Bei Projekten stehen die Stammdaten rechts daneben. Längeren Text bitte im Baustein «Text» erfassen.'),
            ])
            // -> text (Fliesstext)
            ->add_fields('text', 'Inhalt · Text', [
                Field::make('rich_text', 'text', 'Text'),
                module_width_field(),
            ])
            // -> image (einzelnes Bild, z.B. neben einem Text)
            ->add_fields('image', 'Inhalt · Bild', [
                Field::make('image', 'image', 'Bild')->set_required(),
                module_width_field(),
            ])
            // -> gallery (Bildergalerie: mehrere Bilder auf einmal, Anordnung automatisch nach Hoch-/Querformat)
            ->add_fields('gallery', 'Inhalt · Bildergalerie', [
                Field::make('media_gallery', 'images', 'Bilder')
                    ->set_type(['image'])
                    ->set_help_text('Mehrere Bilder auf einmal auswählen oder hochladen (Cmd bzw. Ctrl gedrückt halten oder Dateien ins Fenster ziehen). Reihenfolge per Ziehen ändern. Hochformate werden schmal und hoch, Querformate breit angeordnet.'),
            ])
            // -> carousel (Bilder nebeneinander, das ausgewählte wird grösser)
            ->add_fields('carousel', 'Inhalt · Bild-Karussell', [
                Field::make('text', 'title', 'Titel')->set_default_value('Behind the scenes'),
                Field::make('media_gallery', 'images', 'Bilder')->set_type(['image']),
            ])
            // -> video (YouTube-Video im Querformat)
            ->add_fields('video', 'Inhalt · Video (YouTube)', [
                Field::make('text', 'url', 'YouTube-Link')->set_required(),
                module_width_field(),
            ])
            // -> reels (Videos im Hochformat, nebeneinander)
            ->add_fields('reels', 'Inhalt · Reels (Hochformat)', [
                Field::make('complex', 'items', 'Reels')
                    ->add_fields([
                        Field::make('text', 'url', 'YouTube-Link (Shorts)')->set_width(50),
                        Field::make('file', 'file', 'oder Video-Datei (MP4)')
                            ->set_type(['video'])->set_width(50),
                    ])
                    ->set_max(3),
                module_width_field(),
            ])
            // -> spacer (unsichtbarer Leerraum)
            ->add_fields('spacer', 'Inhalt · Leerraum', [
                Field::make('html', 'spacer_info')
                    ->set_html('<p>Unsichtbar. Über die ganze Breite: etwas mehr Abstand zwischen zwei Bausteinen. Als Hälfte oder Drittel: hält den Platz neben einem anderen schmalen Baustein frei.</p>'),
                module_width_field(),
            ])
            // -> facts (Stammdaten des Projekts in einer Zeile)
            ->add_fields('facts', 'Inhalt · Stammdaten (einzeln)', [
                Field::make('html', 'facts_info')
                    ->set_html('<p>Zeigt die Stammdaten des Projekts (Datum, Kunde, Aufwand, Inspiration) in einer Zeile an.</p>'),
            ])
            // -> intro (Startseite: Begrüssung links, ausgewählte Projekte rechts)
            ->add_fields('intro', 'Seite · Startseite (Intro & Projekte)', [
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
                Field::make('text', 'portrait_hover', 'Text auf dem Porträt')
                    ->set_default_value('Let’s talk')->set_width(33)
                    ->set_help_text('Nur sichtbar, wenn unter Globales → Kontaktformular keine wechselnden Wörter eingetragen sind.'),
                Field::make('association', 'projects', 'Projekte')
                    ->set_types([['type' => 'post', 'post_type' => 'project']])
                    ->set_help_text('Reihenfolge per Drag & Drop festlegen.'),
                Field::make('text', 'cta_title', 'Abschluss unter den Projekten')
                    ->set_default_value('Lust auf ein gemeinsames Projekt?')
                    ->set_help_text('Darunter erscheinen der Button «Kontakt» und ein kleiner Link zur Projekte-Übersicht.'),
            ])
            // -> hero_image (grosses Bild mit Begrüssung und Zitat, z.B. "Über mich")
            ->add_fields('hero_image', 'Seite · Grosses Bild mit Zitat', [
                Field::make('image', 'image', 'Bild')->set_required()->set_width(50),
                Field::make('image', 'image_mobile', 'Bild (Mobile)')->set_help_text('Optional')->set_width(50),
                Field::make('text', 'greeting', 'Begrüssung')->set_default_value('Ciao,')->set_width(33),
                Field::make('text', 'intro', 'Einleitung')->set_default_value('ich bin')->set_width(33),
                Field::make('text', 'name', 'Name')->set_default_value('Ismael')->set_width(33),
                Field::make('textarea', 'quote', 'Zitat')->set_rows(3),
            ])
            // -> story (Text und Bild abwechselnd links/rechts)
            ->add_fields('story', 'Seite · Text & Bild abwechselnd', [
                Field::make('complex', 'items', 'Einträge')
                    ->add_fields([
                        Field::make('text', 'title', 'Titel')->set_width(50),
                        Field::make('image', 'image', 'Bild')->set_width(50),
                        Field::make('textarea', 'text', 'Text')->set_rows(4),
                    ])
                    ->set_header_template('<%- title %>'),
            ])
            // -> collection (alle Projekte mit Kategorie-Filter)
            ->add_fields('collection', 'Seite · Projekte-Übersicht mit Filter', [
                Field::make('text', 'title', 'Titel')->set_default_value('Projekte'),
            ])
            // -> contact (Kontaktformular als Baustein)
            ->add_fields('contact', 'Seite · Kontaktformular', [
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
            // -> cta (Abschluss: Frage + Kontakt-Button + Link zur Projekte-Übersicht)
            ->add_fields('cta', 'Seite · Abschluss mit Kontakt', [
                Field::make('text', 'title', 'Titel')->set_default_value('Lust auf ein gemeinsames Projekt?'),
                Field::make('checkbox', 'show_collection', 'Kleinen Link zu den Projekten darunter anzeigen'),
            ])
            // -> offer_intro (Angebot: Einstieg mit Kontakt-Button, Sprung zu den Angebotskarten und Bildcollage)
            ->add_fields('offer_intro', 'Angebot · Einstieg', [
                Field::make('text', 'kicker', 'Zeile über dem Titel')->set_default_value('Foto- und Videograf aus Bern'),
                Field::make('textarea', 'title', 'Titel')->set_rows(2)->set_default_value('Bilder von Momenten, die bleiben sollen.'),
                Field::make('textarea', 'text', 'Satz zum Nutzen')->set_rows(2),
                Field::make('text', 'button', 'Button-Text')->set_default_value('Anfrage starten')->set_width(50),
                Field::make('text', 'process_link', 'Zweiter Button (springt zu den Angebotskarten)')
                    ->set_default_value('Zum Angebot')
                    ->set_help_text('Leer lassen, um ihn auszublenden.')
                    ->set_width(50),
                Field::make('text', 'note', 'Kleiner Hinweis unter den Buttons')
                    ->set_help_text('z.B. «Antwort innert 24 Stunden · Unverbindlich & kostenlos»'),
                Field::make('media_gallery', 'images', 'Bilder (3 Stück)')
                    ->set_type(['image'])
                    ->set_help_text('Desktop: Collage neben dem Titel (das erste Bild gross). Handy: kleine Reihe unter den Buttons.'),
            ])
            // -> offers (Angebot als Karten)
            ->add_fields('offers', 'Angebot · Karten', [
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
            ->add_fields('process', 'Angebot · Ablauf', [
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
            ->add_fields('testimonials', 'Angebot · Kundenstimmen', [
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
            ->add_fields('faq', 'Angebot · Häufige Fragen', [
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

// -> Prüft, ob die aktuelle Seite einen bestimmten Baustein enthält
function page_has_module(string $slug): bool
{
    $modules = carbon_get_the_post_meta('modules') ?: [];
    return in_array($slug, array_column($modules, '_type'), true);
}

// -> Funktion, um die Module und die entsprechenden Templates auf der Seite zu laden
function render_modules (): void {
    $modules = carbon_get_the_post_meta('modules');
    if (empty($modules)) {
        return;
    }
    // -> Kurzer Text direkt vor einer Bildergalerie wird Teil der ersten Bildreihe (Bilder rund um den Text)
    foreach ($modules as $i => $module) {
        $next = $modules[$i + 1] ?? null;
        if ($module['_type'] === 'text' && $next && $next['_type'] === 'gallery' && !empty($next['images'])
            && ($module['width'] ?? 'full') === 'full'
            && mb_strlen(wp_strip_all_tags($module['text'] ?? '')) <= 600) {
            $modules[$i + 1]['aside_text'] = $module['text'];
            $modules[$i]['_skip'] = true;
        }
    }
    // -> Zeigt der Titel-Baustein die Stammdaten, werden sie nicht ein zweites Mal ausgegeben
    $facts_shown = false;
    // -> Breiten im 12er-Raster
    $spans = ['full' => 12, 'two_thirds' => 8, 'half' => 6, 'third' => 4];
    $row_open = false;
    $row_sum = 0;
    $story_count = 0;
    foreach ($modules as $module) {
        if (!empty($module['_skip'])) {
            continue;
        }
        // -> Modifiziere die Daten des Moduls, falls nötig, basierend auf dem Modultyp
        switch ($module['_type']) {
            case 'intro':
                $module['portrait'] = get_responsive_media($module['portrait'], $module['portrait_mobile'], 'm');
                $module['collection_url'] = get_collection_url();
                // -> Direkt zu den Foto- oder Videoprojekten (Projekte-Übersicht mit gesetztem Filter), nur wenn es solche gibt
                $module['category_links'] = [];
                if ($module['collection_url']) {
                    $used = array_column(get_used_project_categories(), 'slug');
                    foreach (['foto' => 'Fotos', 'video' => 'Videos'] as $slug => $label) {
                        if (in_array($slug, $used, true)) {
                            $module['category_links'][] = [
                                'label' => $label,
                                'url' => add_query_arg('kategorie', $slug, $module['collection_url']),
                            ];
                        }
                    }
                }
                $module['projects'] = array_map(
                    fn ($project) => get_project_teaser_by_id($project['id']),
                    $module['projects']
                );
                break;
            case 'hero_image':
                $module['image'] = get_responsive_media($module['image'], $module['image_mobile'], 'xl');
                break;
            case 'story':
                // -> Links/rechts wechselt über alle Geschichte-Bausteine der Seite hinweg
                foreach ($module['items'] as $i => $item) {
                    $module['items'][$i]['image'] = $item['image'] ? get_media_by_id($item['image'], 'l') : false;
                    $module['items'][$i]['flipped'] = $story_count % 2 === 1;
                    $story_count++;
                }
                break;
            case 'title':
                $module['title'] = $module['title'] ?: get_the_title();
                $module['categories'] = get_post_type() === 'project' ? get_project_categories(get_the_ID()) : [];
                $module['facts'] = get_post_type() === 'project' ? get_project_facts(get_the_ID()) : [];
                $facts_shown = $facts_shown || !empty($module['facts']);
                break;
            case 'facts':
                if ($facts_shown) {
                    continue 2;
                }
                $module['facts'] = get_project_facts(get_the_ID());
                break;
            case 'video':
                $module['video_id'] = get_youtube_id($module['url']);
                break;
            case 'reels':
                foreach ($module['items'] as $i => $item) {
                    $module['items'][$i]['video_id'] = get_youtube_id($item['url']);
                    $module['items'][$i]['file'] = $item['file'] ? wp_get_attachment_url($item['file']) : false;
                }
                break;
            case 'text':
                // -> Zweispaltig nur über die ganze Breite
                $module['is_long'] = ($module['width'] ?? 'full') === 'full'
                    && mb_strlen(wp_strip_all_tags($module['text'] ?? '')) > 600;
                break;
            case 'image':
                $module['image'] = get_media_by_id($module['image'] ?? 0, 'l');
                break;
            case 'gallery':
                if (empty($module['images'])) {
                    continue 2;
                }
                // -> Bildreihen: Bilder einer Reihe sind gleich hoch, die Breite ergibt sich aus dem Format
                // (kein Zuschnitt, keine Lücken). Eine Reihe ist voll, wenn die Summe der Seitenverhältnisse
                // ca. 3.4 erreicht (z.B. zwei Quer- und ein Hochformat) oder 4 Bilder drin sind.
                $rows = [];
                $row = [];
                $sum = 0;
                // -> Text als erstes Feld der ersten Reihe; Breite je nach Textlänge (Seitenverhältnis 1.3–2.2),
                // so dass oben und unten etwas Luft bleibt
                if (!empty($module['aside_text'])) {
                    $length = mb_strlen(wp_strip_all_tags($module['aside_text']));
                    $ratio = round(max(1.3, min(2.2, $length / 270)), 2);
                    $row[] = ['text' => $module['aside_text'], 'ratio' => $ratio];
                    $sum += $ratio;
                }
                foreach ($module['images'] as $id) {
                    $image = get_media_by_id($id, 'l');
                    if (!$image || empty($image['height'])) {
                        continue;
                    }
                    $ratio = round($image['width'] / $image['height'], 4);
                    $row[] = ['image' => $image, 'ratio' => $ratio];
                    $sum += $ratio;
                    if ($sum >= 3.4 || count($row) === 4) {
                        $rows[] = $row;
                        $row = [];
                        $sum = 0;
                    }
                }
                // -> Ein kurzer Rest wird an die letzte Reihe angehängt, statt allein riesig zu werden
                if ($row) {
                    if ($rows && $sum < 2) {
                        $rows[count($rows) - 1] = array_merge($rows[count($rows) - 1], $row);
                    } else {
                        $rows[] = $row;
                    }
                }
                // -> Anteil jedes Bildes an der Reihenbreite, damit der Browser die passende Bildgrösse lädt
                foreach ($rows as $r => $items) {
                    $total = array_sum(array_column($items, 'ratio'));
                    foreach ($items as $k => $item) {
                        $rows[$r][$k]['vw'] = (int) ceil($item['ratio'] / $total * 100);
                    }
                }
                $module['rows'] = $rows;
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
                $module['images'] = array_values(array_filter(array_map(fn ($id) => get_media_by_id($id, 'l'), array_slice($module['images'] ?? [], 0, 3))));
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
        // -> Schmale Bausteine in Reihen zusammenfassen (12er-Raster); passt einer nicht mehr hinein, beginnt eine neue Reihe
        $span = $spans[$module['width'] ?? 'full'] ?? 12;
        if ($row_open && ($span === 12 || $row_sum + $span > 12)) {
            echo '</div>';
            $row_open = false;
        }
        if ($span < 12) {
            if (!$row_open) {
                echo '<div class="module-row">';
                $row_open = true;
                $row_sum = 0;
            }
            $row_sum += $span;
            echo '<div class="module-cell" style="--span: ' . $span . '">';
        }
        // -> Laden des Templates basierend auf dem Modultyp und weitergeben der Daten
        get_template_part(
            'frontend/views/modules/' . $module['_type'],
            null,
            [
                'data' => $module
            ]
        );
        if ($span < 12) {
            echo '</div>';
        }
    }
    if ($row_open) {
        echo '</div>';
    }
}
