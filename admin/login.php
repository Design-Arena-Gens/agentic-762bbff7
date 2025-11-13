<?php
require_once __DIR__ . '/_auth.php';

if (is_admin()) { header('Location: /admin/'); exit; }

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $expected = aips_env('ADMIN_PASSWORD', '');
    if ($expected && hash_equals($expected, $password)) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: /admin/');
        exit;
    } else {
        $err = 'Invalid password.';
    }
}
include __DIR__ . '/../partials/header.php';
?>
<div class="container py-5" style="max-width: 480px">
  <h2 class="mb-3">Admin Login</h2>
  <?php if ($err): ?><div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div><?php endif; ?>
  <form method="post" class="card p-4 shadow-sm">
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn aips-btn w-100">Sign In</button>
  </form>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>