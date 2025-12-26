<?php
include_once '../includes/db_connect.php';
include_once 'auth_check.php';

// --- 1. FETCH DATA FOR SALES OVER TIME CHART ---
// This query groups all 'Delivered' orders by the date they were created and sums up the total amount for each day.
$sales_by_day_sql = "SELECT DATE(created_at) as order_date, SUM(total_amount) as daily_sales 
                     FROM orders 
                     WHERE order_status = 'Delivered' 
                     GROUP BY DATE(created_at) 
                     ORDER BY order_date ASC 
                     LIMIT 30"; // Limit to the last 30 days of sales
$sales_result = $conn->query($sales_by_day_sql);

$sales_labels = [];
$sales_data = [];
while ($row = $sales_result->fetch_assoc()) {
    $sales_labels[] = date("M d", strtotime($row['order_date']));
    $sales_data[] = $row['daily_sales'];
}


// --- 2. FETCH DATA FOR TOP SELLING PRODUCTS ---
// This query joins order_items with products, sums up the quantity sold for each product, and orders them.
$top_products_sql = "SELECT p.name, SUM(oi.quantity) as total_sold
                     FROM order_items oi
                     JOIN products p ON oi.product_id = p.id
                     GROUP BY oi.product_id
                     ORDER BY total_sold DESC
                     LIMIT 5"; // Show the top 5 best-selling products
$top_products_result = $conn->query($top_products_sql);

$top_products_labels = [];
$top_products_data = [];
while ($row = $top_products_result->fetch_assoc()) {
    $top_products_labels[] = $row['name'];
    $top_products_data[] = $row['total_sold'];
}


include_once '../includes/header.php';
?>

<div class="admin-reports">
    <h2>Sales Analytics & Reports</h2>

    <!-- Sales Over Time Chart -->
    <div class="report-card">
        <h3>Sales Revenue Over Time (Last 30 Days)</h3>
        <!-- The chart will be drawn on this canvas element -->
        <canvas id="salesChart"></canvas>
    </div>

    <!-- Top Selling Products Chart -->
    <div class="report-card">
        <h3>Top 5 Best-Selling Products</h3>
        <canvas id="topProductsChart"></canvas>
    </div>
</div>

<!-- Include the Chart.js library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- JavaScript to initialize the charts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Sales Chart (Line Chart)
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($sales_labels); ?>,
            datasets: [{
                label: 'Daily Revenue ($)',
                data: <?php echo json_encode($sales_data); ?>,
                backgroundColor: 'rgba(0, 123, 255, 0.2)',
                borderColor: 'rgba(0, 123, 255, 1)',
                borderWidth: 2,
                tension: 0.1
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

    // 2. Top Products Chart (Bar Chart)
    const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
    const topProductsChart = new Chart(topProductsCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($top_products_labels); ?>,
            datasets: [{
                label: 'Units Sold',
                data: <?php echo json_encode($top_products_data); ?>,
                backgroundColor: [
                    'rgba(40, 167, 69, 0.7)',
                    'rgba(0, 123, 255, 0.7)',
                    'rgba(255, 193, 7, 0.7)',
                    'rgba(220, 53, 69, 0.7)',
                    'rgba(23, 162, 184, 0.