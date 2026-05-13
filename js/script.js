/* =========================================
   CARE Group – Medical Appointment Portal
   Main JavaScript – UI Interactions Only
   ========================================= */

'use strict';

// =====================
// DOM Ready
// =====================
document.addEventListener('DOMContentLoaded', function () {
  initNavbar();
  initSidebar();
  initScrollAnimations();
  initTimeSlots();
  initFormEffects();
  initSmoothScroll();
  initTooltips();
  initToggleSwitches();
  initDiseaseFilters();
  initNewsSearch();
  initAvailabilityTable();
});

// =====================
// Navbar Effects
// =====================
function initNavbar() {
  const navbar = document.querySelector('.navbar-care');
  if (!navbar) return;

  window.addEventListener('scroll', function () {
    if (window.scrollY > 50) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  });

  // Active link highlight
  const links = document.querySelectorAll('.navbar-care .nav-link');
  const currentPage = location.pathname.split('/').pop() || 'index.php';
  links.forEach(link => {
    const href = link.getAttribute('href');
    if (href === currentPage) {
      link.classList.add('active');
    }
  });
}

// =====================
// Sidebar Toggle
// =====================
function initSidebar() {
  const toggleBtn = document.querySelector('.sidebar-toggle');
  const sidebar = document.querySelector('.sidebar');
  const mainContent = document.querySelector('.main-content');
  const overlay = document.querySelector('.sidebar-overlay');

  if (!toggleBtn || !sidebar) return;

  toggleBtn.addEventListener('click', function () {
    const isMobile = window.innerWidth < 992;
    if (isMobile) {
      sidebar.classList.toggle('open');
      if (overlay) overlay.classList.toggle('show');
    } else {
      sidebar.classList.toggle('collapsed');
      if (mainContent) mainContent.classList.toggle('expanded');
    }
  });

  if (overlay) {
    overlay.addEventListener('click', function () {
      sidebar.classList.remove('open');
      overlay.classList.remove('show');
    });
  }

  // Active sidebar link
  const sideLinks = document.querySelectorAll('.sidebar-nav-link');
  const currentPage = location.pathname.split('/').pop();
  sideLinks.forEach(link => {
    if (link.getAttribute('href') === currentPage) {
      link.classList.add('active');
    }
    link.addEventListener('click', function () {
      sideLinks.forEach(l => l.classList.remove('active'));
      this.classList.add('active');
    });
  });
}

// =====================
// Scroll Animations
// =====================
function initScrollAnimations() {
  const targets = document.querySelectorAll('.animate-on-scroll');
  if (targets.length === 0) return;

  const observer = new IntersectionObserver(
    function (entries) {
      entries.forEach(function (entry, index) {
        if (entry.isIntersecting) {
          setTimeout(function () {
            entry.target.classList.add('visible');
          }, index * 80);
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.1, rootMargin: '0px 0px -40px 0px' }
  );

  targets.forEach(function (target) {
    observer.observe(target);
  });
}

// =====================
// Time Slot Selection
// =====================
function initTimeSlots() {
  const slotButtons = document.querySelectorAll('.time-slot-btn:not(.unavailable)');
  const selectedTimeInput = document.getElementById('selectedTime');

  slotButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      slotButtons.forEach(function (b) {
        b.classList.remove('selected');
      });
      this.classList.add('selected');
      if (selectedTimeInput) {
        selectedTimeInput.value = this.textContent.trim();
      }
      // Visual feedback
      this.style.transform = 'scale(1.05)';
      setTimeout(() => {
        this.style.transform = '';
      }, 200);
    });
  });
}

// =====================
// Form Effects
// =====================
function initFormEffects() {
  // Password toggle
  document.querySelectorAll('.toggle-password').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const input = this.closest('.input-group').querySelector('input');
      if (!input) return;
      const isPass = input.type === 'password';
      input.type = isPass ? 'text' : 'password';
      const icon = this.querySelector('i');
      if (icon) {
        icon.classList.toggle('fa-eye', !isPass);
        icon.classList.toggle('fa-eye-slash', isPass);
      }
    });
  });

  // Form validation feedback
  document.querySelectorAll('.needs-validation').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
      }
      form.classList.add('was-validated');
    });
  });

  // Phone number auto-format
  document.querySelectorAll('input[type="tel"]').forEach(function (input) {
    input.addEventListener('input', function () {
      let val = this.value.replace(/\D/g, '');
      if (val.length > 11) val = val.slice(0, 11);
      this.value = val;
    });
  });
}

// =====================
// Smooth Scroll
// =====================
function initSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener('click', function (e) {
      const href = this.getAttribute('href');
      if (href === '#' || href === '#!') return;
      const target = document.querySelector(href);
      if (!target) return;
      e.preventDefault();
      const offset = 80;
      const top = target.getBoundingClientRect().top + window.scrollY - offset;
      window.scrollTo({ top, behavior: 'smooth' });
    });
  });
}

// =====================
// Bootstrap Tooltips
// =====================
function initTooltips() {
  const tooltipEls = document.querySelectorAll('[data-bs-toggle="tooltip"]');
  tooltipEls.forEach(function (el) {
    new bootstrap.Tooltip(el);
  });
}

// =====================
// Availability Toggles
// =====================
function initToggleSwitches() {
  document.querySelectorAll('.availability-toggle').forEach(function (toggle) {
    toggle.addEventListener('click', function () {
      this.classList.toggle('on');
    });
  });
}

// =====================
// Disease Card Filters
// =====================
function initDiseaseFilters() {
  const filterBtns = document.querySelectorAll('.disease-filter-btn');
  const cards = document.querySelectorAll('.disease-card-wrap');

  if (!filterBtns.length) return;

  filterBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      filterBtns.forEach(b => b.classList.remove('active', 'btn-primary-care'));
      filterBtns.forEach(b => b.classList.add('btn-outline-secondary'));
      this.classList.add('active', 'btn-primary-care');
      this.classList.remove('btn-outline-secondary');

      const filter = this.dataset.filter;
      cards.forEach(function (card) {
        if (filter === 'all' || card.dataset.category === filter) {
          card.style.display = '';
          card.style.animation = 'fadeInUp 0.4s ease';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });
}

// =====================
// News Search
// =====================
function initNewsSearch() {
  const searchInput = document.getElementById('newsSearch');
  const cards = document.querySelectorAll('.news-card-item');
  if (!searchInput) return;

  searchInput.addEventListener('input', function () {
    const query = this.value.toLowerCase().trim();
    cards.forEach(function (card) {
      const title = card.querySelector('h5')?.textContent.toLowerCase() || '';
      const text = card.querySelector('.news-excerpt')?.textContent.toLowerCase() || '';
      if (!query || title.includes(query) || text.includes(query)) {
        card.parentElement.style.display = '';
      } else {
        card.parentElement.style.display = 'none';
      }
    });
  });
}

// =====================
// Availability Table (Doctor Dashboard)
// =====================
function initAvailabilityTable() {
  document.querySelectorAll('.avail-cell').forEach(function (cell) {
    cell.addEventListener('click', function () {
      this.classList.toggle('selected');
      const isSelected = this.classList.contains('selected');
      this.style.background = isSelected ? 'var(--light-blue)' : '';
      this.style.color = isSelected ? 'var(--primary)' : '';
      this.style.fontWeight = isSelected ? '600' : '';
    });
  });
}

// =====================
// Doctor Filter (Search Page)
// =====================
function initDoctorSearch() {
  const searchBtn = document.getElementById('searchDoctorsBtn');
  if (!searchBtn) return;

  searchBtn.addEventListener('click', function () {
    const city = document.getElementById('cityFilter')?.value;
    const spec = document.getElementById('specFilter')?.value;
    // UI-only: just show animation
    const cards = document.querySelectorAll('.doctor-card');
    cards.forEach(function (card) {
      card.style.opacity = '0.5';
      card.style.transform = 'scale(0.98)';
    });
    setTimeout(function () {
      cards.forEach(function (card) {
        card.style.opacity = '1';
        card.style.transform = '';
      });
    }, 500);
  });
}
document.addEventListener('DOMContentLoaded', initDoctorSearch);

// =====================
// Appointment Booking Flow
// =====================
function confirmAppointment() {
  const selectedSlot = document.querySelector('.time-slot-btn.selected');
  const confirmModal = document.getElementById('confirmModal');

  if (!selectedSlot) {
    showToast('Please select a time slot first.', 'warning');
    return;
  }
  if (confirmModal) {
    const timeDisplay = document.getElementById('confirmTime');
    if (timeDisplay) timeDisplay.textContent = selectedSlot.textContent.trim();
    new bootstrap.Modal(confirmModal).show();
  }
}

// =====================
// Toast Notifications
// =====================
function showToast(message, type = 'info') {
  const existing = document.querySelectorAll('.care-toast');
  existing.forEach(t => t.remove());

  const colors = {
    info: 'var(--primary)',
    success: 'var(--success)',
    warning: 'var(--warning)',
    error: 'var(--danger)'
  };
  const icons = {
    info: 'fa-info-circle',
    success: 'fa-check-circle',
    warning: 'fa-exclamation-triangle',
    error: 'fa-times-circle'
  };

  const toast = document.createElement('div');
  toast.className = 'care-toast';
  toast.innerHTML = `
    <div style="
      position:fixed; bottom:24px; right:24px; z-index:9999;
      background:white; border-radius:12px; padding:14px 20px;
      box-shadow:0 8px 32px rgba(0,0,0,0.15);
      display:flex; align-items:center; gap:12px;
      border-left:4px solid ${colors[type]};
      animation:fadeInUp 0.3s ease;
      min-width:280px; max-width:360px;
    ">
      <i class="fas ${icons[type]}" style="color:${colors[type]};font-size:1.1rem;"></i>
      <span style="font-size:0.88rem;color:#455A64;font-family:Poppins,sans-serif;">${message}</span>
    </div>
  `;
  document.body.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transition = 'opacity 0.3s';
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// =====================
// Admin Table Row Actions (UI only)
// =====================
document.addEventListener('click', function (e) {
  if (e.target.closest('.action-btn.delete')) {
    const row = e.target.closest('tr');
    if (row) {
      row.style.background = '#FFEBEE';
      setTimeout(() => {
        row.style.opacity = '0';
        row.style.transition = 'opacity 0.3s';
        setTimeout(() => row.remove(), 300);
      }, 400);
    }
  }
  if (e.target.closest('.action-btn.edit')) {
    showToast('Edit form opened successfully.', 'info');
  }
  if (e.target.closest('.btn-add-row')) {
    showToast('Add form ready.', 'success');
  }
});

// =====================
// Hero Section Counter Animation
// =====================
function animateCounters() {
  document.querySelectorAll('.hero-stat-num[data-count]').forEach(function (el) {
    const target = parseInt(el.dataset.count);
    let count = 0;
    const increment = Math.ceil(target / 60);
    const timer = setInterval(function () {
      count += increment;
      if (count >= target) {
        count = target;
        clearInterval(timer);
      }
      el.textContent = count.toLocaleString() + (el.dataset.suffix || '');
    }, 30);
  });
}
window.addEventListener('load', animateCounters);

// =====================
// Tab Switching (Register Page)
// =====================
document.querySelectorAll('.tab-care .nav-link').forEach(function (tab) {
  tab.addEventListener('click', function () {
    document.querySelectorAll('.tab-care .nav-link').forEach(t => t.classList.remove('active'));
    this.classList.add('active');
  });
});

// =====================
// Back to Top Button
// =====================
(function () {
  const btn = document.createElement('button');
  btn.innerHTML = '<i class="fas fa-arrow-up"></i>';
  btn.style.cssText = `
    position:fixed; bottom:80px; right:24px; z-index:999;
    width:42px; height:42px; background:var(--primary);
    color:white; border:none; border-radius:50%;
    cursor:pointer; font-size:0.9rem;
    box-shadow:0 4px 16px rgba(21,101,192,0.35);
    transition:all 0.3s; opacity:0; pointer-events:none;
    display:flex; align-items:center; justify-content:center;
  `;
  document.body.appendChild(btn);

  window.addEventListener('scroll', function () {
    if (window.scrollY > 400) {
      btn.style.opacity = '1';
      btn.style.pointerEvents = 'auto';
    } else {
      btn.style.opacity = '0';
      btn.style.pointerEvents = 'none';
    }
  });

  btn.addEventListener('click', function () {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
  btn.addEventListener('mouseenter', function () {
    this.style.transform = 'translateY(-3px)';
    this.style.boxShadow = '0 8px 20px rgba(21,101,192,0.45)';
  });
  btn.addEventListener('mouseleave', function () {
    this.style.transform = '';
    this.style.boxShadow = '0 4px 16px rgba(21,101,192,0.35)';
  });
})();
