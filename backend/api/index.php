<?php
/**
 * Geschützte Schnittstelle, um Inhalte (Bausteine, Projektfelder, Kategorien, Globales)
 * per Anwendungspasswort zu lesen und zu bearbeiten. Nur für angemeldete Administratoren.
 *
 * GET  /wp-json/ismael/v1/list              -> alle Seiten und Projekte
 * GET  /wp-json/ismael/v1/content/<id>      -> Titel, Bausteine, Felder, Kategorien
 * POST /wp-json/ismael/v1/content/<id>      -> { title, slug, status (publish/draft), modules, meta, categories }
 * POST /wp-json/ismael/v1/create            -> { title, slug, menu_order } neues Projekt als Entwurf
 * GET  /wp-json/ismael/v1/options           -> Felder aus "Globales"
 * POST /wp-json/ismael/v1/options           -> { feld: wert }
 */

// -> Felder, die über die Schnittstelle gelesen und geschrieben werden dürfen
const API_POST_META = [
    'highlight_image', 'highlight_format', 'teaser_video', 'teaser_size', 'highlight_text',
    'fact_date', 'fact_client', 'fact_service', 'fact_effort', 'fact_inspiration',
    'seo_description', 'seo_image',
];
const API_OPTIONS = [
    'contact_name', 'contact_street', 'contact_city', 'contact_phone', 'contact_email',
    'social_instagram', 'social_linkedin',
    'form_recipient', 'form_sender', 'form_title', 'contact_words', 'form_note', 'form_success',
    'seo_description', 'seo_image', 'seo_services',
    'snow',
];

function api_is_admin(): bool
{
    return current_user_can('manage_options');
}

// -> Inhalt eines Beitrags (Seite oder Projekt) in einer Form, die wieder geschrieben werden kann
function api_get_content(int $id): array|WP_Error
{
    $post = get_post($id);
    if (!$post || !in_array($post->post_type, ['page', 'project'], true)) {
        return new WP_Error('not_found', 'Seite oder Projekt nicht gefunden.', ['status' => 404]);
    }
    $meta = [];
    foreach (API_POST_META as $key) {
        if ($post->post_type === 'page' && !str_starts_with($key, 'seo_')) {
            continue;
        }
        $meta[$key] = carbon_get_post_meta($id, $key);
    }
    $terms = get_the_terms($id, 'project_category');
    return [
        'id' => $id,
        'type' => $post->post_type,
        'status' => $post->post_status,
        'title' => $post->post_title,
        'slug' => $post->post_name,
        'link' => get_permalink($id),
        'modules' => carbon_get_post_meta($id, 'modules'),
        'meta' => $meta,
        'categories' => $terms && !is_wp_error($terms) ? wp_list_pluck($terms, 'name') : [],
    ];
}

add_action('rest_api_init', function () {
    register_rest_route('ismael/v1', '/list', [
        'methods' => 'GET',
        'permission_callback' => 'api_is_admin',
        'callback' => function () {
            $posts = get_posts([
                'post_type' => ['page', 'project'],
                'post_status' => ['publish', 'draft', 'private'],
                'numberposts' => -1,
                'orderby' => ['post_type' => 'ASC', 'menu_order' => 'ASC'],
            ]);
            return array_map(fn ($post) => [
                'id' => $post->ID,
                'type' => $post->post_type,
                'status' => $post->post_status,
                'title' => $post->post_title,
                'link' => get_permalink($post),
            ], $posts);
        },
    ]);

    register_rest_route('ismael/v1', '/content/(?P<id>\d+)', [
        [
            'methods' => 'GET',
            'permission_callback' => 'api_is_admin',
            'callback' => fn (WP_REST_Request $request) => api_get_content((int) $request['id']),
        ],
        [
            'methods' => 'POST',
            'permission_callback' => 'api_is_admin',
            'callback' => function (WP_REST_Request $request) {
                $id = (int) $request['id'];
                $current = api_get_content($id);
                if (is_wp_error($current)) {
                    return $current;
                }
                $data = $request->get_json_params() ?: [];
                // -> Titel und Adresse
                $update = ['ID' => $id];
                if (isset($data['title'])) {
                    $update['post_title'] = sanitize_text_field($data['title']);
                }
                if (isset($data['slug'])) {
                    $update['post_name'] = sanitize_title($data['slug']);
                }
                // -> Veröffentlichen oder zurück auf Entwurf
                if (isset($data['status']) && in_array($data['status'], ['publish', 'draft'], true)) {
                    $update['post_status'] = $data['status'];
                }
                if (count($update) > 1) {
                    $result = wp_update_post($update, true);
                    if (is_wp_error($result)) {
                        return $result;
                    }
                }
                // -> Bausteine (vollständige Liste, im Format von carbon_set_post_meta)
                if (isset($data['modules']) && is_array($data['modules'])) {
                    carbon_set_post_meta($id, 'modules', $data['modules']);
                }
                // -> Einzelne Felder, nur aus der erlaubten Liste
                foreach (($data['meta'] ?? []) as $key => $value) {
                    if (in_array($key, API_POST_META, true)) {
                        carbon_set_post_meta($id, $key, $value);
                    }
                }
                // -> Kategorien (Namen; fehlende werden angelegt)
                if (isset($data['categories']) && is_array($data['categories']) && $current['type'] === 'project') {
                    wp_set_object_terms($id, array_map('sanitize_text_field', $data['categories']), 'project_category');
                }
                return api_get_content($id);
            },
        ],
    ]);

    // -> Neues Projekt anlegen, immer als Entwurf (wird erst nach Durchsicht im WordPress veröffentlicht)
    register_rest_route('ismael/v1', '/create', [
        'methods' => 'POST',
        'permission_callback' => 'api_is_admin',
        'callback' => function (WP_REST_Request $request) {
            $data = $request->get_json_params() ?: [];
            $title = sanitize_text_field($data['title'] ?? '');
            if ($title === '') {
                return new WP_Error('no_title', 'Titel fehlt.', ['status' => 400]);
            }
            $id = wp_insert_post([
                'post_type' => 'project',
                'post_status' => 'draft',
                'post_title' => $title,
                'post_name' => sanitize_title($data['slug'] ?? $title),
                'menu_order' => (int) ($data['menu_order'] ?? 0),
            ], true);
            return is_wp_error($id) ? $id : api_get_content($id);
        },
    ]);

    register_rest_route('ismael/v1', '/options', [
        [
            'methods' => 'GET',
            'permission_callback' => 'api_is_admin',
            'callback' => function () {
                $options = [];
                foreach (API_OPTIONS as $key) {
                    $options[$key] = carbon_get_theme_option($key);
                }
                return $options;
            },
        ],
        [
            'methods' => 'POST',
            'permission_callback' => 'api_is_admin',
            'callback' => function (WP_REST_Request $request) {
                foreach (($request->get_json_params() ?: []) as $key => $value) {
                    if (in_array($key, API_OPTIONS, true)) {
                        carbon_set_theme_option($key, $value);
                    }
                }
                $options = [];
                foreach (API_OPTIONS as $key) {
                    $options[$key] = carbon_get_theme_option($key);
                }
                return $options;
            },
        ],
    ]);
});
