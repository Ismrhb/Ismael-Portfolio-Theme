<?php

// -> Kategorien eines Projekts
function get_project_categories(int $id): array
{
    $terms = get_the_terms($id, 'project_category');
    if (!$terms || is_wp_error($terms)) {
        return [];
    }
    // -> Foto und Video zuerst, danach die übrigen Kategorien alphabetisch
    $first = ['foto' => 0, 'video' => 1];
    usort($terms, fn ($a, $b) => [$first[$a->slug] ?? 2, mb_strtolower($a->name)] <=> [$first[$b->slug] ?? 2, mb_strtolower($b->name)]);
    return array_map(fn ($term) => [
        'name' => $term->name,
        'slug' => $term->slug,
    ], $terms);
}

// -> Alle Kategorien, denen mindestens ein Projekt zugewiesen ist
function get_used_project_categories(): array
{
    $terms = get_terms([
        'taxonomy' => 'project_category',
        'hide_empty' => true,
    ]);
    if (is_wp_error($terms)) {
        return [];
    }
    // -> Foto und Video zuerst, danach die übrigen Kategorien alphabetisch
    $first = ['foto' => 0, 'video' => 1];
    usort($terms, fn ($a, $b) => [$first[$a->slug] ?? 2, mb_strtolower($a->name)] <=> [$first[$b->slug] ?? 2, mb_strtolower($b->name)]);
    return array_map(fn ($term) => [
        'name' => $term->name,
        'slug' => $term->slug,
    ], $terms);
}

// -> Stammdaten eines Projekts, leere Felder werden weggelassen
function get_project_facts(int $id): array
{
    $facts = [
        'Datum' => carbon_get_post_meta($id, 'fact_date'),
        'Kunde' => carbon_get_post_meta($id, 'fact_client'),
        'Leistung' => carbon_get_post_meta($id, 'fact_service'),
        'Aufwand' => carbon_get_post_meta($id, 'fact_effort'),
        'Inspiration' => carbon_get_post_meta($id, 'fact_inspiration'),
    ];
    return array_filter($facts);
}

// -> Daten eines Projekts für die Projekte-Übersicht und die Startseite
function get_project_teaser_by_id(int $id): array
{
    // -> Ohne Titelbild = 0, d.h. kein Bild
    $highlight_image = carbon_get_post_meta($id, 'highlight_image') ?: 0;
    return [
        'id' => $id,
        'title' => get_the_title($id),
        'url' => get_permalink($id),
        'categories' => get_project_categories($id),
        // -> Priorität in der Projekte-Übersicht (gespeichert als Grösse: wide = hoch, narrow = niedrig)
        'priority' => ['wide' => 'high', 'narrow' => 'low'][carbon_get_post_meta($id, 'teaser_size')] ?? 'normal',
        'teaser_image' => get_responsive_media($highlight_image, 0, 'l'),
        'highlight_image' => get_media_by_id($highlight_image, 'l'),
        'highlight_text' => carbon_get_post_meta($id, 'highlight_text'),
        // -> Bildbeschreibung aus der Mediathek, sonst der Projekttitel
        'alt' => get_post_meta((int) $highlight_image, '_wp_attachment_image_alt', true) ?: get_the_title($id),
        // -> Optionales Titelvideo (läuft stumm in Schlaufe), Titelbild dient als Vorschau
        'video' => carbon_get_post_meta($id, 'teaser_video') ? wp_get_attachment_url(carbon_get_post_meta($id, 'teaser_video')) : '',
    ];
}

// -> Alle veröffentlichten Projekte, sortiert nach Reihenfolge und Datum
function get_projects(): array
{
    $ids = get_posts([
        'post_type' => 'project',
        'post_status' => 'publish',
        'numberposts' => -1,
        'orderby' => ['menu_order' => 'ASC', 'date' => 'DESC'],
        'fields' => 'ids',
    ]);
    return array_map('get_project_teaser_by_id', $ids);
}

// -> Alle anderen Projekte für "Andere Projekte", beginnend mit dem nächsten in der Reihenfolge der Projekte-Übersicht
function get_other_projects(int $id): array
{
    $projects = get_projects();
    $index = array_search($id, array_column($projects, 'id'), true);
    if ($index === false) {
        return $projects;
    }
    return array_merge(array_slice($projects, $index + 1), array_slice($projects, 0, $index));
}
