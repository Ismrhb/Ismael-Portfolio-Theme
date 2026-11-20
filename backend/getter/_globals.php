<?php

// -> Kontaktangaben für den Footer
function get_globals_contact(): array
{
    $phone = carbon_get_theme_option('contact_phone');
    $email = carbon_get_theme_option('contact_email');
    return [
        'name' => carbon_get_theme_option('contact_name'),
        'street' => carbon_get_theme_option('contact_street'),
        'city' => carbon_get_theme_option('contact_city'),
        'phone' => $phone,
        'phone_link' => $phone ? 'tel:' . get_international_phone($phone) : '',
        'email' => $email,
    ];
}

// -> Telefonnummer international schreiben: 079 123 45 67 -> +41791234567
function get_international_phone(string $phone): string
{
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    if (str_starts_with($phone, '00')) {
        return '+' . substr($phone, 2);
    }
    if (str_starts_with($phone, '0')) {
        return '+41' . substr($phone, 1);
    }
    return $phone;
}

// -> Social-Media-Links, nur ausgefüllte Kanäle werden zurückgegeben
function get_globals_socials(): array
{
    $socials = [
        'instagram' => ['title' => 'Instagram', 'url' => carbon_get_theme_option('social_instagram')],
        'linkedin' => ['title' => 'LinkedIn', 'url' => carbon_get_theme_option('social_linkedin')],
    ];
    return array_filter($socials, fn ($social) => !empty($social['url']));
}

// -> Texte und Empfänger des Kontaktformulars
function get_globals_form(): array
{
    return [
        'title' => carbon_get_theme_option('form_title') ?: 'Let’s talk',
        'success' => carbon_get_theme_option('form_success') ?: 'Danke für deine Nachricht!',
        'note' => carbon_get_theme_option('form_note'),
        // -> Wörter für die Kontakt-Buttons, z.B. «Kaffee?, Telefon?, Frage?»
        'words' => array_values(array_filter(array_map('trim', explode(',', (string) carbon_get_theme_option('contact_words'))))),
        'recipient' => carbon_get_theme_option('form_recipient') ?: get_option('admin_email'),
        'sender' => carbon_get_theme_option('form_sender'),
        'occasions' => ['Event', 'Portrait', 'Hochzeit', 'Anderes'],
        'privacy_url' => get_privacy_policy_url(),
    ];
}

// -> Link zur Projekte-Übersicht: aus "Globales" oder die erste Seite mit dem Baustein "Projekte-Übersicht"
function get_collection_url(): string
{
    static $url = null;
    if ($url !== null) {
        return $url;
    }
    $page = carbon_get_theme_option('collection_page');
    $id = $page[0]['id'] ?? 0;
    if (!$id) {
        global $wpdb;
        $id = (int) $wpdb->get_var(
            "SELECT pm.post_id FROM {$wpdb->postmeta} pm
             JOIN {$wpdb->posts} p ON p.ID = pm.post_id
             WHERE pm.meta_key LIKE '\_modules|||%|value' AND pm.meta_value = 'collection'
             AND p.post_type = 'page' AND p.post_status = 'publish'
             ORDER BY p.menu_order, p.ID LIMIT 1"
        );
    }
    $url = $id ? get_permalink($id) : '';
    return $url;
}

// -> Standardwerte für Beschreibung, Vorschaubild und Leistungen (SEO)
function get_globals_seo(): array
{
    $services = carbon_get_theme_option('seo_services') ?: '';
    return [
        'description' => carbon_get_theme_option('seo_description') ?: get_bloginfo('description'),
        'image' => carbon_get_theme_option('seo_image'),
        'services' => array_values(array_filter(array_map('trim', explode("\n", $services)))),
    ];
}
