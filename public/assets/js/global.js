// Elements
const sidebar = document.getElementById('sidebar');
const mainContent = document.getElementById('mainContent');
const collapseBtn = document.getElementById('collapseBtn');
const mobileMenuBtn = document.getElementById('mobileMenuBtn');
const sidebarOverlay = document.getElementById('sidebarOverlay');

let sidebarCollapsed = false;

// Desktop Collapse Toggle
collapseBtn.addEventListener('click', () => {
    sidebarCollapsed = !sidebarCollapsed;

    if (sidebarCollapsed) {
        sidebar.classList.remove('expanded');
        sidebar.classList.add('collapsed');
        mainContent.classList.remove('sidebar-expanded');
        mainContent.classList.add('sidebar-collapsed');
        collapseBtn.innerHTML = '<i class="bi-chevron-bar-right bi"></i>';
    } else {
        sidebar.classList.remove('collapsed');
        sidebar.classList.add('expanded');
        mainContent.classList.remove('sidebar-collapsed');
        mainContent.classList.add('sidebar-expanded');
        collapseBtn.innerHTML = '<i class="bi-chevron-bar-left bi"></i>';
    }
});

// Mobile Menu Toggle
mobileMenuBtn.addEventListener('click', () => {
    sidebar.classList.toggle('show');
    sidebarOverlay.classList.toggle('show');
});

// Close sidebar when clicking overlay
sidebarOverlay.addEventListener('click', () => {
    sidebar.classList.remove('show');
    sidebarOverlay.classList.remove('show');
});

// Chart Configuration
const ctx = document.getElementById('mainChart').getContext('2d');

const data = {
    labels: ['11/01', '11/02', '11/03', '11/04', '11/05', '11/06', '11/07'],
    datasets: [
        {
            label: 'Muy insatisfecho',
            data: [8, 10, 12, 10, 9, 11, 10],
            backgroundColor: '#dc2626',
            stack: 'Stack 0',
        },
        {
            label: 'Insatisfecho',
            data: [10, 8, 15, 8, 7, 14, 12],
            backgroundColor: '#f87171',
            stack: 'Stack 0',
        },
        {
            label: 'Feliz',
            data: [32, 35, 25, 32, 35, 25, 28],
            backgroundColor: '#4ade80',
            stack: 'Stack 0',
        },
        {
            label: 'Muy feliz',
            data: [50, 47, 48, 50, 49, 50, 50],
            backgroundColor: '#16a34a',
            stack: 'Stack 0',
        },
        {
            type: 'line',
            label: 'Índice de felicidad',
            data: [86, 84, 81, 88, 90, 85, 87],
            borderColor: '#2563eb',
            backgroundColor: 'transparent',
            borderWidth: 3,
            pointRadius: 4,
            pointBackgroundColor: '#2563eb',
            yAxisID: 'y1',
            tension: 0.4
        }
    ]
};

const config = {
    type: 'bar',
    data: data,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        scales: {
            x: {
                stacked: true,
                grid: {
                    display: false
                }
            },
            y: {
                stacked: true,
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: function (value) {
                        return value + '%';
                    }
                },
                grid: {
                    color: '#e5e7eb'
                }
            },
            y1: {
                position: 'right',
                beginAtZero: true,
                max: 100,
                grid: {
                    display: false
                },
                ticks: {
                    callback: function (value) {
                        return value;
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function (context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        if (context.parsed.y !== null) {
                            if (context.dataset.type === 'line') {
                                label += context.parsed.y;
                            } else {
                                label += context.parsed.y + '%';
                            }
                        }
                        return label;
                    }
                }
            }
        }
    }
};

const myChart = new Chart(ctx, config);