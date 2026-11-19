<?php
$menu = get_menu_by_slug('main');
$current_url = trailingslashit(home_url($_SERVER['REQUEST_URI'] ?? '/'));
// -> Auf Projektseiten ist der Menüpunkt der Projekte-Übersicht aktiv
$collection_url = is_singular('project') ? trailingslashit(get_collection_url()) : '';
?>
<header data-layout="header">
    <a class="logo" href="<?= home_url('/') ?>">
        <span class="logo-name">Ismael Rehab</span>
        <span class="logo-subtitle">Foto - Videograf</span>
    </a>
    <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="main-nav" data-nav-toggle>
        <span class="nav-toggle-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Menü</span>
    </button>
    <nav class="nav" id="main-nav" aria-label="Hauptnavigation">
        <ul class="nav-list">
            <?php foreach ($menu as $item) : ?>
                <?php
                $url = trailingslashit($item['url']);
                $current = $url === $current_url ? 'page' : ($collection_url && $url === $collection_url ? 'true' : '');
                ?>
                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="<?= esc_url($item['url']) ?>"
                        <?= $item['target'] ? 'target="' . esc_attr($item['target']) . '"' : '' ?>
                        <?= $current ? 'aria-current="' . $current . '"' : '' ?>
                    ><?= esc_html($item['title']) ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <button class="button-contact" type="button" data-contact-open>Kontakt</button>
</header>
