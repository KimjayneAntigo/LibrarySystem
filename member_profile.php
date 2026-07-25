<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('database.php');
include('layout.php');

if (!isset($_GET['id'])) {
    die("Member ID not found.");
}

$member_id = $_GET['id'];


// ================= MEMBER INFO =================

$member = $conn->query("
    SELECT *
    FROM members
    WHERE member_id = $member_id
")->fetch_assoc();


// ================= TOTAL POINTS =================

$points = $conn->query("
    SELECT IFNULL(SUM(points),0) AS total
    FROM points
    WHERE member_id = $member_id
")->fetch_assoc()['total'];

?>

<div class="container-fluid">

<h2 class="fw-bold mb-4">
👤 Member Profile
</h2>


<!-- MEMBER INFO -->

<div class="card shadow border-0 p-4 mb-4">

<h4>
<?= $member['name'] ?>
</h4>

<p class="mb-1">
<strong>Course:</strong>
<?= $member['course'] ?>
</p>

<p class="mb-1">
<strong>Year Level:</strong>
<?= $member['year_level'] ?>
</p>

<p class="mb-0">
<strong>Total Points:</strong>
<span class="badge bg-primary">
<?= $points ?>
</span>
</p>

</div>



<!-- BORROW HISTORY -->

<div class="card shadow border-0 p-4 mb-4">

<h4 class="mb-3">
📚 Borrow History
</h4>

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>
<th>Book</th>
<th>Borrow Date</th>
<th>Due Date</th>
<th>Return Date</th>
</tr>

</thead>

<tbody>

<?php

$history = $conn->query("
    SELECT
        books.title,
        borrowings.borrow_date,
        borrowings.due_date,
        borrowings.return_date

    FROM borrowings

    JOIN books
        ON borrowings.book_id = books.book_id

    WHERE borrowings.member_id = $member_id

    ORDER BY borrowings.borrow_date ASC
");

if ($history->num_rows > 0):

while($row = $history->fetch_assoc()):
?>

<tr>

<td><?= $row['title'] ?></td>

<td><?= $row['borrow_date'] ?></td>

<td><?= $row['due_date'] ?></td>

<td>
<?php
if ($row['return_date']) {
    echo $row['return_date'];
} else {
    echo '<span class="badge bg-warning text-dark">Not Returned</span>';
}
?>
</td>

</tr>

<?php
endwhile;

else:
?>

<tr>
<td colspan="4" class="text-center text-muted">
No borrowing history found.
</td>
</tr>

<?php endif; ?>

</tbody>

</table>

</div>



<!-- BADGES -->

<div class="card shadow border-0 p-4 mb-4">

<h4 class="mb-3">
🏆 Badges Earned
</h4>

<div class="row">

<?php

$badges = $conn->query("
    SELECT badges.badge_name

    FROM member_badges

    JOIN badges
        ON member_badges.badge_id = badges.badge_id

    WHERE member_badges.member_id = $member_id
");

if ($badges->num_rows > 0):

while($badge = $badges->fetch_assoc()):
?>

<div class="col-md-3 mb-3">

<div class="card border-success shadow-sm p-3 text-center">

<h5>
🎖
</h5>

<p class="fw-bold mb-0">
<?= $badge['badge_name'] ?>
</p>

</div>

</div>

<?php
endwhile;

else:
?>

<p class="text-muted">
No badges earned yet.
</p>

<?php endif; ?>

</div>

</div>



<!-- FINES -->

<div class="card shadow border-0 p-4">

<h4 class="mb-3">
💰 Fine Records
</h4>

<table class="table table-bordered">

<thead class="table-dark">

<tr>
<th>Book</th>
<th>Amount</th>
<th>Status</th>
</tr>

</thead>

<tbody>

<?php

$fines = $conn->query("
    SELECT
        books.title,
        fines.amount,
        fines.status

    FROM fines

    JOIN borrowings
        ON fines.borrow_id = borrowings.borrow_id

    JOIN books
        ON borrowings.book_id = books.book_id

    WHERE borrowings.member_id = $member_id
");

if ($fines->num_rows > 0):

while($fine = $fines->fetch_assoc()):
?>

<tr>

<td><?= $fine['title'] ?></td>

<td>
₱<?= number_format($fine['amount'], 2) ?>
</td>

<td>

<?php if ($fine['status'] == 'Paid'): ?>

<span class="badge bg-success">
Paid
</span>

<?php else: ?>

<span class="badge bg-danger">
Unpaid
</span>

<?php endif; ?>

</td>

</tr>

<?php
endwhile;

else:
?>

<tr>
<td colspan="3" class="text-center text-muted">
No fines found.
</td>
</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

<?php include('layout_end.php'); ?>