<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('database.php');
include('layout.php');

$msg = "";

// RECORD SALE
if (isset($_POST['sale'])) {
    $member_id = (int)$_POST['member_id'];
    $book_id = (int)$_POST['book_id'];
    $quantity = (int)$_POST['quantity'];
    $sale_date = date('Y-m-d');
    $due_date = $sale_date; // kept only because borrowings table requires due_date

    // Check product stock and price
    $checkProduct = $conn->query("
        SELECT title, price, stock, status
        FROM books 
        WHERE book_id = $book_id
    ")->fetch_assoc();

    if (!$checkProduct) {

        $msg = '<div class="alert alert-danger alert-dismissible fade show">
                    Selected product does not exist.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';

    } elseif ($checkProduct['stock'] < $quantity) {

        $msg = '<div class="alert alert-warning alert-dismissible fade show">
                    Not enough stock available for this product.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';

    } else {

        $price = (float)$checkProduct['price'];
        $total_amount = $price * $quantity;

        // Insert sale record using existing borrowings table
        $conn->query("
            INSERT INTO borrowings (member_id, book_id, borrow_date, due_date, return_date)
            VALUES ($member_id, $book_id, '$sale_date', '$due_date', '$sale_date')
        ");

        // Deduct stock
        $conn->query("
            UPDATE books 
            SET stock = stock - $quantity
            WHERE book_id = $book_id
        ");

        // If stock becomes 0, mark as Sold / Unavailable
        $conn->query("
            UPDATE books
            SET status = 'Borrowed'
            WHERE book_id = $book_id
            AND stock <= 0
        ");

        // Add CRM loyalty points
        $conn->query("
            INSERT INTO points (member_id, points, action_type, action_date)
            VALUES ($member_id, 10, 'Product Purchase', '$sale_date')
        ");

        $msg = '<div class="alert alert-success alert-dismissible fade show">
                    Sale recorded successfully. Total Amount: ₱' . number_format($total_amount, 2) . '. Client earned +10 loyalty points.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
    }
}

// Dropdown data
$clients = $conn->query("
    SELECT member_id, name, course, year_level 
    FROM members 
    ORDER BY name ASC
");

$products = $conn->query("
    SELECT 
        books.book_id,
        books.title,
        books.price,
        books.stock,
        authors.author_name,
        categories.category_name

    FROM books, authors, categories

    WHERE books.author_id = authors.author_id
    AND books.category_id = categories.category_id
    AND books.stock > 0

    ORDER BY books.title ASC
");
?>

<h2 class="fw-bold mb-4">🛒 Sales Transaction</h2>

<?= $msg ?>

<div class="row">

    <div class="col-md-5">

        <div class="card shadow border-0 p-4">

            <h4 class="mb-3">Record New Sale</h4>

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Client</label>
                    <select name="member_id" class="form-select" required>
                        <option value="">Select Client</option>

                        <?php while ($c = $clients->fetch_assoc()): ?>
                            <option value="<?= $c['member_id'] ?>">
                                <?= htmlspecialchars($c['name']) ?>
                                — <?= htmlspecialchars($c['course']) ?>
                                <?= htmlspecialchars($c['year_level']) ?>
                            </option>
                        <?php endwhile; ?>

                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Product</label>
                    <select name="book_id" class="form-select" required>
                        <option value="">Select Product</option>

                        <?php while ($p = $products->fetch_assoc()): ?>
                            <option value="<?= $p['book_id'] ?>">
                                <?= htmlspecialchars($p['title']) ?>
                                — ₱<?= number_format($p['price'], 2) ?>
                                | Stock: <?= $p['stock'] ?>
                                | <?= htmlspecialchars($p['category_name']) ?>
                            </option>
                        <?php endwhile; ?>

                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Quantity</label>
                    <input type="number"
                           name="quantity"
                           class="form-control"
                           min="1"
                           value="1"
                           required>
                </div>

                <button type="submit" name="sale" class="btn btn-success w-100">
                    Record Sale
                </button>

            </form>

        </div>

    </div>


    <div class="col-md-7">

        <div class="card shadow border-0 p-4">

            <h4 class="mb-3">Recent Sales Transactions</h4>

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-dark">
                    <tr>
                        <th>Sale ID</th>
                        <th>Client</th>
                        <th>Product</th>
                        <th>Sale Date</th>
                        <th>Price</th>
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
                        borrowings.borrow_date

                    FROM borrowings, members, books

                    WHERE borrowings.member_id = members.member_id
                    AND borrowings.book_id = books.book_id

                    ORDER BY borrowings.borrow_id DESC
                    LIMIT 10
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
                    </tr>

                <?php
                    endwhile;
                else:
                ?>

                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            No sales transactions found.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php include('layout_end.php'); ?>