// filepath: /financial/financial/modules/visualization/assets/js/income-expense.js
document.addEventListener('DOMContentLoaded', function() {
    const incomeForm = document.getElementById('income-form');
    const expenseForm = document.getElementById('expense-form');
    const incomeList = document.getElementById('income-list');
    const expenseList = document.getElementById('expense-list');
    const totalIncomeDisplay = document.getElementById('total-income');
    const totalExpenseDisplay = document.getElementById('total-expense');

    let totalIncome = 0;
    let totalExpense = 0;

    incomeForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const incomeAmount = parseFloat(document.getElementById('income-amount').value);
        const incomeDescription = document.getElementById('income-description').value;

        if (!isNaN(incomeAmount) && incomeAmount > 0) {
            totalIncome += incomeAmount;
            updateIncomeList(incomeDescription, incomeAmount);
            updateTotalIncome();
            incomeForm.reset();
        }
    });

    expenseForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const expenseAmount = parseFloat(document.getElementById('expense-amount').value);
        const expenseDescription = document.getElementById('expense-description').value;

        if (!isNaN(expenseAmount) && expenseAmount > 0) {
            totalExpense += expenseAmount;
            updateExpenseList(expenseDescription, expenseAmount);
            updateTotalExpense();
            expenseForm.reset();
        }
    });

    function updateIncomeList(description, amount) {
        const listItem = document.createElement('li');
        listItem.textContent = `${description}: $${amount.toFixed(2)}`;
        incomeList.appendChild(listItem);
    }

    function updateExpenseList(description, amount) {
        const listItem = document.createElement('li');
        listItem.textContent = `${description}: $${amount.toFixed(2)}`;
        expenseList.appendChild(listItem);
    }

    function updateTotalIncome() {
        totalIncomeDisplay.textContent = `$${totalIncome.toFixed(2)}`;
    }

    function updateTotalExpense() {
        totalExpenseDisplay.textContent = `$${totalExpense.toFixed(2)}`;
    }
});