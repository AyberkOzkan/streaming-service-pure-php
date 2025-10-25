<?php require_once '../app/views/layouts/header.php'; ?>
<?php
    // --- URL & poster ---
    $detailsUrl   = $detailsUrl   ?? ('/anime/' . ($animeDetails['mal_id'] ?? ''));
    $watchBaseUrl = $watchBaseUrl ?? ($detailsUrl . '/watch');

    // poster: Jikan -> jpg.large_image_url, yoksa local poster_url
    $poster = $animeDetails['images']['jpg']['large_image_url']
                ?? ($animeDetails['poster_url'] ?? '/img/placeholder-vertical.jpg');

    $hasMal   = !empty($animeDetails['mal_id']);
    $hasLocal = !empty($animeDetails['id']); // LocalAnimeController 'id' göndermeli
    $followSource = $hasMal ? 'mal' : ($hasLocal ? 'local' : null);
    $followId     = $hasMal ? (int)$animeDetails['mal_id'] : ($hasLocal ? (int)$animeDetails['id'] : 0);

    // --- yardımcılar (view içi) ---
    function fmt_date_display(?string $raw): string {
        if (!$raw) return 'N/A';
        $ts = strtotime($raw);
        return $ts ? date('M d, Y', $ts) : htmlspecialchars($raw);
    }
    function fmt_duration($val): string {
        if ($val === null || $val === '') return 'N/A';
        // Jikan metin döndürebilir (örn. "24 min per ep"); local int (dakika) verebilir
        if (is_numeric($val)) {
            $m = (int)$val;
            $h = intdiv($m, 60);
            $r = $m % 60;
            if ($h && $r) return "{$h} hr {$r} min";
            if ($h) return "{$h} hr";
            return "{$m} min";
        }
        return htmlspecialchars((string)$val);
    }
    function first_studio($studios, $fallback = null): ?string {
        // studios: [[name=>...]] | "MAPPA" | null
        if (is_array($studios)) {
            if (isset($studios[0]['name'])) return $studios[0]['name'];
            if ($studios && is_string($studios[0])) return $studios[0];
        } elseif (is_string($studios)) {
            $parts = array_filter(array_map('trim', explode(',', $studios)));
            return $parts[0] ?? $fallback;
        }
        return $fallback;
    }
    function join_genres($genres): string {
        // genres: [{name:'Action'}] | ["Action","Drama"] | "Action, Drama"
        if (is_array($genres)) {
            $names = array_map(function($g){
                if (is_array($g)) return $g['name'] ?? '';
                return (string)$g;
            }, $genres);
            $names = array_filter(array_map('trim', $names));
            return $names ? htmlspecialchars(implode(', ', $names)) : 'N/A';
        }
        if (is_string($genres)) {
            $names = array_filter(array_map('trim', explode(',', $genres)));
            return $names ? htmlspecialchars(implode(', ', $names)) : 'N/A';
        }
        return 'N/A';
    }

    $typeText     = $animeDetails['type']   ?? 'N/A';
    $statusText   = $animeDetails['status'] ?? 'N/A';
    $studioText   = first_studio($animeDetails['studios'] ?? null, $animeDetails['studio'] ?? null) ?? 'N/A';
    $genreText    = join_genres($animeDetails['genres'] ?? []);
    $dateText     = fmt_date_display($animeDetails['aired']['string'] ?? ($animeDetails['release_date'] ?? null));
    $durationText = fmt_duration($animeDetails['duration'] ?? ($animeDetails['duration_minutes'] ?? null));

    // Jikan'da members sayı; local'de boş -> '—'
    $membersText = isset($animeDetails['members']) ? number_format((int)$animeDetails['members']) : '—';
?>



    <!-- Breadcrumb Begin -->
    <div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__links">
                        <a href="/"><i class="fa fa-home"></i> Home</a>
                        <a href="<?= htmlspecialchars($detailsUrl) ?>">Details</a>
                        <span><?= htmlspecialchars($animeDetails['title']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->

    <!-- Anime Section Begin -->
    <section class="anime-details spad">
        <div class="container">
            <div class="anime__details__content">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="anime__details__pic set-bg" data-setbg="<?= htmlspecialchars($poster) ?>">
                            <div class="comment"><i class="fa fa-comments"></i> 11</div>
                            <div class="view"><i class="fa fa-eye"></i> 
                                <?= isset($animeDetails['members']) ? number_format($animeDetails['members']) : 'Local' ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="anime__details__text">
                            <div class="anime__details__title">
                                 <h3><?= htmlspecialchars($animeDetails['title']) ?></h3>
                            </div>
                            <p><?= htmlspecialchars($animeDetails['synopsis'] ?? '') ?></p>
                            <div class="anime__details__widget">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6">
                                        <ul>
                                            <li><span>Type:</span> <?= htmlspecialchars($typeText) ?></li>
                                            <li><span>Studios:</span> <?= htmlspecialchars($studioText) ?></li>
                                            <li><span>Date aired:</span> <?= htmlspecialchars($dateText) ?></li>
                                            <li><span>Status:</span> <?= htmlspecialchars($statusText) ?></li>
                                        </ul>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <ul>
                                            <li><span>Genre:</span> <?= $genreText ?></li>
                                            <li><span>Duration:</span> <?= htmlspecialchars($durationText) ?></li>
                                            <li><span>Views:</span> <?= htmlspecialchars($membersText) ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="anime__details__btn">
                                <?php if (isset($_SESSION['user_id']) && $followSource && $followId): ?>
                                    <form method="POST"
                                        action="<?= $followSource==='mal' ? '/follow/'.($isFollowing?'remove':'add') : '/follow/local/'.($isFollowing?'remove':'add') ?>"
                                        style="display:inline;">
                                    <input type="hidden" name="anime_id" value="<?= $followId ?>">
                                    <input type="hidden" name="anime_title" value="<?= htmlspecialchars($animeDetails['title']) ?>">
                                    <button type="submit" class="follow-btn">
                                        <i class="fa <?= $isFollowing ? 'fa-heart' : 'fa-heart-o' ?>"></i>
                                        <?= $isFollowing ? 'Unfollow' : 'Follow' ?>
                                    </button>
                                    </form>
                                <?php else: ?>
                                    <a href="/login" class="follow-btn"><i class="fa fa-heart-o"></i> Follow</a>
                                <?php endif; ?>

                                <a href="<?= htmlspecialchars($watchBaseUrl) ?>" class="watch-btn">
                                    <span>Watch Now</span> <i class="fa fa-angle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 col-md-8">
                        <div class="anime__details__review">
                            <div class="section-title"><h5>Reviews</h5></div>
                            <?php if (empty($comments)): ?>
                                <p class="text-white-50">No comments yet.</p>
                            <?php else: ?>
                                <?php foreach ($comments as $c): ?>
                                <div class="anime__review__item">
                                    <div class="anime__review__item__pic">
                                    <img src="/img/avatar-default.png" alt="">
                                    </div>
                                    <div class="anime__review__item__text">
                                    <h6><?= htmlspecialchars($c['user_name'] ?? 'User') ?> - 
                                        <span><?= htmlspecialchars(date('M d, Y H:i', strtotime($c['created_at']))) ?></span>
                                    </h6>
                                    <p><?= nl2br(htmlspecialchars($c['body'])) ?></p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="anime__details__form" id="comments">
                            <div class="section-title"><h5>Your Comment</h5></div>

                            <?php if (isset($_SESSION['user_name']) && isset($_SESSION['user_id'])): ?>
                                <form method="POST" action="/comments/add">
                                    <?php if (!empty($animeDetails['mal_id'])): ?>
                                        <input type="hidden" name="anime_id" value="<?= (int)$animeDetails['mal_id'] ?>">
                                    <?php endif; ?>
                                <textarea name="body" placeholder="Your Comment" required></textarea>
                                <button type="submit"><i class="fa fa-location-arrow"></i> Review</button>
                                </form>
                            <?php else: ?>
                                <p class="text-white">You must <a href="/login" class="text-danger">log in</a> to leave a comment.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="anime__details__sidebar">
                            <div class="section-title">
                                <h5>you might like...</h5>
                            </div>
                            <?php foreach ($recommendations as $rec): ?>
                                <div class="product__sidebar__view__item set-bg" data-setbg="<?= htmlspecialchars($rec['images']['jpg']['image_url']) ?>">
                                    <div class="ep"><?= $rec['episodes'] ?? '?' ?> Episodes</div>
                                    <div class="view"><i class="fa fa-eye"></i> <?= $membersText ?></div>
                                    <h5><a href="/anime/<?= $rec['mal_id'] ?>"><?= htmlspecialchars($rec['title']) ?></a></h5>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Anime Section End -->

<?php require_once '../app/views/layouts/footer.php'; ?>