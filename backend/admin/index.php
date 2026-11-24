<?php
/**
 * Kasten «Bildausschnitt» bei Projekten: Auf dem Titelbild die wichtigste Stelle anklicken (z.B. den Kopf).
 * Gespeichert als "x y" in Prozent (Meta _highlight_focus_point), ausgegeben über get_project_focus().
 */

add_action('add_meta_boxes_project', function () {
    add_meta_box('highlight_focus_box', 'Bildausschnitt', 'render_focus_box', 'project', 'side', 'low');
});

function render_focus_box(WP_Post $post): void
{
    // -> Gespeicherter Punkt, sonst aus der früheren Auswahl (Oben, Unten …), sonst Mitte
    [$x, $y] = array_map('intval', explode(' ', str_replace('%', '', get_project_focus($post->ID))));
    $image_id = (int) carbon_get_post_meta($post->ID, 'highlight_image');
    $src = $image_id ? wp_get_attachment_image_url($image_id, 'medium_large') : '';
    wp_nonce_field('highlight_focus', 'highlight_focus_nonce');
    ?>
    <div class="focus-picker" style="--x: <?= $x ?>%; --y: <?= $y ?>%">
        <p class="focus-help">Auf die wichtigste Stelle im Titelbild klicken, z.B. den Kopf. Beim Zuschneiden bleibt sie sichtbar.</p>
        <div class="focus-image" tabindex="0" role="slider" aria-label="Bildausschnitt, mit Pfeiltasten verschieben" <?= $src ? '' : 'hidden' ?>>
            <img src="<?= esc_url($src) ?>" alt="">
            <span class="focus-dot" aria-hidden="true"></span>
        </div>
        <p class="focus-empty" <?= $src ? 'hidden' : '' ?>>Zuerst ein Titelbild wählen und speichern.</p>
        <div class="focus-previews" <?= $src ? '' : 'hidden' ?>>
            <figure><img src="<?= esc_url($src) ?>" alt="" class="is-landscape"><figcaption>Quer</figcaption></figure>
            <figure><img src="<?= esc_url($src) ?>" alt="" class="is-portrait"><figcaption>Hoch</figcaption></figure>
        </div>
        <p><button type="button" class="button-link focus-reset">Zurück auf Mitte</button></p>
        <input type="hidden" name="highlight_focus_point" value="<?= $x . ' ' . $y ?>">
    </div>
    <style>
        .focus-picker .focus-help { margin-top: 0; }
        .focus-picker .focus-image { position: relative; cursor: crosshair; line-height: 0; touch-action: none; }
        .focus-picker .focus-image img { width: 100%; height: auto; border-radius: 4px; user-select: none; -webkit-user-drag: none; }
        .focus-picker .focus-dot { position: absolute; left: var(--x); top: var(--y); width: 18px; height: 18px; margin: -9px 0 0 -9px; border: 3px solid #fff; border-radius: 50%; box-shadow: 0 0 0 2px #1b4a6f, 0 1px 4px rgb(0 0 0 / 0.5); pointer-events: none; }
        .focus-picker .focus-image:focus-visible { outline: 2px solid #1b4a6f; outline-offset: 2px; }
        .focus-picker .focus-previews { display: grid; grid-template-columns: 16fr 8fr; gap: 8px; align-items: end; margin-top: 12px; }
        .focus-picker figure { margin: 0; }
        .focus-picker figcaption { font-size: 12px; color: #646970; }
        .focus-picker .focus-previews img { display: block; width: 100%; object-fit: cover; object-position: var(--x) var(--y); border-radius: 4px; }
        .focus-picker .is-landscape { aspect-ratio: 16 / 9; }
        .focus-picker .is-portrait { aspect-ratio: 4 / 5; }
    </style>
    <script>
        (() => {
            const box = document.querySelector('.focus-picker');
            const area = box.querySelector('.focus-image');
            const input = box.querySelector('[name="highlight_focus_point"]');
            let x = <?= $x ?>;
            let y = <?= $y ?>;
            // -> Punkt setzen: Anzeige, Vorschau und verstecktes Feld
            const set = (nx, ny) => {
                x = Math.round(Math.min(100, Math.max(0, nx)));
                y = Math.round(Math.min(100, Math.max(0, ny)));
                box.style.setProperty('--x', x + '%');
                box.style.setProperty('--y', y + '%');
                input.value = x + ' ' + y;
                area.setAttribute('aria-valuetext', x + ' % von links, ' + y + ' % von oben');
            };
            const fromPointer = (event) => {
                const rect = area.getBoundingClientRect();
                set((event.clientX - rect.left) / rect.width * 100, (event.clientY - rect.top) / rect.height * 100);
            };
            // -> Klicken oder ziehen
            area.addEventListener('pointerdown', (event) => {
                area.setPointerCapture(event.pointerId);
                fromPointer(event);
            });
            area.addEventListener('pointermove', (event) => {
                if (area.hasPointerCapture(event.pointerId)) {
                    fromPointer(event);
                }
            });
            // -> Mit der Tastatur in 5er-Schritten
            area.addEventListener('keydown', (event) => {
                const step = { ArrowLeft: [-5, 0], ArrowRight: [5, 0], ArrowUp: [0, -5], ArrowDown: [0, 5] }[event.key];
                if (step) {
                    event.preventDefault();
                    set(x + step[0], y + step[1]);
                }
            });
            box.querySelector('.focus-reset').addEventListener('click', () => set(50, 50));
            set(x, y);

            // -> Neues Titelbild gewählt: Bild im Kasten sofort austauschen (Feld von Carbon Fields beobachten)
            let current = '<?= $image_id ?>';
            setInterval(() => {
                const field = document.querySelector('input[name="carbon_fields_compact_input[_highlight_image]"]');
                if (!field || field.value === current || !window.wp?.media?.attachment) {
                    return;
                }
                current = field.value;
                const show = (url) => {
                    box.querySelectorAll('img').forEach((img) => { img.src = url; });
                    area.hidden = !url;
                    box.querySelector('.focus-previews').hidden = !url;
                    box.querySelector('.focus-empty').hidden = !!url;
                };
                if (!current) {
                    show('');
                    return;
                }
                const attachment = wp.media.attachment(current);
                attachment.fetch().then(() => {
                    const sizes = attachment.get('sizes') || {};
                    show((sizes.medium_large || sizes.large || sizes.full || {}).url || attachment.get('url'));
                    set(50, 50);
                });
            }, 800);
        })();
    </script>
    <?php
}

// -> Speichern zusammen mit dem Projekt
add_action('save_post_project', function (int $post_id) {
    if (
        !isset($_POST['highlight_focus_nonce'], $_POST['highlight_focus_point'])
        || !wp_verify_nonce($_POST['highlight_focus_nonce'], 'highlight_focus')
        || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        || !current_user_can('edit_post', $post_id)
    ) {
        return;
    }
    // -> Die frühere Auswahl (Oben, Unten …) wird durch den Punkt ersetzt
    delete_post_meta($post_id, '_highlight_focus');
    $point = sanitize_text_field(wp_unslash($_POST['highlight_focus_point']));
    if (!preg_match('/^\d{1,3} \d{1,3}$/', $point) || $point === '50 50') {
        delete_post_meta($post_id, '_highlight_focus_point');
        return;
    }
    update_post_meta($post_id, '_highlight_focus_point', $point);
});
