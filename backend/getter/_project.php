<?php

// -> Kategorien eines Projekts
function get_project_categories(int $id): array
{
    $terms = get_the_terms($id, 'project_category');
    if (!$terms || is_wp_error($terms)) {
        return [];
    }
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
        'Aufwand' => carbon_get_post_meta($id, 'fact_effort'),
        'Inspiration' => carbon_get_post_meta($id, 'fact_inspiration'),
    ];
    return array_filter($facts);
}

// -> Daten eines Projekts für die Sammlung und die Startseite
function get_project_teaser_by_id(int $id): array
{
    $teaser_image = carbon_get_post_meta($id, 'teaser_image');
    $highlight_image = carbon_get_post_meta($id, 'highlight_image') ?: $teaser_image;
    return [
        'id' => $id,
        'title' => get_the_title($id),
        'url' => get_permalink($id),
        'categories' => get_project_categories($id),
        // -> Priorität in der Sammlung (gespeichert als Grösse: wide = hoch, narrow = niedrig)
        'priority' => ['wide' => 'high', 'narrow' => 'low'][carbon_get_post_meta($id, 'teaser_size')] ?? 'normal',
        'teaser_image' => get_responsive_media($teaser_image, carbon_get_post_meta($id, 'teaser_image_mobile'), 'l'),
        'highlight_image' => get_media_by_id($highlight_image, 'l'),
        'highlight_text' => carbon_get_post_meta($id, 'highlight_text'),
        // -> Bildbeschreibung aus der Mediathek, sonst der Projekttitel
        'alt' => get_post_meta((int) $highlight_image, '_wp_attachment_image_alt', true) ?: get_the_title($id),
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

// -> Nächstes Projekt in der Reihenfolge der Sammlung (nach dem letzten folgt wieder das erste)
function get_next_project(int $id): false|array
{
    $ids = get_posts([
        'post_type' => 'project',
        'post_status' => 'publish',
        'numberposts' => -1,
        'orderby' => ['menu_order' => 'ASC', 'date' => 'DESC'],
        'fields' => 'ids',
    ]);
    $index = array_search($id, $ids, true);
    if ($index === false || count($ids) < 2) {
        return false;
    }
    $next = $ids[($index + 1) % count($ids)];
    return [
        'title' => get_the_title($next),
        'url' => get_permalink($next),
    ];
}
