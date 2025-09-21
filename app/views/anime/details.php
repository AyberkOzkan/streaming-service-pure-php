<?php require_once '../app/views/layouts/header.php'; ?>
<?php

    $detailsUrl   = $detailsUrl   ?? ('/anime/' . ($animeDetails['mal_id'] ?? ''));
    $watchBaseUrl = $watchBaseUrl ?? ($detailsUrl . '/watch');
    $poster = isset($animeDetails['images']['jpg']['large_image_url'])
        ? $animeDetails['images']['jpg']['large_image_url']
        : ($animeDetails['poster_url'] ?? '/img/placeholder-vertical.jpg');

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
                                            <li><span>Type:</span> <?= htmlspecialchars($animeDetails['type'] ?? 'N/A') ?></li>
                                            <li><span>Studios:</span>
                                                <?php
                                                    $studio = $animeDetails['studios'][0]['name'] ?? ($animeDetails['studio'] ?? null);
                                                    echo $studio ? htmlspecialchars($studio) : 'N/A';
                                                ?>
                                            </li>
                                            <li><span>Date aired:</span>
                                                <?= htmlspecialchars($animeDetails['aired']['string'] ?? ($animeDetails['release_date'] ?? 'N/A')) ?>
                                            </li>
                                            <li><span>Status:</span> <?= htmlspecialchars($animeDetails['status'] ?? 'N/A') ?></li>
                                        </ul>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <ul>
                                            <li><span>Genre:</span>
                                                <?php
                                                    // Jikan: [{name: 'Action'}], Local: ['Action','Drama'] ya da hiç yoksa
                                                    $genres = $animeDetails['genres'] ?? [];
                                                    if ($genres) {
                                                        $names = array_map(fn($g) => is_array($g) ? ($g['name'] ?? '') : (string)$g, $genres);
                                                        $names = array_filter($names);
                                                        echo $names ? htmlspecialchars(implode(', ', $names)) : 'N/A';
                                                    } else {
                                                        echo 'N/A';
                                                    }
                                                ?>
                                            </li>
                                            <li><span>Duration:</span> <?= htmlspecialchars($animeDetails['duration'] ?? 'N/A') ?></li>
                                            <!-- <li><span>Quality:</span> HD</li> -->
                                            <li><span>Views:</span>
                                                <?= isset($animeDetails['members']) ? number_format($animeDetails['members']) : '—' ?>
                                            </li>

                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="anime__details__btn">
                                <?php if (isset($_SESSION['user_id']) && !empty($animeDetails['mal_id'])): ?>
                                    <form method="POST" action="/follow/<?= $isFollowing ? 'remove' : 'add' ?>" style="display:inline;">
                                        <input type="hidden" name="anime_id" value="<?= $animeDetails['mal_id'] ?>">
                                        <input type="hidden" name="anime_title" value="<?= htmlspecialchars($animeDetails['title']) ?>">
                                        <button type="submit" class="follow-btn">
                                            <i class="fa <?= $isFollowing ? 'fa-heart' : 'fa-heart-o' ?>"></i>
                                            <?= $isFollowing ? 'Unfollow' : 'Follow' ?>
                                        </button>
                                    </form>
                                <?php elseif (!isset($_SESSION['user_id'])): ?>
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
                                    <div class="view"><i class="fa fa-eye"></i> <?= number_format($rec['members']) ?></div>
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