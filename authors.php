<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('database.php');
include('layout.php');

$msg = "";

// ADD AUTHOR
if (isset($_POST['add'])) {
    $author_name = $conn->real_escape_string(trim($_POST['author_name']));

    if (!empty($author_name)) {
        $conn->query("INSERT INTO authors (author_name) VALUES ('$author_name')");
        $msg = '<div class="alert alert-success">Author added successfully.</div>';
    }
}

// UPDATE AUTHOR
if (isset($_POST['update'])) {
    $author_id = (int)$_POST['author_id'];
    $author_name = $conn->real_escape_string(trim($_POST['author_name']));

    if (!empty($author_name)) {
        $conn->query("
            UPDATE authors 
            SET author_name = '$author_name'
            WHERE author_id = $author_id
        ");

        $msg = '<div class="alert alert-success">Author updated successfully.</div>';
    }
}

// DELETE AUTHOR
if (isset($_GET['delete'])) {
    $author_id = (int)$_GET['delete'];

    $used = $conn->query("
        SELECT COUNT(*) AS total 
        FROM books 
        WHERE author_id = $author_id
    ")->fetch_assoc()['total'];

    if ($used > 0) {
        $msg = '<div class="alert alert-danger">Cannot delete this author because books are assigned to them.</div>';
    } else {
        $conn->query("DELETE FROM authors WHERE author_id = $author_id");
        $msg = '<div class="alert alert-warning">Author deleted successfully.</div>';
    }
}

// EDIT FETCH
$edit_author = null;
if (isset($_GET['edit'])) {
    $author_id = (int)$_GET['edit'];

    $edit_author = $conn->query("
        SELECT * 
        FROM authors 
        WHERE author_id = $author_id
    ")->fetch_assoc();
}

// SEARCH
$search = $_GET['search'] ?? '';
$searchEsc = $conn->real_escape_string($search);

$sql = "SELECT * FROM authors WHERE 1";

if (!empty($searchEsc)) {
    $sql .= " AND author_name LIKE '%$searchEsc%'";
}

$sql .= " ORDER BY author_id ASC";

$authors = $conn->query($sql);
?>

<h2 class="fw-bold mb-4">✍️ Authors</h2>

<?= $msg ?>

<div class="row">

    <div class="col-md-4">

        <div class="card shadow border-0 p-4 mb-4">

            <?php if ($edit_author): ?>

                <h4 class="mb-3">Edit Author</h4>

                <form method="POST">
                    <input type="hidden" name="author_id" value="<?= $edit_author['author_id'] ?>">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Author Name</label>
                        <input type="text" 
                               name="author_name" 
                               class="form-control"
                               value="<?= htmlspecialchars($edit_author['author_name']) ?>"
                               required>
                    </div>

                    <button name="update" class="btn btn-warning w-100 mb-2">
                        Update Author
                    </button>

                    <a href="authors.php" class="btn btn-secondary w-100">
                        Cancel
                    </a>
                </form>

            <?php else: ?>

                <h4 class="mb-3">Add Author</h4>

                <form method="POST">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Author Name</label>
                        <input type="text" 
                               name="author_name" 
                               class="form-control" 
                               required>
                    </div>

                    <button name="add" class="btn btn-primary w-100">
                        Add Author
                    </button>

                </form>

            <?php endif; ?>

        </div>

    </div>


    <div class="col-md-8">

        <div class="card shadow border-0 p-4">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <h4 class="mb-0">Author List</h4>

                <form method="GET" class="d-flex">
                    <input type="text"
                           name="search"
                           class="form-control me-2"
                           placeholder="Search author..."
                           value="<?= htmlspecialchars($search) ?>">

                    <button class="btn btn-success">
                        Search
                    </button>

                    <a href="authors.php" class="btn btn-secondary ms-2">
                        Reset
                    </a>
                </form>

            </div>

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-dark">
                    <tr>
                        <th width="100">ID</th>
                        <th>Author Name</th>
                        <th width="180">Actions</th>
                    </tr>
                </thead>

                <tbody>

                <?php if ($authors && $authors->num_rows > 0): ?>

                    <?php while ($row = $authors->fetch_assoc()): ?>

                        <tr>
                            <td><?= $row['author_id'] ?></td>
                            <td><?= htmlspecialchars($row['author_name']) ?></td>
                            <td>
                                <a href="authors.php?edit=<?= $row['author_id'] ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    Edit
                                </a>

                                <a href="authors.php?delete=<?= $row['author_id'] ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Delete this author?')">
                                    Delete
                                </a>
                            </td>
                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="3" class="text-center text-muted">
                            No authors found.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php include('layout_end.php'); ?>