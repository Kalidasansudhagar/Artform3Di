/** Curtain transition between internal pages. The reveal on load is pure CSS. */
export function initPageTransitions() {
    const curtain = document.querySelector('.page-curtain');
    if (!curtain || !document.documentElement.classList.contains('motion')) return;

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');
        if (!link || event.defaultPrevented || event.button !== 0) return;
        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        if ((link.target && link.target !== '_self') || link.hasAttribute('download') || link.hasAttribute('data-no-transition')) return;

        const url = new URL(link.href, window.location.href);
        if (url.origin !== window.location.origin) return;
        if (url.pathname === window.location.pathname && url.search === window.location.search) return;

        event.preventDefault();
        curtain.classList.add('is-leaving');
        setTimeout(() => (window.location.href = url.href), 520);
    });

    // Restore pages coming back from the back/forward cache.
    window.addEventListener('pageshow', (event) => {
        if (event.persisted) curtain.classList.remove('is-leaving');
    });
}
