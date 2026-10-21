(() => {
    'use strict';

    function init() {
        const menu = document.getElementById('menu');
        if (!menu || document.getElementById('adminer-sidebar-resizer')) return;

        const root = document.documentElement;
        const desktop = window.matchMedia('(min-width: 801px)');
        const storageKey = 'adminer-dynamic-sidebar.width';
        const handle = document.createElement('div');
        let preferredWidth = parseFloat(getComputedStyle(document.body).fontSize) * 36;
        let pointerId = null;
        let startX = 0;
        let startWidth = 0;

        try {
            const stored = Number(localStorage.getItem(storageKey));
            if (Number.isFinite(stored) && stored >= 200) preferredWidth = stored;
        } catch (_) {
            // Resizing remains available when browser storage is blocked.
        }

        handle.id = 'adminer-sidebar-resizer';
        handle.tabIndex = 0;
        handle.setAttribute('role', 'separator');
        handle.setAttribute('aria-orientation', 'vertical');
        handle.setAttribute('aria-label', 'Sidebar width');
        handle.setAttribute('aria-controls', 'menu');
        menu.append(handle);

        const direction = () => getComputedStyle(menu).direction === 'rtl' ? -1 : 1;
        const maximum = () => Math.min(window.innerWidth * 0.7, window.innerWidth - 320);

        function apply() {
            if (!desktop.matches) return;
            const width = Math.round(Math.max(200, Math.min(preferredWidth, maximum())));
            root.style.setProperty('--adminer-sidebar-width', `${width}px`);
            handle.setAttribute('aria-valuemin', '200');
            handle.setAttribute('aria-valuemax', String(Math.floor(maximum())));
            handle.setAttribute('aria-valuenow', String(width));
        }

        function save() {
            preferredWidth = menu.getBoundingClientRect().width;
            try {
                localStorage.setItem(storageKey, String(preferredWidth));
            } catch (_) {
                // Keep the current width for this page when storage is unavailable.
            }
        }

        function finish(event) {
            if (pointerId === null || (event && event.pointerId !== pointerId)) return;
            const captured = pointerId;
            pointerId = null;
            root.classList.remove('adminer-sidebar-dragging');
            if (handle.hasPointerCapture(captured)) handle.releasePointerCapture(captured);
            if (desktop.matches) save();
        }

        handle.addEventListener('pointerdown', event => {
            if (!desktop.matches || !event.isPrimary || event.button !== 0 || pointerId !== null) return;
            event.preventDefault();
            handle.focus();
            pointerId = event.pointerId;
            startX = event.clientX;
            startWidth = menu.getBoundingClientRect().width;
            handle.setPointerCapture(pointerId);
            root.classList.add('adminer-sidebar-dragging');
        });
        handle.addEventListener('pointermove', event => {
            if (event.pointerId !== pointerId) return;
            preferredWidth = Math.max(200, Math.min(startWidth + (event.clientX - startX) * direction(), maximum()));
            apply();
        });
        handle.addEventListener('pointerup', finish);
        handle.addEventListener('pointercancel', finish);
        handle.addEventListener('lostpointercapture', finish);
        handle.addEventListener('keydown', event => {
            if (!desktop.matches || !['ArrowLeft', 'ArrowRight'].includes(event.key)) return;
            event.preventDefault();
            preferredWidth = menu.getBoundingClientRect().width + (event.key === 'ArrowRight' ? 10 : -10) * direction();
            apply();
            save();
        });
        window.addEventListener('resize', () => {
            if (!desktop.matches) finish();
            apply();
        });
        window.addEventListener('blur', () => finish());
        apply();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
})();
