    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center mb-2">
                <h5 class="card-title mb-0"><?= $item ? 'Edit Anime' : 'New Anime' ?></h5>

                <!-- Help toggle -->
                <button type="button"
                        id="formHelpToggle"
                        class="btn btn-sm btn-outline ml-auto"
                        aria-expanded="false"
                        aria-controls="formHelpBox">?
                </button>
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
    <div class="alert-help mb-gap" id="formHelpBox" hidden>
        <div class="help-head">
            <div class="help-title">
                <!-- <span class="dot"></span> -->
                <span>Tips</span>
            </div>
            <!-- <button type="button"
                    class="dismiss btn btn-sm btn-outline"
                    onclick="dismissFormHelp()"
                    aria-label="Close"
                    title="Close">&times;
            </button> -->
        </div>
        <div class="help-body">
            <ul class="mb-0">
                <li><strong>MAL id</strong> is optional. If set, this anime will be linked to the public Jikan pages.</li>
                <li><strong>Poster URL</strong> may be any image (JPG/PNG/WebP). <em>Example:</em> a direct image link or a CDN URL.</li>
                <li><strong>Trailer URL</strong> can be an embeddable URL (e.g. YouTube embed) used by the player if no local episodes.</li>
            </ul>
        </div>
    </div>

    <script>
        (function(){
            const box = document.getElementById('formHelpBox');
            const btn = document.getElementById('formHelpToggle');
            const key = 'hideAnimeFormHelp';
            const hidden = localStorage.getItem(key) === '1';
            if (!hidden) { box.hidden = false; btn?.setAttribute('aria-expanded','true'); }
            btn?.addEventListener('click', () => {
            const isHidden = box.hasAttribute('hidden');
            if (isHidden) {
                box.hidden = false;
                btn.setAttribute('aria-expanded','true');
                localStorage.removeItem(key);
            } else {
                box.hidden = true;
                btn.setAttribute('aria-expanded','false');
                localStorage.setItem(key,'1');
            }
            });
        })();
        function dismissFormHelp(){
            const box = document.getElementById('formHelpBox');
            const btn = document.getElementById('formHelpToggle');
            box.hidden = true;
            btn?.setAttribute('aria-expanded','false');
            try { localStorage.setItem('hideAnimeFormHelp','1'); } catch(e){}
        }
    </script>
