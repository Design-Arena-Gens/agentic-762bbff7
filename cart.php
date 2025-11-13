<?php
require_once __DIR__ . '/partials/header.php';
$conn = db_connect();

if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }

function load_product($conn, $id) {
    $id = (int)$id;
    $sql = "SELECT id, name, description, price, image_url FROM products WHERE id=$id";
    if ($res = mysqli_query($conn, $sql)) {
        $row = mysqli_fetch_assoc($res);
        mysqli_free_result($res);
        return $row;
    }
    return null;
}

$action = $_GET['action'] ?? '';
if ($action === 'add' && isset($_GET['id'])) {
    $pid = (int)$_GET['id'];
    $qty = max(1, (int)($_POST['quantity'] ?? 1));
    $prod = load_product($conn, $pid);
    if ($prod) {
        if (!isset($_SESSION['cart'][$pid])) {
            $_SESSION['cart'][$pid] = [
                'id' => $pid,
                'name' => $prod['name'],
                'price' => (float)$prod['price'],
                'image_url' => $prod['image_url'],
                'quantity' => 0
            ];
        }
        $_SESSION['cart'][$pid]['quantity'] += $qty;
    }
    header('Location: /cart.php');
    exit;
}

if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['qty'] as $pid => $q) {
        $pid = (int)$pid; $q = (int)$q;
        if ($q <= 0) { unset($_SESSION['cart'][$pid]); }
        else { if (isset($_SESSION['cart'][$pid])) { $_SESSION['cart'][$pid]['quantity'] = $q; } }
    }
    header('Location: /cart.php');
    exit;
}

if ($action === 'remove' && isset($_GET['id'])) {
    $pid = (int)$_GET['id'];
    unset($_SESSION['cart'][$pid]);
    header('Location: /cart.php');
    exit;
}

$items = array_values($_SESSION['cart']);
$subtotal = 0.0;
foreach ($items as $it) { $subtotal += $it['price'] * $it['quantity']; }
?>
<div class="container py-5">
  <h2 class="mb-4">Your Cart</h2>
  <?php if (empty($items)): ?>
    <div class="alert alert-info">Your cart is empty. <a href="/shop.php">Continue shopping</a>.</div>
  <?php else: ?>
  <form method="post" action="/cart.php?action=update">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr><th>Product</th><th>Price</th><th>Qty</th><th>Total</th><th></th></tr>
        </thead>
        <tbody>
          <?php foreach ($items as $it): $line = $it['price'] * $it['quantity']; ?>
            <tr>
              <td>
                <div class="d-flex align-items-center gap-3">
                  <?php if (!empty($it['image_url'])): ?><img src="<?php echo htmlspecialchars($it['image_url']); ?>" style="width:64px;height:64px;object-fit:cover"/><?php endif; ?>
                  <div class="fw-semibold"><?php echo htmlspecialchars($it['name']); ?></div>
                </div>
              </td>
              <td class="aips-price"><?php echo money_format_aips($it['price']); ?></td>
              <td style="max-width:120px"><input type="number" class="form-control" name="qty[<?php echo (int)$it['id']; ?>]" value="<?php echo (int)$it['quantity']; ?>" min="0"></td>
              <td class="fw-semibold"><?php echo money_format_aips($line); ?></td>
              <td><a class="btn btn-sm btn-outline-danger" href="/cart.php?action=remove&id=<?php echo (int)$it['id']; ?>">Remove</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="d-flex align-items-center justify-content-between mt-3">
      <a href="/shop.php" class="btn btn-outline-secondary">Continue Shopping</a>
      <div class="d-flex align-items-center gap-3">
        <div class="fs-5">Subtotal: <span class="aips-price"><?php echo money_format_aips($subtotal); ?></span></div>
        <button type="submit" class="btn btn-outline-primary">Update Cart</button>
        <a href="/checkout.php" class="btn aips-btn">Checkout</a>
      </div>
    </div>
  </form>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>