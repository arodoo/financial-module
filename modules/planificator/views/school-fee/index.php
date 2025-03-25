<?php
// filepath: /financial/modules/visualization/views/school-fee/index.php

// Include necessary controllers and models
require_once '../controllers/SchoolFeeController.php';
require_once '../models/SchoolFee.php';

// Initialize the SchoolFeeController
$schoolFeeController = new SchoolFeeController();

// Fetch school fee data for visualization
$schoolFees = $schoolFeeController->getSchoolFees();

// Include the header
include '../header.php';
?>

<div class="school-fee-visualization">
    <h1>School Fee Simulation</h1>
    
    <div class="summary">
        <h2>Summary of School Fees</h2>
        <table>
            <thead>
                <tr>
                    <th>Year</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($schoolFees as $fee): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fee['year']); ?></td>
                        <td><?php echo htmlspecialchars($fee['amount']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="simulation-form">
        <h2>Simulate School Fees</h2>
        <form action="projections.php" method="post">
            <label for="currentYear">Current Year:</label>
            <input type="number" id="currentYear" name="currentYear" required>
            
            <label for="increaseRate">Expected Increase Rate (%):</label>
            <input type="number" id="increaseRate" name="increaseRate" step="0.01" required>
            
            <button type="submit">Simulate</button>
        </form>
    </div>
</div>

<?php
// Include the footer
include '../footer.php';
?>