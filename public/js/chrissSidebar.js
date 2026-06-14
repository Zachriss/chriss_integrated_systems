document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const body = document.body;
    const desktopCollapseKey = 'chrissSidebarCollapsed';

    // Create sidebar overlay for mobile
    function createOverlay() {
        let overlay = document.querySelector('.sidebar-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.className = 'sidebar-overlay';
            document.body.appendChild(overlay);
        }
        return overlay;
    }

    const overlay = createOverlay();

    if (!sidebar || !toggleBtn) {
        return;
    }

    function cleanLabel(text) {
        return (text || '')
            .replace(/\u00a0/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();
    }

    function getLinkLabel(link) {
        if (!link) {
            return '';
        }

        const clone = link.cloneNode(true);
        clone.querySelectorAll('i').forEach(function (icon) {
            icon.remove();
        });

        return cleanLabel(clone.textContent);
    }

    function applyNavTooltips() {
        sidebar.querySelectorAll('.nav-link').forEach(function (link) {
            const label = getLinkLabel(link);
            if (label) {
                link.setAttribute('title', label);
            }
        });
    }

    function readDesktopCollapsedState() {
        try {
            return window.localStorage.getItem(desktopCollapseKey) === 'true';
        } catch (error) {
            return false;
        }
    }

    function writeDesktopCollapsedState(isCollapsed) {
        try {
            window.localStorage.setItem(desktopCollapseKey, String(isCollapsed));
        } catch (error) {
            // Ignore storage failures and keep the UI responsive.
        }
    }

    function isMobileView() {
        return window.innerWidth <= 768;
    }

    function updateToggleButtonState() {
        const sidebarVisible = isMobileView()
            ? sidebar.classList.contains('active')
            : !body.classList.contains('sidebar-collapsed');

        toggleBtn.setAttribute('aria-expanded', String(sidebarVisible));
        toggleBtn.setAttribute(
            'aria-label',
            sidebarVisible ? 'Collapse sidebar' : 'Expand sidebar'
        );
        toggleBtn.setAttribute(
            'title',
            sidebarVisible ? 'Collapse sidebar' : 'Expand sidebar'
        );

        if (isMobileView()) {
            toggleBtn.innerHTML = sidebarVisible ? '<i class="bi bi-x-lg"></i>' : '<i class="bi bi-list"></i>';
        } else {
            toggleBtn.innerHTML = body.classList.contains('sidebar-collapsed')
                ? '<i class="bi bi-layout-sidebar-inset-reverse"></i>'
                : '<i class="bi bi-layout-sidebar-inset"></i>';
        }
    }

    function applyLayoutState() {
        if (isMobileView()) {
            body.classList.remove('sidebar-collapsed');
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        } else {
            body.classList.toggle('sidebar-collapsed', readDesktopCollapsedState());
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        }

        updateToggleButtonState();
    }

    function closeMobileSidebar() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        updateToggleButtonState();
    }

    toggleBtn.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (isMobileView()) {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        } else {
            const isCollapsed = !body.classList.contains('sidebar-collapsed');
            body.classList.toggle('sidebar-collapsed', isCollapsed);
            writeDesktopCollapsedState(isCollapsed);
        }

        updateToggleButtonState();
    });

    // Close sidebar when clicking overlay on mobile
    overlay.addEventListener('click', function () {
        closeMobileSidebar();
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function (event) {
        if (
            isMobileView() &&
            sidebar.classList.contains('active') &&
            !sidebar.contains(event.target) &&
            !toggleBtn.contains(event.target)
        ) {
            closeMobileSidebar();
        }
    });

    // Close sidebar on escape key
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && isMobileView() && sidebar.classList.contains('active')) {
            closeMobileSidebar();
        }
    });

    // Handle window resize
    window.addEventListener('resize', function () {
        applyLayoutState();
    });

    // Initialize
    applyNavTooltips();
    applyLayoutState();
});