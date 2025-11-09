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
.notification-item {
    transition: all 0.2s ease;
    border-bottom: 1px solid #f0f0f0;
    position: relative;
}

.notification-item.read {
    background-color: #ffffff !important;
    opacity: 0.7;
}

.notification-item.unread {
    background-color: #f0f8ff !important;
    border-left: 3px solid #007bff;
}

.notification-item:hover {
    background-color: #f8f9fa !important;
    transform: translateX(2px);
}

.notification-item:last-child {
    border-bottom: none;
}

/* Fix for clickable notifications */
.notification-item .notification-clickable-area {
    cursor: pointer;
    flex-grow: 1;
    padding: 4px 0;
}

.notification-item .notification-actions {
    flex-shrink: 0;
    gap: 4px;
}

/* Notification icon styling */
.notification-icon {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: #f8f9fa;
    flex-shrink: 0;
}

.notification-icon i {
    font-size: 16px;
}

/* Badge improvements */
.notification-item .badge {
    font-size: 0.65rem;
    padding: 3px 8px;
    font-weight: 600;
}

/* Delete button styling */
.delete-single-btn {
    padding: 4px 8px;
    font-size: 12px;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.delete-single-btn:hover {
    background-color: #dc3545;
    color: white;
    transform: scale(1.05);
}

/* Notification header improvements */
.notification-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 2px solid #dee2e6;
    padding: 16px;
    border-radius: 12px 12px 0 0;
}

.notification-header h6 {
    font-size: 16px;
    font-weight: 700;
    color: #333;
    margin: 0;
}

.notification-header .btn-group button {
    border-radius: 6px;
    padding: 6px 10px;
    font-size: 13px;
    transition: all 0.2s ease;
}

.notification-header .btn-group button:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Notification dropdown width fix */
.notification-dropdown-menu {
  width: 420px;
  max-width: 90vw;
  overflow-x: hidden;
  white-space: normal;
  right: 0 !important;
  left: auto !important;
  max-height: 500px;
  overflow-y: auto;
  border-radius: 12px;
  border: 1px solid rgba(0,0,0,0.08);
  box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

/* Search bar extra mini */
.custom-navbar .form-control {
  font-size: 0.6rem;
  padding: 2px 6px;
  height: 25px;
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

.dropdown-menu,
.custom-dropdown {
  text-align: left !important;
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

.username-truncate {
    display: inline-block;
    max-width: 80px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    vertical-align: middle;
}


/* ================= MOBILE RESPONSIVE ================= */
@media (max-width: 991.98px) {
.notification-dropdown-menu {
    position: fixed !important;
    top: 60px !important;
    left: 50% !important;
    right: auto !important;
    transform: translateX(-50%) !important;
    height: auto;
    max-height: 70vh;
    width: 90vw !important;
    max-width: 400px;
    overflow-y: auto;
    border-radius: 12px;
    z-index: 1055;
    background-color: #fff;
    box-shadow: 0 8px 32px rgba(0,0,0,0.2);
  }

  .custom-navbar .container {
    display: flex;
    flex-direction: column;
    align-items: stretch;
  }

  .navbar-toggler,
  .custom-navbar .navbar-brand,
  .navbar-mobile-icons {
    order: 1;
  }

  .custom-navbar .container {
    position: relative;
  }

  .navbar-top-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
  }

  .navbar-toggler {
    margin-right: auto;
    border: none;
    padding: 4px 8px;
  }

  .custom-navbar .navbar-brand {
    margin: 0 auto;
    text-align: center;
  }

  .navbar-mobile-icons {
    display: flex !important;
    align-items: center;
    gap: 10px;
    margin-left: auto;
  }

  .custom-navbar .search-form {
    order: 2;
    width: 100%;
    margin-top: 8px;
  }

  .custom-navbar .search-form input {
    width: 100%;
  }

  .navbar-nav.ms-auto.desktop-only {
    display: none !important;
  }

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

  .navbar-nav {
    margin-left: 0 !important;
    margin-right: 0 !important;
  }
}

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
                    <div class="dropdown notification-dropdown">
                        <button id="notification-button-mobile"
                                class="btn position-relative"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                            <i class="fas fa-bell" style="color: #ffffff"></i>
                            <span id="notification-badge-mobile"
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                style="display:none">0</span>
                        </button>
                        <div id="notification-dropdown-mobile"
                            class="dropdown-menu notification-dropdown-menu p-0 shadow">
                            <div class="notification-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Notifikasi</h6>
                                    <small class="text-muted" id="public-notification-count-text-mobile">Memuat...</small>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-secondary mark-all-read-btn align-center"
                                            title="Tandai semua sebagai dibaca">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-outline-danger delete-all-read-btn"
                                            title="Hapus semua yang sudah dibaca">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="notification-list overflow-auto" style="max-height:400px;">
                                <div class="loading-notifications text-center p-3">
                                    <div class="spinner-border spinner-border-sm" role="status"></div>
                                    <div class="mt-2 small">Memuat...</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User -->
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle fw-bold text-white username-truncate" href="#" id="navbarDropdownMobile"
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

            <!-- Desktop Notif/User -->
            <ul class="navbar-nav ms-auto desktop-only d-none d-lg-flex">
                @auth
                <!-- Notification -->
                <li class="nav-item dropdown me-3 notification-dropdown">
                    <button id="notification-button-desktop"
                            class="btn position-relative"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                        <i class="fas fa-bell" style="color: #ffffff"></i>
                        <span id="notification-badge-desktop"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            style="display:none">0</span>
                    </button>
                    <div id="notification-dropdown-desktop"
                        class="dropdown-menu notification-dropdown-menu p-0 shadow">
                        <div class="notification-header d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Notifikasi</h6>
                                <small class="text-muted" id="public-notification-count-text-desktop">Memuat...</small>
                            </div>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-secondary mark-all-read-btn"
                                        title="Tandai semua sebagai dibaca">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-outline-danger delete-all-read-btn"
                                        title="Hapus semua yang sudah dibaca">
                                        <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="notification-list overflow-auto" style="max-height:400px;">
                            <div class="loading-notifications text-center p-3">
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
// Ensure global functions exist
window.showToast = window.showToast || function(message, type = 'success') {
    console.log('Toast:', message, type);
    alert(message);
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
        this.initialized = false;
    }

    init() {
        if (this.initialized) return;

        console.log('Initializing public notification system...');
        this.setupEventListeners();
        this.fetchNotifications();
        this.setupPeriodicRefresh();
        this.setupWebSocket();
        this.initialized = true;
    }

    setupEventListeners() {
        // Setup button listeners using event delegation
        document.addEventListener('click', (e) => {
            // Mark all as read
            if (e.target.closest('.mark-all-read-btn')) {
                e.preventDefault();
                e.stopPropagation();
                this.markAllAsRead();
            }
            
            // Delete all read
            if (e.target.closest('.delete-all-read-btn')) {
                e.preventDefault();
                e.stopPropagation();
                this.deleteAllRead();
            }
            
            // Delete single notification
            if (e.target.closest('.delete-single-btn')) {
                e.preventDefault();
                e.stopPropagation();
                const btn = e.target.closest('.delete-single-btn');
                const notifId = btn.dataset.notificationId;
                if (notifId) {
                    this.deleteNotification(notifId, btn);
                }
            }
        });

        // Handle Bootstrap dropdown events for both mobile and desktop
        const dropdowns = document.querySelectorAll('.notification-dropdown');
        dropdowns.forEach(dropdown => {
            dropdown.addEventListener('show.bs.dropdown', () => {
                if (this.notifications.length === 0 || this.isLoading) {
                    this.showLoadingState();
                    if (this.notifications.length === 0) {
                        this.fetchNotifications();
                    }
                }
            });

            dropdown.addEventListener('shown.bs.dropdown', () => {
                if (this.notifications.length > 0) {
                    this.renderNotifications();
                }
            });
        });
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
        this.updateNotificationCountText();

        const dropdownOpened = document.querySelector('.notification-dropdown .dropdown-menu.show');
        if (dropdownOpened) {
            this.renderNotifications();
        }
    }

    fetchNotifications() {
        if (this.isLoading) return;

        this.isLoading = true;
        this.showLoadingState();

        fetch('/serenity/notifications', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.getCSRFToken()
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            console.log('Fetched notifications:', data);
            if (data.success && Array.isArray(data.data)) {
                this.notifications = data.data;
            } else {
                this.notifications = [];
            }
            this.updateBadges();
            this.updateNotificationCountText();
            this.renderNotifications();
        })
        .catch(error => {
            console.error('Error fetching notifications:', error);
            this.notifications = [];
            this.updateBadges();
            this.updateNotificationCountText();
            this.showErrorState();
        })
        .finally(() => {
            this.isLoading = false;
        });
    }

    renderNotifications() {
        const lists = document.querySelectorAll('.notification-list');

        lists.forEach(list => {
            const loading = list.querySelector('.loading-notifications');
            if (loading) loading.remove();

            if (!this.notifications || this.notifications.length === 0) {
                list.innerHTML = `
                    <div class="text-center p-4 text-muted">
                        <i class="fas fa-bell-slash fs-4 mb-3"></i>
                        <div class="mb-2">Tidak ada notifikasi</div>
                        <small class="text-muted">Notifikasi akan muncul di sini</small>
                    </div>
                `;
                this.updateNotificationCountText();
                return;
            }

            list.innerHTML = '';
            this.notifications.forEach((notification) => {
                const element = this.createNotificationElement(notification);
                list.appendChild(element);
            });

            this.updateNotificationCountText();
        });
    }

    createNotificationElement(notification) {
        const element = document.createElement("div");
        element.className = `notification-item position-relative ${notification.read ? 'read' : 'unread'}`;
        element.dataset.id = notification.id;

        const hasUrl = notification.url && notification.url !== 'null' && notification.url !== 'undefined' && notification.url !== '';

        element.innerHTML = `
            <div class="d-flex align-items-start p-3 gap-3">
                <div class="notification-icon">
                    ${this.getNotificationIcon(notification.type)}
                </div>
                <div class="notification-clickable-area flex-grow-1"
                     style="cursor: pointer; min-width: 0;">
                    <div class="notification-message mb-1 fw-500" style="font-size: 14px; line-height: 1.4;">
                        ${this.safeHtml(notification.message)}
                    </div>
                    <small class="text-muted d-flex align-items-center" style="font-size: 12px;">
                        <i class="fas fa-clock me-1"></i>${this.safeHtml(notification.time)}
                    </small>
                </div>
                <div class="notification-actions d-flex flex-column align-items-end gap-2">
                    ${!notification.read ?
                        '<span class="badge bg-primary rounded-pill">Baru</span>' :
                        '<span class="badge bg-secondary rounded-pill">Dibaca</span>'}
                    <button type="button" class="btn btn-sm btn-outline-danger delete-single-btn"
                            data-notification-id="${notification.id}"
                            title="Hapus notifikasi">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;

        const clickableArea = element.querySelector('.notification-clickable-area');
        if (clickableArea) {
            clickableArea.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                // Don't do anything if already read
                if (notification.read) {
                    return;
                }
                
                if (hasUrl) {
                    this.redirectToNotification(notification.url, notification.id);
                } else {
                    // Mark as read without redirecting
                    this.markNotificationAsRead(notification.id);
                }
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
        const badges = document.querySelectorAll('[id^="notification-badge-"]');

        badges.forEach(badge => {
            if (unreadCount > 0) {
                badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                badge.style.display = "inline-block";
            } else {
                badge.style.display = "none";
            }
        });
    }

    updateNotificationCountText() {
        const totalCount = this.notifications.length;
        const unreadCount = this.notifications.filter(n => !n.read).length;
        
        const countTextMobile = document.getElementById('public-notification-count-text-mobile');
        const countTextDesktop = document.getElementById('public-notification-count-text-desktop');
        
        const textContent = totalCount === 0 
            ? 'Tidak ada notifikasi' 
            : `${totalCount} total • ${unreadCount} belum dibaca`;
        
        if (countTextMobile) countTextMobile.textContent = textContent;
        if (countTextDesktop) countTextDesktop.textContent = textContent;
    }

    markAllAsRead() {
        const unreadNotifications = this.notifications.filter(n => !n.read);
        if (unreadNotifications.length === 0) {
            if (typeof window.showToast === 'function') {
                window.showToast('Semua notifikasi sudah dibaca.', 'info');
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
                this.updateNotificationCountText();
                this.renderNotifications();
                if (typeof window.showToast === 'function') {
                    window.showToast('Semua notifikasi berhasil ditandai sebagai dibaca.', 'success');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof window.showToast === 'function') {
                window.showToast('Gagal menandai notifikasi sebagai dibaca.', 'danger');
            }
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
                this.updateNotificationCountText();
                this.renderNotifications();

                if (typeof window.showToast === 'function') {
                    window.showToast('Notifikasi berhasil dihapus.', 'success');
                }
            } else {
                buttonElement.innerHTML = originalIcon;
                buttonElement.disabled = false;
                if (typeof window.showToast === 'function') {
                    window.showToast('Gagal menghapus notifikasi.', 'danger');
                }
            }
        })
        .catch(error => {
            console.error('Error deleting notification:', error);
            buttonElement.innerHTML = originalIcon;
            buttonElement.disabled = false;
            if (typeof window.showToast === 'function') {
                window.showToast('Gagal menghapus notifikasi.', 'danger');
            }
        });
    }

    deleteAllRead() {
        const readNotifications = this.notifications.filter(n => n.read);
        if (readNotifications.length === 0) {
            if (typeof window.showToast === 'function') {
                window.showToast('Tidak ada notifikasi yang sudah dibaca untuk dihapus.', 'info');
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
                this.updateNotificationCountText();
                this.renderNotifications();
                if (typeof window.showToast === 'function') {
                    window.showToast(`${data.deleted || 0} notifikasi telah dihapus.`, 'success');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof window.showToast === 'function') {
                window.showToast('Gagal menghapus notifikasi.', 'danger');
            }
        });
    }

    markNotificationAsRead(notificationId) {
        console.log('Marking notification as read:', notificationId);

        // Update UI immediately with animation
        document.querySelectorAll(`.notification-item[data-id="${notificationId}"]`).forEach(item => {
            item.style.transition = 'all 0.3s ease';
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
        this.updateNotificationCountText();

        // Mark as read in backend
        fetch('/serenity/notifications/mark-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.getCSRFToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ notification_ids: [notificationId] })
        })
        .then(response => {
            console.log('Mark-read response status:', response.status);
            if (!response.ok) {
                console.error('Mark-read response not OK:', response.status);
                throw new Error('Failed to mark as read');
            }
            return response.json();
        })
        .then(data => {
            console.log('Mark-read response data:', data);
            if (data.success || data.status === 'success') {
                if (typeof window.showToast === 'function') {
                    window.showToast('Notifikasi ditandai sebagai dibaca', 'success');
                }
            }
        })
        .catch(error => {
            console.error('Mark-read failed:', error);
            if (typeof window.showToast === 'function') {
                window.showToast('Gagal menandai notifikasi sebagai dibaca', 'danger');
            }
        });
    }

    redirectToNotification(url, notificationId) {
        if (!url || url === 'null' || url === 'undefined') return;

        console.log('Redirecting to notification:', notificationId, url);

        // Update UI immediately
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
        this.updateNotificationCountText();

        // Mark as read in backend
        fetch('/serenity/notifications/mark-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.getCSRFToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ notification_ids: [notificationId] })
        })
        .then(response => {
            console.log('Mark-read response status:', response.status);
            if (!response.ok) {
                console.error('Mark-read response not OK:', response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Mark-read response data:', data);
            window.location.href = url;
        })
        .catch(error => {
            console.error('Mark-read failed:', error);
            window.location.href = url;
        });
    }

    showLoadingState() {
        const lists = document.querySelectorAll('.notification-list');
        lists.forEach(list => {
            list.innerHTML = `
                <div class="loading-notifications text-center p-3">
                    <div class="spinner-border spinner-border-sm" role="status"></div>
                    <div class="mt-2 small">Memuat...</div>
                </div>
            `;
        });

        const countTextMobile = document.getElementById('public-notification-count-text-mobile');
        const countTextDesktop = document.getElementById('public-notification-count-text-desktop');
        if (countTextMobile) countTextMobile.textContent = 'Memuat...';
        if (countTextDesktop) countTextDesktop.textContent = 'Memuat...';
    }

    showErrorState() {
        const lists = document.querySelectorAll('.notification-list');
        lists.forEach(list => {
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
        });

        const countTextMobile = document.getElementById('public-notification-count-text-mobile');
        const countTextDesktop = document.getElementById('public-notification-count-text-desktop');
        if (countTextMobile) countTextMobile.textContent = 'Gagal memuat';
        if (countTextDesktop) countTextDesktop.textContent = 'Gagal memuat';
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
    console.log('DOM ready, initializing notification system...');
    window.publicNotificationSystem = new PublicNotificationSystem();
    window.publicNotificationSystem.init();
});

// Global helper functions for backward compatibility
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