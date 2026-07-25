<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('database.php');
include('layout.php');

// AUTO-AWARD LOYALTY REWARDS BASED ON PURCHASE COUNT
$clients = $conn->query("
    SELECT member_id, COUNT(*) AS total_purchases
    FROM borrowings
    GROUP BY member_id
");

while ($c = $clients->fetch_assoc()) {
    $member_id = $c['member_id'];
    $total = $c['total_purchases'];

    if ($total >= 1) {
        $conn->query("
            INSERT IGNORE INTO member_badges (member_id, badge_id, date_awarded)
            VALUES ($member_id, 1, CURDATE())
        ");
    }

    if ($total >= 5) {
        $conn->query("
            INSERT IGNORE INTO member_badges (member_id, badge_id, date_awarded)
            VALUES ($member_id, 2, CURDATE())
        ");
    }

    if ($total >= 10) {
        $conn->query("
            INSERT IGNORE INTO member_badges (member_id, badge_id, date_awarded)
            VALUES ($member_id, 3, CURDATE())
        ");
    }
}

$result = $conn->query("
    SELECT
        members.name,
        members.course,
        members.year_level,
        badges.badge_name,
        badges.description,
        member_badges.date_awarded

    FROM member_badges, members, badges

    WHERE member_badges.member_id = members.member_id
    AND member_badges.badge_id = badges.badge_id

    ORDER BY member_badges.date_awarded DESC
");
?>

<h2 class="fw-bold mb-4">🎁 Loyalty Rewards</h2>

<div class="card shadow border-0 p-4">

    <h4 class="mb-3">Customer Rewards and Engagement</h4>

    <table class="table table-bordered table-hover align-middle">

        <thead class="table-dark">
            <tr>
                <th>Client</th>
                <th>Customer Group</th>
                <th>Client Level</th>
                <th>Reward</th>
                <th>Description</th>
                <th>Date Awarded</th>
            </tr>
        </thead>

        <tbody>

        <?php if ($result && $result->num_rows > 0): ?>

            <?php while ($row = $result->fetch_assoc()): ?>

                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['course']) ?></td>
                    <td><?= htmlspecialchars($row['year_level']) ?></td>

                    <td>
                        <span class="badge bg-warning text-dark">
                            🎁 <?= htmlspecialchars($row['badge_name']) ?>
                        </span>
                    </td>

                    <td><?= htmlspecialchars($row['description']) ?></td>

                    <td><?= $row['date_awarded'] ?></td>
                </tr>

            <?php endwhile; ?>

        <?php else: ?>

            <tr>
                <td colspan="6" class="text-center text-muted">
                    No loyalty rewards awarded yet.
                </td>
            </tr>

        <?php endif; ?>

        </tbody>

    </table>

</div>

<?php include('layout_end.php'); ?>