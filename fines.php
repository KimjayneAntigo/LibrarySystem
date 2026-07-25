<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('database.php');
include('layout.php');
?>

<div class="container-fluid">

    <h2 class="fw-bold mb-4">💳 Payments Management</h2>

    <div class="card shadow border-0 p-4">

        <h4 class="mb-3">Sales Payment Records</h4>

        <table class="table table-bordered table-hover align-middle">

            <thead class="table-dark">
                <tr>
                    <th>Payment ID</th>
                    <th>Client</th>
                    <th>Product</th>
                    <th>Transaction Date</th>
                    <th>Amount Paid</th>
                    <th>Payment Status</th>
                </tr>
            </thead>

            <tbody>

            <?php
            $payments = $conn->query("
                SELECT
                    borrowings.borrow_id AS payment_id,
                    members.name AS client_name,
                    books.title AS product_name,
                    books.price AS amount_paid,
                    borrowings.borrow_date AS transaction_date

                FROM borrowings, members, books

                WHERE borrowings.member_id = members.member_id
                AND borrowings.book_id = books.book_id

                ORDER BY borrowings.borrow_id DESC
            ");

            if ($payments && $payments->num_rows > 0):
                while ($row = $payments->fetch_assoc()):
            ?>

            <tr>
                <td><?= $row['payment_id'] ?></td>
                <td><?= htmlspecialchars($row['client_name']) ?></td>
                <td><?= htmlspecialchars($row['product_name']) ?></td>
                <td><?= $row['transaction_date'] ?></td>
                <td>₱<?= number_format($row['amount_paid'], 2) ?></td>
                <td>
                    <span class="badge bg-success">Paid</span>
                </td>
            </tr>

            <?php
                endwhile;
            else:
            ?>

            <tr>
                <td colspan="6" class="text-center text-muted">
                    No payment records found.
                </td>
            </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<?php include('layout_end.php'); ?>