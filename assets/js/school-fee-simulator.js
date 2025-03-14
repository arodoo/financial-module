// filepath: /financial/financial/modules/visualization/assets/js/school-fee-simulator.js
document.addEventListener('DOMContentLoaded', function() {
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