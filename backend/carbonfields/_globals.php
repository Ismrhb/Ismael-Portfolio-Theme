<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

// -> Inhaltsfeld-Konfiguration der globalen Einstellungen des Themes
Container::make('theme_options', 'Globales')
    ->set_icon('dashicons-admin-site-alt3')
    ->add_tab('Kontakt', [
        Field::make('text', 'contact_name', 'Name')->set_default_value('Ismael Rehab'),
        Field::make('text', 'contact_street', 'Strasse')->set_width(50),
        Field::make('text', 'contact_city', 'PLZ & Ort')->set_width(50)
            ->set_help_text('Format: «3014 Bern»'),
        Field::make('text', 'contact_phone', 'Telefon')->set_width(50)
            ->set_help_text('Schweizer Nummern werden automatisch mit +41 verlinkt.'),
        Field::make('text', 'contact_email', 'E-Mail (wird im Footer angezeigt)')->set_width(50),
    ])
    ->add_tab('Social Media', [
        Field::make('text', 'social_instagram', 'Instagram-Link')->set_attribute('type', 'url'),
        Field::make('text', 'social_linkedin', 'LinkedIn-Link')->set_attribute('type', 'url'),
    ])
    ->add_tab('Kontaktformular', [
        Field::make('text', 'form_recipient', 'Anfragen senden an (E-Mail)')
            ->set_help_text('Leer lassen, um die E-Mail-Adresse aus den WordPress-Einstellungen zu verwenden.'),
        Field::make('text', 'form_sender', 'Absender-Adresse (E-Mail)')
            ->set_help_text('Von dieser Adresse werden die Anfragen verschickt. Sie muss beim Hosting als Postfach existieren, sonst wird die E-Mail oft blockiert.'),
        Field::make('text', 'form_title', 'Titel')->set_default_value('Let’s talk'),
        Field::make('text', 'form_success', 'Erfolgsmeldung')
            ->set_default_value('Danke für deine Nachricht! Ich melde mich so schnell wie möglich.'),
    ])
    ->add_tab('Seiten', [
        Field::make('association', 'collection_page', 'Seite mit der Sammlung')
            ->set_types([['type' => 'post', 'post_type' => 'page']])
            ->set_max(1)
            ->set_help_text('Wird für «Alle Projekte ansehen», «Zurück zur Sammlung» und den aktiven Menüpunkt auf Projektseiten verwendet. Leer lassen = die Seite mit dem Baustein «Sammlung» wird automatisch gesucht.'),
    ])
    ->add_tab('SEO & Teilen', [
        Field::make('textarea', 'seo_description', 'Standard-Beschreibung für Google')
            ->set_rows(3)
            ->set_attribute('maxLength', 160)
            ->set_help_text('140–160 Zeichen. Gilt für alle Seiten ohne eigene Beschreibung.'),
        Field::make('image', 'seo_image', 'Standard-Vorschaubild beim Teilen')
            ->set_help_text('Ideal 1200 × 630 px, z.B. das Portrait. Erscheint, wenn ein Link auf WhatsApp, Instagram usw. geteilt wird.'),
        Field::make('textarea', 'seo_services', 'Leistungen (für Google)')
            ->set_rows(3)
            ->set_help_text('Eine Leistung pro Zeile, z.B. «Hochzeitsfotografie». Wird unsichtbar für Suchmaschinen ausgegeben.'),
    ]);
