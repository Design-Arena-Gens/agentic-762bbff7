<?php
require_once __DIR__ . '/_auth.php';
require_admin();
$conn = db_connect();
include __DIR__ . '/../partials/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editing = $id > 0;
$name = $description = $image_url = $category = '';
$price = 0.00;

if ($editing) {
    $sql = "SELECT * FROM products WHERE id=$id";
    if ($res = mysqli_query($conn, $sql)) {
        if ($row = mysqli_fetch_assoc($res)) {
            $name = $row['name'];
            $description = $row['description'];
            $price = (float)$row['price'];
            $image_url = $row['image_url'];
            $category = $row['category'];
        }
        mysqli_free_result($res);
    }
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $image_url = trim($_POST['image_url'] ?? '');
    $category = trim($_POST['category'] ?? '');

    if (!$name) { $err = 'Name is required.'; }
    else {
        $n = safe($conn, $name);
        $d = safe($conn, $description);
        $p = $price;
        $u = safe($conn, $image_url);
        $c = safe($conn, $category);
        if ($editing) {
            $sql = "UPDATE products SET name='$n', description='$d', price=$p, image_url='$u', category='$c' WHERE id=$id";
        } else {
            $sql = "INSERT INTO products(name, description, price, image_url, category) VALUES('$n', '$d', $p, '$u', '$c')";
        }
        if (mysqli_query($conn, $sql)) {
            header('Location: /admin/products.php');
            exit;
        } else {
            $err = 'Unable to save product.';
        }
    }
}
?>
<div class="container py-5" style="max-width: 720px">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <h2 class="mb-0"><?php echo $editing ? 'Edit' : 'Add'; ?> Product</h2>
    <a href="/admin/products.php" class="btn btn-outline-secondary">Back</a>
  </div>

  <?php if ($err): ?><div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div><?php endif; ?>

  <form method="post" class="card p-4 shadow-sm">
    <div class="row g-3">
      <div class="col-md-8">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Price (USD)</label>
        <input type="number" step="0.01" name="price" class="form-control" value="<?php echo htmlspecialchars($price); ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Category</label>
        <input type="text" name="category" class="form-control" placeholder="cups, boxes, containers" value="<?php echo htmlspecialchars($category); ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Image URL</label>
        <input type="url" name="image_url" class="form-control" value="<?php echo htmlspecialchars($image_url); ?>" placeholder="https://...">
      </div>
      <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" rows="5" class="form-control"><?php echo htmlspecialchars($description); ?></textarea>
      </div>
    </div>
    <div class="mt-4 d-flex justify-content-end gap-2">
      <a href="/admin/products.php" class="btn btn-outline-secondary">Cancel</a>
      <button type="submit" class="btn aips-btn">Save</button>
    </div>
  </form>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>