<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('database.php');
include('layout.php');

$leaders = $conn->query("
    SELECT 
        members.member_id,
        members.name,
        members.course,
        members.year_level,
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
        members.name,
        members.course,
        members.year_level

    ORDER BY total_purchases DESC, total_spent DESC
");
?>

<h2 class="fw-bold mb-4">🏆 Top Clients</h2>

<div class="card shadow border-0 p-4">

    <h4 class="mb-3">Customer Loyalty and Purchase Ranking</h4>

    <table class="table table-bordered table-hover align-middle">

        <thead class="table-dark">
            <tr>
                <th>Rank</th>
                <th>Client</th>
                <th>Customer Group</th>
                <th>Client Level</th>
                <th>Total Purchases</th>
                <th>Total Spent</th>
                <th>Loyalty Points</th>
                <th>Profile</th>
            </tr>
        </thead>

        <tbody>

        <?php
        $rank = 1;

        if ($leaders && $leaders->num_rows > 0):

            while ($row = $leaders->fetch_assoc()):
        ?>

        <tr>
            <td>
                <?php if ($rank == 1): ?>
                    🥇
                <?php elseif ($rank == 2): ?>
                    🥈
                <?php elseif ($rank == 3): ?>
                    🥉
                <?php else: ?>
                    <?= $rank ?>
                <?php endif; ?>
            </td>

            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['course']) ?></td>
            <td><?= htmlspecialchars($row['year_level']) ?></td>

            <td>
                <span class="badge bg-success">
                    <?= $row['total_purchases'] ?> purchases
                </span>
            </td>

            <td>
                ₱<?= number_format($row['total_spent'], 2) ?>
            </td>

            <td>
                <span class="badge bg-primary">
                    <?= $row['loyalty_points'] ?> pts
                </span>
            </td>

            <td>
                <a href="member_profile.php?id=<?= $row['member_id'] ?>" 
                   class="btn btn-sm btn-outline-primary">
                    View Client
                </a>
            </td>
        </tr>

        <?php
            $rank++;
            endwhile;

        else:
        ?>

        <tr>
            <td colspan="8" class="text-center text-muted">
                No client records found.
            </td>
        </tr>

        <?php endif; ?>

        </tbody>

    </table>

</div>

<?php include('layout_end.php'); ?>