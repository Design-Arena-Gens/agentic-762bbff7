<?php
require_once __DIR__ . '/_auth.php';
require_admin();
include __DIR__ . '/../partials/header.php';
$conn = db_connect();
$products = [];
$sql = "SELECT id, name, price, image_url, category FROM products ORDER BY id DESC";
if ($res = mysqli_query($conn, $sql)) {
  while ($row = mysqli_fetch_assoc($res)) { $products[] = $row; }
  mysqli_free_result($res);
}
?>
<div class="container py-5">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <h2 class="mb-0">Products</h2>
    <div class="d-flex gap-2">
      <a href="/admin/product_form.php" class="btn aips-btn">Add Product</a>
      <a href="/admin/logout.php" class="btn btn-outline-light">Logout</a>
    </div>
  </div>
  <?php if (empty($products)): ?>
    <div class="alert alert-info">No products yet. Click "Add Product" to create one.</div>
  <?php else: ?>
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr><th>ID</th><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th></th></tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p): ?>
          <tr>
            <td><?php echo (int)$p['id']; ?></td>
            <td><?php if (!empty($p['image_url'])): ?><img src="<?php echo htmlspecialchars($p['image_url']); ?>" style="width:56px;height:56px;object-fit:cover"/><?php endif; ?></td>
            <td><?php echo htmlspecialchars($p['name']); ?></td>
            <td><?php echo htmlspecialchars($p['category']); ?></td>
            <td class="aips-price"><?php echo money_format_aips($p['price']); ?></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="/admin/product_form.php?id=<?php echo (int)$p['id']; ?>">Edit</a>
              <a class="btn btn-sm btn-outline-danger" href="/admin/delete_product.php?id=<?php echo (int)$p['id']; ?>" onclick="return confirm('Delete this product?');">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>