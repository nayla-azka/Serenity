@extends('admin.layouts.layout')

@section('title', 'Dashboard')

@section('content')

<style>
.bg-serenity {
    background: linear-gradient(135deg, rgb(131, 122, 182) 0%, rgb(151, 140, 200) 100%) !important;
}

.text-serenity {
    color: rgb(131, 122, 182) !important;
}

.text-darkserenity {
    color: #511e61 !important;
}

.bg-cardserenity {
    background-color: rgba(131, 122, 182, 0.437) !important;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(131, 122, 182, 0.2);
}

.bg-innercardserenity {
    background-color: rgb(248, 246, 255) !important;
    border: 1px solid rgba(131, 122, 182, 0.1);
}

/* Enhanced card stats */
.card-stats {
    font-size: 0.85rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.card-stats::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, transparent, rgba(131, 122, 182, 0.8), transparent);
    transition: left 0.5s ease;
}

.card-stats:hover::before {
    left: 100%;
}

.card-stats:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 20px 40px rgba(131, 122, 182, 0.15);
}

.card-stats h2 {
    font-size: 1.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, #511e61, #837ab6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.card-stats .card-body {
    padding: 1rem;
    position: relative;
    z-index: 1;
}

.card-stats p,
.card-stats small {
    margin-bottom: 0.25rem;
}

/* Enhanced chat and report items */
.chat-avatar {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #837ab6, #9d8dc4);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-weight: bold;
    color: white;
    box-shadow: 0 4px 12px rgba(131, 122, 182, 0.3);
    transition: all 0.3s ease;
}

.unread-badge {
    background: linear-gradient(135deg, #dc3545, #e74c3c);
    color: white;
    border-radius: 50%;
    width: 22px;
    height: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    font-weight: bold;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.chat-item, .report-item {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-left: 3px solid transparent;
}

.chat-item:hover, .report-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(131, 122, 182, 0.15);
    border-left-color: #837ab6;
}

.admin-activity-icon {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Enhanced animations */
.animated-counter {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Enhanced growth badges */
.growth-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 16px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-top: 6px;
    transition: all 0.2s ease;
}

.growth-positive {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(16, 185, 129, 0.1));
    color: #059669;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.growth-negative {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(239, 68, 68, 0.1));
    color: #dc2626;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

/* Enhanced period selector */
.period-selector {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 12px;
    padding: 6px;
    border: 1px solid rgba(131, 122, 182, 0.2);
    margin-bottom: 24px;
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 16px rgba(131, 122, 182, 0.1);
}

.period-btn {
    padding: 8px 16px;
    border: none;
    background: transparent;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    color: #64748b;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    position: relative;
}

.period-btn:hover {
    background: rgba(131, 122, 182, 0.1);
    transform: translateY(-1px);
}

.period-btn.active {
    background: linear-gradient(135deg, rgb(131, 122, 182), rgb(151, 140, 200));
    color: white;
    box-shadow: 0 4px 12px rgba(131, 122, 182, 0.3);
}

/* Chart container enhancement */
.chart-container {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 8px 32px rgba(131, 122, 182, 0.1);
    border: 1px solid rgba(131, 122, 182, 0.1);
    backdrop-filter: blur(10px);
    margin-bottom: 24px;
}

/* Loading animation */
.loading-shimmer {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

/* Responsive improvements */
@media (max-width: 768px) {
    .card-stats h2 {
        font-size: 1.25rem;
    }

    .period-btn {
        padding: 6px 12px;
        font-size: 0.8rem;
    }

    .chat-avatar, .admin-activity-icon {
        width: 40px;
        height: 40px;
    }
}

/* Enhanced section headers */
.section-header {
    background: linear-gradient(135deg, rgba(131, 122, 182, 0.05), rgba(151, 140, 200, 0.02));
    border-left: 4px solid #837ab6;
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 16px;
}


</style>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h2 fw-bold">Dashboard Overview</h1>
    </div>
    <div class="period-selector">
        <button class="period-btn active" data-period="month">This Month</button>
        <button class="period-btn" data-period="quarter">Quarter</button>
        <button class="period-btn" data-period="year">Year</button>
    </div>
</div>


<!-- Stats Cards -->
<div class="row row-cols-1 row-cols-md-2 g-3 mb-4">
    <!-- Pengunjung -->
    <div class="col">
        <div class="card shadow-sm border-0 h-100 card-stats">
            <div class="card-body rounded bg-cardserenity">
                <p class="text-secondary mb-1 text-black small">Pengunjung</p>
                <h2 class="animated-counter" data-target="{{ $totalVisitors }}">
                    0
                </h2>
                <small class="text-darkserenity">
                    <span class="growth-badge {{ $visitorGrowth >= 0 ? 'growth-positive' : 'growth-negative' }}">
                        <i class="fas fa-trending-{{ $visitorGrowth >= 0 ? 'up' : 'down' }}" style="font-size: 10px;"></i>
                        {{ $visitorGrowth >= 0 ? '+' : '' }}{{ $visitorGrowth }}%
                    </span>
                    <span class="comparison-text">dibandingkan bulan lalu</span>
                </small>
            </div>
        </div>
    </div>

    <!-- Interaksi Artikel -->
    <div class="col">
        <div class="card shadow-sm border-0 h-100 card-stats">
            <div class="card-body rounded bg-cardserenity">
                <p class="text-secondary mb-1 text-black small">Interaksi Artikel</p>
                <h2 class="animated-counter" data-target="{{ $totalInteractions }}">
                    0
                </h2>
                <small class="text-darkserenity">
                    <span class="growth-badge {{ $interactionGrowth >= 0 ? 'growth-positive' : 'growth-negative' }}">
                        <i class="fas fa-trending-{{ $interactionGrowth >= 0 ? 'up' : 'down' }}" style="font-size: 10px;"></i>
                        {{ $interactionGrowth >= 0 ? '+' : '' }}{{ $interactionGrowth }}%
                    </span>
                    <span class="comparison-text">dibandingkan bulan lalu</span>
                </small>
            </div>
        </div>
    </div>

    <!-- Laporan -->
    <div class="col">
        <div class="card shadow-sm border-0 h-100 card-stats">
            <div class="card-body rounded bg-cardserenity">
                <p class="text-secondary mb-1 text-black small">Laporan</p>
                <h2 class="animated-counter" data-target="{{ $totalReports }}">
                    0
                </h2>
                <small class="text-darkserenity">
                    <span class="growth-badge {{ $reportGrowth >= 0 ? 'growth-positive' : 'growth-negative' }}">
                        <i class="fas fa-trending-{{ $reportGrowth >= 0 ? 'up' : 'down' }}" style="font-size: 10px;"></i>
                        {{ $reportGrowth >= 0 ? '+' : '' }}{{ $reportGrowth }}%
                    </span>
                    <span class="comparison-text">dibandingkan bulan lalu</span>
                </small>
            </div>
        </div>
    </div>

    <!-- Konseling Digital -->
    <div class="col">
        <div class="card shadow-sm border-0 h-100 card-stats">
            <div class="card-body rounded bg-cardserenity">
                <p class="text-secondary mb-1 text-black small">Konseling Digital</p>
                <h2 class="animated-counter" data-target="{{ $totalChatsStarted }}">
                    0
                </h2>
                <small class="text-darkserenity">
                    <span class="growth-badge {{ $chatGrowth >= 0 ? 'growth-positive' : 'growth-negative' }}">
                        <i class="fas fa-trending-{{ $chatGrowth >= 0 ? 'up' : 'down' }}" style="font-size: 10px;"></i>
                        {{ $chatGrowth >= 0 ? '+' : '' }}{{ $chatGrowth }}%
                    </span>
                    <span class="comparison-text">dibandingkan bulan lalu</span>
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Chart -->
<div class="chart-container">
    <canvas id="statsChart" height="120"></canvas>
</div>
<!-- Chat + Laporan (Counselors) / Comment Reports (Admins) -->
<div class="row row-cols-1 row-cols-lg-2 g-4">

    @if(auth()->user()->role === 'counselor' || auth()->user()->role === 'konselor')
        {{-- Chat Section - Only for Counselors --}}
        <div class="col">
            <div class="card p-3 rounded bg-cardserenity">
                <div class="section-header px-3">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <h3 class="font-bold text-black mb-0" style="font-size: 1.25rem;"><i class="bi bi-chat-left-dots-fill"></i> Chat</h3>
                        <a href="{{ route('admin.konseling.index') }}" class="d-flex align-items-center gap-1 text-decoration-none text-serenity" style="font-size: 0.85rem; font-weight: 600;">
                            View All
                            <i class="fas fa-arrow-right" style="font-size: 12px;"></i>
                        </a>
                    </div>
                </div>

                <div class="mb-3">
                    @if(count($recentChats) > 0)
                        @foreach($recentChats as $chat)
                            <a href="{{ route('admin.konseling.show', $chat->id_session) }}" class="text-decoration-none">
                                <div class="card p-3 mb-3 rounded bg-innercardserenity chat-item">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="chat-avatar">
                                            {{ strtoupper(substr($chat->student->user->name ?? 'S', 0, 1)) }}
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="min-w-0 flex-grow-1">
                                                    <p class="font-semibold mb-1 text-truncate text-dark" style="font-weight: 600;">
                                                        {{ $chat->student->user->name ?? 'Student' }}
                                                    </p>
                                                    <p class="text-sm text-secondary mb-0 text-truncate" style="font-size: 0.85rem;">
                                                        {{ $chat->latest_message_text }}
                                                    </p>
                                                </div>
                                                <div class="d-flex flex-column align-items-end gap-1">
                                                    {{-- UPDATED: Show timezone-aware time with tooltip --}}
                                                    <span class="text-xs text-secondary" 
                                                        style="font-size: 0.75rem;"
                                                        title="{{ $chat->latest_message_date }} {{ $chat->latest_message_time }} ({{ $userTimezone }})">
                                                        @if($chat->latest_message_relative)
                                                            {{ $chat->latest_message_relative }}
                                                        @else
                                                            {{ $chat->latest_message_time }}
                                                        @endif
                                                    </span>
                                                    @if($chat->unread_count > 0)
                                                        <div class="unread-badge">
                                                            {{ $chat->unread_count > 99 ? '99+' : $chat->unread_count }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <div class="card p-4 mb-3 rounded bg-innercardserenity text-center">
                            <div class="text-muted">
                                <div class="mb-3">
                                    <i class="fas fa-comments" style="font-size: 3rem; opacity: 0.3;"></i>
                                </div>
                                <p class="mb-1" style="font-weight: 600;">Tidak ada chat dengan pesan baru</p>
                                <small class="text-muted">Chat akan muncul di sini ketika siswa mengirim pesan</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Laporan Section - Only for Counselors --}}
        <div class="col">
            <div class="card p-3 rounded bg-cardserenity">
                <div class="section-header px-3">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <h3 class="font-bold text-black mb-0" style="font-size: 1.25rem;"><i class="bi bi-envelope-fill"></i> Laporan</h3>
                        <a href="{{ route('admin.laporan.index') }}" class="d-flex align-items-center gap-1 text-decoration-none text-serenity" style="font-size: 0.85rem; font-weight: 600;">
                            View All
                            <i class="fas fa-arrow-right" style="font-size: 12px;"></i>
                        </a>
                    </div>
                </div>
                    <div class="mb-3">
                        @foreach ($laporan as $row)
                            @continue(!in_array($row->status, ['Menunggu', 'Diproses']))

                            <a href="{{ route('admin.laporan.index', ['highlight' => $row->id]) }}" 
                            class="text-decoration-none text-dark">
                                <div class="card mb-3 rounded report-item">
                                    <div class="card-body d-flex justify-content-between bg-innercardserenity rounded p-3">
                                        <div>
                                            <p class="font-semibold mb-1" style="font-weight: 600;">{{ $row->topic }}</p>
                                            <small class="text-secondary">Pelapor: {{ $row->is_anonymous ? 'Anonim' : ($row->name ?? 'Anonim') }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge 
                                                @if($row->status == 'Menunggu') bg-warning 
                                                @elseif($row->status == 'Diproses') bg-primary 
                                                @else bg-secondary 
                                                @endif">
                                                {{ $row->status }}
                                            </span>
                                            {{-- UPDATED: Show timezone-aware time with relative time --}}
                                            @if(isset($row->created_at_relative))
                                                <span class="text-xs text-secondary d-block" 
                                                    title="{{ $row->created_at_formatted ?? '' }}">
                                                    {{ $row->created_at_relative }}
                                                </span>
                                            @else
                                                <span class="text-xs text-secondary d-block">
                                                    {{ \Carbon\Carbon::parse($row->created_at)->format('d-m-Y') }}
                                                </span>
                                                <span class="text-xs text-secondary">
                                                    {{ \Carbon\Carbon::parse($row->created_at)->format('H:i') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
            </div>
        </div>

    @elseif(auth()->user()->role === 'admin')
        {{-- Comment Reports Section - Only for Admins --}}
        <div class="col">
            <div class="card p-3 rounded bg-cardserenity">
                <div class="section-header px-3">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <h3 class="font-bold text-black mb-0" style="font-size: 1.25rem;"><i class="bi bi-exclamation-triangle-fill"></i> Pending Reports</h3>
                        <a href="{{ route('admin.report.index') }}" class="d-flex align-items-center gap-1 text-decoration-none text-serenity" style="font-size: 0.85rem; font-weight: 600;">
                            View All
                            <i class="fas fa-arrow-right" style="font-size: 12px;"></i>
                        </a>
                    </div>
                </div>

                <div class="mb-3">
                    @if($pendingReports->count() > 0)
                        @foreach($pendingReports as $report)
                            <a href="{{ route('admin.report.index', ['highlight' => $report->report_id]) }}" class="text-decoration-none">
                                <div class="card p-3 mb-3 rounded bg-innercardserenity chat-item">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="admin-activity-icon bg-warning text-dark">
                                            <i class="fas fa-flag"></i>
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="min-w-0 flex-grow-1">
                                                    <p class="font-semibold mb-1 text-dark" style="font-weight: 600;">
                                                        {{ $report->reporter->name }}
                                                    </p>
                                                    <p class="text-sm text-secondary mb-1" style="font-size: 0.85rem;">
                                                        melaporkan: {{ $report->targetUser()?->name ?? 'Pengguna dihapus' }}
                                                    </p>
                                                    <p class="text-xs text-muted mb-0 text-truncate" style="font-size: 0.75rem;">
                                                        "{{ $report->target()?->comment_text ?? 'Konten sudah dihapus' }}"
                                                    </p>
                                                    <small class="text-primary">
                                                        Alasan: {{ \Str::limit($report->reason, 25) }}
                                                    </small>
                                                </div>
                                                <div class="d-flex flex-column align-items-end">
                                                    {{-- UPDATED: Show timezone-aware time --}}
                                                    @if(isset($report->created_at_relative))
                                                        <span class="text-xs text-secondary" 
                                                            style="font-size: 0.75rem;"
                                                            title="{{ $report->created_at_formatted ?? '' }} ({{ $userTimezone }})">
                                                            {{ $report->created_at_relative }}
                                                        </span>
                                                    @else
                                                        <span class="text-xs text-secondary" style="font-size: 0.75rem;">
                                                            {{ $report->created_at->format('d-m-Y H:i') }}
                                                        </span>
                                                    @endif
                                                    <span class="badge bg-warning mt-1">
                                                        Pending
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <div class="card p-4 mb-3 rounded bg-innercardserenity text-center">
                            <div class="text-muted">
                                <div class="mb-3">
                                    <i class="fas fa-shield-check" style="font-size: 3rem; opacity: 0.3;"></i>
                                </div>
                                <p class="mb-1" style="font-weight: 600;">Tidak ada laporan yang pending</p>
                                <small class="text-muted">Semua laporan telah ditangani</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Recent Activity or Stats for Admin --}}
        <div class="col">
            <div class="card p-3 rounded bg-cardserenity">
                <div class="section-header">
                    <h3 class="font-bold text-black mb-0" style="font-size: 1.25rem;"><i class="bi bi-activity"></i> Recent Activity</h3>
                </div>
                <div class="mb-3">
                    <div class="card p-3 mb-3 rounded bg-innercardserenity">
                        <div class="d-flex align-items-center gap-3">
                            <div class="admin-activity-icon bg-info text-white">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div>
                                <p class="font-semibold mb-1" style="font-weight: 600;">Total Laporan Komentar</p>
                                <p class="text-sm text-secondary mb-0" style="font-size: 0.85rem;">
                                    {{ $pendingReports->count() }} pending dari total aktivitas hari ini
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card p-3 mb-3 rounded bg-innercardserenity">
                        <div class="d-flex align-items-center gap-3">
                            <div class="admin-activity-icon bg-success text-white">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <p class="font-semibold mb-1" style="font-weight: 600;">Aktivitas Pengguna</p>
                                <p class="text-sm text-secondary mb-0" style="font-size: 0.85rem;">
                                    {{ $totalVisitors }} pengunjung bulan ini
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
<!-- Include Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<script>
function updateCurrentTime() {
    const userTimezone = '{{ $userTimezone }}';
    const now = new Date();
    
    const options = {
        timeZone: userTimezone,
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    };
    
    const timeString = now.toLocaleTimeString('en-US', options);
    const dateString = now.toLocaleDateString('en-US', {
        timeZone: userTimezone,
        weekday: 'short',
        month: 'short',
        day: 'numeric'
    });
    
    const timeElement = document.getElementById('current-time');
    if (timeElement) {
        timeElement.textContent = `${dateString}, ${timeString}`;
    }
}
// Pass data from Laravel to JavaScript
const chartData = {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    visitors: @json($visitorsData),
    interactions: @json($interactionsData),
    reports: @json($reportsData),
    chats: @json($chatsData)
};

// Animate counters with easing
function animateCounter(element, target) {
    const duration = 2000;
    const start = 0;
    const increment = target / (duration / 16);
    let current = start;

    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current).toLocaleString();
    }, 16);
}

// Initialize counter animations on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCurrentTime();
    setInterval(updateCurrentTime, 1000);
    
    // Initialize tooltips for timestamp hovers
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        // Bootstrap tooltip initialization if you're using it
        new bootstrap.Tooltip(tooltipTriggerEl);
    });

    const counters = document.querySelectorAll('.animated-counter');

    // Intersection Observer for counter animation
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = parseInt(entry.target.dataset.target);
                animateCounter(entry.target, target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => observer.observe(counter));
});

// Enhanced time formatting helper
function formatRelativeTime(dateString, timezone) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = now - date;
    
    const seconds = Math.floor(diff / 1000);
    const minutes = Math.floor(seconds / 60);
    const hours = Math.floor(minutes / 60);
    const days = Math.floor(hours / 24);
    
    if (seconds < 60) return 'baru saja';
    if (minutes < 60) return `${minutes} menit yang lalu`;
    if (hours < 24) return `${hours} jam yang lalu`;
    if (days === 1) return 'kemarin';
    if (days < 7) return `${days} hari yang lalu`;
    
    // For older dates, show formatted date
    return date.toLocaleDateString('id-ID', {
        timeZone: timezone,
        day: 'numeric',
        month: 'short',
        year: date.getFullYear() !== now.getFullYear() ? 'numeric' : undefined
    });
}

// Auto-refresh relative times every minute
setInterval(() => {
    const timeElements = document.querySelectorAll('[data-timestamp]');
    const timezone = '{{ $userTimezone }}';
    
    timeElements.forEach(element => {
        const timestamp = element.dataset.timestamp;
        if (timestamp) {
            const relativeTime = formatRelativeTime(timestamp, timezone);
            element.textContent = relativeTime;
        }
    });
}, 60000); // Update every minute

// Main Chart Configuration
let mainChart;
function initMainChart() {
    const ctx = document.getElementById('statsChart');
    if (!ctx) return;

    const ctxContext = ctx.getContext('2d');

    const gradient1 = ctxContext.createLinearGradient(0, 0, 0, 400);
    gradient1.addColorStop(0, 'rgba(131, 122, 182, 0.8)');
    gradient1.addColorStop(1, 'rgba(131, 122, 182, 0.1)');

    const gradient2 = ctxContext.createLinearGradient(0, 0, 0, 400);
    gradient2.addColorStop(0, 'rgba(16, 185, 129, 0.8)');
    gradient2.addColorStop(1, 'rgba(16, 185, 129, 0.1)');

    const gradient3 = ctxContext.createLinearGradient(0, 0, 0, 400);
    gradient3.addColorStop(0, 'rgba(245, 158, 11, 0.8)');
    gradient3.addColorStop(1, 'rgba(245, 158, 11, 0.1)');

    const gradient4 = ctxContext.createLinearGradient(0, 0, 0, 400);
    gradient4.addColorStop(0, 'rgba(59, 130, 246, 0.8)');
    gradient4.addColorStop(1, 'rgba(59, 130, 246, 0.1)');

    mainChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    label: 'Pengunjung',
                    data: chartData.visitors,
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: gradient4,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 8
                },
                {
                    label: 'Interaksi Artikel',
                    data: chartData.interactions,
                    borderColor: 'rgba(16, 185, 129, 1)',
                    backgroundColor: gradient2,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(16, 185, 129, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 8
                },
                {
                    label: 'Laporan',
                    data: chartData.reports,
                    borderColor: 'rgba(245, 158, 11, 1)',
                    backgroundColor: gradient3,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(245, 158, 11, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 8
                },
                {
                    label: 'Konseling Digital',
                    data: chartData.chats,
                    borderColor: 'rgba(131, 122, 182, 1)',
                    backgroundColor: gradient1,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(131, 122, 182, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 13,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: '#1e293b',
                    bodyColor: '#64748b',
                    borderColor: 'rgba(131, 122, 182, 0.2)',
                    borderWidth: 1,
                    cornerRadius: 12,
                    displayColors: true,
                    padding: 12
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#64748b',
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(148, 163, 184, 0.1)'
                    },
                    ticks: {
                        color: '#64748b',
                        font: {
                            size: 12,
                            weight: '500'
                        },
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeInOutCubic'
            }
        }
    });
}

// Period selector functionality
document.addEventListener('DOMContentLoaded', function() {
    const periodButtons = document.querySelectorAll('.period-btn');

    periodButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            periodButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');

            const period = this.dataset.period;
            fetchDashboardData(period);
            updateComparisonText(period);
        });
    });
});

// Update comparison text based on selected period
function updateComparisonText(period) {
    const comparisonTexts = document.querySelectorAll('.comparison-text');
    let text = '';

    switch(period) {
        case 'month':
            text = 'dibandingkan bulan lalu';
            break;
        case 'quarter':
            text = 'dibandingkan quarter lalu';
            break;
        case 'year':
            text = 'dibandingkan tahun lalu';
            break;
    }

    comparisonTexts.forEach(element => {
        element.textContent = text;
    });
}

// Fetch dashboard data for different periods
async function fetchDashboardData(period) {
    try {
        const response = await fetch(`/admin/dashboard/data?period=${period}`);
        const data = await response.json();

        // Update stat cards
        updateStatCard('visitors', data.visitors);
        updateStatCard('interactions', data.interactions);
        updateStatCard('reports', data.reports);
        updateStatCard('chats', data.chats);

    } catch (error) {
        console.error('Error fetching dashboard data:', error);
    }
}

// Update stat card with new data
function updateStatCard(type, data) {
    const cards = document.querySelectorAll('.card-stats');
    const cardMap = {
        'visitors': 0,
        'interactions': 1,
        'reports': 2,
        'chats': 3
    };

    const cardIndex = cardMap[type];
    const card = cards[cardIndex];

    if (!card) return;

    const numberElement = card.querySelector('.animated-counter');
    const growthBadge = card.querySelector('.growth-badge');

    // Animate the counter
    animateCounter(numberElement, data.total);

    // Update growth badge
    const isPositive = data.growth >= 0;
    growthBadge.className = `growth-badge ${isPositive ? 'growth-positive' : 'growth-negative'}`;
    growthBadge.innerHTML = `
        <i class="fas fa-trending-${isPositive ? 'up' : 'down'}" style="font-size: 10px;"></i>
        ${data.growth >= 0 ? '+' : ''}${data.growth}%
    `;
}

// Initialize everything when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Initialize main chart
    initMainChart();

    // Add loading states and error handling
    const statCards = document.querySelectorAll('.card-stats');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px) scale(1.02)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});

// Refresh data periodically (every 5 minutes)
setInterval(() => {
    const activeButton = document.querySelector('.period-btn.active');
    if (activeButton) {
        const period = activeButton.dataset.period;
        fetchDashboardData(period);
    }
}, 300000); // 5 minutes
</script>

@endsection
