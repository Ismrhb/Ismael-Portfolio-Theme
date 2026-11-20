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
    // -> Per Delegation, damit auch kopierte Elemente (Endlos-Bänder) das Formular öffnen
    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-contact-open]');
        // -> In einer seitlichen Angebotskarte holt der Klick erst die Karte in die Mitte
        if (!button || button.closest('.is-loop .offer:not(.is-active)')) {
            return;
        }
        // -> Buttons mit data-occasion (z.B. auf der Angebotsseite) wählen den Anlass vor
        if (occasionField && button.dataset.occasion) {
            occasionField.value = button.dataset.occasion;
        }
        contactDialog.showModal();
        document.documentElement.classList.add('is-locked');
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

// -> Projekte-Übersicht: bei jedem Besuch neu gemischtes Raster mit Reihen aus 2, 3 oder 4 Projekten
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

    layoutCollection(grid);
    // -> Erst jetzt zeigen: so springen die Kacheln beim Laden nicht an neue Plätze
    grid.removeAttribute('data-pending');
    // -> Beim Wechsel zwischen Tablet und Desktop neu anordnen
    window.matchMedia('(max-width: 1024px)').addEventListener('change', () => layoutCollection(grid));

    if (!filter) {
        return;
    }
    const buttons = [...filter.querySelectorAll('[data-filter]')];
    const tools = collection.querySelector('.collection-tools');
    const toggle = collection.querySelector('.filter-toggle');
    // -> Filter-Symbol: Kategorien ein- und ausblenden
    const setFilterOpen = (open) => {
        filter.hidden = !open;
        tools.classList.toggle('is-open', open);
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? 'Filter ausblenden' : 'Filter einblenden');
    };
    toggle.addEventListener('click', () => setFilterOpen(filter.hidden));
    // -> Immer nur eine Kategorie aktiv (oder "Alle"); Auswahl aus der URL übernehmen, z.B. ?kategorie=video
    const slugs = buttons.map((button) => button.dataset.filter).filter(Boolean);
    const fromUrl = (new URL(location.href).searchParams.get('kategorie') ?? '').split(',')[0];
    const selected = new Set(slugs.includes(fromUrl) ? [fromUrl] : []);

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
        // -> Grüner Punkt am Symbol, wenn eine Kategorie aktiv ist
        tools.classList.toggle('has-filter', selected.size > 0);
        // -> Gefilterte Ansicht in die URL schreiben, damit man sie teilen kann
        const url = new URL(location.href);
        if (selected.size) {
            url.searchParams.set('kategorie', [...selected].join(','));
        } else {
            url.searchParams.delete('kategorie');
        }
        history.replaceState(null, '', url);
    };
    // -> Mit einer Kategorie in der Adresse (z.B. ?kategorie=video) ist der Filter gleich offen
    if (selected.size) {
        applyFilter();
        setFilterOpen(true);
    }

    // -> Filtern: Raster kurz ausblenden, neu anordnen, dann gleiten die Bilder nacheinander herein
    // (ohne Verzerren; das Raster ändert sich ja komplett)
    let filterTimer = null;
    const animateFilter = () => {
        if (reduceMotion) {
            applyFilter();
            return;
        }
        clearTimeout(filterTimer);
        grid.classList.add('is-filtering');
        filterTimer = setTimeout(() => {
            applyFilter();
            const visible = teasers.filter((teaser) => !teaser.hidden);
            visible.forEach((teaser, index) => {
                teaser.style.setProperty('--enter-delay', `${Math.min(index, 8) * 0.05}s`);
                teaser.classList.add('is-entering');
            });
            grid.classList.remove('is-filtering');
            requestAnimationFrame(() => requestAnimationFrame(() => {
                visible.forEach((teaser) => teaser.classList.remove('is-entering'));
            }));
        }, 220);
    };

    buttons.forEach((button) => {
        button.addEventListener('click', () => {
            // -> Eine andere Kategorie ersetzt die aktive; "Alle" oder ein zweiter Klick auf die aktive zeigt wieder alles
            const slug = button.dataset.filter;
            const wasActive = selected.has(slug);
            selected.clear();
            if (slug && !wasActive) {
                selected.add(slug);
            }
            animateFilter();
        });
    });
});

// -> Endloses Band (Bild-Karussell und "Andere Projekte"): das Element in der Mitte ist aktiv,
// die Nachbarn bekommen data-distance (1, 2, 3) für Grösse und Unschärfe im CSS.
// Klick auf ein seitliches Element holt es in die Mitte; bei Links öffnet erst der Klick auf das mittlere.
const setupLoopTrack = (track, itemSelector, startIndex = null) => {
    const originals = [...track.querySelectorAll(itemSelector)];
    originals.forEach((item, index) => {
        item.dataset.index = index;
    });
    // -> Für den Endloseffekt je eine Kopie aller Elemente vor und nach den Originalen
    if (originals.length > 2) {
        const makeCopies = () => originals.map((item) => {
            const copy = item.cloneNode(true);
            copy.dataset.clone = item.dataset.index;
            copy.setAttribute('aria-hidden', 'true');
            copy.tabIndex = -1;
            return copy;
        });
        track.prepend(...makeCopies());
        track.append(...makeCopies());
    }
    const items = [...track.querySelectorAll(itemSelector)];

    const centerItem = (item, behavior = 'smooth') => {
        const trackRect = track.getBoundingClientRect();
        const itemRect = item.getBoundingClientRect();
        const offset = itemRect.left + itemRect.width / 2 - (trackRect.left + trackRect.width / 2);
        track.scrollBy({ left: offset, behavior });
    };

    const activeIndex = () => {
        const trackRect = track.getBoundingClientRect();
        const center = trackRect.left + trackRect.width / 2;
        let closest = 0;
        let closestDistance = Infinity;
        items.forEach((item, index) => {
            const rect = item.getBoundingClientRect();
            const distance = Math.abs(rect.left + rect.width / 2 - center);
            if (distance < closestDistance) {
                closest = index;
                closestDistance = distance;
            }
        });
        return closest;
    };

    const updateActive = () => {
        const active = activeIndex();
        items.forEach((item, index) => {
            const distance = Math.min(Math.abs(index - active), 3);
            item.classList.toggle('is-active', distance === 0);
            item.dataset.distance = distance;
        });
    };

    // -> Steht eine Kopie in der Mitte, unbemerkt um eine Runde zurück zu den Originalen springen
    const recenter = () => {
        const active = items[activeIndex()];
        if (!active || active.dataset.clone === undefined) {
            return;
        }
        const loop = originals[0].offsetLeft - items[0].offsetLeft;
        const before = items.indexOf(active) < originals.length;
        track.scrollBy({ left: before ? loop : -loop, behavior: 'instant' });
        updateActive();
    };

    let frame = null;
    let settle = null;
    track.addEventListener('scroll', () => {
        cancelAnimationFrame(frame);
        frame = requestAnimationFrame(updateActive);
        clearTimeout(settle);
        settle = setTimeout(recenter, 150);
    }, { passive: true });
    items.forEach((item) => {
        item.addEventListener('click', (event) => {
            if (item.matches('a') && item.classList.contains('is-active')) {
                return;
            }
            event.preventDefault();
            centerItem(item);
        });
    });
    track.addEventListener('keydown', (event) => {
        const direction = { ArrowRight: 1, ArrowLeft: -1 }[event.key];
        const next = items[activeIndex() + (direction ?? 0)];
        if (direction && next) {
            event.preventDefault();
            centerItem(next);
        }
    });

    // -> Startposition: angegebenes Element, sonst das mittlere. Direkt über die Position setzen und das Einrasten
    // kurz ausschalten, sonst rastet der Browser beim ersten Laden teilweise ein Element daneben ein.
    const start = originals[startIndex ?? Math.floor(originals.length / 2)] ?? items[0];
    const snap = track.style.scrollSnapType;
    track.style.scrollSnapType = 'none';
    track.scrollLeft = start.offsetLeft - items[0].offsetLeft + parseFloat(getComputedStyle(track).paddingLeft) + start.offsetWidth / 2 - track.clientWidth / 2;
    updateActive();
    requestAnimationFrame(() => {
        track.style.scrollSnapType = snap;
    });
};

document.querySelectorAll('[data-module="carousel"] .carousel-track').forEach((track) => setupLoopTrack(track, '.carousel-item'));
// -> Bei "Andere Projekte" steht das nächste Projekt zuerst in der Mitte
document.querySelectorAll('.more-projects-track').forEach((track) => setupLoopTrack(track, '.more-project', 0));

// -> Angebotskarten (Handy): eine Karte gross in der Mitte, die Nachbarn leicht verschwommen, endlos im Kreis.
// Auf dem Desktop bleiben die drei Karten nebeneinander (die Kopien blendet das CSS dort aus).
const offersList = document.querySelector('[data-module="offers"] .offers-list');
const offersMobile = window.matchMedia('(max-width: 768px)');
const setupOffers = () => {
    if (offersList && offersMobile.matches && !offersList.classList.contains('is-loop')) {
        offersList.classList.add('is-loop');
        setupLoopTrack(offersList, '.offer', 0);
    }
};
setupOffers();
offersMobile.addEventListener('change', setupOffers);

// -> Bildansicht: Bilder der Projektseite anklicken = gross ansehen und durchklicken
// (Desktop: Zurück/Weiter und Pfeiltasten; Handy: wischen). Im Karussell öffnet auf dem Desktop nur das
// mittlere Bild die Ansicht, ein Klick auf ein seitliches holt es wie bisher in die Mitte.
const lightbox = document.querySelector('[data-layout="lightbox"]');
const lightboxImages = [...document.querySelectorAll('[data-page="project"] :is(.gallery-image, .image-module-image, .carousel-item:not([data-clone]) .carousel-image)')];
const isMobile = window.matchMedia('(max-width: 768px)');

if (lightbox && lightboxImages.length) {
    const track = lightbox.querySelector('.lightbox-track');
    const count = lightbox.querySelector('.lightbox-count');
    // -> Grösste Variante aus dem srcset verwenden
    const largestSource = (image) => {
        const candidates = (image.getAttribute('srcset') || '').split(',').map((entry) => {
            const [url, width] = entry.trim().split(/\s+/);
            return { url, width: parseInt(width, 10) || 0 };
        }).filter((candidate) => candidate.url);
        return candidates.sort((a, b) => b.width - a.width)[0]?.url || image.currentSrc || image.src;
    };
    const currentIndex = () => Math.round(track.scrollLeft / Math.max(track.clientWidth, 1));
    const updateCount = () => {
        count.textContent = `${currentIndex() + 1} / ${lightboxImages.length}`;
    };
    // -> Am Ende geht es wieder von vorne los
    const goTo = (index, behavior = 'smooth') => {
        const total = lightboxImages.length;
        const target = (index + total) % total;
        const jump = Math.abs(target - currentIndex()) > 1 ? 'instant' : behavior;
        track.scrollTo({ left: track.children[target].offsetLeft, behavior: jump });
    };

    lightboxImages.forEach((image, index) => {
        const item = document.createElement('figure');
        item.className = 'lightbox-item';
        const copy = document.createElement('img');
        copy.alt = image.alt;
        copy.dataset.src = largestSource(image);
        item.append(copy);
        track.append(item);

        const open = (event) => {
            const carouselItem = event.currentTarget.closest('.carousel-item');
            if (carouselItem && !isMobile.matches && !carouselItem.classList.contains('is-active')) {
                return;
            }
            event.stopPropagation();
            track.querySelectorAll('img').forEach((img) => {
                img.src ||= img.dataset.src;
            });
            lightbox.showModal();
            // -> Erst nach dem Öffnen hat die Spur ihre Breite, dann zum angeklickten Bild springen
            requestAnimationFrame(() => {
                goTo(index, 'instant');
                updateCount();
                track.focus({ preventScroll: true });
            });
        };
        // -> Capture-Phase: das Antippen öffnet die Ansicht statt das Karussell zu verschieben.
        // Im Endlos-Karussell öffnen auch die Kopien eines Bildes dieselbe Ansicht.
        const original = image.closest('.gallery-item, .carousel-item, [data-module="image"]');
        const copies = original.matches('.carousel-item')
            ? [...original.parentElement.querySelectorAll(`[data-clone="${original.dataset.index}"]`)]
            : [];
        [original, ...copies].forEach((target) => target.addEventListener('click', open, { capture: true }));
    });

    lightbox.querySelector('[data-lightbox-close]').addEventListener('click', () => lightbox.close());
    lightbox.querySelectorAll('[data-lightbox-step]').forEach((button) => {
        button.addEventListener('click', () => goTo(currentIndex() + Number(button.dataset.lightboxStep)));
    });
    // -> Klick neben das Bild (auf die dunkle Fläche) schliesst die Ansicht
    track.addEventListener('click', (event) => {
        if (event.target.classList.contains('lightbox-item')) {
            lightbox.close();
        }
    });
    track.addEventListener('scroll', updateCount, { passive: true });
    lightbox.addEventListener('keydown', (event) => {
        const direction = { ArrowRight: 1, ArrowLeft: -1 }[event.key];
        if (direction) {
            event.preventDefault();
            goTo(currentIndex() + direction);
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

// -> Inhalte gleiten beim Scrollen herein (nur Elemente, die beim Laden noch nicht sichtbar sind)
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const revealSelector = [
    '.project-highlight', '.project-teaser', '.story-item', '.offer', '.process-phase', '.faq-item',
    '.gallery-item', '.video-frame', '.reel', '.image-module-image', '.testimonial', '.section-header', '.cta',
    '[data-module="text"] .text-content', '[data-module="facts"]', '.more-projects',
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

// -> Header am Handy beim Runterscrollen ausblenden, beim Hochscrollen wieder zeigen
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

// -> Titelvideos der Projekte: nur abspielen, solange sie sichtbar sind (spart Leistung und Daten).
// Bei "Bewegung reduzieren" oder aktivem Datensparmodus bleibt das Titelbild stehen.
let teaserVideoObserver = null;
const observeTeaserVideos = (root = document) => {
    if (reduceMotion || navigator.connection?.saveData) {
        return;
    }
    teaserVideoObserver ??= new IntersectionObserver((entries) => {
        entries.forEach(({ target, isIntersecting }) => {
            if (isIntersecting) {
                target.play().catch(() => {});
            } else {
                target.pause();
            }
        });
    }, { rootMargin: '200px 0px' });
    root.querySelectorAll('[data-teaser-video]').forEach((video) => teaserVideoObserver.observe(video));
};
observeTeaserVideos();

// -> Startseite Desktop: Projekte laufen rechts als endloses Band langsam nach oben.
// Maus darüber = Pause, Mausrad bewegt das Band von Hand. Mobil und bei "Bewegung reduzieren" normale Liste.
const marqueeBox = document.querySelector('[data-module="intro"] .intro-projects');
const marqueeDesktop = window.matchMedia('(min-width: 769px)');

if (marqueeBox && !reduceMotion) {
    const originals = [...marqueeBox.querySelectorAll('.project-highlight')];
    const speed = 35;
    let track = null;
    let offset = 0;
    let loop = 0;
    let paused = false;
    let last = 0;
    let frame = null;

    // -> Kopie eines Projekts: für Screenreader und Tastatur unsichtbar, ohne Namen für Seitenübergänge
    const cloneProject = (project) => {
        const copy = project.cloneNode(true);
        copy.classList.remove('reveal', 'is-visible');
        copy.setAttribute('aria-hidden', 'true');
        copy.querySelectorAll('a').forEach((link) => { link.tabIndex = -1; });
        copy.querySelectorAll('[style*="view-transition-name"]').forEach((element) => {
            element.style.removeProperty('view-transition-name');
        });
        observeTeaserVideos(copy);
        return copy;
    };

    // -> Länge einer Runde messen und so viele Kopien anhängen, dass das Band nie leer wird
    const measure = () => {
        if (!track) {
            return;
        }
        loop = track.children[originals.length].offsetTop - track.children[0].offsetTop;
        while (loop && track.scrollHeight < loop + marqueeBox.clientHeight * 1.5) {
            originals.forEach((project) => track.append(cloneProject(project)));
        }
    };

    const tick = (time) => {
        const delta = last ? Math.min((time - last) / 1000, 0.1) : 0;
        last = time;
        if (!paused) {
            offset += speed * delta;
        }
        if (loop) {
            offset = ((offset % loop) + loop) % loop;
        }
        track.style.transform = `translateY(${-offset}px)`;
        frame = requestAnimationFrame(tick);
    };

    const start = () => {
        if (track || originals.length < 2) {
            return;
        }
        track = document.createElement('div');
        track.className = 'intro-marquee-track';
        originals.forEach((project) => {
            project.classList.remove('reveal', 'is-visible');
            track.append(project);
        });
        originals.forEach((project) => track.append(cloneProject(project)));
        marqueeBox.querySelector('h2').after(track);
        marqueeBox.classList.add('is-marquee');
        requestAnimationFrame(() => {
            measure();
            last = 0;
            frame = requestAnimationFrame(tick);
        });
    };

    const stop = () => {
        if (!track) {
            return;
        }
        cancelAnimationFrame(frame);
        originals.forEach((project) => track.before(project));
        track.remove();
        track = null;
        offset = 0;
        marqueeBox.classList.remove('is-marquee');
    };

    marqueeBox.addEventListener('mouseenter', () => { paused = true; });
    marqueeBox.addEventListener('mouseleave', () => { paused = false; });
    marqueeBox.addEventListener('focusin', () => { paused = true; });
    marqueeBox.addEventListener('focusout', () => { paused = false; });
    marqueeBox.addEventListener('wheel', (event) => {
        if (track) {
            event.preventDefault();
            offset += event.deltaY;
        }
    }, { passive: false });
    // -> Bilder laden nach und ändern die Höhe: Runde neu messen
    new ResizeObserver(measure).observe(marqueeBox);
    window.addEventListener('load', measure);

    if (marqueeDesktop.matches) {
        start();
    }
    marqueeDesktop.addEventListener('change', () => (marqueeDesktop.matches ? start() : stop()));
}

// -> Seitenwechsel zu einem Projekt: nur das angeklickte Bild gleitet ins Titelbild der Projektseite.
// Die anderen Projektbilder bekommen keinen Übergangsnamen, damit sie nicht ineinander verschwimmen.
window.addEventListener('pageswap', (event) => {
    document.querySelectorAll('[data-vt-project]').forEach((element) => {
        element.style.removeProperty('view-transition-name');
        delete element.dataset.vtProject;
    });
    const url = event.activation?.entry?.url;
    if (!event.viewTransition || !url || !url.includes('/projekt/')) {
        return;
    }
    // -> Im Projekt-Band der Startseite gibt es Kopien: das sichtbare angeklickte Bild nehmen
    const link = [...document.querySelectorAll('.project-teaser-link, .project-highlight-link')].find((candidate) => {
        const rect = candidate.getBoundingClientRect();
        return candidate.href === url && rect.bottom > 0 && rect.top < window.innerHeight;
    });
    const picture = link?.querySelector('picture');
    if (picture) {
        picture.style.viewTransitionName = 'project-image';
        picture.dataset.vtProject = '';
    }
});

// -> Kontakt-Buttons: beim Darüberfahren wechselt «Kontakt» durch kurze Wörter (Globales → Kontaktformular),
// z.B. «Kaffee?», «Telefon?», «Frage?». Alle Wörter liegen übereinander, so bleibt der Button gleich breit.
const contactWords = (document.body.dataset.contactWords || '').split('|').filter(Boolean);
const wordDelay = 1000;

const buildWordSwap = (element, words) => {
    const box = document.createElement('span');
    box.className = 'word-swap';
    box.setAttribute('aria-hidden', 'true');
    box.append(...words.map((word, index) => {
        const span = document.createElement('span');
        span.textContent = word;
        span.classList.toggle('is-active', index === 0);
        return span;
    }));
    element.replaceChildren(box);
    return [...box.children];
};

// -> Zeigt Wort Nummer index (0 = Ausgangstext)
const showWord = (spans, index) => {
    spans.forEach((span, position) => {
        // -> Das bisherige Wort gleitet nach oben weg, alle anderen warten unsichtbar unten
        span.classList.toggle('is-leaving', span.classList.contains('is-active') && position !== index);
        span.classList.toggle('is-active', position === index);
    });
};

if (contactWords.length) {
    // -> Buttons: nur mit Maus; der Ausgangstext bleibt als Name für Screenreader
    if (window.matchMedia('(hover: hover)').matches) {
        document.querySelectorAll('.button-contact[data-contact-open]').forEach((button) => {
            const label = button.textContent.trim();
            button.setAttribute('aria-label', label);
            const spans = buildWordSwap(button, [label, ...contactWords]);
            let timer = null;
            let index = 0;
            button.addEventListener('mouseenter', () => {
                index = 1;
                showWord(spans, index);
                timer = setInterval(() => {
                    index = index % contactWords.length + 1;
                    showWord(spans, index);
                }, wordDelay);
            });
            button.addEventListener('mouseleave', () => {
                clearInterval(timer);
                showWord(spans, 0);
            });
        });
    }

    // -> Porträt auf der Startseite: Wörter wechseln beim Darüberfahren, auf dem Handy (Hinweis immer sichtbar) von selbst
    const portrait = document.querySelector('[data-module="intro"] .intro-portrait');
    const portraitLabel = portrait?.querySelector('.intro-portrait-hover');
    if (portraitLabel && contactWords.length > 1) {
        const spans = buildWordSwap(portraitLabel, contactWords);
        let timer = null;
        let index = 0;
        const start = (delay) => {
            clearInterval(timer);
            timer = setInterval(() => {
                index = (index + 1) % contactWords.length;
                showWord(spans, index);
            }, delay);
        };
        if (window.matchMedia('(hover: hover)').matches) {
            portrait.addEventListener('mouseenter', () => start(wordDelay));
            portrait.addEventListener('mouseleave', () => {
                clearInterval(timer);
                index = 0;
                showWord(spans, 0);
            });
        } else if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            start(wordDelay * 2);
        }
    }

    // -> Titel des Kontaktformulars (Fenster und Baustein): Wörter wechseln von selbst, solange er sichtbar ist.
    // Der ursprüngliche Titel (z.B. «Let’s talk») bleibt für Screenreader erhalten.
    if (contactWords.length > 1 && !reduceMotion) {
        document.querySelectorAll('.contact-title').forEach((title) => {
            const label = title.textContent.trim();
            const spans = buildWordSwap(title, contactWords);
            const hidden = document.createElement('span');
            hidden.className = 'visually-hidden';
            hidden.textContent = label;
            title.prepend(hidden);
            title.querySelector('.word-swap').classList.add('is-start');
            let timer = null;
            let index = 0;
            new IntersectionObserver(([entry]) => {
                clearInterval(timer);
                if (!entry.isIntersecting) {
                    index = 0;
                    showWord(spans, 0);
                    return;
                }
                timer = setInterval(() => {
                    index = (index + 1) % contactWords.length;
                    showWord(spans, index);
                }, wordDelay * 1.5);
            }).observe(title);
        });
    }
}

// -> Eigener Mauszeiger (nur mit Maus/Trackpad): kleiner Punkt, über Klickbarem ein Ring.
// In Eingabefeldern, über Videos (YouTube) und bei offenem Kontaktfenster gilt der normale Zeiger.
const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)');

if (finePointer.matches) {
    const cursor = document.createElement('div');
    cursor.className = 'cursor';
    cursor.setAttribute('aria-hidden', 'true');
    document.body.append(cursor);
    document.documentElement.classList.add('has-cursor');

    const interactive = 'a, button, summary, label, [data-contact-open], .gallery-item, .carousel-item, [data-page="project"] [data-module="image"]';
    const textField = 'input, textarea, select';
    let x = 0;
    let y = 0;
    let frame = null;

    document.addEventListener('mousemove', (event) => {
        x = event.clientX;
        y = event.clientY;
        const target = event.target instanceof Element ? event.target : null;
        cursor.classList.add('is-visible');
        cursor.classList.toggle('is-hover', Boolean(target?.closest(interactive)));
        cursor.classList.toggle('is-text', Boolean(target?.closest(textField)));
        if (!frame) {
            frame = requestAnimationFrame(() => {
                cursor.style.transform = `translate(${x}px, ${y}px)`;
                frame = null;
            });
        }
    }, { passive: true });
    // -> Verlässt die Maus das Fenster oder geht sie in ein Video (iframe), Punkt ausblenden
    document.addEventListener('mouseout', (event) => {
        if (!event.relatedTarget) {
            cursor.classList.remove('is-visible');
        }
    });
}

// -> Saison: wenige kleine Schneeflocken gleiten ganz langsam über die Seite (Schalter in Globales → Saison).
// Creme mit feinem dunklem Schimmer, damit sie auf hellen und dunklen Flächen dezent sichtbar sind.
const snowCanvas = document.querySelector('.snow');

if (snowCanvas && !reduceMotion) {
    const context = snowCanvas.getContext('2d');
    const styles = getComputedStyle(document.documentElement);
    const cream = styles.getPropertyValue('--color-cream').trim() || '#fffeec';
    let width = 0;
    let height = 0;
    let flakes = [];
    let last = performance.now();

    const makeFlake = (anywhere) => ({
        x: Math.random() * width,
        y: anywhere ? Math.random() * height : -10,
        radius: 1 + Math.random() * 1.8,
        speed: 8 + Math.random() * 14,
        sway: 6 + Math.random() * 14,
        phase: Math.random() * Math.PI * 2,
        drift: 0.2 + Math.random() * 0.4,
    });

    const resize = () => {
        const ratio = Math.min(window.devicePixelRatio || 1, 2);
        width = window.innerWidth;
        height = window.innerHeight;
        snowCanvas.width = width * ratio;
        snowCanvas.height = height * ratio;
        context.setTransform(ratio, 0, 0, ratio, 0, 0);
        // -> Wenige Flocken: ca. eine pro 40'000 px² Bildschirmfläche, höchstens 45
        const count = Math.min(45, Math.round((width * height) / 40000));
        flakes = Array.from({ length: count }, () => makeFlake(true));
    };

    const draw = (now) => {
        const delta = Math.min(now - last, 100) / 1000;
        last = now;
        context.clearRect(0, 0, width, height);
        context.fillStyle = cream;
        context.shadowColor = 'rgba(73, 74, 69, 0.45)';
        context.shadowBlur = 2;
        flakes.forEach((flake, index) => {
            flake.y += flake.speed * delta;
            flake.phase += flake.drift * delta;
            const x = flake.x + Math.sin(flake.phase) * flake.sway;
            if (flake.y > height + 10) {
                flakes[index] = makeFlake(false);
            }
            context.beginPath();
            context.arc(x, flake.y, flake.radius, 0, Math.PI * 2);
            context.fill();
        });
        requestAnimationFrame(draw);
    };

    resize();
    window.addEventListener('resize', resize);
    requestAnimationFrame(draw);
}
