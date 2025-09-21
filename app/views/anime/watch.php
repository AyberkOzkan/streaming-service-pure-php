<?php require_once '../app/views/layouts/header.php'; ?>

<div class="breadcrumb-option">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="breadcrumb__links">
          <a href="/"><i class="fa fa-home"></i> Home</a>
          <a href="<?= htmlspecialchars($detailsUrl ?? '/') ?>">Details</a>
          <span><?= htmlspecialchars($animeDetails['title'] ?? 'Watch') ?> — Watch</span>
        </div>
      </div>
    </div>
  </div>
</div>

<section class="anime-details spad">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">

        <?php
          $playerUrl = $localPlayerUrl ?: ($currentPromoUrl ?? null);
          $isVideo = $playerUrl && preg_match('~\.(m3u8|mp4)(\?.*)?$~i', $playerUrl);
        ?>

        <div class="anime__video__player" style="aspect-ratio:16/9;">
          <?php if ($playerUrl && $isVideo): ?>
            <video
              src="<?= htmlspecialchars($playerUrl) ?>"
              <?= !empty($playerPoster) ? 'poster="'.htmlspecialchars($playerPoster).'"' : '' ?>
              controls playsinline
              style="width:100%; height:100%; border-radius:12px; background:#000;">
            </video>
          <?php elseif ($playerUrl): ?>
            <iframe
              src="<?= htmlspecialchars($playerUrl) ?>"
              title="<?= htmlspecialchars($currentPromoTitle ?? ($animeDetails['title'] ?? 'Video')) ?>"
              frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              allowfullscreen
              style="width:100%; height:100%; border-radius:12px;">
            </iframe>
          <?php else: ?>
            <div class="text-white-50" style="padding:2rem; background:#111; border-radius:12px;">
              No playable source found.
            </div>
          <?php endif; ?>
        </div>

        <div class="anime__details__episodes" id="episodes" style="margin-top:24px;">
          <div class="section-title">
            <h5>Episodes</h5>
          </div>

          <?php if (!empty($episodes)): ?>
            <?php
              // Local ep listesi (ep_no, title, stream_url)
              usort($episodes, fn($a,$b) => (int)$a['ep_no'] <=> (int)$b['ep_no']);
              foreach ($episodes as $epRow):
                $no = (int)$epRow['ep_no']; if ($no<=0) continue;
            ?>
              <a
                href="<?= htmlspecialchars(($watchBaseUrl ?? '#').'?ep='.$no) ?>"
                class="<?= $no === (int)($currentEpisode ?? 1) ? 'active' : '' ?>"
                style="margin:6px; display:inline-block;">
                Ep <?= str_pad((string)$no, 2, '0', STR_PAD_LEFT) ?>
              </a>
            <?php endforeach; ?>
          <?php else: ?>
            <?php for ($i = 1; $i <= max(1, (int)($episodeCount ?? 12)); $i++): ?>
              <a
                href="<?= htmlspecialchars(($watchBaseUrl ?? '#').'?ep='.$i) ?>"
                class="<?= $i === (int)($currentEpisode ?? 1) ? 'active' : '' ?>"
                style="margin:6px; display:inline-block;">
                Ep <?= str_pad((string)$i, 2, '0', STR_PAD_LEFT) ?>
              </a>
            <?php endfor; ?>
          <?php endif; ?>

        </div>

      </div>
    </div>

    <div class="row" style="margin-top:24px;">
      <div class="col-lg-8">
        <div class="anime__details__review" id="comments">
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
                  <h6>
                    <?= htmlspecialchars($c['user_name'] ?? 'User') ?>
                    <span><?= htmlspecialchars(date('M d, Y H:i', strtotime($c['created_at']))) ?></span>
                  </h6>
                  <p><?= nl2br(htmlspecialchars($c['body'])) ?></p>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <div class="anime__details__form">
          <div class="section-title"><h5>Your Comment</h5></div>
          <?php if (isset($_SESSION['user_id'])): ?>
            <form action="/comments/add" method="POST">
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
    </div>
  </div>
</section>

<?php require_once '../app/views/layouts/footer.php'; ?>
