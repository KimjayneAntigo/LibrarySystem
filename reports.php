<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('database.php');
include('layout.php');

$report = $_GET['report'] ?? '';
?>

<h2 class="fw-bold mb-4">📊 Business & CRM Reports</h2>

<div class="card shadow border-0 p-4 mb-4">

    <form method="GET">
        <div class="row align-items-end">

            <div class="col-md-6">
                <label class="form-label fw-bold">Select Report</label>

                <select name="report" class="form-select" required>
                    <option value="">Choose Report</option>

                    <option value="clients" <?= $report == 'clients' ? 'selected' : '' ?>>
                        Client Records Report
                    </option>

                    <option value="sales" <?= $report == 'sales' ? 'selected' : '' ?>>
                        Sales Transaction Report
                    </option>

                    <option value="inventory" <?= $report == 'inventory' ? 'selected' : '' ?>>
                        Inventory Report
                    </option>

                    <option value="lowstock" <?= $report == 'lowstock' ? 'selected' : '' ?>>
                        Low Stock Inventory Report
                    </option>

                    <option value="revenue" <?= $report == 'revenue' ? 'selected' : '' ?>>
                        Financial / Revenue Report
                    </option>

                    <option value="topclients" <?= $report == 'topclients' ? 'selected' : '' ?>>
                        Top Clients Report
                    </option>

                    <option value="feedback" <?= $report == 'feedback' ? 'selected' : '' ?>>
                        Customer Feedback Report
                    </option>
                </select>
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary w-100">
                    Generate Report
                </button>
            </div>

        </div>
    </form>

</div>

<?php
$title = "";
$query = "";

if ($report == "clients") {
    $title = "👥 Client Records Report";

    $query = "
        SELECT 
            members.member_id AS client_id,
            members.name AS client_name,
            members.course AS customer_group,
            members.year_level AS client_level,
            IFNULL(SUM(points.points), 0) AS loyalty_points

        FROM members

        LEFT JOIN points 
            ON members.member_id = points.member_id

        GROUP BY 
            members.member_id,
            members.name,
            members.course,
            members.year_level

        ORDER BY members.member_id ASC
    ";
}

if ($report == "sales") {
    $title = "🛒 Sales Transaction Report";

    $query = "
        SELECT
            borrowings.borrow_id AS sales_id,
            members.name AS client_name,
            books.title AS product_name,
            books.price AS selling_price,
            borrowings.borrow_date AS sales_date

        FROM borrowings, members, books

        WHERE members.member_id = borrowings.member_id
        AND borrowings.book_id = books.book_id

        ORDER BY borrowings.borrow_id DESC
    ";
}

if ($report == "inventory") {
    $title = "📦 Inventory Report";

    $query = "
        SELECT
            books.book_id AS product_id,
            books.title AS product_name,
            authors.author_name AS supplier_author,
            categories.category_name AS category,
            books.price,
            books.stock,
            CASE
                WHEN books.stock <= 5 THEN 'Low Stock'
                ELSE 'In Stock'
            END AS inventory_status

        FROM books, authors, categories

        WHERE books.author_id = authors.author_id
        AND books.category_id = categories.category_id

        ORDER BY books.book_id ASC
    ";
}

if ($report == "lowstock") {
    $title = "⚠️ Low Stock Inventory Report";

    $query = "
        SELECT
            books.book_id AS product_id,
            books.title AS product_name,
            categories.category_name AS category,
            books.price,
            books.stock

        FROM books, categories

        WHERE books.category_id = categories.category_id
        AND books.stock <= 5

        ORDER BY books.stock ASC
    ";
}

if ($report == "revenue") {
    $title = "💰 Financial / Revenue Report";

    $query = "
        SELECT
            DATE_FORMAT(borrowings.borrow_date, '%Y-%m') AS sales_month,
            COUNT(borrowings.borrow_id) AS total_transactions,
            IFNULL(SUM(books.price), 0) AS gross_revenue

        FROM borrowings, books

        WHERE borrowings.book_id = books.book_id

        GROUP BY DATE_FORMAT(borrowings.borrow_date, '%Y-%m')

        ORDER BY sales_month DESC
    ";
}

if ($report == "topclients") {
    $title = "🏆 Top Clients Report";

    $query = "
        SELECT
            members.member_id AS client_id,
            members.name AS client_name,
            COUNT(borrowings.borrow_id) AS total_purchases,
            IFNULL(SUM(books.price), 0) AS total_spent,
            IFNULL(SUM(points.points), 0) AS loyalty_points

        FROM members

        LEFT JOIN borrowings
            ON members.member_id = borrowings.member_id

        LEFT JOIN books
            ON borrowings.book_id = books.book_id

        LEFT JOIN points
            ON members.member_id = points.member_id

        GROUP BY 
            members.member_id,
            members.name

        ORDER BY total_purchases DESC, total_spent DESC
    ";
}

if ($report == "feedback") {
    $title = "⭐ Customer Feedback Report";

    $query = "
        SELECT
            feedback.feedback_id,
            members.name AS client_name,
            books.title AS product_name,
            feedback.rating,
            feedback.comment,
            feedback.feedback_date

        FROM feedback

        LEFT JOIN members
            ON feedback.member_id = members.member_id

        LEFT JOIN books
            ON feedback.book_id = books.book_id

        ORDER BY feedback.feedback_date DESC
    ";
}
?>

<?php if (!empty($report) && !empty($query)): ?>

<div class="card shadow border-0 p-4">

    <h4 class="mb-3"><?= $title ?></h4>

    <?php
    $result = $conn->query($query);
    ?>

    <?php if ($result && $result->num_rows > 0): ?>

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-dark">
                    <tr>
                        <?php
                        $fields = $result->fetch_fields();

                        foreach ($fields as $field):
                            $label = ucwords(str_replace("_", " ", $field->name));
                        ?>
                            <th><?= htmlspecialchars($label) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>

                <tbody>

                    <?php while ($row = $result->fetch_assoc()): ?>

                    <tr>
                        <?php foreach ($row as $value): ?>
                            <td><?= htmlspecialchars($value) ?></td>
                        <?php endforeach; ?>
                    </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    <?php else: ?>

        <div class="alert alert-warning mb-0">
            No records found for this report.
        </div>

    <?php endif; ?>

</div>

<?php endif; ?>

<?php include('layout_end.php'); ?>