/**
 * Alpine stores and components.
 *
 * The bag lives on the server (session) — addToBag / buyNow POST to /cart.
 * The wishlist is kept in this browser (localStorage).
 * Sign-in and register validate in the browser, then POST to Laravel.
 */

const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

async function postJson(url, data) {
    const response = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf() },
        body: JSON.stringify(data),
    });
    const body = await response.json().catch(() => ({}));
    return { ok: response.ok, status: response.status, body };
}

const firstErrors = (body) => Object.fromEntries(Object.entries(body.errors ?? {}).map(([key, messages]) => [key, messages[0]]));

function storedWishlist() {
    try {
        return JSON.parse(localStorage.getItem('artform-wishlist') ?? '[]');
    } catch {
        return [];
    }
}

export default function registerAlpine(Alpine) {
    Alpine.store('ui', {
        menuOpen: false,
        searchOpen: false,
        accountOpen: false,
        quickView: null,
        toast: null,
        bagCount: Number(document.querySelector('meta[name="cart-count"]')?.content ?? 0),
        bagBusy: false,
        headerHidden: false,
        wishlist: storedWishlist(),
        toastTimer: null,

        get locked() {
            return this.menuOpen || this.searchOpen || this.accountOpen || this.quickView !== null;
        },

        notify(message) {
            this.toast = message;
            clearTimeout(this.toastTimer);
            this.toastTimer = setTimeout(() => (this.toast = null), 3800);
        },

        async addToBag(slug, quantity = 1) {
            if (this.bagBusy) return false;
            this.bagBusy = true;
            try {
                const { ok, body } = await postJson('/cart', { slug, quantity });
                if (!ok) {
                    this.notify(Object.values(firstErrors(body))[0] ?? 'Sorry, that could not be added. Please try again.');
                    return false;
                }
                this.bagCount = body.count;
                this.notify(body.message);
                return true;
            } catch {
                this.notify('Sorry, that could not be added. Please check your connection and try again.');
                return false;
            } finally {
                this.bagBusy = false;
            }
        },

        async buyNow(slug, quantity = 1) {
            if (await this.addToBag(slug, quantity)) window.location.href = '/checkout';
        },

        account() {
            this.menuOpen = false;
            this.accountOpen = true;
        },

        isWished(slug) {
            return this.wishlist.includes(slug);
        },

        toggleWish(slug) {
            const index = this.wishlist.indexOf(slug);
            index > -1 ? this.wishlist.splice(index, 1) : this.wishlist.push(slug);
            try {
                localStorage.setItem('artform-wishlist', JSON.stringify(this.wishlist));
            } catch {
                // Private browsing: the wishlist still works for this visit.
            }
            this.notify(index > -1 ? 'Removed from your wishlist.' : 'Saved to your wishlist.');
        },

        openSearch() {
            this.menuOpen = false;
            this.searchOpen = true;
        },

        closeAll() {
            this.menuOpen = false;
            this.searchOpen = false;
            this.accountOpen = false;
            this.quickView = null;
        },
    });

    Alpine.data('siteHeader', () => ({
        scrolled: false,
        hidden: false,
        lastY: 0,

        init() {
            const onScroll = () => {
                const y = window.scrollY;
                const delta = y - this.lastY;
                this.scrolled = y > 24;
                if (Math.abs(delta) > 6) {
                    this.hidden = y > 360 && delta > 0 && !this.$store.ui.menuOpen;
                    this.$store.ui.headerHidden = this.hidden;
                    this.lastY = y;
                }
            };
            onScroll();
            window.addEventListener('scroll', onScroll, { passive: true });
        },
    }));

    Alpine.data('shop', (config) => ({
        products: config.products.map((p, index) => ({ ...p, index })),
        categories: config.categories,
        category: 'all',
        query: '',
        sort: 'featured',
        priceCeiling: config.priceCeiling,
        maxPrice: config.priceCeiling,
        filtersOpen: false,

        init() {
            const params = new URLSearchParams(window.location.search);
            this.query = params.get('q') ?? '';
            const category = params.get('category');
            if (category && this.categories[category]) this.category = category;

            this.$watch('results', () => {
                this.syncUrl();
                this.$nextTick(() => window.dispatchEvent(new Event('layout:changed')));
            });
        },

        get results() {
            const q = this.query.trim().toLowerCase();
            const sorters = {
                featured: (a, b) => a.index - b.index,
                'price-asc': (a, b) => a.price - b.price,
                'price-desc': (a, b) => b.price - a.price,
                name: (a, b) => a.name.localeCompare(b.name),
            };

            return this.products
                .filter((p) => this.category === 'all' || p.category === this.category)
                .filter((p) => p.price <= this.maxPrice)
                .filter((p) => !q || `${p.name} ${p.category_name} ${p.material}`.toLowerCase().includes(q))
                .sort(sorters[this.sort]);
        },

        isVisible(slug) {
            return this.results.some((p) => p.slug === slug);
        },

        position(slug) {
            return this.results.findIndex((p) => p.slug === slug);
        },

        get activeFilters() {
            return (this.category !== 'all') + (this.query.trim() !== '') + (this.maxPrice < this.priceCeiling);
        },

        selectCategory(category) {
            this.category = category;
            document.getElementById('catalogue')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        },

        reset() {
            this.category = 'all';
            this.query = '';
            this.sort = 'featured';
            this.maxPrice = this.priceCeiling;
        },

        formatPrice(value) {
            return '₹' + Number(value).toLocaleString('en-IN');
        },

        syncUrl() {
            const params = new URLSearchParams(window.location.search);
            this.category !== 'all' ? params.set('category', this.category) : params.delete('category');
            this.query.trim() ? params.set('q', this.query.trim()) : params.delete('q');
            const qs = params.toString();
            window.history.replaceState({}, '', window.location.pathname + (qs ? `?${qs}` : ''));
        },
    }));

    // Shared by the sign-in drawer and the login, register, forgot and reset password pages.
    Alpine.data('authForm', (type, options = {}) => ({
        type,
        form: { name: '', email: '', phone: '', password: '', password_confirmation: '', remember: true, terms: false },
        errors: options.errors ?? {},
        showPassword: false,
        sending: false,
        done: false,

        get strength() {
            const p = this.form.password;
            if (!p) return 0;
            return [p.length >= 8, /[a-z]/.test(p) && /[A-Z]/.test(p), /\d/.test(p), /[^A-Za-z0-9]/.test(p)].filter(Boolean).length;
        },

        get strengthLabel() {
            return ['', 'Weak', 'Fair', 'Good', 'Strong'][this.strength];
        },

        validate() {
            const f = this.form;
            const e = {};
            const needsEmail = ['login', 'register', 'forgot'].includes(this.type);
            const newPassword = ['register', 'reset'].includes(this.type);

            if (this.type === 'register' && !f.name.trim()) e.name = 'Please enter your full name.';
            if (needsEmail && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(f.email)) e.email = 'Please enter a valid email address.';
            if (this.type === 'register' && f.phone && !/^[+\d\s()-]{7,}$/.test(f.phone)) e.phone = 'Please enter a valid phone number.';
            if (this.type === 'login' && !f.password) e.password = 'Please enter your password.';
            if (newPassword) {
                if (f.password.length < 8 || !/[A-Za-z]/.test(f.password) || !/\d/.test(f.password)) {
                    e.password = 'Use at least 8 characters with letters and numbers.';
                }
                if (f.password_confirmation !== f.password) e.password_confirmation = 'Passwords do not match.';
            }
            if (this.type === 'register' && !f.terms) e.terms = 'Please accept the terms to continue.';

            this.errors = e;
            return Object.keys(e).length === 0;
        },

        async submit() {
            if (!this.validate()) {
                this.$nextTick(() => this.$root.querySelector('[aria-invalid="true"]')?.focus());
                return;
            }

            // Sign in and register post to Laravel; the server answers with where to go next.
            if (['login', 'register'].includes(this.type)) {
                this.sending = true;
                try {
                    const { ok, status, body } = await postJson(this.$root.getAttribute('action'), this.form);
                    if (ok && body.redirect) {
                        window.location.href = body.redirect;
                        return;
                    }
                    this.errors = status === 422 ? firstErrors(body) : { email: 'Something went wrong. Please refresh the page and try again.' };
                    this.$nextTick(() => this.$root.querySelector('[aria-invalid="true"]')?.focus());
                } catch {
                    this.errors = { email: 'Could not connect. Please check your connection and try again.' };
                }
                this.sending = false;
                return;
            }

            // Forgot / reset password: walk-through only — no email is sent yet.
            this.sending = true;
            setTimeout(() => {
                this.sending = false;
                if (options.redirect) {
                    window.location.href = options.redirect;
                    return;
                }
                this.done = true;
            }, 900);
        },
    }));

    // Quote / contact form: validates in the browser, then saves to Admin → Enquiries via POST /enquiries.
    Alpine.data('contactForm', () => ({
        form: { name: '', email: '', phone: '', subject: '', message: '', website: '' },
        errors: {},
        sending: false,
        submitted: false,
        failed: false,

        validate() {
            const errors = {};
            if (!this.form.name.trim()) errors.name = 'Please tell us your name.';
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.form.email)) errors.email = 'Please enter a valid email address.';
            if (this.form.phone && !/^[+\d\s()-]{7,}$/.test(this.form.phone)) errors.phone = 'Please enter a valid phone number.';
            if (!this.form.subject) errors.subject = 'Please choose the service you need.';
            if (this.form.message.trim().length < 10) errors.message = 'Please describe your project and how the part will be used.';
            this.errors = errors;

            return Object.keys(errors).length === 0;
        },

        async submit() {
            if (!this.validate()) {
                this.$nextTick(() => this.$root.querySelector('[aria-invalid="true"]')?.focus());
                return;
            }
            this.sending = true;
            this.failed = false;
            try {
                const { ok, status, body } = await postJson('/enquiries', this.form);
                if (status === 422) {
                    this.errors = firstErrors(body);
                    this.$nextTick(() => this.$root.querySelector('[aria-invalid="true"]')?.focus());
                    return;
                }
                if (!ok) throw new Error(`HTTP ${status}`);
                this.submitted = true;
            } catch {
                this.failed = true;
            } finally {
                this.sending = false;
            }
        },

        restart() {
            this.form = { name: '', email: '', phone: '', subject: '', message: '', website: '' };
            this.submitted = false;
            this.failed = false;
        },
    }));
}
