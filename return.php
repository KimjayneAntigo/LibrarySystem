<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('database.php');
include('layout.php');
?>

<h2 class="fw-bold mb-4">🕘 Sales History</h2>

<div class="card shadow border-0 p-4">

    <h4 class="mb-3">Completed Sales Transactions</h4>

    <table class="table table-bordered table-hover align-middle">

        <thead class="table-dark">
            <tr>
                <th>Sale ID</th>
                <th>Client</th>
                <th>Product</th>
                <th>Sale Date</th>
                <th>Price</th>
                <th>Stock Status</th>
            </tr>
        </thead>

        <tbody>

        <?php
        $sales = $conn->query("
            SELECT
                borrowings.borrow_id,
                members.name AS client_name,
                books.title AS product_name,
                books.price,
                books.stock,
                borrowings.borrow_date

            FROM borrowings, members, books

            WHERE borrowings.member_id = members.member_id
            AND borrowings.book_id = books.book_id

            ORDER BY borrowings.borrow_id DESC
        ");

        if ($sales && $sales->num_rows > 0):
            while ($row = $sales->fetch_assoc()):
        ?>

        <tr>
            <td><?= $row['borrow_id'] ?></td>
            <td><?= htmlspecialchars($row['client_name']) ?></td>
            <td><?= htmlspecialchars($row['product_name']) ?></td>
            <td><?= $row['borrow_date'] ?></td>
            <td>₱<?= number_format($row['price'], 2) ?></td>
            <td>
                <?php if ($row['stock'] <= 5): ?>
                    <span class="badge bg-danger">Low Stock</span>
                <?php else: ?>
                    <span class="badge bg-success">In Stock</span>
                <?php endif; ?>
            </td>
        </tr>

        <?php
            endwhile;
        else:
        ?>

        <tr>
            <td colspan="6" class="text-center text-muted">
                No sales transactions found.
            </td>
        </tr>

        <?php endif; ?>

        </tbody>

    </table>

</div>

<?php include('layout_end.php'); ?>