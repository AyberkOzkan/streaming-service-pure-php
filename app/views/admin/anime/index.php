    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-1">Anime</h5>

            <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
            <?php endif; ?>
            <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div>
            <?php endif; ?>

            <div class="d-flex align-items-center mb-3" style="gap:1.5rem;">
                <!-- Arama + MAL manage + New -->
                <form class="form-inline mr-2" method="GET" action="/admin/anime">
                    <input type="text" class="form-control mr-2" name="q" value="<?= htmlspecialchars($q ?? '') ?>" placeholder="Search title/synopsis">
                    <button class="btn btn-outline-secondary">Search</button>
                </form>

                <form class="form-inline ml-2" method="GET" action="/admin/anime/episodes/by-mal">
                    <input type="number" min="1" class="form-control mr-2" name="mal_id" placeholder="MAL id">
                    <button class="btn btn-outline-info">Manage by MAL id</button>
                </form>

                <a href="/admin/anime/new" class="btn btn-primary ml-auto">+ New Anime</a>
                <!-- Help toggle -->
                <button type="button"
                        id="helpToggle"
                        class="btn btn-sm btn-outline"
                        aria-expanded="false"
                        aria-controls="animeHelpBox">?
                </button>
            </div>

            <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>MAL</th>
                    <th>Title</th>
                    <th>Release</th>
                    <th>Total Ep</th>
                    <th>Created</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach (($rows ?? []) as $r): ?>
                <tr>
                    <td><?= (int)$r['id'] ?></td>
                    <td><?= htmlspecialchars($r['mal_id'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($r['title']) ?></td>
                    <td><?= htmlspecialchars($r['release_date'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($r['total_episodes'] ?? '—') ?></td>
                    <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($r['created_at']))) ?></td>
                    <td class="text-right">
                        <a href="/admin/anime/edit?id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <a href="/admin/anime/episodes?anime_id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-info">Episodes</a>
                        <form method="POST" action="/admin/anime/delete" class="d-inline" onsubmit="return confirm('Delete this anime?');">
                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($rows)): ?>
                    <tr><td colspan="7" class="text-muted">No records.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            </div>

            <?php if (($pages ?? 1) > 1): ?>
            <nav><ul class="pagination pagination-sm">
                <?php for ($i=1; $i<=$pages; $i++): ?>
                <li class="page-item <?= $i==($page ?? 1) ? 'active' : '' ?>">
                    <a class="page-link" href="/admin/anime?page=<?= $i ?>&q=<?= urlencode($q ?? '') ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
            </ul></nav>
            <?php endif; ?>
        </div>
    </div>
    <div id="animeHelpContainer" style="position:relative;">
        <!-- <button type="button"
            class="dismiss btn btn-sm btn-outline"
            onclick="dismissAnimeHelp()"
            aria-label="Dismiss"
            title="Close">&times;
        </button> -->
    </div>
    <div class="alert-help mb-gap" id="animeHelpBox" hidden>
        <div class="help-head">

            <div class="help-title">
                <!-- <span class="dot"></span> -->
                <span>How it works</span>
            </div>

        </div>
        <div class="help-body">
            <ol class="mb-2 mt-2">
                <li><em>Preferred:</em> Use <strong>Manage by MAL id</strong> to link a MAL title, then add episodes.</li>
                <li>Or click <strong>+ New Anime</strong> to create a local entry (you can set MAL id later in Edit).</li>
                <li>Open <strong>Episodes</strong> to add <code>ep_no</code>, <code>title</code>, and a <code>stream_url</code> (HLS/MP4/iframe).</li>
                <li>On public pages, if local episodes exist for a MAL id, your <code>stream_url</code> is used instead of Jikan trailers.</li>
            </ol>
            <small class="text-muted d-block" style="opacity:.85">
                Tip: Re-saving the same <code>ep_no</code> updates that episode (upsert).
            </small>
        </div>
    </div>

    <script>
        (function(){
            const box = document.getElementById('animeHelpBox');
            const container = document.getElementById('animeHelpContainer');
            const btn = document.getElementById('helpToggle');
            const key = 'hideAnimeHelp';
            const hidden = localStorage.getItem(key) === '1';
            if (!hidden) { box.hidden = false; btn?.setAttribute('aria-expanded','true'); }
            // Toggle
            btn?.addEventListener('click', () => {
                const isHidden = box.hasAttribute('hidden');
                if (isHidden) {
                    box.hidden = false;
                    container.hidden = false;
                    btn.setAttribute('aria-expanded','true');
                    localStorage.removeItem(key);
                } else {
                    box.hidden = true;
                    container.hidden = true;
                    btn.setAttribute('aria-expanded','false');
                    localStorage.setItem(key,'1');
                }
            });
        })();
        function dismissAnimeHelp(){
            const box = document.getElementById('animeHelpBox');
            const container = document.getElementById('animeHelpContainer');
            const btn = document.getElementById('helpToggle');
            box.hidden = true;
            container.hidden = true;
            btn?.setAttribute('aria-expanded','false');
            try { localStorage.setItem('hideAnimeHelp','1'); } catch(e){}
        }
        </script>

