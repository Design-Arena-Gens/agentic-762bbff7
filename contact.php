<?php
require_once __DIR__ . '/partials/header.php';
$conn = db_connect();

$success = false; $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($name && $email && $message) {
        $n = safe($conn, $name);
        $e = safe($conn, $email);
        $m = safe($conn, $message);
        $sql = "INSERT INTO messages(name,email,message) VALUES('$n','$e','$m')";
        if (mysqli_query($conn, $sql)) { $success = true; }
        else { $error = 'Unable to submit your message at this time.'; }
    } else { $error = 'All fields are required.'; }
}
?>
<div class="container py-5" style="max-width: 800px">
  <h2 class="mb-3">Contact Us</h2>
  <p class="text-muted mb-4">We typically reply within 1-2 business days.</p>
  <?php if ($success): ?>
    <div class="alert alert-success">Thank you! Your message has been received.</div>
  <?php elseif ($error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>
  <form method="post" class="card p-4 shadow-sm">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="col-12">
        <label class="form-label">Message</label>
        <textarea name="message" rows="5" class="form-control" required></textarea>
      </div>
    </div>
    <div class="d-flex align-items-center justify-content-between mt-4">
      <div>
        <div><strong>Email:</strong> <?php echo htmlspecialchars($AIPS_SETTINGS['company_email']); ?></div>
        <div><strong>Phone:</strong> <?php echo htmlspecialchars($AIPS_SETTINGS['company_phone']); ?></div>
        <div><strong>Address:</strong> <?php echo htmlspecialchars($AIPS_SETTINGS['company_address']); ?></div>
      </div>
      <button type="submit" class="btn aips-btn">Send Message</button>
    </div>
  </form>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>