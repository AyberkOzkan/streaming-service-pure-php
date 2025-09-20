    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Episodes for: <?= htmlspecialchars($item['title'] ?? '') ?> (#<?= (int)$item['id'] ?>)</h5>
            <div class="alert alert-light border mb-3">
                <strong>Adding episodes</strong>
                <ul class="mb-2">
                    <li><strong>Ep #</strong>: integer starting from 1 (unique per anime).</li>
                    <li><strong>Title</strong>: short episode title. Example: <em>Episode 1 — The Beginning</em></li>
                    <li><strong>Stream URL</strong>: HLS (<code>.m3u8</code>), MP4, or an embeddable iframe URL. This will be played on <code>/anime/{MAL_ID}/watch</code>.</li>
                    <li><strong>Duration (sec)</strong> is optional, for display/metrics only.</li>
                </ul>
                <small class="text-muted">
                    Notes: Saving the same <code>ep_no</code> updates the record. Deleting an episode does not affect others.
                </small>
            </div>

            <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
            <?php endif; ?>
            <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div>
            <?php endif; ?>

            <form class="form-inline mb-3" method="POST" action="/admin/anime/episodes/create">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="anime_id" value="<?= (int)$item['id'] ?>">
            <input type="number" class="form-control mr-2" name="ep_no" placeholder="Ep #" required>
            <input type="text" class="form-control mr-2" name="title" placeholder="Title" required>
            <input type="url" class="form-control mr-2" name="stream_url" placeholder="Stream URL">
            <input type="number" class="form-control mr-2" name="duration_seconds" placeholder="Duration (sec)">
            <button class="btn btn-primary">Save episode</button>
            </form>

            <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead><tr><th>#</th><th>Title</th><th>Stream</th><th>Duration</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach (($episodes ?? []) as $e): ?>
                <tr>
                    <td><?= (int)$e['ep_no'] ?></td>
                    <td><?= htmlspecialchars($e['title']) ?></td>
                    <td class="text-truncate" style="max-width:300px;"><?= htmlspecialchars($e['stream_url'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($e['duration_seconds'] ?? '—') ?></td>
                    <td class="text-right">
                    <form method="POST" action="/admin/anime/episodes/delete" class="d-inline" onsubmit="return confirm('Delete this episode?');">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                        <input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
                        <input type="hidden" name="anime_id" value="<?= (int)$item['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($episodes)): ?>
                    <tr><td colspan="5" class="text-muted">No episodes yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            </div>

            <a class="btn btn-light" href="/admin/anime">Back</a>
        </div>
    </div>
