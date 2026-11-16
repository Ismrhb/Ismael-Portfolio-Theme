<?php
/**
 * Suchmaschinen und geteilte Links: Seitentitel, Meta-Description,
 * Open-Graph-Tags, strukturierte Daten (JSON-LD) und Favicon.
 * Alles wird über wp_head() im <head> ausgegeben.
 */

// -> Seitentitel: Startseite "Name – Untertitel", Unterseiten "Seite | Name"
function get_document_title_text(): string
{
    $site = wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);
    $tagline = wp_specialchars_decode(get_bloginfo('description'), ENT_QUOTES);
    if (is_front_page()) {
        return $tagline ? "{$site} – {$tagline}" : $site;
    }
    if (is_404()) {
        return "Seite nicht gefunden | {$site}";
    }
    return wp_strip_all_tags(get_the_title()) . " | {$site}";
}

// -> Beschreibung: eigenes Feld, bei Projekten sonst die Kurzbeschreibung, zuletzt der Standard
function get_seo_description(): string
{
    $description = '';
    if (is_singular()) {
        $id = get_queried_object_id();
        $description = carbon_get_post_meta($id, 'seo_description');
        if (!$description && get_post_type($id) === 'project') {
            $description = carbon_get_post_meta($id, 'highlight_text');
        }
    }
    $description = $description ?: get_globals_seo()['description'];
    return wp_html_excerpt(trim(preg_replace('/\s+/', ' ', $description)), 160, '…');
}

// -> Vorschaubild: eigenes Feld, bei Projekten sonst das Startseiten- oder Titelbild, zuletzt der Standard
function get_seo_image(): false|array
{
    $image_id = 0;
    if (is_singular()) {
        $id = get_queried_object_id();
        $image_id = carbon_get_post_meta($id, 'seo_image');
        if (!$image_id && get_post_type($id) === 'project') {
            $image_id = carbon_get_post_meta($id, 'highlight_image') ?: carbon_get_post_meta($id, 'teaser_image');
        }
    }
    $image_id = $image_id ?: get_globals_seo()['image'];
    return $image_id ? get_media_by_id($image_id, 'l') : false;
}

add_action('wp_head', function () {
    $title = get_document_title_text();
    $description = get_seo_description();
    $image = get_seo_image();
    $url = is_singular() ? get_permalink() : home_url(add_query_arg([]));
    ?>
    <?php if ($description) : ?>
        <meta name="description" content="<?= esc_attr($description) ?>">
        <meta property="og:description" content="<?= esc_attr($description) ?>">
    <?php endif; ?>
    <meta property="og:type" content="website">
    <meta property="og:locale" content="de_CH">
    <meta property="og:site_name" content="<?= esc_attr(get_bloginfo('name')) ?>">
    <meta property="og:title" content="<?= esc_attr($title) ?>">
    <meta property="og:url" content="<?= esc_url($url) ?>">
    <?php if ($image && !empty($image['width'])) : ?>
        <meta property="og:image" content="<?= esc_url($image['src']) ?>">
        <meta property="og:image:width" content="<?= (int) $image['width'] ?>">
        <meta property="og:image:height" content="<?= (int) $image['height'] ?>">
        <meta property="og:image:alt" content="<?= esc_attr($image['alt']) ?>">
        <meta name="twitter:card" content="summary_large_image">
    <?php endif; ?>
    <?php
}, 1);

// -> Strukturierte Daten für die lokale Suche, nur auf der Startseite
add_action('wp_head', function () {
    if (!is_front_page()) {
        return;
    }
    $contact = get_globals_contact();
    $seo = get_globals_seo();
    $image = get_seo_image();
    // -> "3014 Bern" in Postleitzahl und Ort aufteilen
    preg_match('/^\s*(\d{4})\s+(.+)$/', $contact['city'] ?? '', $city);
    $data = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'ProfessionalService',
        'name' => $contact['name'] ?: get_bloginfo('name'),
        'description' => $seo['description'],
        'url' => home_url('/'),
        'image' => $image ? $image['src'] : null,
        'telephone' => $contact['phone'] ? get_international_phone($contact['phone']) : null,
        'email' => $contact['email'] ?: null,
        'address' => array_filter([
            '@type' => 'PostalAddress',
            'streetAddress' => $contact['street'] ?: null,
            'postalCode' => $city[1] ?? null,
            'addressLocality' => $city[2] ?? ($contact['city'] ?: null),
            'addressCountry' => 'CH',
        ]),
        'areaServed' => $city[2] ?? null,
        'sameAs' => array_values(array_column(get_globals_socials(), 'url')) ?: null,
        'knowsAbout' => $seo['services'] ?: null,
    ]);
    echo '<script type="application/ld+json">' . wp_json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}, 2);

// -> Favicon aus dem Theme, solange in WordPress kein eigenes Website-Icon gesetzt ist
add_action('wp_head', function () {
    if (has_site_icon()) {
        return;
    }
    $dir = get_template_directory_uri() . '/frontend/assets/favicon';
    ?>
    <link rel="icon" href="<?= esc_url($dir) ?>/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="<?= esc_url($dir) ?>/favicon-512.png" type="image/png" sizes="512x512">
    <link rel="apple-touch-icon" href="<?= esc_url($dir) ?>/apple-touch-icon.png">
    <?php
}, 3);

// -> M8: Portrait der Startseite früh vorladen, damit das grösste Bild (LCP) schneller erscheint
add_action('wp_head', function () {
    if (!is_front_page()) {
        return;
    }
    $modules = carbon_get_post_meta(get_queried_object_id(), 'modules') ?: [];
    foreach ($modules as $module) {
        if ($module['_type'] !== 'intro') {
            continue;
        }
        $media = get_responsive_media($module['portrait'], $module['portrait_mobile'], 'm');
        if (!$media) {
            return;
        }
        $variants = $media['mobile']
            ? ['(max-width: 768px)' => $media['mobile'], '(min-width: 769px)' => $media['desktop']]
            : ['' => $media['desktop']];
        foreach ($variants as $query => $image) {
            printf(
                '<link rel="preload" as="image" href="%s" imagesrcset="%s" imagesizes="%s" fetchpriority="high"%s>' . "\n",
                esc_url($image['src']),
                esc_attr($image['srcset']),
                esc_attr($query === '(max-width: 768px)' ? '100vw' : '(max-width: 768px) 100vw, 40vw'),
                $query ? ' media="' . esc_attr($query) . '"' : ''
            );
        }
        return;
    }
}, 1);
