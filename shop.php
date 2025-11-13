<?php
require_once __DIR__ . '/partials/header.php';
$conn = db_connect();
$products = [];
$sql = "SELECT id, name, description, price, image_url FROM products ORDER BY id DESC";
if ($res = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($res)) { $products[] = $row; }
    mysqli_free_result($res);
}
?>
<div class="container py-5">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <h2 class="mb-0">Shop</h2>
    <a href="/cart.php" class="btn btn-outline-success">View Cart</a>
  </div>
  <?php if (empty($products)): ?>
    <div class="alert alert-info">No products available yet. Please check back soon.</div>
  <?php else: ?>
    <div class="row g-4">
      <?php foreach ($products as $p): ?>
        <div class="col-md-4">
          <div class="card h-100 shadow-sm">
            <?php if (!empty($p['image_url'])): ?>
              <img src="<?php echo htmlspecialchars($p['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($p['name']); ?>" style="object-fit:cover;height:200px">
            <?php endif; ?>
            <div class="card-body d-flex flex-column">
              <h5 class="card-title mb-1"><?php echo htmlspecialchars($p['name']); ?></h5>
              <div class="aips-price mb-2"><?php echo money_format_aips($p['price']); ?></div>
              <p class="card-text flex-grow-1"><?php echo htmlspecialchars($p['description']); ?></p>
              <form method="post" action="/cart.php?action=add&id=<?php echo (int)$p['id']; ?>" class="d-flex gap-2">
                <input type="number" name="quantity" value="1" min="1" class="form-control" style="max-width:100px">
                <button type="submit" class="btn aips-btn">Add to Cart</button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>