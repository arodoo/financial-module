// filepath: /financial/modules/visualization/assets/js/loan-simulator.js
document.addEventListener('DOMContentLoaded', function() {
    const loanForm = document.getElementById('loan-form');
    const resultContainer = document.getElementById('loan-results');

    loanForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const amount = parseFloat(document.getElementById('loan-amount').value);
        const interestRate = parseFloat(document.getElementById('interest-rate').value) / 100;
        const years = parseInt(document.getElementById('loan-years').value);

        if (isNaN(amount) || isNaN(interestRate) || isNaN(years)) {
            alert('Please enter valid numbers for all fields.');
            return;
        }

        const monthlyPayment = calculateMonthlyPayment(amount, interestRate, years);
        const totalPayment = monthlyPayment * years * 12;
        const totalInterest = totalPayment - amount;

        displayResults(monthlyPayment, totalPayment, totalInterest);
    });

    function calculateMonthlyPayment(principal, annualRate, years) {
        const monthlyRate = annualRate / 12;
        const numberOfPayments = years * 12;
        return (principal * monthlyRate) / (1 - Math.pow(1 + monthlyRate, -numberOfPayments));
    }

    function displayResults(monthlyPayment, totalPayment, totalInterest) {
        resultContainer.innerHTML = `
            <h3>Loan Simulation Results</h3>
            <p>Monthly Payment: $${monthlyPayment.toFixed(2)}</p>
            <p>Total Payment: $${totalPayment.toFixed(2)}</p>
            <p>Total Interest: $${totalInterest.toFixed(2)}</p>
        `;
    }
});