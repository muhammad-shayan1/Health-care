/* ============================================================
   Healthcare Admin Dashboard - script.js
   UI interactions only — no backend logic
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

    /* ── Sidebar Toggle ────────────────────────────────────── */
    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
    const sidebar          = document.getElementById('sidebar');
    const overlay          = document.getElementById('sidebarOverlay');
    const isDesktop        = () => window.innerWidth >= 992;

    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', function () {
            if (isDesktop()) {
                // Collapse/expand on desktop
                document.body.classList.toggle('sidebar-collapsed');
            } else {
                // Show/hide on mobile
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }

    /* ── Animated Number Counters ──────────────────────────── */
    function animateCounter(el) {
        const target   = parseInt(el.getAttribute('data-target'), 10);
        const duration = 1400;
        const step     = Math.ceil(target / (duration / 16));
        let   current  = 0;

        const timer = setInterval(function () {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            el.textContent = current.toLocaleString();
        }, 16);
    }

    const counterEls = document.querySelectorAll('[data-target]');
    if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.3 });

        counterEls.forEach(function (el) { io.observe(el); });
    } else {
        counterEls.forEach(animateCounter);
    }

    /* ── Toast Notification Demo ───────────────────────────── */
    function showToast(type, title, message) {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const icons = { success: 'bi-check-circle-fill', error: 'bi-x-circle-fill', info: 'bi-info-circle-fill' };
        const toast = document.createElement('div');
        toast.className = 'toast-custom ' + type;
        toast.innerHTML =
            '<i class="bi ' + (icons[type] || icons.info) + ' toast-icon"></i>' +
            '<div><p>' + title + '</p><small>' + message + '</small></div>' +
            '<button onclick="this.parentElement.remove()" style="background:none;border:none;margin-left:auto;font-size:16px;cursor:pointer;color:#7f8fa6;">×</button>';

        container.appendChild(toast);
        setTimeout(function () {
            if (toast.parentElement) {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity .3s ease';
                setTimeout(function () { if (toast.parentElement) toast.remove(); }, 300);
            }
        }, 4000);
    }

    // Demo toast on dashboard page load
    if (document.getElementById('dashboardPage')) {
        setTimeout(function () {
            showToast('success', 'Dashboard Loaded', 'All systems are operational.');
        }, 1000);
    }

    // Expose for button usage on all pages
    window.showToast = showToast;

    /* ── Active Sidebar Link ───────────────────────────────── */
    const currentFile = window.location.pathname.split('/').pop() || 'dashboard.php';
    document.querySelectorAll('.sidebar-nav .nav-link').forEach(function (link) {
        const href = link.getAttribute('href');
        if (href && href.split('/').pop() === currentFile) {
            link.classList.add('active');
        }
    });

    /* ── Search Filter (client-side, demo) ─────────────────── */
    const searchInput = document.getElementById('tableSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            document.querySelectorAll('.admin-table tbody tr').forEach(function (row) {
                row.style.display = row.textContent.toLowerCase().includes(query) ? '' : 'none';
            });
        });
    }

    /* ── Modal "Add New" shortcut key (Ctrl+N) ─────────────── */
    document.addEventListener('keydown', function (e) {
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            const addBtn = document.getElementById('addNewBtn');
            if (addBtn) addBtn.click();
        }
    });

    /* ── Smooth form feedback (UI only) ────────────────────── */
    document.querySelectorAll('.modal-save-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const modal = this.closest('.modal');
            const inputs = modal ? modal.querySelectorAll('.form-control-custom[required]') : [];
            let valid = true;

            inputs.forEach(function (input) {
                input.style.borderColor = '';
                if (!input.value.trim()) {
                    input.style.borderColor = '#dc3545';
                    valid = false;
                }
            });

            if (valid) {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) modalInstance.hide();
                showToast('success', 'Saved Successfully', 'The record has been saved.');
            }
        });
    });

});
