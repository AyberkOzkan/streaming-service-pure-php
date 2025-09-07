    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Comments</h5>

                <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
                <?php endif; ?>
                <?php if (!empty($_SESSION['flash_success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div>
                <?php endif; ?>

                <form class="form-inline mb-3" method="GET" action="/admin/comments">
                <input type="text" class="form-control mr-2" name="q" value="<?= htmlspecialchars($q ?? '') ?>" placeholder="Search name/email/body">
                <button class="btn btn-outline-secondary">Search</button>
                </form>

                <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Anime</th>
                        <th style="min-width:320px;">Body</th>
                        <th>Created</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($comments as $c): ?>
                    <tr>
                        <td><?= (int)$c['id'] ?></td>
                        <td><?= htmlspecialchars($c['user_name'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($c['user_email'] ?? '—') ?></td>
                        <td>
                        <a href="/anime/<?= (int)$c['anime_id'] ?>" target="_blank">#<?= (int)$c['anime_id'] ?></a>
                        </td>
                        <td class="text-monospace" style="max-width:480px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        <?= htmlspecialchars($c['body'] ?? '') ?>
                        </td>
                        <td><?= htmlspecialchars(isset($c['created_at']) ? date('Y-m-d H:i', strtotime($c['created_at'])) : '—') ?></td>
                        <td class="text-right">
                        <form method="POST" action="/admin/comments/delete" class="d-inline" onsubmit="return confirm('Delete this comment?');">
                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                            <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($comments)): ?>
                    <tr><td colspan="7" class="text-muted">No records.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
                </div>

                <?php if (($pages ?? 1) > 1): ?>
                <nav>
                    <ul class="pagination pagination-sm">
                    <?php for ($i=1; $i<=$pages; $i++): ?>
                        <li class="page-item <?= $i==($page ?? 1) ? 'active' : '' ?>">
                        <a class="page-link" href="/admin/comments?page=<?= $i ?>&q=<?= urlencode($q ?? '') ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    </ul>
                </nav>
                <?php endif; ?>

            </div>
            </div>
        </div>
    </div>
