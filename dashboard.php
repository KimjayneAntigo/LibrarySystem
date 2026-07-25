<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('database.php');
include('layout.php');

$totalProducts = $conn->query("SELECT COUNT(*) AS total FROM books")->fetch_assoc()['total'];

$totalSales = $conn->query("
    SELECT COUNT(*) AS total
    FROM borrowings
")->fetch_assoc()['total'];

$inStock = $conn->query("
    SELECT COUNT(*) AS total
    FROM books
    WHERE status='Available'
")->fetch_assoc()['total'];

$lowStock = $conn->query("
    SELECT COUNT(*) AS total
    FROM books
    WHERE stock <= 5
")->fetch_assoc()['total'];

$totalClients = $conn->query("
    SELECT COUNT(*) AS total
    FROM members
")->fetch_assoc()['total'];

$totalRevenue = $conn->query("
    SELECT IFNULL(SUM(books.price),0) AS total
    FROM borrowings, books
    WHERE borrowings.book_id = books.book_id
")->fetch_assoc()['total'];

$totalFeedback = $conn->query("
    SELECT COUNT(*) AS total
    FROM feedback
")->fetch_assoc()['total'];
?>

<div class="card shadow border-0 p-4 mt-4">
    <h4 class="mb-3">📈 Monthly Sales Data</h4>

    <canvas id="monthlySalesChart" height="100"></canvas>
</div>

<?php
$monthlySales = $conn->query("
    SELECT 
        DATE_FORMAT(borrow_date, '%M') AS month_name,
        MONTH(borrow_date) AS month_number,
        COUNT(*) AS total_sales

    FROM borrowings

    WHERE borrow_date BETWEEN '2026-03-01' AND '2026-05-31'

    GROUP BY 
        MONTH(borrow_date),
        DATE_FORMAT(borrow_date, '%M')

    ORDER BY month_number ASC
");

$months = [];
$sales = [];

while ($row = $monthlySales->fetch_assoc()) {
    $months[] = $row['month_name'];
    $sales[] = $row['total_sales'];
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const months = <?= json_encode($months) ?>;
const sales = <?= json_encode($sales) ?>;

new Chart(document.getElementById('monthlySalesChart'), {
    type: 'bar',
    data: {
        labels: months,
        datasets: [{
            label: 'Total Sales per Month',
            data: sales
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        }
    }
});

</script>

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold">📈 Business Dashboard</h2>
        <p class="text-muted mb-0">
            Welcome to BookStore CRM, <?= $_SESSION['fullname']; ?>.
        </p>
    </div>

    <span class="badge bg-success">Business CRM</span>
</div>

<div class="row g-4 mb-4">

    <div class="col-md-2">
        <div class="card shadow border-0 p-3 text-center">
            <h6 class="text-muted">Total Products</h6>
            <h2 class="fw-bold"><?= $totalProducts ?></h2>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card shadow border-0 p-3 text-center">
            <h6 class="text-muted">Total Sales</h6>
            <h2 class="fw-bold text-success"><?= $totalSales ?></h2>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card shadow border-0 p-3 text-center">
            <h6 class="text-muted">In Stock</h6>
            <h2 class="fw-bold text-primary"><?= $inStock ?></h2>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card shadow border-0 p-3 text-center">
            <h6 class="text-muted">Low Stock</h6>
            <h2 class="fw-bold text-danger"><?= $lowStock ?></h2>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card shadow border-0 p-3 text-center">
            <h6 class="text-muted">Clients</h6>
            <h2 class="fw-bold text-warning"><?= $totalClients ?></h2>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card shadow border-0 p-3 text-center">
            <h6 class="text-muted">Revenue</h6>
            <h2 class="fw-bold">₱<?= number_format($totalRevenue, 2) ?></h2>
        </div>
    </div>

</div>

<div class="card shadow border-0 p-4 mb-4">
    <h5 class="fw-bold mb-3">⚡ Quick Business Actions</h5>

    <div class="row g-3">
        <div class="col-md-3">
            <a href="books.php" class="btn btn-primary w-100">
                📦 Manage Inventory
            </a>
        </div>

        <div class="col-md-3">
            <a href="borrow.php" class="btn btn-success w-100">
                🛒 Record Sales
            </a>
        </div>

        <div class="col-md-3">
            <a href="members.php" class="btn btn-warning w-100">
                👥 Manage Clients
            </a>
        </div>

        <div class="col-md-3">
            <a href="reports.php" class="btn btn-dark w-100">
                📊 View Reports
            </a>
        </div>
    </div>
</div>

<div class="row">

    <div class="col-md-7">
        <div class="card shadow border-0 p-4 mb-4">
            <h4 class="mb-3">📦 Low Stock Inventory</h4>

            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                <?php
                $lowStockItems = $conn->query("
                    SELECT title, price, stock, status
                    FROM books
                    WHERE stock <= 5
                    ORDER BY stock ASC
                ");

                if ($lowStockItems && $lowStockItems->num_rows > 0):
                    while ($row = $lowStockItems->fetch_assoc()):
                ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td>₱<?= number_format($row['price'], 2) ?></td>
                        <td class="text-danger fw-bold"><?= $row['stock'] ?></td>
                        <td>
                            <span class="badge bg-warning text-dark">
                                <?= htmlspecialchars($row['status']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            No low stock products.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card shadow border-0 p-4 mb-4">
            <h4 class="mb-3">🛒 Recent Sales</h4>

            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Client</th>
                        <th>Product</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                <?php
                $recentSales = $conn->query("
                    SELECT members.name, books.title, borrowings.borrow_date
                    FROM borrowings, members, books
                    WHERE borrowings.member_id = members.member_id
                    AND borrowings.book_id = books.book_id
                    ORDER BY borrowings.borrow_id DESC
                    LIMIT 5
                ");

                if ($recentSales && $recentSales->num_rows > 0):
                    while ($sale = $recentSales->fetch_assoc()):
                ?>
                    <tr>
                        <td><?= htmlspecialchars($sale['name']) ?></td>
                        <td><?= htmlspecialchars($sale['title']) ?></td>
                        <td><?= $sale['borrow_date'] ?></td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted">
                            No recent sales.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="card shadow border-0 p-4">
            <h4 class="mb-3">⭐ Customer Feedback</h4>

            <h2 class="fw-bold text-primary"><?= $totalFeedback ?></h2>
            <p class="text-muted mb-0">Total feedback and reviews recorded.</p>
        </div>
    </div>

</div>

</div>

<?php include('layout_end.php'); ?>