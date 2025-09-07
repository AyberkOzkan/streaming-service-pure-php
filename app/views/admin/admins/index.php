<div class="row">
  <div class="col-lg-8">
    <div class="card mb-4">
      <div class="card-body">
        <h5 class="card-title">Admin List</h5>

        <?php if (!empty($_SESSION['flash_error'])): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['flash_success'])): ?>
          <div class="alert alert-success"><?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div>
        <?php endif; ?>

        <div class="table-responsive">
          <table class="table table-sm table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>User</th>
                <th>E-mail</th>
                <th>Role</th>
                <th>Status</th>
                <th>Created</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($admins as $a): ?>
                <tr>
                  <td><?= (int)$a['id'] ?></td>
                  <td><?= htmlspecialchars($a['name'] ?? '—') ?></td>
                  <td><?= htmlspecialchars($a['email'] ?? '—') ?></td>
                  <td><span class="badge badge-info"><?= htmlspecialchars($a['role']) ?></span></td>
                  <td><span class="badge badge-<?= $a['status']==='active'?'success':'secondary' ?>"><?= htmlspecialchars($a['status']) ?></span></td>
                  <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($a['created_at']))) ?></td>
                  <td>
                    <form method="POST" action="/admin/admins/delete" onsubmit="return confirm('Are you sure?')">
                      <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                      <button class="btn btn-sm btn-outline-danger">Del</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($admins)): ?>
                <tr><td colspan="7" class="text-muted">No Record.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Add New Admin</h5>
        <form method="POST" action="/admin/admins/create">
          <div class="form-group">
            <label>E-mail</label>
            <input type="email" name="email" class="form-control" required placeholder="kullanici@site.com">
          </div>
          <div class="form-group">
            <label>Role</label>
            <select name="role" class="form-control">
              <option value="editor">editor</option>
              <option value="moderator">moderator</option>
              <option value="superadmin">superadmin</option>
            </select>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
              <option value="active">active</option>
              <option value="inactive">inactive</option>
            </select>
          </div>
          <button class="btn btn-primary">Add</button>
        </form>
      </div>
    </div>
  </div>
</div>
