</main>
<footer class="aips-footer mt-5 py-4">
  <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-2">
      <img src="/<?php echo get_logo_path(); ?>" alt="AIPS" style="height:32px"/>
      <div>
        <div class="fw-semibold">All In Packaging Solution</div>
        <small>Safety & Clean</small>
      </div>
    </div>
    <div>
      <small><?php echo htmlspecialchars($AIPS_SETTINGS['company_address']); ?> ? <?php echo htmlspecialchars($AIPS_SETTINGS['company_phone']); ?> ? <?php echo htmlspecialchars($AIPS_SETTINGS['company_email']); ?></small>
    </div>
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>