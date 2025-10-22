/**
 * Enhanced Chat System Helper Functions with Real-time Read Status
 * FIXED: Date separator duplication issue
 */
class ChatHelper {
    constructor() {
        this.pusher = null;
        this.channel = null;
        this.sessionId = null;
        this.userLocale = navigator.language || 'en-US';
        this.userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        this.lastDate = null;
        this.isInitialized = false;
        this.isPreviewMode = false;
        this.messageCache = new Set();
        this.currentUserType = 'student';
        this.markAsReadUrl = null;
        this.hasMarkedAsRead = false;
        this.isPageVisible = true;
        this.pendingReadMark = false;
        this.lastReadMarkTime = 0;
        this.dateTracker = {}; // ADDED: Track which dates have separators
    }

    initializePusher(pusherKey, pusherCluster, sessionId) {
        try {
            if (!sessionId || sessionId === 'null' || sessionId === null) {
                console.warn('Invalid session ID provided to Pusher initialization:', sessionId);
                return null;
            }

            console.log('🚀 Initializing Pusher...', {
                key: pusherKey,
                cluster: pusherCluster,
                session_id: sessionId
            });

            this.sessionId = sessionId;
            this.pusher = new Pusher(pusherKey, {
                cluster: pusherCluster,
                forceTLS: true,
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }
            });

            const channelName = `private-chat.${sessionId}`;
            console.log('📡 Subscribing to channel:', channelName);
            
            this.channel = this.pusher.subscribe(channelName);

            this.channel.bind('pusher:subscription_succeeded', () => {
                console.log('✅ Successfully subscribed to chat channel:', sessionId);
                console.log('🔌 Pusher connection state:', this.pusher.connection.state);
            });

            this.channel.bind('pusher:subscription_error', (error) => {
                console.error('❌ Channel subscription failed:', error);
                this.showError('Failed to connect to real-time chat. You may not receive new messages immediately.');
            });

            this.pusher.connection.bind('state_change', (states) => {
                console.log('🔄 Pusher connection state changed:', {
                    previous: states.previous,
                    current: states.current
                });
            });

            return this.channel;
        } catch (error) {
            console.error('Pusher initialization failed:', error);
            this.showError('Real-time chat connection failed');
            return null;
        }
    }

    setupConnectionHandlers() {
        if (!this.pusher) return;

        this.pusher.connection.bind('connected', () => {
            console.log('✅ Pusher connected');
        });
        
        this.pusher.connection.bind('disconnected', () => {
            console.log('⚠️ Pusher disconnected');
        });
        
        this.pusher.connection.bind('error', (error) => {
            console.error('❌ Pusher connection error:', error);
            this.showError('Connection error. Messages may not update in real-time.');
        });
    }

    markMessagesAsRead() {
        if (!this.markAsReadUrl || !this.sessionId) {
            console.log('⚠️ Mark as read skipped: missing URL or session');
            return;
        }

        if (!this.hasUnreadMessages()) {
            console.log('ℹ️ No unread messages to mark');
            return;
        }

        const now = Date.now();
        if (this.pendingReadMark && (now - this.lastReadMarkTime) < 500) {
            console.log('⚠️ Read mark already in progress, skipping');
            return;
        }

        this.pendingReadMark = true;
        this.lastReadMarkTime = now;

        console.log('📤 SENDING mark-as-read request', {
            session_id: this.sessionId,
            url: this.markAsReadUrl,
            user_type: this.currentUserType
        });

        $.ajax({
            url: this.markAsReadUrl,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: (response) => {
                console.log('✅ Mark as read SUCCESS:', response);
                
                if (response.updated_count > 0) {
                    console.log(`✅ ${response.updated_count} messages marked as read on server`);
                }
                
                this.updateUnreadCount(this.sessionId, 0);
            },
            error: (xhr, status, error) => {
                console.error('❌ Mark as read FAILED:', {
                    status: xhr.status,
                    response: xhr.responseText,
                    error: error
                });
            },
            complete: () => {
                setTimeout(() => {
                    this.pendingReadMark = false;
                }, 300);
            }
        });
    }

    setupVisibilityTracking() {
        document.addEventListener('visibilitychange', () => {
            this.isPageVisible = !document.hidden;
            
            console.log('👁️ Visibility changed:', this.isPageVisible ? 'visible' : 'hidden');
            
            if (this.isPageVisible) {
                console.log('📱 Page became visible, checking for unread messages...');
                setTimeout(() => {
                    if (this.hasUnreadMessages()) {
                        console.log('📖 Found unread messages, marking as read');
                        this.markMessagesAsRead();
                    } else {
                        console.log('✅ No unread messages found');
                    }
                }, 500);
            }
        });

        let scrollTimeout;
        $('#chat-box').on('scroll', () => {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                if (this.isScrolledToBottom()) {
                    console.log('📜 Scrolled to bottom, checking for unread messages...');
                    if (this.hasUnreadMessages()) {
                        console.log('📖 Found unread messages at bottom, marking as read');
                        this.markMessagesAsRead();
                    }
                }
            }, 200);
        });

        $(window).on('focus', () => {
            console.log('🔍 Window focused, checking for unread messages...');
            setTimeout(() => {
                if (this.hasUnreadMessages() && this.isScrolledToBottom()) {
                    console.log('📖 Found unread messages on focus, marking as read');
                    this.markMessagesAsRead();
                }
            }, 300);
        });
    }

    isScrolledToBottom() {
        const chatBox = $('#chat-box')[0];
        if (!chatBox) return false;
        
        const threshold = 100;
        const isAtBottom = chatBox.scrollHeight - chatBox.scrollTop - chatBox.clientHeight < threshold;
        
        console.log('Scroll check:', {
            scrollHeight: chatBox.scrollHeight,
            scrollTop: chatBox.scrollTop,
            clientHeight: chatBox.clientHeight,
            distance_from_bottom: chatBox.scrollHeight - chatBox.scrollTop - chatBox.clientHeight,
            isAtBottom: isAtBottom
        });
        
        return isAtBottom;
    }

    hasUnreadMessages() {
        let unreadSelector;
        if (this.currentUserType === 'student') {
            unreadSelector = '.chat-bubble.received';
        } else {
            unreadSelector = '.chat-bubble.received';
        }
        
        const $receivedMessages = $(unreadSelector);
        const hasReceived = $receivedMessages.length > 0;
        
        console.log('Unread check:', {
            userType: this.currentUserType,
            selector: unreadSelector,
            receivedCount: $receivedMessages.length,
            hasReceived: hasReceived
        });
        
        return hasReceived;
    }

    updateUnreadCount(sessionId, count) {
        const sessionItem = $(`.session-item[href*="${sessionId}"]`);
        if (sessionItem.length) {
            const badge = sessionItem.find('.unread-badge');
            if (count > 0) {
                if (badge.length) {
                    badge.text(count);
                } else {
                    sessionItem.find('.flex-grow-1').first()
                        .find('.d-flex').first()
                        .append(`<span class="unread-badge">${count}</span>`);
                }
            } else {
                badge.remove();
            }
        }

        this.updateTotalUnreadCount();
    }

    updateTotalUnreadCount() {
        let total = 0;
        $('.unread-badge').each(function() {
            const count = parseInt($(this).text());
            if (!isNaN(count)) {
                total += count;
            }
        });
        $('#total-unread').text(total);
    }

    updateMessageReadStatus(messageIds) {
        console.log('🔄 Updating read status for messages:', messageIds);
        
        if (!Array.isArray(messageIds) || messageIds.length === 0) {
            console.warn('⚠️ Invalid message IDs:', messageIds);
            return;
        }
        
        let updatedCount = 0;
        
        messageIds.forEach(messageId => {
            let $message = $(`[data-message-id="${messageId}"]`);
            
            if ($message.length === 0) {
                $message = $(`[data-message-id*="${messageId}"]`);
            }
            
            if ($message.length === 0) {
                console.warn(`⚠️ Message element not found for ID: ${messageId}`);
                return;
            }

            const $bubble = $message.find('.chat-bubble');
            if (!$bubble.hasClass('sent')) {
                console.log(`⏩ Skipping read indicator for received message ${messageId}`);
                return;
            }
            
            const $statusIcon = $message.find('.message-meta i');
            
            if ($statusIcon.length === 0) {
                const $meta = $message.find('.message-meta');
                if ($meta.length > 0) {
                    $meta.append('<i class="fas fa-check-double ms-1" style="color: #ffffff;"></i>');
                    updatedCount++;
                    console.log(`✅ Added double check to message ${messageId}`);
                }
                return;
            }
            
            if ($statusIcon.hasClass('fa-check') && !$statusIcon.hasClass('fa-check-double')) {
                $statusIcon.removeClass('fa-check').addClass('fa-check-double');
                $statusIcon.css('color', '#ffffff');
                updatedCount++;
                console.log(`✅ Updated message ${messageId} to read (double check)`);
            } else if ($statusIcon.hasClass('fa-check-double')) {
                console.log(`⏩ Message ${messageId} already marked as read`);
            }
        });
        
        console.log(`📊 Total messages updated: ${updatedCount} out of ${messageIds.length}`);
        
        if (updatedCount > 0) {
            console.log(`✨ ${updatedCount} message(s) marked as read with animation`);
        }
    }

    formatDateLabel(dateStr) {
        try {
            const nowInUserTz = new Date(new Date().toLocaleString("en-US", {timeZone: this.userTimezone}));
            const todayInUserTz = new Date(nowInUserTz.getFullYear(), nowInUserTz.getMonth(), nowInUserTz.getDate());
            
            const yesterdayInUserTz = new Date(todayInUserTz);
            yesterdayInUserTz.setDate(todayInUserTz.getDate() - 1);

            const todayStr = todayInUserTz.getFullYear() + '-' + 
                           String(todayInUserTz.getMonth() + 1).padStart(2, '0') + '-' + 
                           String(todayInUserTz.getDate()).padStart(2, '0');
            
            const yesterdayStr = yesterdayInUserTz.getFullYear() + '-' + 
                               String(yesterdayInUserTz.getMonth() + 1).padStart(2, '0') + '-' + 
                               String(yesterdayInUserTz.getDate()).padStart(2, '0');

            if (dateStr === todayStr) {
                return 'Today';
            } else if (dateStr === yesterdayStr) {
                return 'Yesterday';
            } else {
                const parts = dateStr.split("-");
                if (parts.length !== 3) return dateStr;
                
                const year = parseInt(parts[0]);
                const month = parseInt(parts[1]) - 1;
                const day = parseInt(parts[2]);
                
                if (isNaN(year) || isNaN(month) || isNaN(day)) return dateStr;
                
                const msgDate = new Date(year, month, day);
                return msgDate.toLocaleDateString(this.userLocale, {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }
        } catch (error) {
            console.error('Error formatting date label:', error, dateStr);
            return dateStr || 'Unknown Date';
        }
    }

    appendDateSeparator(dateText) {
        // FIXED: Check if separator already exists for this date
        const existingSeparator = $(`.date-divider span:contains("${dateText}")`);
        if (existingSeparator.length > 0) {
            console.log('⏩ Date separator already exists for:', dateText);
            return;
        }

        const separatorHtml = `
            <div class="date-divider" data-date="${dateText}">
                <span>${dateText}</span>
            </div>
        `;
        $('#chat-box').append(separatorHtml);
        console.log('✅ Added date separator:', dateText);
    }

    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    appendMessage(msg, currentUserType = 'student') {
        if (!msg || !msg.message) {
            console.warn('Invalid message data:', msg);
            return;
        }

        const messageId = msg.id_message || `${msg.sender_type}_${msg.message}_${msg.sent_at}`;
        
        // FIXED: Check if message already exists in DOM
        const existingMessage = document.querySelector(`[data-message-id="${messageId}"]`);
        if (existingMessage) {
            console.log('⏩ Message already displayed, skipping:', messageId);
            return;
        }

        // FIXED: Also check cache
        if (this.messageCache.has(messageId)) {
            console.log('⏩ Message in cache, skipping:', messageId);
            return;
        }
        
        this.messageCache.add(messageId);

        console.log('Appending message:', {
            id: msg.id_message,
            sender_type: msg.sender_type,
            date: msg.date,
            sent_at: msg.sent_at,
            status: msg.status
        });

        try {
            // FIXED: Handle date separator logic
            if (!this.isPreviewMode && !msg.is_preview) {
                let messageDate;
                
                if (msg.date) {
                    messageDate = msg.date;
                } else if (msg.sent_at) {
                    try {
                        if (/^\d{2}:\d{2}$/.test(msg.sent_at)) {
                            const nowInUserTz = new Date(new Date().toLocaleString("en-US", {timeZone: this.userTimezone}));
                            messageDate = nowInUserTz.getFullYear() + '-' + 
                                        String(nowInUserTz.getMonth() + 1).padStart(2, '0') + '-' + 
                                        String(nowInUserTz.getDate()).padStart(2, '0');
                        } else {
                            const parsedDate = new Date(msg.sent_at);
                            if (!isNaN(parsedDate.getTime())) {
                                const dateInUserTz = new Date(parsedDate.toLocaleString("en-US", {timeZone: this.userTimezone}));
                                messageDate = dateInUserTz.getFullYear() + '-' + 
                                            String(dateInUserTz.getMonth() + 1).padStart(2, '0') + '-' + 
                                            String(dateInUserTz.getDate()).padStart(2, '0');
                            }
                        }
                    } catch (e) {
                        console.warn('Error parsing sent_at for date:', msg.sent_at);
                    }
                }
                
                if (!messageDate) {
                    const nowInUserTz = new Date(new Date().toLocaleString("en-US", {timeZone: this.userTimezone}));
                    messageDate = nowInUserTz.getFullYear() + '-' + 
                                String(nowInUserTz.getMonth() + 1).padStart(2, '0') + '-' + 
                                String(nowInUserTz.getDate()).padStart(2, '0');
                }
                
                // FIXED: Only append date separator if date changed AND not already in DOM
                if (this.lastDate !== messageDate) {
                    const dateLabel = this.formatDateLabel(messageDate);
                    this.appendDateSeparator(dateLabel);
                    this.lastDate = messageDate;
                }
            }
            
            const isFromCurrentUser = (currentUserType === 'student' && msg.sender_type === 'student') || 
                                     (currentUserType === 'counselor' && msg.sender_type === 'counselor');
            
            let messageTime = '';
            if (!msg.is_preview && !this.isPreviewMode) {
                if (msg.sent_at && /^\d{2}:\d{2}$/.test(msg.sent_at)) {
                    messageTime = msg.sent_at;
                } else {
                    const nowInUserTz = new Date(new Date().toLocaleString("en-US", {timeZone: this.userTimezone}));
                    messageTime = nowInUserTz.toLocaleTimeString(this.userLocale, {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
            }
            
            const alignment = isFromCurrentUser ? 'justify-content-end' : 'justify-content-start';
            let bubbleClass = isFromCurrentUser ? 'sent' : 'received';
            
            if (msg.is_preview || this.isPreviewMode) {
                bubbleClass += ' preview';
            }
            
            let statusIcon = '';
            if (!msg.is_preview && !this.isPreviewMode && isFromCurrentUser && msg.status) {
                if (msg.status === 'read') {
                    statusIcon = '<i class="fas fa-check-double ms-1" style="color: #ffffff;"></i>';
                } else if (msg.status === 'sent') {
                    statusIcon = '<i class="fas fa-check ms-1"></i>';
                }
            }
            
            let previewBadge = '';
            if (msg.is_preview || this.isPreviewMode) {
                previewBadge = '<span class="preview-badge">Preview</span>';
            }
            
            const messageHtml = `
                <div class="d-flex ${alignment} mb-2" data-message-id="${messageId}">
                    <div class="chat-bubble ${bubbleClass}">
                        <div>${this.escapeHtml(msg.message)}</div>
                        ${previewBadge}
                        ${!msg.is_preview && !this.isPreviewMode && messageTime ? `
                            <div class="message-meta">
                                ${messageTime}
                                ${statusIcon}
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
            
            $('#chat-box').append(messageHtml);
            if (!msg.is_preview && !this.isPreviewMode) {
                this.scrollToBottom();
            }
        } catch (error) {
            console.error('Error appending message:', error, msg);
        }
    }

    scrollToBottom() {
        const chatBox = $('#chat-box')[0];
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    }

    showError(message) {
        const errorDiv = $('#error-message');
        if (errorDiv.length) {
            errorDiv.html('<strong>Error:</strong> ' + message).removeClass('d-none').show();
            $('#success-message').addClass('d-none').hide();
            setTimeout(() => errorDiv.fadeOut(), 8000);
        } else {
            console.error('Error:', message);
        }
    }

    showSuccess(message) {
        console.log('Success:', message);
    }

    setLoading(loading) {
        const btn = $('#send-btn');
        const icon = $('#send-icon');
        const loadingSpinner = $('#send-loading');
        
        if (btn.length) {
            btn.prop('disabled', loading);
        }
        
        if (loading) {
            icon.hide();
            loadingSpinner.show();
        } else {
            loadingSpinner.hide();
            icon.show();
        }
    }

    sendMessage(sessionId, message, sendUrl, csrfToken, counselorId = null, onSuccess, onError) {
        if (!message.trim()) {
            this.showError('Please enter a message');
            return;
        }

        if (message.length > 1000) {
            this.showError('Message too long (max 1000 characters)');
            return;
        }

        const data = {
            _token: csrfToken,
            message: message,
            timezone: this.userTimezone
        };

        if (sessionId && sessionId !== 'null' && sessionId !== null) {
            data.id_session = sessionId;
        } else if (counselorId) {
            data.id_counselor = counselorId;
        } else {
            this.showError('Invalid session or counselor data');
            return;
        }

        $.ajax({
            url: sendUrl,
            method: 'POST',
            data: data,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: (response) => {
                console.log('Message sent successfully:', response);
                if (onSuccess) onSuccess(response);
            },
            error: (xhr, status, error) => {
                console.error('AJAX Error:', xhr.responseText);
                
                let errorMessage = 'Failed to send message';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.error) {
                        errorMessage = response.error;
                    } else if (response.errors) {
                        errorMessage = Object.values(response.errors).flat().join(', ');
                    }
                } catch (e) {
                    if (xhr.status === 500) {
                        errorMessage = 'Server error occurred. Please try again.';
                    }
                }
                
                this.showError(errorMessage);
                if (onError) onError(xhr, status, error);
            }
        });
    }
    
    fetchMessages(sessionId, fetchUrl, onSuccess, onError) {
        if (!sessionId || sessionId === 'null' || sessionId === null) {
            console.error('Invalid session ID for fetching messages:', sessionId);
            if (onError) onError(null, 'error', 'Invalid session ID');
            return;
        }

        $.ajax({
            url: fetchUrl,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: (data) => {
                console.log('Messages loaded:', data);
                if (onSuccess) onSuccess(data);
            },
            error: (xhr, status, error) => {
                console.error('Failed to load messages:', xhr.responseText);
                this.showError('Failed to load messages. Please refresh the page.');
                if (onError) onError(xhr, status, error);
            }
        });
    }

    initializeChat(config) {
        const {
            pusherKey,
            pusherCluster,
            sessionId,
            currentUserType = 'student',
            formSelector = '#chat-form',
            sendUrl,
            fetchUrl,
            csrfToken,
            counselorId = null,
            markAsReadUrl = null
        } = config;

        if (!sessionId || sessionId === 'null' || sessionId === null) {
            console.warn('No valid session ID provided. Chat initialization skipped.');
            return;
        }

        this.sessionId = sessionId;
        this.currentUserType = currentUserType;
        this.markAsReadUrl = markAsReadUrl;
        this.isInitialized = true;

        const channel = this.initializePusher(pusherKey, pusherCluster, sessionId);
        this.setupConnectionHandlers();
        this.setupVisibilityTracking();

        if (channel) {
            channel.bind('message.sent', (data) => {
                console.log('📨 New message received via Pusher:', data);
                
                const isFromCurrentUser = (currentUserType === 'student' && data.sender_type === 'student') || 
                                         (currentUserType === 'counselor' && data.sender_type === 'counselor');
                
                if (!isFromCurrentUser) {
                    if (data.sent_at) {
                        const messageTimeUtc = new Date(data.sent_at);
                        
                        if (!isNaN(messageTimeUtc.getTime())) {
                            const formatter = new Intl.DateTimeFormat('en-US', {
                                timeZone: this.userTimezone,
                                year: 'numeric',
                                month: '2-digit',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: false
                            });
                            
                            const parts = formatter.formatToParts(messageTimeUtc);
                            const partsObj = {};
                            parts.forEach(part => {
                                if (part.type !== 'literal') {
                                    partsObj[part.type] = part.value;
                                }
                            });
                            
                            data.sent_at = `${partsObj.hour}:${partsObj.minute}`;
                            data.date = `${partsObj.year}-${partsObj.month}-${partsObj.day}`;
                        }
                    }
                    
                    this.appendMessage(data, currentUserType);
                    
                    if (this.isPageVisible && this.isScrolledToBottom()) {
                        console.log('📨 New message visible, marking as read...');
                        setTimeout(() => {
                            this.markMessagesAsRead();
                        }, 500);
                    }
                }
            });

            channel.bind('messages.read', (data) => {
                console.log('='.repeat(50));
                console.log('📬 READ RECEIPT RECEIVED');
                console.log('='.repeat(50));
                console.log('Data:', data);
                console.log('Message IDs:', data.message_ids);
                console.log('='.repeat(50));
                
                if (data.message_ids && Array.isArray(data.message_ids) && data.message_ids.length > 0) {
                    console.log('➡️ Updating read status for messages:', data.message_ids);
                    this.updateMessageReadStatus(data.message_ids);
                } else {
                    console.warn('⚠️ Invalid or empty message_ids in read event');
                }
            });

            channel.bind_global((eventName, data) => {
                console.log(`📡 Pusher event: ${eventName}`, data);
            });
        }

        $(formSelector).on('submit', (e) => {
            e.preventDefault();
            
            const messageInput = $(formSelector + ' input[name="message"]');
            const message = messageInput.val().trim();
            
            this.setLoading(true);
            
            this.sendMessage(
                sessionId,
                message,
                sendUrl,
                csrfToken,
                counselorId,
                (response) => {
                    if (response.messages && Array.isArray(response.messages)) {
                        response.messages.forEach(msg => {
                            this.appendMessage(msg, currentUserType);
                        });
                    } else if (response.message) {
                        this.appendMessage(response, currentUserType);
                    }
                    
                    messageInput.val('');
                    this.setLoading(false);

                    if (response.is_new_session && response.session_id) {
                        this.sessionId = response.session_id;
                        window.history.replaceState({}, '', window.location.pathname.replace(/\/start\/\d+$/, '/session/' + response.session_id));
                        
                        setTimeout(() => {
                            this.disconnect();
                            config.sessionId = response.session_id;
                            this.initializeChat(config);
                        }, 1000);
                    }
                },
                () => {
                    this.setLoading(false);
                }
            );
        });

        $(formSelector + ' input[name="message"]').on('keypress', (e) => {
            if (e.which === 13 && !e.shiftKey) {
                e.preventDefault();
                $(formSelector).submit();
            }
        });

        // Load initial messages
        this.fetchMessages(
            sessionId,
            fetchUrl,
            (messages) => {
                $('#loading').hide();
                
                if (Array.isArray(messages) && messages.length > 0) {
                    this.messageCache.clear();
                    this.lastDate = null; // FIXED: Reset lastDate before loading messages
                    
                    console.log(`📥 Loading ${messages.length} messages...`);
                    
                    messages.forEach(msg => this.appendMessage(msg, currentUserType));
                    
                    setTimeout(() => {
                        console.log('⏰ Initial load complete, checking read status...');
                        console.log('Page visible:', this.isPageVisible);
                        console.log('At bottom:', this.isScrolledToBottom());
                        console.log('Has unread:', this.hasUnreadMessages());
                        
                        if (this.isPageVisible && this.isScrolledToBottom() && this.hasUnreadMessages()) {
                            console.log('📖 Messages loaded and visible, marking as read');
                            this.markMessagesAsRead();
                        } else {
                            console.log('ℹ️ Not marking as read:', {
                                visible: this.isPageVisible,
                                atBottom: this.isScrolledToBottom(),
                                hasUnread: this.hasUnreadMessages()
                            });
                        }
                    }, 1000);
                } else {
                    $('#chat-box').append('<div class="text-center text-muted"><em>No messages yet. Start the conversation!</em></div>');
                }
                this.scrollToBottom();
            },
            () => {
                $('#loading').hide();
                $('#chat-box').append('<div class="text-center text-danger"><em>Failed to load messages. Please refresh the page.</em></div>');
            }
        );

        $('input[name="message"]').focus();

        $(window).on('beforeunload', () => {
            if (this.pusher) {
                this.pusher.disconnect();
            }
        });
    }

    disconnect() {
        if (this.pusher) {
            this.pusher.disconnect();
            this.pusher = null;
            this.channel = null;
        }
        this.isInitialized = false;
        this.messageCache.clear();
        this.hasMarkedAsRead = false;
        this.pendingReadMark = false;
        this.lastDate = null; // FIXED: Reset lastDate on disconnect
        this.dateTracker = {}; // FIXED: Reset date tracker
    }
}

window.ChatHelper = ChatHelper;