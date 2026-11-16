<?php
/**
 * Verarbeitung des Kontaktformulars. Das Formular wird per JavaScript
 * an admin-ajax.php gesendet und hier per wp_mail() weitergeleitet.
 */

// -> Gültig ist eine E-Mail-Adresse oder eine Telefonnummer mit 7 bis 15 Ziffern
function is_valid_contact(string $contact): bool
{
    if (is_email($contact)) {
        return true;
    }
    $digits = preg_replace('/\D/', '', $contact);
    return preg_match('/^[0-9+\s()\/.-]+$/', $contact) && strlen($digits) >= 7 && strlen($digits) <= 15;
}

function handle_contact_form(): void
{
    if (!check_ajax_referer('contact_form', 'contact_nonce', false)) {
        wp_send_json_error(['message' => 'Die Sitzung ist abgelaufen. Bitte lade die Seite neu.'], 403);
    }
    // -> Honeypot: dieses Feld ist unsichtbar und wird nur von Bots ausgefüllt
    if (!empty($_POST['website'])) {
        wp_send_json_success();
    }

    $form = get_globals_form();
    $name = sanitize_text_field(wp_unslash($_POST['name'] ?? ''));
    $contact = sanitize_text_field(wp_unslash($_POST['contact'] ?? ''));
    $occasion = sanitize_text_field(wp_unslash($_POST['occasion'] ?? ''));
    $date = sanitize_text_field(wp_unslash($_POST['date'] ?? ''));
    $message = sanitize_textarea_field(wp_unslash($_POST['message'] ?? ''));

    // -> Fehler pro Feld sammeln, damit das Formular sie direkt beim Feld anzeigen kann
    $errors = [];
    if (!$name) {
        $errors['name'] = 'Bitte gib deinen Namen an.';
    }
    if (!is_valid_contact($contact)) {
        $errors['contact'] = 'Bitte gib eine E-Mail oder Telefonnummer an.';
    }
    if (!$message) {
        $errors['message'] = 'Bitte schreib mir eine kurze Nachricht.';
    }
    if ($errors) {
        wp_send_json_error(['message' => 'Bitte prüfe die markierten Felder.', 'fields' => $errors], 400);
    }
    if (!in_array($occasion, $form['occasions'], true)) {
        $occasion = '';
    }
    $date = preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) ? date_i18n('j. F Y', strtotime($date)) : '';

    $headers = [];
    // -> Absender: viele Hoster verschicken nur von Adressen, die bei ihnen als Postfach existieren
    if (is_email($form['sender'])) {
        $headers[] = 'From: ' . wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES) . ' <' . $form['sender'] . '>';
    }
    if (is_email($contact)) {
        $headers[] = 'Reply-To: ' . $name . ' <' . $contact . '>';
    }
    $body = "Name: {$name}\nKontakt: {$contact}\n";
    $body .= $occasion ? "Anlass: {$occasion}\n" : '';
    $body .= $date ? "Datum: {$date}\n" : '';
    $body .= "\n{$message}";

    // -> Grund eines Fehlers merken, er wird nur eingeloggten Administratoren angezeigt
    $error = '';
    add_action('wp_mail_failed', function ($wp_error) use (&$error) {
        $error = $wp_error->get_error_message();
    });
    $subject = 'Neue Anfrage von ' . $name . ($occasion ? " ({$occasion})" : '');
    $sent = wp_mail($form['recipient'], $subject, $body, $headers);
    if (!$sent) {
        $hint = $error && current_user_can('manage_options') ? " (Nur für Admins sichtbar: {$error})" : '';
        wp_send_json_error(['message' => 'Die Nachricht konnte nicht gesendet werden. Bitte versuche es später nochmals.' . $hint], 500);
    }
    wp_send_json_success(['message' => $form['success']]);
}

add_action('wp_ajax_contact_form', 'handle_contact_form');
add_action('wp_ajax_nopriv_contact_form', 'handle_contact_form');
