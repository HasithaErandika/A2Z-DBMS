<!-- views/reports/cost_calculation.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Cost Calculation Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1, h2 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .summary { margin-top: 20px; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Cost Calculation Report</h1>
    <p>Welcome, <?php echo htmlspecialchars($username); ?>!</p>
    <p>Database: <?php echo htmlspecialchars($dbname); ?></p>

    <!-- Summary Section -->
    <div class="summary">
        <p>Total Payments: $<?php echo number_format($total_payments, 2); ?></p>
        <p>Daily Wage Costs: $<?php echo number_format($daily_wage_costs, 2); ?></p>
        <p>Total Salary Increments: $<?php echo number_format($total_increments, 2); ?></p>
        <p>Total Cost: $<?php echo number_format($total_cost, 2); ?></p>
    </div>

    <!-- Employee Cost Breakdown -->
    <h2>Employee Cost Breakdown</h2>
    <table>
        <thead>
            <tr>
                <th>Employee ID</th>
                <th>Employee Name</th>
                <th>Payments</th>
                <th>Daily Wages</th>
                <th>Increments</th>
                <th>Total Cost</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($employee_breakdown as $employee): ?>
                <tr>
                    <td><?php echo htmlspecialchars($employee['emp_id']); ?></td>
                    <td><?php echo htmlspecialchars($employee['emp_name']); ?></td>
                    <td>$<?php echo number_format($employee['payment_total'], 2); ?></td>
                    <td>$<?php echo number_format($employee['daily_wage_total'], 2); ?></td>
                    <td>$<?php echo number_format($employee['increment_total'], 2); ?></td>
                    <td>$<?php echo number_format($employee['payment_total'] + $employee['daily_wage_total'] + $employee['increment_total'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>