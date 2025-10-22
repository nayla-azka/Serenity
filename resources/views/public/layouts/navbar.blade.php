<style>
/* public */

/* Dropdown */
.custom-dropdown {
  border-radius: 4px;
  box-shadow: 0px 2px 6px rgba(0,0,0,0.1);
}
.custom-dropdown .dropdown-item {
  font-weight: 500;
  font-size: 0.6rem;
  padding: 3px 6px;
}
.custom-dropdown .dropdown-item:hover {
  background-color: #f5f5f5;
}
.custom-dropdown .dropdown-item.text-danger {
  color: #e3342f;
}

/* Nav links */
.navbar-nav .nav-link {
  color: white !important;
  font-weight: 500;
  font-size: 0.65rem;
  margin-left: 3px;
  padding: 2px 3px;
}
.navbar-nav .nav-link:hover {
  color: #ffe9ff !important;
}
.navbar-nav .nav-link.active {
  border-bottom: 1px solid #fff;
}

/* Navbar container */
.custom-navbar {
  background: linear-gradient(rgb(157, 147, 210), rgb(178, 145, 210));
  padding: 3px 10px;
  position: relative;
  z-index: 1000;
  font-size: 0.65rem;
}

/* Logo */
.navbar-brand img {
  width: 30px;
  height: 30px;
}
.navbar-brand {
  font-size: 0.7rem;
}

.notification-message {
    white-space: normal;
    word-wrap: break-word;
    word-break: break-word;
}

/* Notification item styles for read/unread */
.notification-item.read {
    background-color: #f8f9fa !important;
    opacity: 0.8;
}

.notification-item.unread {
    background-color: #e7f3ff !important;
    border-left: 3px solid #007bff;
}

.notification-item {
    transition: all 0.2s ease;
    cursor: pointer;
}

.notification-item:hover {
    background-color: #e9ecef !important;
}

/* Fix for clickable notifications */
.notification-item .notification-clickable-area {
    cursor: pointer;
    flex-grow: 1;
}

.notification-item .notification-actions {
    flex-shrink: 0;
}

/* Search bar extra mini */
.custom-navbar .form-control {
  font-size: 0.6rem;
  padding: 2px 6px;
  height: 25px; /* biar ramping */
  width: 170px;
  border-radius: 4px;
}

.custom-navbar .btn {
  font-size: 0.6rem;
  padding: 2px 6px;
  height: 22px;
  line-height: 1;
  border-radius: 4px;
}

#searchResults {
  max-height: 250px;
  overflow-y: auto;
  background: #fff;
  border-radius: 6px;
}
#searchResults a {
  cursor: pointer;
}
#searchResults a:hover {
  background-color: #f1f1f1;
}
#admin-notification-dropdown-menu {
  width: 400px;          /* lebar fix */
  max-width: 90vw;       /* jangan lebih dari 90% viewport */
  overflow-x: hidden;    /* buang horizontal scroll */
  white-space: normal;   /* teks panjang pindah baris */
  right: 0;              /* paksa nempel kanan */
  left: auto;            /* cegah geser kiri */
}

.dropdown-menu,
.custom-dropdown {
  text-align: left !important;
  left: 0 !important;
  right: auto !important;
}

.dropdown-menu {
  opacity: 0;
  transform: translateY(10px);
  transition: opacity 0.25s ease, transform 0.25s cubic-bezier(.4,2,.6,1);
  display: block; /* biar transisi jalan, tapi tetap pakai .show untuk tampil */
  pointer-events: none;
}
.dropdown-menu.show {
  opacity: 1;
  transform: translateY(0);
  pointer-events: auto;
}

/* Animasi collapse navbar (mobile) */
.navbar-collapse {
  max-height: 0;
  transition: max-height 0.35s ease;
}

.navbar-collapse.show {
  max-height: 500px; /* cukup tinggi untuk semua menu */
}

/* Biar isi rata kiri */
.navbar-nav .nav-link {
  text-align: left !important;
  justify-content: flex-start !important;
}


/* ================= MOBILE RESPONSIVE ================= */
@media (max-width: 991.98px) {
  /* Pecah jadi 2 baris: atas & bawah */
  .custom-navbar .container {
    display: flex;
    flex-direction: column;
    align-items: stretch;
  }

  /* Baris atas: dropdown + logo + notif/user */
  .navbar-toggler,
  .custom-navbar .navbar-brand,
  .navbar-mobile-icons {
    order: 1;
  }

  .custom-navbar .container {
    position: relative;
  }

  /* Biar sejajar */
  .navbar-top-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
  }

  /* Dropdown (toggler) ke kiri */
  .navbar-toggler {
    margin-right: auto;
    border: none;
    padding: 4px 8px;
  }

  /* Logo di tengah */
  .custom-navbar .navbar-brand {
    margin: 0 auto;
    text-align: center;
  }

  /* Notif + user ke kanan */
  .navbar-mobile-icons {
    display: flex !important;
    align-items: center;
    gap: 10px;
    margin-left: auto;
  }

  /* Baris bawah: search bar */
  .custom-navbar .search-form {
    order: 2;
    width: 100%;
    margin-top: 8px;
  }

  .custom-navbar .search-form input {
    width: 100%;
  }

  /* Desktop-only sembunyi */
  .navbar-nav.ms-auto.desktop-only {
    display: none !important;
  }

  /* FIX: Navbar collapse smooth animation */
  .navbar-collapse {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-in-out;
  }

  .navbar-collapse.collapsing {
    transition: max-height 0.3s ease-in-out;
  }

  .navbar-collapse.show {
    max-height: 300px;
  }

  /* FIX: Menu items horizontal & left aligned */
  .navbar-collapse .navbar-nav {
    flex-direction: row !important;
    justify-content: flex-start !important;
    align-items: center !important;
    gap: 8px;
    padding: 10px 0;
  }

  .navbar-collapse .navbar-nav .nav-item {
    margin: 0 !important;
  }

  .navbar-collapse .navbar-nav .nav-link {
    text-align: left !important;
    padding: 4px 10px !important;
    white-space: nowrap;
    font-size: 0.65rem !important;
  }

  /* FIX: Remove center alignment */
  .navbar-nav {
    margin-left: 0 !important;
    margin-right: 0 !important;
  }
}

/* User/Notification dropdowns positioning */
.dropdown-menu {
  text-align: left !important;
  left: auto !important;
  right: 0 !important;
}

.dropdown-menu .dropdown-item {
  text-align: left !important;
  justify-content: flex-start !important;
}

/* Smooth dropdown animation */
.dropdown-menu {
  opacity: 0;
  transform: translateY(-10px);
  transition: opacity 0.2s ease, transform 0.2s ease;
  display: block;
  pointer-events: none;
  visibility: hidden;
}

.dropdown-menu.show {
  opacity: 1;
  transform: translateY(0);
  pointer-events: auto;
  visibility: visible;
}

/* Hide/show based on screen size */
@media (min-width: 992px) {
  .navbar-top-row {
    display: none !important;
  }
  .navbar-brand.d-lg-flex {
    display: flex !important;
  }
  .navbar-mobile-icons {
    display: none !important;
  }
}

@media (max-width: 991.98px) {
  .navbar-brand.d-lg-flex {
    display: none !important;
  }
}
</style>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
{{-- <link rel="stylesheet" href="{{ asset('css/custom.css') }}"> <!-- CSS kustom --> --}}

<!-- filepath: d:\13' 12\Tilas Berkarakter\projek\serenity\resources\views\public\layouts\navbar.blade.php -->
<!-- filepath: d:\13' 12\Tilas Berkarakter\projek\serenity\resources\views\public\layouts\navbar.blade.php -->
<nav class="navbar navbar-expand-lg navbar-dark custom-navbar m-0">
    <div class="container align-items-center">

        <!-- Logo (desktop only, kiri) -->
        <a class="navbar-brand fw-bold d-none d-lg-flex align-items-center" style="font-size: 15px;" href="{{ url('/') }}">
            <img src="{{ asset('images/logoSerenity.jpg') }}" alt="Logo"
                 class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
            Serenity
        </a>

        <!-- TOP ROW: Toggler | Logo (mobile) | Notif+User (mobile) -->
        <div class="navbar-top-row w-100 d-flex d-lg-none">
    <!-- Toggler -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNavDropdown"
            aria-controls="navbarNavDropdown" aria-expanded="false"
            aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Logo -->
    <a class="navbar-brand fw-bold mx-auto d-flex align-items-center justify-content-center"
       style="font-size: 15px;" href="{{ url('/') }}">
        <img src="{{ asset('images/logoSerenity.jpg') }}" alt="Logo"
             class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
        Serenity
    </a>

    <!-- Notif + User / Placeholder -->
    <div class="navbar-mobile-icons d-flex align-items-center">
        @auth
            <!-- Notification -->
            <div class="notification-dropdown">
                <button id="admin-notification-button-mobile"
                        class="btn position-relative"
                        type="button">
                    <i class="fas fa-bell" style="color: #ffffff"></i>
                    <span id="admin-notification-badge-mobile"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        style="display:none">0</span>
                </button>
                <div id="admin-notification-dropdown-menu-mobile"
                    class="dropdown-menu dropdown-menu-end p-0 shadow">
                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-light">
                        <h6 class="mb-0 fw-bold">Notifikasi</h6>
                        <div class="btn-group btn-group-sm">
                            <button id="mark-all-read-btn-mobile"
                                    class="btn btn-outline-secondary"
                                    title="Tandai semua sebagai dibaca">
                                <i class="fas fa-check"></i>
                            </button>
                            <button id="delete-all-read-btn-mobile"
                                    class="btn btn-outline-danger"
                                    title="Hapus semua yang sudah dibaca">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div id="admin-notification-list-mobile" class="overflow-auto" style="max-height:400px;">
                        <div id="loading-notifications-mobile" class="text-center p-3">
                            <div class="spinner-border spinner-border-sm" role="status"></div>
                            <div class="mt-2 small">Memuat...</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User -->
            <div class="dropdown">
                <a class="nav-link dropdown-toggle fw-bold text-white" href="#" id="navbarDropdownMobile"
                   role="button" data-bs-toggle="dropdown" aria-expanded="false">
                   {{ Auth::user()->name }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end custom-dropdown" aria-labelledby="navbarDropdownMobile">
                    <li><a class="dropdown-item" href="{{ url('/serenity/profile') }}">Profil</a></li>
                    <li>
                        <form method="POST" action="{{ route('public.logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger" type="submit">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        @else
            <!-- Placeholder kosong agar tetap seimbang -->
            <div style="width: 60px;"></div>
        @endauth
    </div>
</div>


        <!-- SEARCH BAR -->
        <form class="d-flex mx-auto search-form position-relative"
              role="search"
              autocomplete="off"
              method="GET"
              action="{{ route('public.artikel') }}">
            <input id="searchInput"
                   name="q"
                   class="form-control me-2"
                   type="search"
                   placeholder="Telusuri artikel..."
                   aria-label="Search">
            <div id="searchResults"
                 class="list-group position-absolute mt-1 w-200 shadow"
                 style="z-index: 1000; display: none; top: 110%;">
            </div>
        </form>

        <!-- Navbar Links & Desktop Notif/User -->
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
              @auth
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('serenity/konseling*') ? 'active' : '' }}"
                       href="{{ route('public.konseling.index') }}">Konseling Digital</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('serenity/lapor*') ? 'active' : '' }}"
                       href="{{ url('/serenity/lapor') }}">Lapor</a>
                </li>
              @endauth
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('serenity/artikel*') ? 'active' : '' }}"
                      href="{{ route('public.artikel') }}">Artikel</a>
                </li>
            </ul>

            <!-- 🔔 Notifikasi + User versi DESKTOP -->
            <ul class="navbar-nav ms-auto desktop-only d-none d-lg-flex">
                @auth
                <!-- Notifikasi -->
                <li class="nav-item dropdown me-3 notification-dropdown">
                    <button id="admin-notification-button"
                            class="btn position-relative"
                            type="button">
                        <i class="fas fa-bell" style="color: #ffffff"></i>
                        <span id="admin-notification-badge"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            style="display:none">0</span>
                    </button>
                    <div id="admin-notification-dropdown-menu"
                        class="dropdown-menu dropdown-menu-end p-0 shadow">
                        <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-light">
                            <h6 class="mb-0 fw-bold">Notifikasi</h6>
                            <div class="btn-group btn-group-sm">
                                <button id="mark-all-read-btn"
                                        class="btn btn-outline-secondary"
                                        title="Tandai semua sebagai dibaca">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button id="delete-all-read-btn"
                                        class="btn btn-outline-danger"
                                        title="Hapus semua yang sudah dibaca">
                                        <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div id="admin-notification-list" class="overflow-auto" style="max-height:400px;">
                            <div id="loading-notifications" class="text-center p-3">
                                <div class="spinner-border spinner-border-sm" role="status"></div>
                                <div class="mt-2 small">Memuat...</div>
                            </div>
                        </div>
                    </div>
                </li>
                <!-- User -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle fw-bold" href="#" id="navbarDropdown"
                       role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end custom-dropdown" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="{{ url('/serenity/profile') }}">Profil</a></li>
                        <li>
                            <form method="POST" action="{{ route('public.logout') }}">
                                @csrf
                                <button class="dropdown-item text-danger" type="submit">Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

@push('scripts')
<script>
// Make functions globally accessible first
window.showToast = window.showToast || function(message, type = 'success') {
    console.log('Toast:', message, type);
};

window.showConfirm = window.showConfirm || function(message, callback, title = 'Konfirmasi') {
    if (confirm(message)) {
        callback();
    }
};

// Notification System Class
class PublicNotificationSystem {
    constructor() {
        this.notifications = [];
        this.isLoading = false;

        // Desktop elements
        this.desktopBadge = document.getElementById("admin-notification-badge");
        this.desktopList = document.getElementById("admin-notification-list");
        this.desktopDropdown = document.getElementById("admin-notification-dropdown-menu");
        this.desktopButton = document.getElementById("admin-notification-button");

        // Mobile elements
        this.mobileBadge = document.getElementById("admin-notification-badge-mobile");
        this.mobileList = document.getElementById("admin-notification-list-mobile");
        this.mobileDropdown = document.getElementById("admin-notification-dropdown-menu-mobile");
        this.mobileButton = document.getElementById("admin-notification-button-mobile");

        this.currentOpenDropdown = null; // 'desktop' or 'mobile'
    }

    init() {
        console.log('Initializing public notification system...');
        this.setupEventListeners();
        this.fetchNotifications();
        this.setupPeriodicRefresh();
        this.setupWebSocket();
    }

    setupEventListeners() {
        // Desktop button
        if (this.desktopButton) {
            this.desktopButton.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleDropdown('desktop');
            });
        }

        // Mobile button
        if (this.mobileButton) {
            this.mobileButton.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleDropdown('mobile');
            });
        }

        // Desktop action buttons
        const desktopMarkAllBtn = document.getElementById('mark-all-read-btn');
        if (desktopMarkAllBtn) {
            desktopMarkAllBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.markAllAsRead();
            });
        }

        const desktopDeleteAllBtn = document.getElementById('delete-all-read-btn');
        if (desktopDeleteAllBtn) {
            desktopDeleteAllBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.deleteAllRead();
            });
        }

        // Mobile action buttons
        const mobileMarkAllBtn = document.getElementById('mark-all-read-btn-mobile');
        if (mobileMarkAllBtn) {
            mobileMarkAllBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.markAllAsRead();
            });
        }

        const mobileDeleteAllBtn = document.getElementById('delete-all-read-btn-mobile');
        if (mobileDeleteAllBtn) {
            mobileDeleteAllBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.deleteAllRead();
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (event) => {
            if (!event.target.closest('.notification-dropdown')) {
                this.closeAllDropdowns();
            }
        });
    }

    toggleDropdown(type) {
        console.log('Toggle dropdown:', type);
        const dropdown = type === 'desktop' ? this.desktopDropdown : this.mobileDropdown;

        if (!dropdown) {
            console.error('Dropdown not found for:', type);
            return;
        }

        const isOpen = dropdown.classList.contains('show');

        // Close all dropdowns first
        this.closeAllDropdowns();

        // If it wasn't open, open it
        if (!isOpen) {
            dropdown.classList.add('show');
            this.currentOpenDropdown = type;

            if (this.notifications.length === 0 || this.isLoading) {
                this.showLoadingState(type);
                this.fetchNotifications();
            } else {
                this.renderNotifications(type);
            }
        }
    }

    closeAllDropdowns() {
        if (this.desktopDropdown) {
            this.desktopDropdown.classList.remove('show');
        }
        if (this.mobileDropdown) {
            this.mobileDropdown.classList.remove('show');
        }
        this.currentOpenDropdown = null;
    }

    setupPeriodicRefresh() {
        setInterval(() => this.fetchNotifications(), 30000);
    }

    setupWebSocket() {
        if (typeof window.Echo !== 'undefined' && window.Echo) {
            @if(Auth::check())
                try {
                    @if(Auth::user()->role === 'siswa')
                        window.Echo.private(`user.{{ Auth::id() }}`)
                            .listen('.notification.sent', (event) => this.handleRealTimeNotification(event));
                    @endif
                } catch (error) {
                    console.warn('WebSocket connection failed, using polling fallback');
                }
            @endif
        }
    }

    handleRealTimeNotification(event) {
        const notification = {
            id: 'temp-' + Date.now(),
            message: event.message || 'Notifikasi baru',
            type: event.type || 'general',
            url: event.url || null,
            time: 'Baru saja',
            isNew: true,
            read: false
        };

        this.notifications.unshift(notification);
        this.updateBadges();

        if (this.currentOpenDropdown) {
            this.renderNotifications(this.currentOpenDropdown);
        }
    }

    fetchNotifications() {
        if (this.isLoading) return;

        this.isLoading = true;

        fetch('/serenity/notifications', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.getCSRFToken()
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && Array.isArray(data.data)) {
                this.notifications = data.data;
            } else {
                this.notifications = [];
            }
            this.updateBadges();

            if (this.currentOpenDropdown) {
                this.renderNotifications(this.currentOpenDropdown);
            }
        })
        .catch(error => {
            console.error('Error fetching notifications:', error);
            this.notifications = [];
            this.updateBadges();

            if (this.currentOpenDropdown) {
                this.showErrorState(this.currentOpenDropdown);
            }
        })
        .finally(() => {
            this.isLoading = false;
        });
    }

    renderNotifications(type) {
        const list = type === 'desktop' ? this.desktopList : this.mobileList;
        if (!list) return;

        // Remove loading indicator
        const loadingEl = list.querySelector(`#loading-notifications${type === 'mobile' ? '-mobile' : ''}`);
        if (loadingEl) loadingEl.remove();

        if (!this.notifications || this.notifications.length === 0) {
            this.showEmptyState(type);
            return;
        }

        list.innerHTML = '';
        this.notifications.forEach((notification) => {
            const element = this.createNotificationElement(notification);
            list.appendChild(element);
        });
    }

    createNotificationElement(notification) {
        const element = document.createElement("div");
        element.className = `notification-item border-bottom position-relative ${notification.read ? 'read' : 'unread'}`;
        element.dataset.id = notification.id;

        element.innerHTML = `
            <div class="d-flex align-items-start p-3">
                <div class="me-2 mt-1">
                    ${this.getNotificationIcon(notification.type)}
                </div>
                <div class="notification-clickable-area flex-grow-1"
                     data-url="${notification.url || ''}"
                     data-id="${notification.id}"
                     style="cursor: ${notification.url ? 'pointer' : 'default'}">
                    <div class="notification-message mb-1">${this.safeHtml(notification.message)}</div>
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>${this.safeHtml(notification.time)}
                    </small>
                </div>
                <div class="notification-actions ms-2 d-flex flex-column align-items-end">
                    ${!notification.read ?
                        '<span class="badge bg-primary rounded-pill mb-1">Baru</span>' :
                        '<span class="badge bg-secondary rounded-pill mb-1">Dibaca</span>'}
                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                            data-notification-id="${notification.id}"
                            title="Hapus notifikasi">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;

        // Add click handler
        const clickableArea = element.querySelector('.notification-clickable-area');
        if (clickableArea) {
            const url = clickableArea.dataset.url;
            if (url && url !== 'null' && url !== 'undefined' && url !== '') {
                clickableArea.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.redirectToNotification(url, clickableArea.dataset.id);
                });
            }
        }

        // Add delete handler
        const deleteBtn = element.querySelector('.delete-btn');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                e.preventDefault();
                this.deleteNotification(notification.id, deleteBtn);
            });
        }

        return element;
    }

    getNotificationIcon(type) {
        const icons = {
            'like': '<i class="fas fa-heart text-danger"></i>',
            'comment': '<i class="fas fa-comment text-primary"></i>',
            'reply': '<i class="fas fa-reply text-info"></i>',
            'konseling': '<i class="fas fa-user-friends text-success"></i>',
            'lapor': '<i class="fas fa-exclamation-triangle text-warning"></i>',
            'general': '<i class="fas fa-bell text-secondary"></i>',
            'chat_reply': '<i class="fas fa-comments text-success"></i>',
            'chat_message': '<i class="fas fa-comment-dots text-primary"></i>'
        };
        return icons[type] || icons['general'];
    }

    updateBadges() {
        const unreadCount = this.notifications.filter(n => !n.read).length;

        // Update both badges
        [this.desktopBadge, this.mobileBadge].forEach(badge => {
            if (badge) {
                if (unreadCount > 0) {
                    badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                    badge.style.display = "inline-block";
                } else {
                    badge.style.display = "none";
                }
            }
        });
    }

    markAllAsRead() {
        const unreadNotifications = this.notifications.filter(n => !n.read);
        if (unreadNotifications.length === 0) {
            if (typeof window.showToast === 'function') {
                window.showToast('Semua notifikasi sudah dibaca.');
            }
            return;
        }

        fetch('/serenity/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.getCSRFToken()
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' || data.success) {
                this.notifications.forEach(n => {
                    n.read = true;
                    n.isNew = false;
                });
                this.updateBadges();
                if (this.currentOpenDropdown) {
                    this.renderNotifications(this.currentOpenDropdown);
                }
                if (typeof window.showToast === 'function') {
                    window.showToast('Semua notifikasi berhasil ditandai sebagai dibaca.');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    deleteNotification(notificationId, buttonElement) {
        const self = this;
        if (typeof window.showConfirm === 'function') {
            window.showConfirm('Yakin ingin menghapus notifikasi ini?', function() {
                self.performDelete(notificationId, buttonElement);
            }, 'Konfirmasi Hapus');
        } else {
            if (confirm('Yakin ingin menghapus notifikasi ini?')) {
                this.performDelete(notificationId, buttonElement);
            }
        }
    }

    performDelete(notificationId, buttonElement) {
        const originalIcon = buttonElement.innerHTML;
        buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        buttonElement.disabled = true;

        fetch('/serenity/notifications/delete', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.getCSRFToken()
            },
            body: JSON.stringify({ notification_ids: [notificationId] })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' || data.success) {
                this.notifications = this.notifications.filter(n => n.id !== notificationId);
                this.updateBadges();

                const notificationEl = buttonElement.closest('.notification-item');
                if (notificationEl) {
                    notificationEl.remove();
                }

                if (this.notifications.length === 0 && this.currentOpenDropdown) {
                    this.showEmptyState(this.currentOpenDropdown);
                }

                if (typeof window.showToast === 'function') {
                    window.showToast('Notifikasi berhasil dihapus.');
                }
            } else {
                buttonElement.innerHTML = originalIcon;
                buttonElement.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error deleting notification:', error);
            buttonElement.innerHTML = originalIcon;
            buttonElement.disabled = false;
        });
    }

    deleteAllRead() {
        const readNotifications = this.notifications.filter(n => n.read);
        if (readNotifications.length === 0) {
            if (typeof window.showToast === 'function') {
                window.showToast('Tidak ada notifikasi yang sudah dibaca untuk dihapus.');
            }
            return;
        }

        const self = this;
        if (typeof window.showConfirm === 'function') {
            window.showConfirm('Yakin ingin menghapus semua notifikasi yang sudah dibaca?', function() {
                self.performDeleteAllRead();
            }, 'Konfirmasi Hapus');
        } else {
            if (confirm('Yakin ingin menghapus semua notifikasi yang sudah dibaca?')) {
                this.performDeleteAllRead();
            }
        }
    }

    performDeleteAllRead() {
        fetch('/serenity/notifications/delete-read', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.getCSRFToken()
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' || data.success) {
                this.notifications = this.notifications.filter(n => !n.read);
                this.updateBadges();
                if (this.currentOpenDropdown) {
                    this.renderNotifications(this.currentOpenDropdown);
                }
                if (typeof window.showToast === 'function') {
                    window.showToast(`${data.deleted || 0} notifikasi telah dihapus.`);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    redirectToNotification(url, notificationId) {
        if (!url || url === 'null' || url === 'undefined') return;

        // Update UI immediately in both lists
        document.querySelectorAll(`.notification-item[data-id="${notificationId}"]`).forEach(item => {
            item.classList.remove('unread');
            item.classList.add('read');
            const badge = item.querySelector(".badge.bg-primary");
            if (badge) {
                badge.classList.remove('bg-primary');
                badge.classList.add('bg-secondary');
                badge.textContent = 'Dibaca';
            }
        });

        // Update notification in array
        const notification = this.notifications.find(n => n.id === notificationId);
        if (notification) {
            notification.read = true;
            notification.isNew = false;
        }

        this.updateBadges();

        // Mark as read in backend
        fetch('/serenity/notifications/mark-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.getCSRFToken()
            },
            body: JSON.stringify({ notification_ids: [notificationId] })
        })
        .then(() => {
            window.location.href = url;
        })
        .catch(error => {
            console.error('Mark-read failed:', error);
            window.location.href = url;
        });
    }

    showLoadingState(type) {
        const list = type === 'desktop' ? this.desktopList : this.mobileList;
        const loadingId = type === 'mobile' ? 'loading-notifications-mobile' : 'loading-notifications';

        if (list) {
            list.innerHTML = `
                <div id="${loadingId}" class="text-center p-3">
                    <div class="spinner-border spinner-border-sm" role="status"></div>
                    <div class="mt-2 small">Memuat...</div>
                </div>
            `;
        }
    }

    showEmptyState(type) {
        const list = type === 'desktop' ? this.desktopList : this.mobileList;
        if (list) {
            list.innerHTML = `
                <div class="text-center p-4 text-muted">
                    <i class="fas fa-bell-slash fs-4 mb-3"></i>
                    <div class="mb-2">Tidak ada notifikasi</div>
                    <small class="text-muted">Notifikasi akan muncul di sini</small>
                </div>
            `;
        }
    }

    showErrorState(type) {
        const list = type === 'desktop' ? this.desktopList : this.mobileList;
        if (list) {
            list.innerHTML = `
                <div class="text-center p-4 text-danger">
                    <i class="fas fa-exclamation-triangle text-danger fs-4 mb-2"></i>
                    <div>Gagal memuat notifikasi</div>
                    <small class="text-muted mb-3 d-block">Coba muat ulang halaman</small>
                    <button onclick="window.location.reload()" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-redo"></i> Muat Ulang
                    </button>
                </div>
            `;
        }
    }

    safeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        let safe = div.innerHTML;
        safe = safe
            .replace(/&lt;b&gt;/g, '<b>').replace(/&lt;\/b&gt;/g, '</b>')
            .replace(/&lt;i&gt;/g, '<i>').replace(/&lt;\/i&gt;/g, '</i>')
            .replace(/&lt;u&gt;/g, '<u>').replace(/&lt;\/u&gt;/g, '</u>');
        return safe;
    }

    getCSRFToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return token ? token.content : '';
    }
}

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", function() {
    console.log('Initializing public notification system...');
    window.publicNotificationSystem = new PublicNotificationSystem();
    window.publicNotificationSystem.init();
});

// Global wrapper functions
window.toggleNotificationDropdown = function(type) {
    if (window.publicNotificationSystem) {
        window.publicNotificationSystem.toggleDropdown(type);
    }
};

window.markAllAsRead = function() {
    if (window.publicNotificationSystem) {
        window.publicNotificationSystem.markAllAsRead();
    }
};

window.deleteAllRead = function() {
    if (window.publicNotificationSystem) {
        window.publicNotificationSystem.deleteAllRead();
    }
};
</script>
@endpush
