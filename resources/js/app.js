import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import AOS from 'aos';
import 'aos/dist/aos.css';
import confetti from 'canvas-confetti';

/* ── Reward moment ─────────────────────────────────────────────
   Fires on a successful submission and on a payout request. Two
   bursts from the lower corners so the centre of the page — where
   the confirmation copy lives — stays readable.
   ───────────────────────────────────────────────────────────── */
const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

window.sortifyaCelebrate = function celebrate() {
    if (reducedMotion.matches) return;

    const palette = ['#10b981', '#14b8a6', '#34d399', '#5eead4', '#f8fafc'];
    const shared = { particleCount: 55, spread: 70, colors: palette, disableForReducedMotion: true, zIndex: 9999 };

    confetti({ ...shared, angle: 60, origin: { x: 0, y: 0.85 } });
    confetti({ ...shared, angle: 120, origin: { x: 1, y: 0.85 } });

    window.setTimeout(() => {
        confetti({ ...shared, particleCount: 40, spread: 100, startVelocity: 34, origin: { x: 0.5, y: 0.7 } });
    }, 180);
};

/* ── Theme ─────────────────────────────────────────────────────
   Three states, not two: 'light', 'dark', and no stored value at
   all, which means "follow the OS". The <head> bootstrap script
   applies the class before paint; this store keeps it in sync.
   ───────────────────────────────────────────────────────────── */
Alpine.store('theme', {
    dark: document.documentElement.classList.contains('dark'),

    toggle() {
        this.dark = !this.dark;
        document.documentElement.classList.toggle('dark', this.dark);
        localStorage.setItem('sortifya.theme', this.dark ? 'dark' : 'light');
    },
});

/* ── Signature: the PDF → sheet transformation ─────────────────
   The thesis of the product, animated. Ragged scan lines on the
   left commit, one row at a time, into an aligned ledger on the
   right. Loops with a pause so it reads as a cycle, not a jitter.
   ───────────────────────────────────────────────────────────── */
Alpine.data('sheetHero', () => ({
    rows: [
        { ref: '2', date: '02-14', vendor: 'Nadeem Print Co.', amount: '148.00' },
        { ref: '3', date: '02-16', vendor: 'Halabi Logistics', amount: '92.40' },
        { ref: '4', date: '02-19', vendor: 'Cedar Supply Ltd.', amount: '1,204.75' },
        { ref: '5', date: '02-23', vendor: 'Mouawad Hardware', amount: '316.20' },
        { ref: '6', date: '02-27', vendor: 'Beirut Freight', amount: '540.00' },
    ],
    committed: 0,
    timer: null,

    init() {
        if (reducedMotion.matches) {
            this.committed = this.rows.length;
            return;
        }

        this.tick();
        // Pause the loop when the tab is hidden; nobody is watching.
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                clearTimeout(this.timer);
            } else {
                this.tick();
            }
        });
    },

    tick() {
        clearTimeout(this.timer);

        const atEnd = this.committed >= this.rows.length;
        this.timer = setTimeout(
            () => {
                this.committed = atEnd ? 0 : this.committed + 1;
                this.tick();
            },
            atEnd ? 2600 : 620,
        );
    },

    isCommitted(index) {
        return index < this.committed;
    },

    get progress() {
        return Math.round((this.committed / this.rows.length) * 100);
    },
}));

/* ── Stats counter ─────────────────────────────────────────────
   Counts up once, when the figure actually scrolls into view.
   ───────────────────────────────────────────────────────────── */
Alpine.data('counter', (target, { decimals = 0, prefix = '', suffix = '' } = {}) => ({
    value: 0,
    done: false,

    init() {
        // Alpine replaces the server-rendered figure the moment it binds, so
        // the honest value has to be the resting state. Only a figure that is
        // still off-screen — where nobody can read it — starts at zero.
        this.value = target;

        if (reducedMotion.matches) {
            this.done = true;
            return;
        }

        const box = this.$el.getBoundingClientRect();
        const visibleNow = box.top < window.innerHeight && box.bottom > 0;

        if (visibleNow) {
            this.done = true;
            this.value = 0;
            this.run();

            return;
        }

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting || this.done) return;
                    this.done = true;
                    this.value = 0;
                    this.run();
                    observer.disconnect();
                });
            },
            { threshold: 0.4 },
        );

        observer.observe(this.$el);
    },

    run() {
        const duration = 1400;
        const started = performance.now();

        const frame = (now) => {
            const progress = Math.min((now - started) / duration, 1);
            // easeOutExpo — fast commit, gentle settle.
            const eased = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
            this.value = target * eased;
            if (progress < 1) requestAnimationFrame(frame);
        };

        requestAnimationFrame(frame);
    },

    get display() {
        return (
            prefix +
            this.value.toLocaleString('en-US', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals,
            }) +
            suffix
        );
    },
}));

/* ── Task countdown ────────────────────────────────────────────
   A claimed task holds a 45-minute lock. The figure turns amber
   under five minutes — the one place amber is allowed to appear.
   ───────────────────────────────────────────────────────────── */
Alpine.data('countdown', (expiresAtIso, expiredLabel = 'Expired') => ({
    remaining: 0,
    interval: null,

    init() {
        this.refresh();
        this.interval = setInterval(() => this.refresh(), 1000);
    },

    destroy() {
        clearInterval(this.interval);
    },

    refresh() {
        const deadline = new Date(expiresAtIso).getTime();
        this.remaining = Math.max(0, Math.floor((deadline - Date.now()) / 1000));
        if (this.remaining === 0) clearInterval(this.interval);
    },

    get expired() {
        return this.remaining === 0;
    },

    get urgent() {
        return this.remaining > 0 && this.remaining <= 300;
    },

    get label() {
        if (this.expired) return expiredLabel;
        const minutes = String(Math.floor(this.remaining / 60)).padStart(2, '0');
        const seconds = String(this.remaining % 60).padStart(2, '0');
        return `${minutes}:${seconds}`;
    },
}));

/* ── Upload dropzone ───────────────────────────────────────────
   Drag-and-drop with a real <input type=file> underneath, so the
   keyboard path and the pointer path lead to the same place.
   ───────────────────────────────────────────────────────────── */
Alpine.data('dropzone', (accept = ['xlsx', 'xls', 'csv'], maxMb = 10) => ({
    over: false,
    fileName: '',
    fileSize: '',
    error: '',

    handleDrop(event) {
        this.over = false;
        const file = event.dataTransfer?.files?.[0];
        if (!file) return;

        // Push the dropped file into the real input so the form submits it.
        const transfer = new DataTransfer();
        transfer.items.add(file);
        this.$refs.input.files = transfer.files;
        this.adopt(file);
    },

    handleSelect(event) {
        const file = event.target.files?.[0];
        if (file) this.adopt(file);
    },

    adopt(file) {
        const extension = file.name.split('.').pop().toLowerCase();

        if (!accept.includes(extension)) {
            this.reset();
            this.error = this.$refs.zone.dataset.errorType;
            return;
        }

        if (file.size > maxMb * 1024 * 1024) {
            this.reset();
            this.error = this.$refs.zone.dataset.errorSize;
            return;
        }

        this.error = '';
        this.fileName = file.name;
        this.fileSize = this.humanize(file.size);
    },

    reset() {
        this.fileName = '';
        this.fileSize = '';
        this.error = '';
        this.$refs.input.value = '';
    },

    humanize(bytes) {
        if (bytes < 1024) return `${bytes} B`;
        if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
        return `${(bytes / 1024 / 1024).toFixed(2)} MB`;
    },
}));

Alpine.plugin(collapse);

window.Alpine = Alpine;
Alpine.start();

AOS.init({
    duration: 620,
    easing: 'ease-out-cubic',
    once: true,
    offset: 60,
    disable: () => reducedMotion.matches,
});
