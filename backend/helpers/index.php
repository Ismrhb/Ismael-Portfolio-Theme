<?php
/**
 * Funktionen in dieser Datei können überall im Projekt verwendet werden.
 * Wenn eine neue globale Funktion benötigt wird, bitte hier ergänzen.
 *
 * Diese globalen Funktionen sind nicht Wordpress-spezifisch.
 */

// -> PHP-Daten in der Browser-Konsole anzeigen
function js_log(mixed ...$content): void
{
    foreach ($content as $c) {
        echo '<script>console.log(' . json_encode($c) . ')</script>';
    }
}

// -> Details einer Mediendatei anhand der ID laden
function get_media_by_id(int|string $id, string $size = 'm'): false|array
{
    $id = (int) $id;
    if (!$id) {
        return false;
    }
    $data = [
        'title' => get_the_title($id),
        'alt' => get_post_meta($id, '_wp_attachment_image_alt', true) ?: get_the_title($id),
    ];
    $image = wp_get_attachment_image_src($id, $size);
    if ($image) {
        $data['src'] = $image[0];
        $data['width'] = $image[1];
        $data['height'] = $image[2];
        $data['srcset'] = wp_get_attachment_image_srcset($id, $size) ?: '';
        $data['type'] = 'image';
    } else {
        $data['src'] = wp_get_attachment_url($id);
        $data['type'] = get_post_mime_type($id);
    }
    return $data;
}

// -> Ein Menu anhand des Keys laden
function get_menu_by_slug(string $slug): array
{
    $theme_locations = get_nav_menu_locations();
    if (!array_key_exists($slug, $theme_locations)) {
        return [];
    }
    $menu_obj = get_term($theme_locations[$slug], 'nav_menu');
    $menu = wp_get_nav_menu_items($menu_obj->name) ?: [];
    $items = [];
    foreach ($menu as $item) {
        $items[] = [
            'ID' => (int) $item->ID,
            'title' => $item->title,
            'url' => $item->url,
            'target' => $item->target,
        ];
    }
    return $items;
}

// -> Desktop- und optionales Mobile-Bild laden (ohne Mobile-Bild wird das Desktop-Bild verwendet)
function get_responsive_media(int|string $id, int|string $mobile_id = 0, string $size = 'l'): false|array
{
    $desktop = get_media_by_id($id, $size);
    if (!$desktop) {
        return false;
    }
    return [
        'desktop' => $desktop,
        'mobile' => get_media_by_id($mobile_id, $size),
    ];
}

// -> Die Video-ID aus einem YouTube-Link auslesen (watch, youtu.be, shorts, embed)
function get_youtube_id(?string $url): string
{
    if (!$url) {
        return '';
    }
    preg_match('~(?:youtu\.be/|v=|/shorts/|/embed/|/live/)([A-Za-z0-9_-]{11})~', $url, $matches);
    return $matches[1] ?? '';
}
