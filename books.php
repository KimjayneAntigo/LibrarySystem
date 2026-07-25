<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('database.php');
include('layout.php');

$msg = "";

// ADD SUPPLIER / AUTHOR
if (isset($_POST['add_supplier'])) {
    $author_name = $conn->real_escape_string(trim($_POST['author_name']));

    if (!empty($author_name)) {
        $conn->query("
            INSERT INTO authors (author_name)
            VALUES ('$author_name')
        ");

        $msg = '<div class="alert alert-success alert-dismissible fade show">
                    Supplier / Author added successfully.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
    }
}

// ADD PRODUCT CATEGORY
if (isset($_POST['add_category'])) {
    $category_name = $conn->real_escape_string(trim($_POST['category_name']));

    if (!empty($category_name)) {
        $conn->query("
            INSERT INTO categories (category_name)
            VALUES ('$category_name')
        ");

        $msg = '<div class="alert alert-success alert-dismissible fade show">
                    Product Category added successfully.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
    }
}

// ADD PRODUCT
if (isset($_POST['add'])) {

    $title = $conn->real_escape_string(trim($_POST['title']));
    $author_name = $conn->real_escape_string(trim($_POST['author_name']));
    $category_name = $conn->real_escape_string(trim($_POST['category_name']));
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];

    if ($title && $author_name && $category_name) {

        // CHECK IF AUTHOR EXISTS
        $authorCheck = $conn->query("
            SELECT author_id 
            FROM authors 
            WHERE author_name = '$author_name'
            LIMIT 1
        ");

        if ($authorCheck && $authorCheck->num_rows > 0) {

            $author = $authorCheck->fetch_assoc();
            $aid = $author['author_id'];

        } else {

            // AUTO ADD NEW AUTHOR
            $conn->query("
                INSERT INTO authors (author_name)
                VALUES ('$author_name')
            ");

            $aid = $conn->insert_id;
        }


        // CHECK IF CATEGORY EXISTS
        $categoryCheck = $conn->query("
            SELECT category_id
            FROM categories
            WHERE category_name = '$category_name'
            LIMIT 1
        ");

        if ($categoryCheck && $categoryCheck->num_rows > 0) {

            $category = $categoryCheck->fetch_assoc();
            $cid = $category['category_id'];

        } else {

            // AUTO ADD NEW CATEGORY
            $conn->query("
                INSERT INTO categories (category_name)
                VALUES ('$category_name')
            ");

            $cid = $conn->insert_id;
        }


        // INSERT PRODUCT
        $conn->query("
            INSERT INTO books
            (title, author_id, category_id, price, stock, status)

            VALUES
            ('$title', $aid, $cid, $price, $stock, 'Available')
        ");

        $msg = '<div class="alert alert-success alert-dismissible fade show">
                    Product added successfully with supplier and category.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
    }
}

// EDIT PRODUCT
// EDIT PRODUCT
if (isset($_POST['edit'])) {
    $id = (int)$_POST['book_id'];
    $title = $conn->real_escape_string(trim($_POST['title']));
    $aid = (int)$_POST['author_id'];
    $cid = (int)$_POST['category_id'];
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $status = $conn->real_escape_string($_POST['status']);

    $conn->query("
        UPDATE books
        SET title='$title',
            author_id=$aid,
            category_id=$cid,
            price=$price,
            stock=$stock,
            status='$status'
        WHERE book_id=$id
    ");

    $msg = '<div class="alert alert-success alert-dismissible fade show">
                Product updated successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
}

// DELETE PRODUCT
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM books WHERE book_id=$id");

    $msg = '<div class="alert alert-warning alert-dismissible fade show">
                Product deleted successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
}

// FILTER VALUES
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$status = $_GET['status'] ?? '';

$searchEsc = $conn->real_escape_string($search);
$categoryEsc = (int)$category;
$statusEsc = $conn->real_escape_string($status);

// EDIT FETCH
$edit_book = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_book = $conn->query("SELECT * FROM books WHERE book_id=$edit_id")->fetch_assoc();
}

// DROPDOWN DATA
$authors = $conn->query("SELECT * FROM authors ORDER BY author_name ASC");
$categories = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");

// INVENTORY QUERY
$sql = "
    SELECT 
        books.book_id,
        books.title,
        authors.author_name,
        categories.category_name,
        books.price,
        books.stock,
        books.status,
        books.author_id,
        books.category_id
    FROM books, authors, categories
    WHERE books.author_id = authors.author_id
    AND books.category_id = categories.category_id
";

if (!empty($searchEsc)) {
    $sql .= " AND books.title LIKE '%$searchEsc%'";
}

if (!empty($category)) {
    $sql .= " AND books.category_id = $categoryEsc";
}

if (!empty($statusEsc)) {
    $sql .= " AND books.status = '$statusEsc'";
}

$sql .= " ORDER BY books.book_id ASC";

$books = $conn->query($sql);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-box-seam me-2 text-primary"></i>Inventory Management
    </h4>

    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-plus-lg me-1"></i>Add Product
    </button>
</div>

<?= $msg ?>

<!-- SEARCH AND FILTER -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">

            <div class="col-md-4">
                <label class="form-label fw-semibold">Search Product</label>
                <input type="text" 
                       name="search" 
                       class="form-control"
                       placeholder="Search by product name"
                       value="<?= htmlspecialchars($search) ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">Product Category</label>
                <select name="category" class="form-select">
                    <option value="">All Categories</option>

                    <?php
                    $catFilter = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
                    while ($cat = $catFilter->fetch_assoc()):
                    ?>
                        <option value="<?= $cat['category_id'] ?>"
                            <?= ($category == $cat['category_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['category_name']) ?>
                        </option>
                    <?php endwhile; ?>

                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">Inventory Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="Available" <?= ($status == 'Available') ? 'selected' : '' ?>>In Stock</option>
                    <option value="Borrowed" <?= ($status == 'Borrowed') ? 'selected' : '' ?>>Sold / Unavailable</option>
                </select>
            </div>

            <div class="col-md-1 d-flex align-items-end">
                <button class="btn btn-success w-100">Filter</button>
            </div>

            <div class="col-md-1 d-flex align-items-end">
                <a href="books.php" class="btn btn-secondary w-100">Reset</a>
            </div>

        </form>
    </div>
</div>

<!-- INVENTORY TABLE -->
<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0 table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Product Name</th>
                    <th>Supplier / Author</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

            <?php if ($books && $books->num_rows > 0): ?>

                <?php while ($row = $books->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['book_id'] ?></td>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['author_name']) ?></td>
                        <td><?= htmlspecialchars($row['category_name']) ?></td>

                        <td>₱<?= number_format($row['price'], 2) ?></td>

                        <td>
                            <?php if ($row['stock'] <= 5): ?>
                                <span class="badge bg-danger">
                                    <?= $row['stock'] ?> Low
                                </span>
                            <?php else: ?>
                                <span class="badge bg-success">
                                    <?= $row['stock'] ?> Available
                                </span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if ($row['status'] == 'Available'): ?>
                                <span class="badge rounded-pill bg-success px-3 py-1">
                                    In Stock
                                </span>
                            <?php else: ?>
                                <span class="badge rounded-pill bg-warning text-dark px-3 py-1">
                                    Out of Stock
                                </span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <a href="?edit=<?= $row['book_id'] ?>" class="btn btn-outline-primary btn-sm py-0">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <a href="?delete=<?= $row['book_id'] ?>" 
                               class="btn btn-outline-danger btn-sm py-0"
                               onclick="return confirm('Delete this product?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        No products found.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>

<!-- ADD MODAL -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Product Name</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
<div class="mb-3">
    <label class="form-label fw-semibold">Supplier / Author</label>
    <input type="text"
           name="author_name"
           class="form-control"
           placeholder="Type supplier or author name"
           required>
</div>
<div class="mb-3">
    <label class="form-label fw-semibold">
        Product Category
    </label>

    <input type="text"
           name="category_name"
           class="form-control mb-2"
           placeholder="Type new product category"
           list="categoryList"
           required>

    <datalist id="categoryList">
        <?php 
        $categories->data_seek(0);

        while ($c = $categories->fetch_assoc()): 
        ?>
            <option value="<?= htmlspecialchars($c['category_name']) ?>">
        <?php endwhile; ?>
    </datalist>

    <small class="text-muted">
        You can type a new category or select an existing one.
    </small>
</div> 

                <div class="mb-3">
                    <label class="form-label fw-semibold">Product Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Stock Quantity</label>
                    <input type="number" name="stock" class="form-control" required>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>

                <button type="submit" name="add" class="btn btn-primary">
                    Add Product
                </button>
            </div>

        </form>
    </div>
</div>

<!-- EDIT FORM -->
<?php if ($edit_book): ?>
    <!-- <div class="card shadow border-0 p-4 mb-4">
    <h5 class="fw-bold mb-3">Inventory Setup</h5>

    <div class="d-flex flex-wrap gap-2">
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#authorModal">
            + Add Author
        </button>

        <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#categoryModal">
            + Add Product Category
        </button>

        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            + Add Product
        </button>
    </div>
</div> -->

<div class="card mt-4">
    <div class="card-header bg-warning text-dark fw-bold">
        Edit Product #<?= $edit_book['book_id'] ?>
    </div>

    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="book_id" value="<?= $edit_book['book_id'] ?>">

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Product Name</label>
                    <input type="text" 
                           name="title" 
                           class="form-control"
                           value="<?= htmlspecialchars($edit_book['title']) ?>" 
                           required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Author</label>
                    <select name="author_id" class="form-select">
                        <?php
                        $authorsEdit = $conn->query("SELECT * FROM authors ORDER BY author_name ASC");
                        while ($a = $authorsEdit->fetch_assoc()):
                        ?>
                            <option value="<?= $a['author_id'] ?>"
                                <?= $a['author_id'] == $edit_book['author_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($a['author_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Product Category</label>
                    <select name="category_id" class="form-select">
                        <?php
                        $categoriesEdit = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
                        while ($c = $categoriesEdit->fetch_assoc()):
                        ?>
                            <option value="<?= $c['category_id'] ?>"
                                <?= $c['category_id'] == $edit_book['category_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['category_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Product Price</label>
                    <input type="number" 
                           step="0.01" 
                           name="price" 
                           class="form-control"
                           value="<?= $edit_book['price'] ?>" 
                           required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Stock Quantity</label>
                    <input type="number" 
                           name="stock" 
                           class="form-control"
                           value="<?= $edit_book['stock'] ?>" 
                           required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Inventory Status</label>
                    <select name="status" class="form-select">
                        <option value="Available" <?= $edit_book['status'] == 'Available' ? 'selected' : '' ?>>
                            In Stock
                        </option>

                        <option value="Borrowed" <?= $edit_book['status'] == 'Borrowed' ? 'selected' : '' ?>>
                            Sold / Unavailable
                        </option>
                    </select>
                </div>

            </div>

            <div class="mt-3">
                <button type="submit" name="edit" class="btn btn-warning">
                    Save Changes
                </button>

                <a href="books.php" class="btn btn-secondary">
                    Cancel
                </a>
            </div>

        </form>
    </div>
</div>

<!-- ADD SUPPLIER MODAL -->
<div class="modal fade" id="supplierModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add Author</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <label class="form-label fw-semibold">Author Name</label>
                <input type="text" name="author_name" class="form-control" required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>

                <button type="submit" name="add_supplier" class="btn btn-primary">
                    Save Supplier
                </button>
            </div>

        </form>
    </div>
</div>


<!-- ADD CATEGORY MODAL -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add Product Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <label class="form-label fw-semibold">Category Name</label>
                <input type="text" name="category_name" class="form-control" required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>

                <button type="submit" name="add_category" class="btn btn-success">
                    Save Category
                </button>
            </div>

        </form>
    </div>
</div>
<?php endif; ?>

<?php include('layout_end.php'); ?>