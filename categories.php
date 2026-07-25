<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('database.php');
include('layout.php');

$msg = "";

// ADD CATEGORY
if (isset($_POST['add'])) {
    $category_name = $conn->real_escape_string(trim($_POST['category_name']));

    if (!empty($category_name)) {
        $conn->query("INSERT INTO categories (category_name) VALUES ('$category_name')");
        $msg = '<div class="alert alert-success">Category added successfully.</div>';
    }
}

// UPDATE CATEGORY
if (isset($_POST['update'])) {
    $category_id = (int)$_POST['category_id'];
    $category_name = $conn->real_escape_string(trim($_POST['category_name']));

    if (!empty($category_name)) {
        $conn->query("
            UPDATE categories 
            SET category_name = '$category_name'
            WHERE category_id = $category_id
        ");

        $msg = '<div class="alert alert-success">Category updated successfully.</div>';
    }
}

// DELETE CATEGORY
if (isset($_GET['delete'])) {
    $category_id = (int)$_GET['delete'];

    $used = $conn->query("
        SELECT COUNT(*) AS total 
        FROM books 
        WHERE category_id = $category_id
    ")->fetch_assoc()['total'];

    if ($used > 0) {
        $msg = '<div class="alert alert-danger">Cannot delete this category because books are assigned to it.</div>';
    } else {
        $conn->query("DELETE FROM categories WHERE category_id = $category_id");
        $msg = '<div class="alert alert-warning">Category deleted successfully.</div>';
    }
}

// EDIT FETCH
$edit_category = null;
if (isset($_GET['edit'])) {
    $category_id = (int)$_GET['edit'];

    $edit_category = $conn->query("
        SELECT * 
        FROM categories 
        WHERE category_id = $category_id
    ")->fetch_assoc();
}

// SEARCH
$search = $_GET['search'] ?? '';
$searchEsc = $conn->real_escape_string($search);

$sql = "SELECT * FROM categories WHERE 1";

if (!empty($searchEsc)) {
    $sql .= " AND category_name LIKE '%$searchEsc%'";
}

$sql .= " ORDER BY category_id ASC";

$categories = $conn->query($sql);
?>

<h2 class="fw-bold mb-4">🏷️ Categories</h2>

<?= $msg ?>

<div class="row">

    <div class="col-md-4">

        <div class="card shadow border-0 p-4 mb-4">

            <?php if ($edit_category): ?>

                <h4 class="mb-3">Edit Category</h4>

                <form method="POST">
                    <input type="hidden" name="category_id" value="<?= $edit_category['category_id'] ?>">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category Name</label>
                        <input type="text" 
                               name="category_name" 
                               class="form-control"
                               value="<?= htmlspecialchars($edit_category['category_name']) ?>"
                               required>
                    </div>

                    <button name="update" class="btn btn-warning w-100 mb-2">
                        Update Category
                    </button>

                    <a href="categories.php" class="btn btn-secondary w-100">
                        Cancel
                    </a>
                </form>

            <?php else: ?>

                <h4 class="mb-3">Add Category</h4>

                <form method="POST">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category Name</label>
                        <input type="text" 
                               name="category_name" 
                               class="form-control" 
                               required>
                    </div>

                    <button name="add" class="btn btn-primary w-100">
                        Add Category
                    </button>

                </form>

            <?php endif; ?>

        </div>

    </div>


    <div class="col-md-8">

        <div class="card shadow border-0 p-4">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <h4 class="mb-0">Category List</h4>

                <form method="GET" class="d-flex">
                    <input type="text"
                           name="search"
                           class="form-control me-2"
                           placeholder="Search category..."
                           value="<?= htmlspecialchars($search) ?>">

                    <button class="btn btn-success">
                        Search
                    </button>

                    <a href="categories.php" class="btn btn-secondary ms-2">
                        Reset
                    </a>
                </form>

            </div>

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-dark">
                    <tr>
                        <th width="100">ID</th>
                        <th>Category Name</th>
                        <th width="180">Actions</th>
                    </tr>
                </thead>

                <tbody>

                <?php if ($categories && $categories->num_rows > 0): ?>

                    <?php while ($row = $categories->fetch_assoc()): ?>

                        <tr>
                            <td><?= $row['category_id'] ?></td>
                            <td><?= htmlspecialchars($row['category_name']) ?></td>
                            <td>
                                <a href="categories.php?edit=<?= $row['category_id'] ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    Edit
                                </a>

                                <a href="categories.php?delete=<?= $row['category_id'] ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Delete this category?')">
                                    Delete
                                </a>
                            </td>
                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="3" class="text-center text-muted">
                            No categories found.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php include('layout_end.php'); ?>