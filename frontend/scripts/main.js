// -> Mobiles Menü (Burger) öffnen und schliessen
const header = document.querySelector('[data-layout="header"]');
const navToggle = header?.querySelector('[data-nav-toggle]');

if (navToggle) {
    const setNavOpen = (open) => {
        header.classList.toggle('is-open', open);
        navToggle.setAttribute('aria-expanded', String(open));
    };
    navToggle.addEventListener('click', () => setNavOpen(!header.classList.contains('is-open')));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && header.classList.contains('is-open')) {
            setNavOpen(false);
            navToggle.focus();
        }
    });
    // -> Klick ausserhalb des Headers schliesst das Menü
    document.addEventListener('click', (event) => {
        if (!header.contains(event.target)) {
            setNavOpen(false);
        }
    });
}

// -> Kontakt-Overlay öffnen und schliessen
const contactDialog = document.querySelector('[data-layout="contact"]');

if (contactDialog) {
    const occasionField = contactDialog.querySelector('[name="occasion"]');
    document.querySelectorAll('[data-contact-open]').forEach((button) => {
        button.addEventListener('click', () => {
            // -> Buttons mit data-occasion (z.B. auf der Angebotsseite) wählen den Anlass vor
            if (occasionField && button.dataset.occasion) {
                occasionField.value = button.dataset.occasion;
            }
            contactDialog.showModal();
            document.documentElement.classList.add('is-locked');
        });
    });
    contactDialog.addEventListener('close', () => {
        document.documentElement.classList.remove('is-locked');
    });
    contactDialog.querySelectorAll('[data-contact-close]').forEach((button) => {
        button.addEventListener('click', () => contactDialog.close());
    });
    // -> Klick neben das Overlay (auf den Hintergrund) schliesst es
    contactDialog.addEventListener('click', (event) => {
        const rect = contactDialog.getBoundingClientRect();
        const outside = event.clientX < rect.left || event.clientX > rect.right
            || event.clientY < rect.top || event.clientY > rect.bottom;
        if (outside) {
            contactDialog.close();
        }
    });
}

// -> Kontaktformular: Felder prüfen, Fehler direkt beim Feld zeigen und per fetch senden
const contactMessages = {
    name: 'Bitte gib deinen Namen an.',
    contact: 'Bitte gib eine E-Mail oder Telefonnummer an.',
    message: 'Bitte schreib mir eine kurze Nachricht.',
};

// -> Gültig ist eine E-Mail-Adresse oder eine Telefonnummer mit 7 bis 15 Ziffern
const isValidContact = (value) => {
    if (/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value)) {
        return true;
    }
    const digits = value.replace(/\D/g, '');
    return /^[0-9+\s()/.-]+$/.test(value) && digits.length >= 7 && digits.length <= 15;
};

document.querySelectorAll('[data-contact-form]').forEach((form) => {
    const status = form.querySelector('.contact-status');
    const submit = form.querySelector('.button-submit');
    const submitLabel = submit.textContent;

    const setFieldError = (name, message) => {
        const input = form.elements[name];
        const field = input?.closest('.contact-field');
        if (!field) {
            return;
        }
        field.classList.toggle('is-invalid', Boolean(message));
        input.setAttribute('aria-invalid', String(Boolean(message)));
        field.querySelector('.contact-error').textContent = message || '';
    };

    const validate = () => {
        const errors = {};
        Object.keys(contactMessages).forEach((name) => {
            const value = form.elements[name].value.trim();
            const valid = name === 'contact' ? isValidContact(value) : value !== '';
            if (!valid) {
                errors[name] = contactMessages[name];
            }
            setFieldError(name, errors[name]);
        });
        return errors;
    };

    // -> Nach dem ersten Fehler wird beim Tippen laufend neu geprüft
    form.addEventListener('input', (event) => {
        const name = event.target.name;
        if (contactMessages[name] && event.target.closest('.is-invalid')) {
            const value = event.target.value.trim();
            const valid = name === 'contact' ? isValidContact(value) : value !== '';
            setFieldError(name, valid ? '' : contactMessages[name]);
        }
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const errors = validate();
        if (Object.keys(errors).length) {
            status.dataset.state = 'error';
            status.textContent = 'Bitte prüfe die markierten Felder.';
            form.elements[Object.keys(errors)[0]].focus();
            return;
        }
        form.classList.add('is-sending');
        status.textContent = '';
        try {
            // -> getAttribute, weil das versteckte Feld "action" die Eigenschaft form.action überdeckt
            const response = await fetch(form.getAttribute('action'), {
                method: 'POST',
                body: new FormData(form),
                credentials: 'same-origin',
            });
            // -> Antwortet der Server nicht mit JSON, greift die allgemeine Fehlermeldung
            const result = await response.json().catch(() => ({}));
            if (!result.success) {
                Object.entries(result.data?.fields ?? {}).forEach(([name, message]) => setFieldError(name, message));
                throw new Error(result.data?.message);
            }
            form.reset();
            form.classList.add('is-sent');
            submit.textContent = 'Gesendet';
            status.dataset.state = 'success';
            status.textContent = result.data?.message ?? '';
            setTimeout(() => {
                form.classList.remove('is-sent');
                submit.textContent = submitLabel;
            }, 4000);
        } catch (error) {
            status.dataset.state = 'error';
            status.textContent = error.message || 'Die Nachricht konnte nicht gesendet werden.';
        } finally {
            form.classList.remove('is-sending');
        }
    });
});

// -> Sammlung: bei jedem Besuch neu gemischtes Raster mit Reihen aus 2, 3 oder 4 Projekten
// Das Raster hat 12 Spalten; jedes Muster beschreibt die Breiten einer Reihe.
const rowPatterns = {
    2: [[8, 4], [4, 8], [6, 6], [7, 5], [5, 7]],
    3: [[4, 4, 4], [6, 3, 3], [3, 6, 3], [3, 3, 6], [5, 4, 3], [3, 4, 5]],
    4: [[3, 3, 3, 3]],
};
const priorityWeight = { high: 3, normal: 2, low: 1 };

// -> Zufallszahlen mit festem Startwert: während eines Besuchs bleibt das Raster beim Filtern gleich gemischt
const createRandom = (seed) => () => {
    seed = (seed + 0x6d2b79f5) | 0;
    let t = Math.imul(seed ^ (seed >>> 15), 1 | seed);
    t = (t + Math.imul(t ^ (t >>> 7), 61 | t)) ^ t;
    return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
};
const pick = (random, list) => list[Math.floor(random() * list.length)];
const collectionSeed = Math.floor(Math.random() * 2 ** 31);

const layoutCollection = (grid) => {
    const random = createRandom(collectionSeed);
    // -> Auf dem Tablet höchstens 3 Projekte pro Reihe
    const maxCount = window.matchMedia('(max-width: 1024px)').matches ? 3 : 4;
    const teasers = [...grid.querySelectorAll('.project-teaser:not([hidden])')];
    let index = 0;
    let lastCount = 0;

    while (index < teasers.length) {
        const remaining = teasers.length - index;
        let options = [2, 3, 4].filter((count) => count <= Math.min(maxCount, remaining) && count !== lastCount);
        // -> Nie ein einzelnes Projekt übrig lassen
        options = options.filter((count) => remaining - count !== 1);
        if (!options.length) {
            options = [Math.min(remaining, maxCount)];
        }
        let count = pick(random, options);
        let row = teasers.slice(index, index + count);
        // -> Ein Projekt mit hoher Priorität kommt nie in eine Viererreihe
        if (count === 4 && row.some((teaser) => teaser.dataset.priority === 'high')) {
            count = remaining - 3 === 1 ? 2 : 3;
            row = teasers.slice(index, index + count);
        }

        let widths = [12];
        if (count > 1) {
            // -> Das Muster wählen, bei dem wichtige Projekte die breitesten Felder bekommen
            const score = (pattern) => pattern.reduce((sum, width, i) => sum + width * priorityWeight[row[i].dataset.priority || 'normal'], 0);
            const patterns = rowPatterns[count];
            const best = Math.max(...patterns.map(score));
            widths = pick(random, patterns.filter((pattern) => score(pattern) === best));
        }
        row.forEach((teaser, i) => teaser.style.setProperty('--span', widths[i]));

        index += count;
        lastCount = count;
    }
};

document.querySelectorAll('[data-module="collection"]').forEach((collection) => {
    const grid = collection.querySelector('.collection-grid');
    const filter = collection.querySelector('.collection-filter');
    const teasers = [...grid.querySelectorAll('.project-teaser')];

    teasers.forEach((teaser) => {
        teaser.style.viewTransitionName = `project-${teaser.dataset.id}`;
    });
    layoutCollection(grid);
    // -> Beim Wechsel zwischen Tablet und Desktop neu anordnen
    window.matchMedia('(max-width: 1024px)').addEventListener('change', () => layoutCollection(grid));

    if (!filter) {
        return;
    }
    const buttons = [...filter.querySelectorAll('[data-filter]')];
    // -> Mehrere Kategorien wählbar: gezeigt wird jedes Projekt, das in mindestens einer davon ist
    // -> Auswahl aus der URL übernehmen, z.B. ?kategorie=hochzeiten,video
    const slugs = buttons.map((button) => button.dataset.filter).filter(Boolean);
    const fromUrl = (new URL(location.href).searchParams.get('kategorie') ?? '').split(',');
    const selected = new Set(fromUrl.filter((slug) => slugs.includes(slug)));

    const applyFilter = () => {
        buttons.forEach((button) => {
            const slug = button.dataset.filter;
            const pressed = slug ? selected.has(slug) : selected.size === 0;
            button.setAttribute('aria-pressed', String(pressed));
        });
        teasers.forEach((teaser) => {
            const categories = teaser.dataset.categories.split(' ');
            teaser.hidden = selected.size > 0 && !categories.some((slug) => selected.has(slug));
        });
        layoutCollection(grid);
        // -> Gefilterte Ansicht in die URL schreiben, damit man sie teilen kann
        const url = new URL(location.href);
        if (selected.size) {
            url.searchParams.set('kategorie', [...selected].join(','));
        } else {
            url.searchParams.delete('kategorie');
        }
        history.replaceState(null, '', url);
    };
    if (selected.size) {
        applyFilter();
    }

    buttons.forEach((button) => {
        button.addEventListener('click', () => {
            // -> "Alle" leert die Auswahl, ein Klick auf eine aktive Kategorie wählt sie wieder ab
            const slug = button.dataset.filter;
            if (!slug) {
                selected.clear();
            } else if (selected.has(slug)) {
                selected.delete(slug);
            } else {
                selected.add(slug);
            }
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (document.startViewTransition && !reduceMotion) {
                document.startViewTransition(applyFilter);
            } else {
                applyFilter();
            }
        });
    });
});

// -> Karussell: das Bild in der Mitte wird grösser dargestellt
document.querySelectorAll('[data-module="carousel"] .carousel-track').forEach((track) => {
    const items = [...track.querySelectorAll('.carousel-item')];

    const centerItem = (item, behavior = 'smooth') => {
        const trackRect = track.getBoundingClientRect();
        const itemRect = item.getBoundingClientRect();
        const offset = itemRect.left + itemRect.width / 2 - (trackRect.left + trackRect.width / 2);
        track.scrollBy({ left: offset, behavior });
    };

    const updateActive = () => {
        const trackRect = track.getBoundingClientRect();
        const center = trackRect.left + trackRect.width / 2;
        let closest = items[0];
        let closestDistance = Infinity;
        items.forEach((item) => {
            const rect = item.getBoundingClientRect();
            const distance = Math.abs(rect.left + rect.width / 2 - center);
            if (distance < closestDistance) {
                closest = item;
                closestDistance = distance;
            }
        });
        items.forEach((item) => item.classList.toggle('is-active', item === closest));
    };

    let frame = null;
    track.addEventListener('scroll', () => {
        cancelAnimationFrame(frame);
        frame = requestAnimationFrame(updateActive);
    }, { passive: true });
    items.forEach((item) => {
        item.addEventListener('click', () => centerItem(item));
    });
    track.addEventListener('keydown', (event) => {
        const active = items.findIndex((item) => item.classList.contains('is-active'));
        if (event.key === 'ArrowRight' && items[active + 1]) {
            event.preventDefault();
            centerItem(items[active + 1]);
        }
        if (event.key === 'ArrowLeft' && items[active - 1]) {
            event.preventDefault();
            centerItem(items[active - 1]);
        }
    });

    centerItem(items[Math.floor(items.length / 2)], 'instant');
    updateActive();
});

// -> M15: Bilder der Projektseite mobil antippen = Vollbild, wischen zum nächsten Bild
const lightbox = document.querySelector('[data-layout="lightbox"]');
const lightboxImages = [...document.querySelectorAll('[data-page="project"] :is(.gallery-image, .carousel-image)')];
const isMobile = window.matchMedia('(max-width: 768px)');

if (lightbox && lightboxImages.length) {
    const track = lightbox.querySelector('.lightbox-track');
    // -> Grösste Variante aus dem srcset verwenden
    const largestSource = (image) => {
        const candidates = (image.getAttribute('srcset') || '').split(',').map((entry) => {
            const [url, width] = entry.trim().split(/\s+/);
            return { url, width: parseInt(width, 10) || 0 };
        }).filter((candidate) => candidate.url);
        return candidates.sort((a, b) => b.width - a.width)[0]?.url || image.currentSrc || image.src;
    };

    lightboxImages.forEach((image, index) => {
        const item = document.createElement('figure');
        item.className = 'lightbox-item';
        const copy = document.createElement('img');
        copy.alt = image.alt;
        copy.dataset.src = largestSource(image);
        item.append(copy);
        track.append(item);

        // -> Capture-Phase: auf dem Handy öffnet das Antippen die Ansicht statt das Karussell zu verschieben
        image.closest('.gallery-item, .carousel-item').addEventListener('click', (event) => {
            if (!isMobile.matches) {
                return;
            }
            event.stopPropagation();
            track.querySelectorAll('img').forEach((img) => {
                img.src ||= img.dataset.src;
            });
            lightbox.showModal();
            // -> Erst nach dem Öffnen hat die Spur ihre Breite, dann zum angetippten Bild springen
            requestAnimationFrame(() => {
                track.scrollTo({ left: track.children[index].offsetLeft, behavior: 'instant' });
            });
        }, { capture: true });
    });

    lightbox.querySelector('[data-lightbox-close]').addEventListener('click', () => lightbox.close());
    track.addEventListener('keydown', (event) => {
        const direction = { ArrowRight: 1, ArrowLeft: -1 }[event.key];
        if (direction) {
            event.preventDefault();
            track.scrollBy({ left: track.clientWidth * direction, behavior: 'smooth' });
        }
    });
}

// -> Startseite: Titel "Ciao, ich bin Ismael" beim ersten Besuch Buchstabe für Buchstabe eintippen
const typingHeading = document.documentElement.classList.contains('is-typing')
    && document.querySelector('[data-module="intro"] .intro-heading');

if (typingHeading) {
    const parts = [...typingHeading.querySelectorAll('.greeting, .intro, .name, .subtitle')];
    // -> Tempo pro Teil in ms pro Buchstabe: der Name wird bewusst langsamer getippt
    const speeds = { greeting: 65, intro: 65, name: 140, subtitle: 55 };
    const steps = [];
    parts.forEach((part) => {
        const text = part.textContent;
        // -> Screenreader lesen den ganzen Teil, nicht die einzelnen Buchstaben
        part.setAttribute('aria-label', text);
        part.textContent = '';
        [...text].forEach((letter, index) => {
            const char = document.createElement('span');
            char.className = 'char';
            char.setAttribute('aria-hidden', 'true');
            char.textContent = letter;
            part.append(char);
            steps.push({ char, speed: speeds[part.className] ?? 65, pause: index === text.length - 1 });
        });
    });
    document.documentElement.classList.replace('is-typing', 'is-typing-js');

    // -> Kurze Pause nach jedem Teil
    let delay = 300;
    steps.forEach(({ char, speed, pause }, index) => {
        setTimeout(() => {
            steps[index - 1]?.char.classList.remove('is-caret');
            char.classList.add('is-typed', 'is-caret');
        }, delay);
        delay += pause ? 320 : speed;
    });
    setTimeout(() => {
        steps.at(-1)?.char.classList.remove('is-caret');
    }, delay + 900);
}

// -> A1: Inhalte gleiten beim Scrollen herein (nur Elemente, die beim Laden noch nicht sichtbar sind)
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const revealSelector = [
    '.project-highlight', '.project-teaser', '.story-item', '.offer', '.process-phase', '.faq-item',
    '.gallery-item', '.video-frame', '.reel', '.testimonial', '.section-header', '.cta',
    '[data-module="text"] .text-content', '[data-module="facts"]', '.project-next',
].join(', ');

if (!reduceMotion && 'IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
        // -> Was gleichzeitig erscheint, kommt leicht versetzt nacheinander
        entries.filter((entry) => entry.isIntersecting).forEach((entry, index) => {
            const element = entry.target;
            element.style.setProperty('--reveal-delay', `${Math.min(index, 3) * 0.12}s`);
            element.classList.add('is-visible');
            observer.unobserve(element);
            // -> Danach die Hilfsklassen entfernen, damit Hover-Effekte wieder normal reagieren
            setTimeout(() => {
                element.classList.remove('reveal', 'is-visible');
                element.style.removeProperty('--reveal-delay');
            }, 2000);
        });
        // -> Schon beim Hereinkommen starten, damit man das Gleiten sieht
    }, { rootMargin: '0px 0px -4% 0px' });

    document.querySelectorAll(revealSelector).forEach((element) => {
        if (element.getBoundingClientRect().top < window.innerHeight) {
            return;
        }
        element.classList.add('reveal');
        observer.observe(element);
    });
}

// -> B2: Header am Handy beim Runterscrollen ausblenden, beim Hochscrollen wieder zeigen
if (header && !reduceMotion) {
    let lastY = window.scrollY;
    let ticking = false;
    window.addEventListener('scroll', () => {
        if (ticking) {
            return;
        }
        ticking = true;
        requestAnimationFrame(() => {
            const y = window.scrollY;
            const hide = isMobile.matches && y > lastY && y > header.offsetHeight && !header.classList.contains('is-open');
            header.classList.toggle('is-hidden', hide);
            lastY = y;
            ticking = false;
        });
    }, { passive: true });
}
