<div class="row">
  <div class="col-lg-8">
    <div class="card mb-4">
      <div class="card-body">
        <h5 class="card-title">Users</h5>

        <?php if (!empty($_SESSION['flash_error'])): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['flash_success'])): ?>
          <div class="alert alert-success"><?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div>
        <?php endif; ?>

        <form class="form-inline mb-3" method="GET" action="/admin/users">
          <input type="text" class="form-control mr-2" name="q" value="<?= htmlspecialchars($q ?? '') ?>" placeholder="Search name/email">
          <button class="btn btn-outline-secondary">Search</button>
        </form>

        <div class="table-responsive">
          <table class="table table-sm table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Ban reason</th>
                <th>Created</th>
                <th>Last login</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $u): ?>
              <tr>
                <td><?= (int)$u['id'] ?></td>
                <td><?= htmlspecialchars($u['name'] ?? '—') ?></td>
                <td><?= htmlspecialchars($u['email'] ?? '—') ?></td>
                <td>
                  <?php if (!empty($u['is_banned'])): ?>
                    <span class="badge badge-danger">banned</span>
                  <?php else: ?>
                    <span class="badge badge-success">active</span>
                  <?php endif; ?>
                </td>
                <td class="text-muted"><?= htmlspecialchars($u['banned_reason'] ?? '—') ?></td>
                <td><?= htmlspecialchars(isset($u['created_at']) ? date('Y-m-d H:i', strtotime($u['created_at'])) : '—') ?></td>
                <td><?= htmlspecialchars(isset($u['last_login_at']) ? date('Y-m-d H:i', strtotime($u['last_login_at'])) : '—') ?></td>
                <td class="text-right">
                  <?php if (!empty($u['is_banned'])): ?>
                    <form method="POST" action="/admin/users/unban" class="d-inline">
                      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                      <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                      <button class="btn btn-sm btn-outline-success" onclick="return confirm('Remove ban?')">Unban</button>
                    </form>
                  <?php else: ?>
                    <form method="POST" action="/admin/users/ban" class="d-inline" onsubmit="return confirmBan(event, <?= (int)$u['id'] ?>)">
                      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                      <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                      <input type="hidden" name="reason" value="">
                      <button class="btn btn-sm btn-outline-danger">Ban</button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($users)): ?>
              <tr><td colspan="8" class="text-muted">No records.</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>

        <?php if (($pages ?? 1) > 1): ?>
          <nav>
            <ul class="pagination pagination-sm">
              <?php for ($i=1; $i<=$pages; $i++): ?>
                <li class="page-item <?= $i==($page ?? 1) ? 'active' : '' ?>">
                  <a class="page-link" href="/admin/users?page=<?= $i ?>&q=<?= urlencode($q ?? '') ?>"><?= $i ?></a>
                </li>
              <?php endfor; ?>
            </ul>
          </nav>
        <?php endif; ?>

      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Notes</h5>
        <p class="text-muted mb-2">Only <strong>superadmin</strong> can access this page. Banned users cannot log in.</p>
      </div>
    </div>
  </div>
</div>

<script>
function confirmBan(e, id) {
  const reason = prompt('Ban reason (optional):', '');
  if (reason === null) { e.preventDefault(); return false; }
  e.target.querySelector('input[name="reason"]').value = (reason || '').trim();
  return confirm('Ban user #' + id + '?');
}
</script>
