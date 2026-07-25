<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('database.php');
include('layout.php');

$msg = "";

// ADD CLIENT
if (isset($_POST['add'])) {
    $name = $conn->real_escape_string(trim($_POST['name']));
    $customer_type = $conn->real_escape_string(trim($_POST['customer_type']));
    $loyalty_level = (int)$_POST['loyalty_level'];

    if (!empty($name)) {
        $conn->query("
            INSERT INTO members (name, course, year_level)
            VALUES ('$name', '$customer_type', $loyalty_level)
        ");

        $msg = '<div class="alert alert-success alert-dismissible fade show">
                    Client added successfully.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
    }
}

// UPDATE CLIENT
if (isset($_POST['update'])) {
    $member_id = (int)$_POST['member_id'];
    $name = $conn->real_escape_string(trim($_POST['name']));
    $customer_type = $conn->real_escape_string(trim($_POST['customer_type']));
    $loyalty_level = (int)$_POST['loyalty_level'];

    $conn->query("
        UPDATE members
        SET name = '$name',
            course = '$customer_type',
            year_level = $loyalty_level
        WHERE member_id = $member_id
    ");

    $msg = '<div class="alert alert-success alert-dismissible fade show">
                Client updated successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
}

// DELETE CLIENT
if (isset($_GET['delete'])) {
    $member_id = (int)$_GET['delete'];

    $used = $conn->query("
        SELECT COUNT(*) AS total
        FROM borrowings
        WHERE member_id = $member_id
    ")->fetch_assoc()['total'];

    if ($used > 0) {
        $msg = '<div class="alert alert-danger alert-dismissible fade show">
                    Cannot delete this client because they already have sales records.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
    } else {
        $conn->query("DELETE FROM members WHERE member_id = $member_id");

        $msg = '<div class="alert alert-warning alert-dismissible fade show">
                    Client deleted successfully.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
    }
}

// EDIT FETCH
$edit_client = null;
if (isset($_GET['edit'])) {
    $member_id = (int)$_GET['edit'];

    $edit_client = $conn->query("
        SELECT *
        FROM members
        WHERE member_id = $member_id
    ")->fetch_assoc();
}

// SEARCH
$search = $_GET['search'] ?? '';
$searchEsc = $conn->real_escape_string($search);

$sql = "SELECT * FROM members WHERE 1";

if (!empty($searchEsc)) {
    $sql .= "
        AND (
            name LIKE '%$searchEsc%'
            OR course LIKE '%$searchEsc%'
        )
    ";
}

$sql .= " ORDER BY member_id ASC";

$clients = $conn->query($sql);
?>

<h2 class="fw-bold mb-4">👥 Client Management</h2>

<?= $msg ?>

<div class="row">

    <div class="col-md-4">

        <div class="card shadow border-0 p-4 mb-4">

            <?php if ($edit_client): ?>

                <h4 class="mb-3">Edit Client</h4>

                <form method="POST">

                    <input type="hidden" name="member_id" value="<?= $edit_client['member_id'] ?>">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Client Name</label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               value="<?= htmlspecialchars($edit_client['name']) ?>"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Customer Type</label>
                        <select name="customer_type" class="form-select" required>
                            <option value="Regular Customer" <?= $edit_client['course'] == 'Regular Customer' ? 'selected' : '' ?>>Regular Customer</option>
                            <option value="Student Buyer" <?= $edit_client['course'] == 'Student Buyer' ? 'selected' : '' ?>>Student Buyer</option>
                            <option value="Teacher / Faculty" <?= $edit_client['course'] == 'Teacher / Faculty' ? 'selected' : '' ?>>Teacher / Faculty</option>
                            <option value="Bulk Buyer" <?= $edit_client['course'] == 'Bulk Buyer' ? 'selected' : '' ?>>Bulk Buyer</option>
                            <option value="VIP Customer" <?= $edit_client['course'] == 'VIP Customer' ? 'selected' : '' ?>>VIP Customer</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Loyalty Level</label>
                        <select name="loyalty_level" class="form-select" required>
                            <option value="1" <?= $edit_client['year_level'] == 1 ? 'selected' : '' ?>>Level 1 - New Client</option>
                            <option value="2" <?= $edit_client['year_level'] == 2 ? 'selected' : '' ?>>Level 2 - Returning Client</option>
                            <option value="3" <?= $edit_client['year_level'] == 3 ? 'selected' : '' ?>>Level 3 - Loyal Client</option>
                            <option value="4" <?= $edit_client['year_level'] == 4 ? 'selected' : '' ?>>Level 4 - VIP Client</option>
                        </select>
                    </div>

                    <button name="update" class="btn btn-warning w-100 mb-2">
                        Update Client
                    </button>

                    <a href="members.php" class="btn btn-secondary w-100">
                        Cancel
                    </a>

                </form>

            <?php else: ?>

                <h4 class="mb-3">Add Client</h4>

                <form method="POST">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Client Name</label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               placeholder="Enter client name"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Customer Type</label>
                        <select name="customer_type" class="form-select" required>
                            <option value="">Select Customer Type</option>
                            <option value="Regular Customer">Regular Customer</option>
                            <option value="Student Buyer">Student Buyer</option>
                            <option value="Teacher / Faculty">Teacher / Faculty</option>
                            <option value="Bulk Buyer">Bulk Buyer</option>
                            <option value="VIP Customer">VIP Customer</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Loyalty Level</label>
                        <select name="loyalty_level" class="form-select" required>
                            <option value="">Select Loyalty Level</option>
                            <option value="1">Level 1 - New Client</option>
                            <option value="2">Level 2 - Returning Client</option>
                            <option value="3">Level 3 - Loyal Client</option>
                            <option value="4">Level 4 - VIP Client</option>
                        </select>
                    </div>

                    <button name="add" class="btn btn-primary w-100">
                        Add Client
                    </button>

                </form>

            <?php endif; ?>

        </div>

    </div>


    <div class="col-md-8">

        <div class="card shadow border-0 p-4">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <h4 class="mb-0">Client List</h4>

                <span class="badge bg-primary">
                    CRM Customer Records
                </span>

            </div>

            <form method="GET" class="d-flex mb-3">

                <input type="text"
                       name="search"
                       class="form-control me-2"
                       placeholder="Search client..."
                       value="<?= htmlspecialchars($search) ?>">

                <button class="btn btn-success">
                    Search
                </button>

                <a href="members.php" class="btn btn-secondary ms-2">
                    Reset
                </a>

            </form>

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Client Name</th>
                        <th>Customer Type</th>
                        <th>Loyalty Level</th>
                        <th>Profile / Purchase History</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                <?php if ($clients && $clients->num_rows > 0): ?>

                    <?php while ($row = $clients->fetch_assoc()): ?>

                    <tr>
                        <td><?= $row['member_id'] ?></td>

                        <td><?= htmlspecialchars($row['name']) ?></td>

                        <td><?= htmlspecialchars($row['course']) ?></td>

                        <td>
                            Level <?= htmlspecialchars($row['year_level']) ?>
                        </td>

                        <td>
                            <a href="member_profile.php?id=<?= $row['member_id'] ?>" 
                               class="btn btn-sm btn-primary">
                                View Client Profile
                            </a>
                        </td>

                        <td>
                            <a href="members.php?edit=<?= $row['member_id'] ?>"
                               class="btn btn-sm btn-outline-primary">
                                Edit
                            </a>

                            <a href="members.php?delete=<?= $row['member_id'] ?>"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Delete this client?')">
                                Delete
                            </a>
                        </td>
                    </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            No client records found.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php include('layout_end.php'); ?>