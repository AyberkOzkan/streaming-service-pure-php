    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><?= $item ? 'Edit Anime' : 'New Anime' ?></h5>
            <div class="alert alert-secondary mb-3">
                <strong>Form tips</strong>
                <ul class="mb-0">
                    <li><strong>MAL id</strong> is optional. If set, this anime will be linked to the public Jikan pages.</li>
                    <li><strong>Poster URL</strong> may be any image (JPG/PNG/WebP). <em>Example:</em> a direct image link or a CDN URL.</li>
                    <li><strong>Trailer URL</strong> can be an embeddable URL (e.g. YouTube embed) used by the player if no local episodes.</li>
                </ul>
            </div>

            <form method="POST" action="<?= $item ? '/admin/anime/update' : '/admin/anime/create' ?>">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
            <?php if ($item): ?><input type="hidden" name="id" value="<?= (int)$item['id'] ?>"><?php endif; ?>

            <div class="form-row">
                <div class="form-group col-md-4">
                <label>MAL id (optional)</label>
                <input type="number" name="mal_id" class="form-control" value="<?= htmlspecialchars($item['mal_id'] ?? '') ?>">
                </div>
                <div class="form-group col-md-8">
                <label>Title</label>
                <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($item['title'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Synopsis</label>
                <textarea name="synopsis" class="form-control" rows="4"><?= htmlspecialchars($item['synopsis'] ?? '') ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                <label>Poster URL</label>
                <input type="url" name="poster_url" class="form-control" value="<?= htmlspecialchars($item['poster_url'] ?? '') ?>">
                </div>
                <div class="form-group col-md-6">
                <label>Trailer URL (embed)</label>
                <input type="url" name="trailer_url" class="form-control" value="<?= htmlspecialchars($item['trailer_url'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                <label>Release date</label>
                <input type="date" name="release_date" class="form-control" value="<?= htmlspecialchars($item['release_date'] ?? '') ?>">
                </div>
                <div class="form-group col-md-4">
                <label>Total episodes</label>
                <input type="number" name="total_episodes" class="form-control" value="<?= htmlspecialchars($item['total_episodes'] ?? '') ?>">
                </div>
            </div>

            <button class="btn btn-primary"><?= $item ? 'Update' : 'Create' ?></button>
            <a class="btn btn-light" href="/admin/anime">Cancel</a>
            </form>
        </div>
    </div>
