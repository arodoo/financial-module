<?php
// Loan Simulator View

// Include necessary files
require_once '../../controllers/LoanSimulatorController.php';

// Initialize the controller
$loanSimulatorController = new LoanSimulatorController();

// Fetch loan simulation results if available
$simulationResults = isset($_SESSION['simulation_results']) ? $_SESSION['simulation_results'] : null;

// Clear results from session after fetching
unset($_SESSION['simulation_results']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="../../assets/js/loan-simulator.js" defer></script>
    <title>Loan Simulator</title>
</head>
<body>
    <div class="container">
        <h1>Loan Simulator</h1>
        <form id="loan-simulation-form" method="POST" action="results.php">
            <label for="loan-amount">Loan Amount:</label>
            <input type="number" id="loan-amount" name="loan_amount" required>

            <label for="interest-rate">Interest Rate (%):</label>
            <input type="number" id="interest-rate" name="interest_rate" step="0.01" required>

            <label for="loan-term">Loan Term (years):</label>
            <input type="number" id="loan-term" name="loan_term" required>

            <button type="submit">Simulate Loan</button>
        </form>

        <?php if ($simulationResults): ?>
            <h2>Simulation Results</h2>
            <div id="simulation-results">
                <!-- Display simulation results here -->
                <?php echo $simulationResults; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>