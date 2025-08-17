<?php require_once '../app/views/layouts/header.php'; ?>

<div class="breadcrumb-option">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="breadcrumb__links">
          <a href="/"><i class="fa fa-home"></i> Home</a>
          <a href="/anime/<?= $animeDetails['mal_id'] ?>">Details</a>
          <span><?= htmlspecialchars($animeDetails['title']) ?> — Watch</span>
        </div>
      </div>
    </div>
  </div>
</div>

<section class="anime-details spad">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">

        <div class="anime__video__player" style="aspect-ratio:16/9;">
          <?php if ($currentPromoUrl): ?>
            <!-- Trailer embed -->
            <iframe
              src="<?= htmlspecialchars($currentPromoUrl) ?>"
              title="<?= htmlspecialchars($currentPromoTitle ?? $animeDetails['title']) ?>"
              frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              allowfullscreen
              style="width:100%; height:100%; border-radius:12px;">
            </iframe>
          <?php else: ?>
            <div class="text-white-50" style="padding:2rem; background:#111; border-radius:12px;">
              Official trailer bulunamadı. Yine de bölümlere göz atabilirsiniz.
            </div>
          <?php endif; ?>
        </div>

        <div class="anime__details__episodes" id="episodes" style="margin-top:24px;">
          <div class="section-title">
            <h5>Episodes</h5>
          </div>
          <?php for ($i = 1; $i <= $episodeCount; $i++): ?>
            <a
              href="/anime/<?= $animeDetails['mal_id'] ?>/watch?ep=<?= $i ?>"
              class="<?= $i === $currentEpisode ? 'active' : '' ?>"
              style="margin:6px; display:inline-block;">
              Ep <?= str_pad((string)$i, 2, '0', STR_PAD_LEFT) ?>
            </a>
          <?php endfor; ?>
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
              <input type="hidden" name="anime_id" value="<?= $animeDetails['mal_id'] ?>">
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
