/**
 * School Fee Simulator JavaScript
 * Handles interactive UI elements for the school fee simulation module
 */
document.addEventListener('DOMContentLoaded', function() {
    // Format currency inputs
    const formatCurrency = (input) => {
        if (!input) return;
        
        input.addEventListener('input', function(e) {
            // Remove all non-digits
            let value = this.value.replace(/\D/g, '');
            // Format with thousand separators
            this.value = new Intl.NumberFormat('fr-FR').format(value);
        });
        
        // Before form submission, clean the input
        if (input.form) {
            input.form.addEventListener('submit', function(e) {
                input.value = input.value.replace(/\D/g, '');
            });
        }
    };
    
    // Format all currency inputs
    formatCurrency(document.getElementById('annual_tuition'));
    formatCurrency(document.getElementById('additional_expenses'));
    
    // Calculate and display child's age when birthdate changes
    const birthdateInput = document.getElementById('child_birthdate');
    const calculateAge = function() {
        if (!birthdateInput) return;
        
        if (birthdateInput.value) {
            const birthDate = new Date(birthdateInput.value);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            
            // Update the education level suggestion based on age
            updateEducationLevelSuggestion(age);
            
            // Display the age if there's an element for it
            const ageDisplay = document.getElementById('age_display');
            if (ageDisplay) {
                ageDisplay.textContent = age + ' ans';
            }
        }
    };
    
    // Update education level suggestion based on child's age
    function updateEducationLevelSuggestion(age) {
        const levelSelect = document.getElementById('current_level');
        if (!levelSelect) return;
        
        // French education system levels with typical ages
        const educationLevelsByAge = [
            { maxAge: 2, level: null },
            { maxAge: 5, level: 'maternelle' },
            { maxAge: 10, level: 'primaire' },
            { maxAge: 14, level: 'college' },
            { maxAge: 17, level: 'lycee' },
            { maxAge: 100, level: 'superieur' }
        ];
        
        // Find the appropriate level for the age
        let suggestedLevel = null;
        for (let i = 0; i < educationLevelsByAge.length; i++) {
            if (age <= educationLevelsByAge[i].maxAge) {
                suggestedLevel = educationLevelsByAge[i].level;
                break;
            }
        }
        
        // Select the suggested level if it exists
        if (suggestedLevel && !levelSelect.dataset.userSelected) {
            for (let i = 0; i < levelSelect.options.length; i++) {
                if (levelSelect.options[i].value === suggestedLevel) {
                    levelSelect.selectedIndex = i;
                    break;
                }
            }
        }
    }
    
    // Mark the education level as user-selected when changed
    const levelSelect = document.getElementById('current_level');
    if (levelSelect) {
        levelSelect.addEventListener('change', function() {
            this.dataset.userSelected = 'true';
        });
    }
    
    if (birthdateInput) {
        birthdateInput.addEventListener('change', calculateAge);
        // Calculate on page load if birthdate is set
        calculateAge();
    }
    
    // Handle chart visualization if Chart.js is available
    if (typeof Chart !== 'undefined' && document.getElementById('fees-chart')) {
        const ctx = document.getElementById('fees-chart').getContext('2d');
        
        // Extract data from the page if available
        const chartData = window.feesChartData || {
            labels: [],
            tuitionData: [],
            additionalData: []
        };
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Frais de Scolarité',
                        data: chartData.tuitionData,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Dépenses Supplémentaires',
                        data: chartData.additionalData,
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Coût (€)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Année'
                        }
                    }
                }
            }
        });
    }

    const feeForm = document.getElementById('school-fee-form');
    const resultContainer = document.getElementById('fee-simulation-results');

    feeForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const feeAmount = parseFloat(document.getElementById('fee-amount').value);
        const duration = parseInt(document.getElementById('duration').value);
        const interestRate = parseFloat(document.getElementById('interest-rate').value) / 100;

        const totalFees = calculateTotalFees(feeAmount, duration, interestRate);
        displayResults(totalFees);
    });

    function calculateTotalFees(fee, years, rate) {
        return fee * Math.pow((1 + rate), years);
    }

    function displayResults(total) {
        resultContainer.innerHTML = `Total projected school fees over the period: $${total.toFixed(2)}`;
    }
});