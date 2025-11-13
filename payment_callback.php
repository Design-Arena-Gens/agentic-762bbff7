<?php
require_once __DIR__ . '/partials/header.php';
$conn = db_connect();

$status = $_GET['status'] ?? '';
$tx_ref = $_GET['tx_ref'] ?? '';
$transaction_id = $_GET['transaction_id'] ?? '';

$verified = false; $message = '';

if ($transaction_id) {
    global $FLW_SECRET_KEY;
    if ($FLW_SECRET_KEY) {
        $url = 'https://api.flutterwave.com/v3/transactions/' . urlencode($transaction_id) . '/verify';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $FLW_SECRET_KEY,
        ]);
        $resp = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($http >= 200 && $http < 300 && $resp) {
            $data = json_decode($resp, true);
            if (($data['status'] ?? '') === 'success' && ($data['data']['status'] ?? '') === 'successful') {
                $verified = true;
                $tx_ref = $data['data']['tx_ref'] ?? $tx_ref;
            }
        }
    }
}

if ($tx_ref) {
    $tx = safe($conn, $tx_ref);
    if ($verified) {
        mysqli_query($conn, "UPDATE orders SET status='paid' WHERE tx_ref='$tx'");
        // clear cart after success
        $_SESSION['cart'] = [];
        $message = 'Payment successful. Thank you for your order!';
    } else if ($status === 'successful') {
        // Fallback if verification not available
        mysqli_query($conn, "UPDATE orders SET status='paid' WHERE tx_ref='$tx'");
        $_SESSION['cart'] = [];
        $message = 'Payment processed. Thank you for your order!';
    } else if ($status === 'cancelled') {
        mysqli_query($conn, "UPDATE orders SET status='cancelled' WHERE tx_ref='$tx'");
        $message = 'Payment cancelled.';
    } else {
        $message = 'Payment verification failed or was not completed.';
    }
}
?>
<div class="container py-5" style="max-width: 700px">
  <h2 class="mb-3">Payment Status</h2>
  <div class="card p-4 shadow-sm">
    <p><?php echo htmlspecialchars($message); ?></p>
    <div class="mt-3 d-flex gap-2">
      <a href="/shop.php" class="btn btn-outline-secondary">Continue Shopping</a>
      <a href="/" class="btn aips-btn">Go Home</a>
    </div>
  </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>