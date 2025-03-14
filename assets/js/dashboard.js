const dashboard = {
    init: function() {
        this.bindEvents();
        this.loadData();
    },

    bindEvents: function() {
        document.getElementById('refreshButton').addEventListener('click', this.loadData.bind(this));
    },

    loadData: function() {
        // Fetch data for the dashboard summary
        fetch('/financial/modules/visualization/controllers/DashboardController.php')
            .then(response => response.json())
            .then(data => {
                this.updateDashboard(data);
            })
            .catch(error => console.error('Error loading dashboard data:', error));
    },

    updateDashboard: function(data) {
        // Update the dashboard with the fetched data
        document.getElementById('totalIncome').innerText = data.totalIncome;
        document.getElementById('totalExpenses').innerText = data.totalExpenses;
        document.getElementById('netSavings').innerText = data.netSavings;

        this.renderCharts(data.incomeData, data.expenseData);
    },

    renderCharts: function(incomeData, expenseData) {
        // Render income and expense charts using a charting library
        const ctxIncome = document.getElementById('incomeChart').getContext('2d');
        const ctxExpense = document.getElementById('expenseChart').getContext('2d');

        new Chart(ctxIncome, {
            type: 'bar',
            data: {
                labels: incomeData.labels,
                datasets: [{
                    label: 'Income',
                    data: incomeData.values,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        new Chart(ctxExpense, {
            type: 'bar',
            data: {
                labels: expenseData.labels,
                datasets: [{
                    label: 'Expenses',
                    data: expenseData.values,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
};

document.addEventListener('DOMContentLoaded', function() {
    dashboard.init();
});