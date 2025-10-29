import './bootstrap';
import Chart from 'chart.js/auto';

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

// Main Chart Configuration
let mainChart;
function initMainChart() {
    const ctx = document.getElementById('mainChart');
    if (!ctx) return;
    
    const gradient1 = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
    gradient1.addColorStop(0, 'rgba(131, 122, 182, 0.8)');
    gradient1.addColorStop(1, 'rgba(131, 122, 182, 0.1)');
    
    const gradient2 = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
    gradient2.addColorStop(0, 'rgba(16, 185, 129, 0.8)');
    gradient2.addColorStop(1, 'rgba(16, 185, 129, 0.1)');
    
    const gradient3 = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
    gradient3.addColorStop(0, 'rgba(245, 158, 11, 0.8)');
    gradient3.addColorStop(1, 'rgba(245, 158, 11, 0.1)');
    
    const gradient4 = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
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

// Mini Charts for stat cards
function createMiniChart(canvasId, data, color) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels.slice(-6), // Last 6 months
            datasets: [{
                data: data.slice(-6),
                borderColor: color,
                backgroundColor: color + '20',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false }
            },
            scales: {
                x: { display: false },
                y: { display: false }
            },
            elements: {
                line: { borderWidth: 2 },
                point: { radius: 0 }
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
        });
    });
});

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
    const cards = {
        'visitors': 0,
        'interactions': 1,
        'reports': 2,
        'chats': 3
    };
    
    const cardIndex = cards[type];
    const statCards = document.querySelectorAll('.stat-card');
    const card = statCards[cardIndex];
    
    if (!card) return;
    
    const numberElement = card.querySelector('.stat-number');
    const growthBadge = card.querySelector('.growth-badge');
    
    // Animate the counter
    animateCounter(numberElement, data.total);
    
    // Update growth badge
    const isPositive = data.growth >= 0;
    growthBadge.className = `growth-badge ${isPositive ? 'growth-positive' : 'growth-negative'}`;
    growthBadge.innerHTML = `
        <i class="fas fa-trending-${isPositive ? 'up' : 'down'}" style="font-size: 12px;"></i>
        ${data.growth >= 0 ? '+' : ''}${data.growth}%
    `;
}

// Chart filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const chartButtons = document.querySelectorAll('[data-chart]');
    
    chartButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            chartButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            const chartType = this.dataset.chart;
            filterMainChart(chartType);
        });
    });
});

// Filter main chart datasets
function filterMainChart(type) {
    if (!mainChart) return;
    
    const datasets = mainChart.data.datasets;
    
    if (type === 'all') {
        datasets.forEach(dataset => dataset.hidden = false);
    } else {
        datasets.forEach((dataset, index) => {
            const datasetTypes = ['chats', 'interactions', 'reports', 'visitors'];
            dataset.hidden = datasetTypes[index] !== type;
        });
    }
    
    mainChart.update('active');
}

// Initialize everything when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Initialize main chart
    initMainChart();
    
    // Initialize mini charts
    createMiniChart('visitorsChart', chartData.visitors, '#3b82f6');
    createMiniChart('interactionsChart', chartData.interactions, '#10b981');
    createMiniChart('reportsChart', chartData.reports, '#f59e0b');
    createMiniChart('chatsChart', chartData.chats, '#837ab6');
    
    // Add loading states and error handling
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
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