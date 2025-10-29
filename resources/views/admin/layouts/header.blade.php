<style>
/* === Admin Navbar mirip Public === */
.admin-navbar {
  background-color: #837ab6;
  padding: 3px 10px;
  font-size: 0.65rem;
  position: fixed;
  top: 0;
  width: 100%;
  z-index: 1000;
}

/* Logo */
.admin-navbar .navbar-brand {
  display: flex;
  align-items: center;
  font-size: 0.7rem;
  font-weight: bold;
}
.admin-navbar .navbar-brand img {
  width: 30px;
  height: 30px;
  border-radius: 4px;
  margin-right: 6px;
}

/* Nav link */
.admin-navbar .nav-link {
  color: white !important;
  font-weight: 500;
  font-size: 0.65rem;
  margin-left: 3px;
  padding: 2px 4px;
}
.admin-navbar .nav-link:hover {
  color: #ffe9ff !important;
}
.admin-navbar .nav-link.active {
  border-bottom: 1px solid #fff;
}

/* === Dropdown umum (Profil + Notif) === */
.admin-navbar .dropdown-menu {
  border-radius: 6px;
  box-shadow: 0px 2px 6px rgba(0,0,0,0.1);
  font-size: 0.65rem;
  min-width: 120px;
  padding: 4px 0;
  max-width: 90vw;
  overflow-x: hidden;
  white-space: normal;
  right: 0;
  left: auto;
}

/* Item dropdown */
.admin-navbar .dropdown-item {
  font-size: 0.65rem;
  padding: 4px 10px;
  font-weight: 500;
}
.admin-navbar .dropdown-item:hover {
  background-color: #f5f5f5;
}
.admin-navbar .dropdown-item.text-danger {
  color: #e3342f;
}

/* === Dropdown Notifikasi khusus === */
#admin-notification-dropdown-menu {
  width: 380px;
  max-width: 90vw;
  right: 0;
  left: auto;
  overflow-x: hidden;
}
#admin-notification-list {
  max-height: 350px;
  overflow-y: auto;
  overflow-x: hidden;
}

/* Notifikasi item */
.notification-item.read {
  background-color: #f8f9fa !important;
  opacity: 0.85;
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
/* Notifikasi Icon lebih kecil */
#admin-notification-button i {
  font-size: 12px;
  line-height: 1;
}

/* Badge lebih kecil */
#admin-notification-badge {
  font-size: 0.55rem;
  padding: 2px 4px;
  transform: translate(-30%, -30%);
}
</style>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark admin-navbar">
    <div class="container-fluid">
    
        <!-- Logo / App Name -->
        <a class="navbar-brand fw-bold" style="font-size: 15px;" href="{{ url('/admin/dashboard') }}">
            <img src="{{ asset('images/logoSerenity.jpg') }}" alt="Logo" class="rounded me-2">
            Serenity
        </a>
        
        <ul class="navbar-nav ms-auto align-items-center">
            <!-- Notification -->
            <li class="nav-item dropdown me-3 notification-dropdown">
                <button id="admin-notification-button"
                        class="btn position-relative"
                        onclick="toggleNotificationDropdown()">
                    <i class="fas fa-bell" style="color: #ffffff"></i>
                    <span id="admin-notification-badge"
                          class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                          style="display:none;">0</span>
                </button>
                <div id="admin-notification-dropdown-menu"
                     class="dropdown-menu dropdown-menu-end p-0 shadow">
                    <!-- UPDATED: Added notification count text -->
                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-light">
                        <div>
                            <h6 class="mb-0 fw-bold">Notifikasi</h6>
                            <small class="text-muted" id="admin-notification-count-text">Memuat...</small>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <button onclick="markAllAsRead()" class="btn btn-outline-secondary" title="Tandai semua sebagai dibaca"><i class="fas fa-check"></i></button>
                            <button onclick="deleteAllRead()" class="btn btn-outline-danger" title="Hapus semua yang sudah dibaca"><i class="fas fa-trash"></i></button>
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

            <!-- Profile Dropdown - Fixed -->
            <li class="nav-item dropdown profile-dropdown">
                <button class="btn nav-link dropdown-toggle fw-bold text-white border-0" 
                        type="button"
                        id="profileDropdown" 
                        onclick="toggleProfileDropdown()"
                        style="background: transparent;">
                    {{ Auth::user()->name }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end" id="profileDropdownMenu">
                    <li><a class="dropdown-item" href="{{ route('admin.profile.index') }}">Profil</a></li>
                    <li>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger" type="submit">Logout</button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

@push('scripts')
<script>
// Profile dropdown toggle function
function toggleProfileDropdown() {
    const dropdownMenu = document.getElementById('profileDropdownMenu');
    if (dropdownMenu) {
        dropdownMenu.classList.toggle('show');
    }
}

// Close profile dropdown when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.profile-dropdown')) {
        const profileDropdown = document.getElementById('profileDropdownMenu');
        if (profileDropdown) {
            profileDropdown.classList.remove('show');
        }
    }
});

// FIXED: Complete notification JavaScript with all missing functions
document.addEventListener("DOMContentLoaded", function() {
    console.log('DOM loaded, initializing notifications...');
    
    // DOM Elements
    const badge = document.getElementById("admin-notification-badge");
    const list = document.getElementById("admin-notification-list");
    const dropdownMenu = document.getElementById("admin-notification-dropdown-menu");
    const notificationButton = document.getElementById("admin-notification-button");
    
    // State variables
    let notifications = [];
    let dropdownOpened = false;
    let isLoading = false;

    // Initialize
    init();

    function init() {
        console.log('Initializing notification system...');
        fetchNotifications();
        setupEventListeners();
        requestNotificationPermission();
    }

    function setupEventListeners() {
        // Close notification dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.notification-dropdown')) {
                closeNotificationDropdown();
            }
        });

        // Listen for real-time notifications (if Laravel Echo is available)
        if (typeof window.Echo !== 'undefined' && window.Echo) {
            @if(Auth::check())
                try {
                    @if(Auth::user()->role === 'konselor' || Auth::user()->role === 'counselor')
                        window.Echo.private(`konselor.{{ Auth::id() }}`)
                            .listen('.notification.sent', handleRealTimeNotification)
                            .error((error) => {
                                console.warn('Konselor channel WebSocket connection failed:', error);
                            });
                    @elseif(Auth::user()->role === 'admin')
                        window.Echo.channel('admin')
                            .listen('.notification.sent', handleRealTimeNotification)
                            .error((error) => {
                                console.warn('Admin public channel WebSocket connection failed:', error);
                            });
                    @elseif(Auth::user()->role === 'siswa')
                        window.Echo.private(`user.{{ Auth::id() }}`)
                            .listen('.notification.sent', handleRealTimeNotification)
                            .error((error) => {
                                console.warn('Student channel WebSocket connection failed:', error);
                            });
                    @endif
                } catch (error) {
                    console.warn('WebSocket connection failed, using polling fallback');
                }
            @endif
        } else {
            console.warn('Laravel Echo not available, using polling only');
        }

        // Periodic refresh fallback
        setInterval(fetchNotifications, 30000);
    }

    function handleRealTimeNotification(event) {
        console.log('New real-time notification:', event);
        
        const notification = {
            id: 'temp-' + Date.now(),
            message: event.message || 'Notifikasi baru',
            type: event.type || 'general',
            url: event.url || null,
            time: 'Baru saja',
            time_full: formatTime(new Date()),
            isNew: true,
            read: false
        };
        
        notifications.unshift(notification);
        updateBadge();
        updateNotificationCountText();
        
        if (dropdownOpened) {
            renderNotifications();
        }
        
        showBrowserNotification(notification.message);
    }

    function fetchNotifications() {
        if (isLoading) return;
        
        console.log('Fetching notifications...');
        isLoading = true;
        
        fetch('/serenity/notifications', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCSRFToken()
            }
        })
        .then(response => {
            console.log('Fetch response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Fetched notifications data:', data);
            if (data.success && Array.isArray(data.data)) {
                notifications = data.data;
                console.log('Processed notifications count:', notifications.length);
                
                if (notifications.length > 0) {
                    console.log('Sample notifications:', notifications.slice(0, 3));
                }
            } else {
                console.warn('Invalid notification data structure:', data);
                notifications = [];
            }
            updateBadge();
            updateNotificationCountText();
            
            if (dropdownOpened) {
                renderNotifications();
            }
        })
        .catch(error => {
            console.error('Error fetching notifications:', error);
            notifications = [];
            updateBadge();
            updateNotificationCountText();
            
            if (dropdownOpened) {
                showErrorState();
            }
        })
        .finally(() => {
            isLoading = false;
        });
    }

    // NEW FUNCTION: Update notification count text
    function updateNotificationCountText() {
        const totalCount = notifications.length;
        const unreadCount = notifications.filter(n => !n.read).length;
        const readCount = totalCount - unreadCount;
        
        const countText = document.getElementById('admin-notification-count-text');
        if (countText) {
            if (totalCount === 0) {
                countText.textContent = 'Tidak ada notifikasi';
            } else {
                countText.textContent = `${totalCount} total (${unreadCount} belum dibaca, ${readCount} sudah dibaca)`;
            }
        }
    }

    function renderNotifications() {
        console.log('Rendering notifications, count:', notifications.length);
        const loadingEl = document.getElementById('loading-notifications');
        if (loadingEl) loadingEl.remove();

        if (!notifications || notifications.length === 0) {
            showEmptyState();
            return;
        }

        list.innerHTML = '';
        notifications.forEach((notification, index) => {
            console.log(`Creating notification ${index}:`, notification);
            const element = createNotificationElement(notification);
            list.appendChild(element);
        });
        
        // Update count text after rendering
        updateNotificationCountText();
    }

    function createNotificationElement(notification) {
        console.log('Creating notification element for:', notification);
        
        const element = document.createElement("div");
        element.className = `notification-item border-bottom position-relative ${notification.read ? 'read' : 'unread'}`;
        element.dataset.id = notification.id;
        
        element.innerHTML = `
            <div class="d-flex align-items-start p-3">
                <div class="me-2 mt-1">
                    ${getNotificationIcon(notification.type)}
                </div>
                <div class="notification-clickable-area flex-grow-1" data-url="${notification.url || ''}" data-id="${notification.id}">
                    <div class="notification-message mb-1">${safeHtml(notification.message)}</div>
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>${safeHtml(notification.time)}
                    </small>
                </div>
                <div class="notification-actions ms-2 d-flex flex-column align-items-end">
                    ${!notification.read ? '<span class="badge bg-primary rounded-pill mb-1">Baru</span>' : '<span class="badge bg-secondary rounded-pill mb-1">Dibaca</span>'}
                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn" 
                            data-notification-id="${notification.id}"
                            title="Hapus notifikasi">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        
        const clickableArea = element.querySelector('.notification-clickable-area');
        if (clickableArea) {
            const url = clickableArea.dataset.url;
            if (url && url !== 'null' && url !== 'undefined' && url !== '') {
                console.log('Adding click handler for notification:', notification.id, 'URL:', url);
                clickableArea.style.cursor = 'pointer';
                
                clickableArea.addEventListener('click', function(e) {
                    console.log('Notification clickable area clicked!');
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const clickUrl = this.dataset.url;
                    const clickId = this.dataset.id;
                    
                    console.log('Redirecting to URL:', clickUrl, 'ID:', clickId);
                    
                    if (clickUrl && clickId) {
                        redirectToNotification(clickUrl, clickId);
                    }
                });
            } else {
                console.log('No valid URL for notification:', notification.id);
            }
        }
        
        const deleteBtn = element.querySelector('.delete-btn');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                console.log('Delete button clicked for:', notification.id);
                deleteNotification(notification.id, this);
            });
        }
        
        return element;
    }

    function getNotificationIcon(type) {
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

    function updateBadge() {
        const unreadCount = notifications.filter(n => !n.read).length;
        console.log('Updating badge, unread count:', unreadCount);
        
        if (badge) {
            if (unreadCount > 0) {
                badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                badge.style.display = "inline-block";
            } else {
                badge.style.display = "none";
            }
        }
    }

    function showEmptyState() {
        list.innerHTML = `
            <div class="text-center p-4 text-muted">
                <i class="fas fa-bell-slash fs-4 mb-3"></i>
                <div class="mb-2">Tidak ada notifikasi</div>
                <small class="text-muted">Notifikasi akan muncul di sini</small>
            </div>
        `;
        updateNotificationCountText();
    }

    function showErrorState() {
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
        updateNotificationCountText();
    }

    function showBrowserNotification(message) {
        if ('Notification' in window && Notification.permission === 'granted') {
            try {
                new Notification('Serenity', {
                    body: message,
                    icon: '/images/logoSerenity.jpg',
                    tag: 'serenity-notification'
                });
            } catch (error) {
                console.warn('Browser notification failed:', error);
            }
        }
    }

    function requestNotificationPermission() {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission().then(function(permission) {
                console.log('Notification permission:', permission);
            });
        }
    }

    function safeHtml(text) {
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

    function formatTime(date) {
        try {
            return date.toLocaleDateString('id-ID', { 
                day: 'numeric', 
                month: 'short', 
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (error) {
            return 'Baru saja';
        }
    }

    function getCSRFToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return token ? token.content : '';
    }

    // Global functions
    window.toggleNotificationDropdown = function() {
        console.log('Toggle notification dropdown called, current state:', dropdownOpened);
        if (dropdownOpened) {
            closeNotificationDropdown();
        } else {
            openNotificationDropdown();
        }
    };

    function openNotificationDropdown() {
        console.log('Opening notification dropdown');
        dropdownOpened = true;
        if (dropdownMenu) {
            dropdownMenu.classList.add('show');
        }
        
        if (notifications.length === 0 || isLoading) {
            list.innerHTML = `
                <div id="loading-notifications" class="text-center p-3">
                    <div class="spinner-border spinner-border-sm" role="status"></div>
                    <div class="mt-2 small">Memuat...</div>
                </div>
            `;
            fetchNotifications();
        } else {
            renderNotifications();
        }
    }

    function closeNotificationDropdown() {
        console.log('Closing notification dropdown');
        dropdownOpened = false;
        if (dropdownMenu) {
            dropdownMenu.classList.remove('show');
        }
    }

    window.markAllAsRead = function() {
        const unreadNotifications = notifications.filter(n => !n.read);
        if (unreadNotifications.length === 0) {
            showToast('Semua notifikasi sudah dibaca.');
            return;
        }

        console.log('Marking all as read...');

        fetch('/serenity/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCSRFToken()
            }
        })
        .then(response => {
            console.log('Mark all read response:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Mark all read data:', data);
            if (data.status === 'success' || data.success) {
                notifications.forEach(n => {
                    n.read = true;
                    n.isNew = false;
                });
                updateBadge();
                updateNotificationCountText();
                renderNotifications();
                showToast(data.message || 'Semua notifikasi berhasil ditandai sebagai dibaca.');
            } else {
                showToast('Gagal menandai notifikasi sebagai dibaca.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Terjadi kesalahan saat menandai notifikasi sebagai dibaca.');
        });
    };

    window.deleteNotification = function(notificationId, buttonElement) {
        showConfirm('Yakin ingin menghapus notifikasi ini?', function () {
            console.log('Deleting notification:', notificationId);
        
            const originalIcon = buttonElement.innerHTML;
            buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            buttonElement.disabled = true;
        
            fetch('/serenity/notifications/delete', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCSRFToken()
                },
                body: JSON.stringify({ notification_ids: [notificationId] })
            })
            .then(response => {
                console.log('Delete response:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Delete data:', data);
                if (data.status === 'success' || data.success) {
                    notifications = notifications.filter(n => n.id !== notificationId);
                    updateBadge();
                    updateNotificationCountText();
                    
                    const notificationEl = buttonElement.closest('.notification-item');
                    if (notificationEl) {
                        notificationEl.remove();
                    }
                    
                    if (notifications.length === 0) {
                        showEmptyState();
                    }
                    showToast('Notifikasi berhasil dihapus.');
                } else {
                    showToast('Gagal menghapus notifikasi.');
                    buttonElement.innerHTML = originalIcon;
                    buttonElement.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error deleting notification:', error);
                showToast('Terjadi kesalahan saat menghapus notifikasi.');
                buttonElement.innerHTML = originalIcon;
                buttonElement.disabled = false;
            });
        });
    };

    window.deleteAllRead = function() {
        const readNotifications = notifications.filter(n => n.read);
        if (readNotifications.length === 0) {
            showToast('Tidak ada notifikasi yang sudah dibaca untuk dihapus.');
            return;
        }

        showConfirm(`Yakin ingin menghapus ${readNotifications.length} notifikasi yang sudah dibaca?`, function () {
            console.log('Deleting all read notifications...');
    
            fetch('/serenity/notifications/delete-read', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCSRFToken()
                }
            })
            .then(response => {
                console.log('Delete all read response:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Delete all read data:', data);
                if (data.status === 'success' || data.success) {
                    notifications = notifications.filter(n => !n.read);
                    updateBadge();
                    updateNotificationCountText();
                    renderNotifications();
                    showToast(data.message || `${data.deleted || 0} notifikasi telah dihapus.`);
                } else {
                    showToast('Gagal menghapus notifikasi.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Terjadi kesalahan saat menghapus notifikasi.');
            });
        });
    };

    window.redirectToNotification = function(url, notificationId) {
        console.log('=== REDIRECT NOTIFICATION START ===');
        console.log('URL:', url);
        console.log('Notification ID:', notificationId);
        
        if (!url || url === 'null' || url === 'undefined') {
            console.error('Invalid URL provided for redirect:', url);
            return;
        }
        
        if (!notificationId) {
            console.error('No notification ID provided');
            return;
        }
        
        const item = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
        console.log('Found notification item:', item);
        
        if (item) {
            item.classList.remove('unread');
            item.classList.add('read');
            const badge = item.querySelector(".badge.bg-primary");
            if (badge) {
                badge.classList.remove('bg-primary');
                badge.classList.add('bg-secondary');
                badge.textContent = 'Dibaca';
            }
        }

        const notification = notifications.find(n => n.id === notificationId);
        if (notification) {
            notification.read = true;
            notification.isNew = false;
        }
        
        updateBadge();
        updateNotificationCountText();

        console.log('Sending mark-read request...');
        
        fetch('/serenity/notifications/mark-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCSRFToken()
            },
            body: JSON.stringify({ notification_ids: [notificationId] })
        })
        .then(response => {
            console.log('Mark-read response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Mark-read response data:', data);
            console.log('Redirecting to:', url);
            window.location.href = url;
        })
        .catch(error => {
            console.error('Mark-read failed:', error);
            console.log('Still redirecting to:', url);
            window.location.href = url;
        });
        
        console.log('=== REDIRECT NOTIFICATION END ===');
    };

    console.log('Notification system initialized');
});
</script>
@endpush