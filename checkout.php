<?php
require_once __DIR__ . '/partials/header.php';
$conn = db_connect();

$items = isset($_SESSION['cart']) ? array_values($_SESSION['cart']) : [];
if (empty($items)) {
  echo '<div class="container py-5"><div class="alert alert-info">Your cart is empty. <a href="/shop.php">Shop now</a>.</div></div>';
  include __DIR__ . '/partials/footer.php';
  exit;
}

$subtotal = 0.0; foreach ($items as $it) { $subtotal += $it['price'] * $it['quantity']; }
$currency = 'USD';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    if (!$name || !$email) { $err = 'Name and email are required.'; }

    if (!$err) {
        // Create order
        $tx_ref = 'AIPS-' . time() . '-' . bin2hex(random_bytes(4));
        $n = safe($conn, $name); $e = safe($conn, $email); $p = safe($conn, $phone);
        $amount = $subtotal;
        $sql = "INSERT INTO orders(tx_ref, customer_name, customer_email, customer_phone, amount, currency, status) VALUES('$tx_ref', '$n', '$e', '$p', $amount, '$currency', 'pending')";
        if (!mysqli_query($conn, $sql)) {
            $err = 'Unable to create order.';
        } else {
            $order_id = mysqli_insert_id($conn);
            foreach ($items as $it) {
                $pid = (int)$it['id'];
                $pname = safe($conn, $it['name']);
                $price = (float)$it['price'];
                $qty = (int)$it['quantity'];
                $img = safe($conn, $it['image_url']);
                mysqli_query($conn, "INSERT INTO order_items(order_id, product_id, product_name, price, quantity, image_url) VALUES($order_id, $pid, '$pname', $price, $qty, '$img')");
            }

            // Create Flutterwave payment
            global $FLW_SECRET_KEY, $FLW_REDIRECT_URL;
            if (!$FLW_SECRET_KEY) {
                $err = 'Payment is not available right now. Please try again later.';
            } else {
                $redirect_url = $FLW_REDIRECT_URL ?: site_url('payment_callback.php');
                $payload = [
                    'tx_ref' => $tx_ref,
                    'amount' => round($amount, 2),
                    'currency' => $currency,
                    'redirect_url' => $redirect_url,
                    'customer' => [ 'email' => $email, 'name' => $name, 'phonenumber' => $phone ],
                    'customizations' => [ 'title' => 'AIPS Order', 'description' => 'Eco-friendly packaging', 'logo' => site_url(get_logo_path()) ]
                ];
                $ch = curl_init('https://api.flutterwave.com/v3/payments');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $FLW_SECRET_KEY,
                ]);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                $resp = curl_exec($ch);
                $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $errc = curl_error($ch);
                curl_close($ch);
                if ($http >= 200 && $http < 300 && $resp) {
                    $data = json_decode($resp, true);
                    if (isset($data['data']['link'])) {
                        header('Location: ' . $data['data']['link']);
                        exit;
                    }
                }
                $err = 'Unable to initiate payment. Please try again.';
            }
        }
    }
}
?>
<div class="container py-5" style="max-width: 900px">
  <h2 class="mb-4">Checkout</h2>
  <?php if ($err): ?><div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div><?php endif; ?>
  <div class="row g-4">
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Customer Details</h5>
          <form method="post" class="mt-3">
            <div class="mb-3">
              <label class="form-label">Full Name</label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Phone</label>
              <input type="tel" name="phone" class="form-control">
            </div>
            <button type="submit" class="btn aips-btn">Pay with Flutterwave</button>
          </form>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Order Summary</h5>
          <ul class="list-group list-group-flush mt-3">
            <?php foreach ($items as $it): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-semibold"><?php echo htmlspecialchars($it['name']); ?></div>
                  <small class="text-muted">Qty: <?php echo (int)$it['quantity']; ?></small>
                </div>
                <div class="fw-semibold"><?php echo money_format_aips($it['price'] * $it['quantity']); ?></div>
              </li>
            <?php endforeach; ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>Total</div>
              <div class="aips-price"><?php echo money_format_aips($subtotal); ?></div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>