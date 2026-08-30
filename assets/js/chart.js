
    // Bar Chart
    const ctxBar = document.getElementById('barChart').getContext('2d');
    const barLabels = window.dashboardBarLabels || [];
    const barData = window.dashboardBarData || [];

    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: barLabels,
            datasets: [{
                label: 'Jumlah Lahan',
                data: barData,
                backgroundColor: '#4caf50'
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Pie Chart
    const ctxPie = document.getElementById('pieChart').getContext('2d');
    const pieLabels = window.dashboardPieLabels || [];
    const pieData = window.dashboardPieData || [];
    const pieColors = window.dashboardPieColors || [];

    new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: pieLabels,
            datasets: [{
                data: pieData,
                backgroundColor: pieColors
            }]
        },
        options: {
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
