

    <section class="genre-filter spad">
        <div class="container">
            <div class="section-title">
                <h4>Browse by Genre</h4>
            </div>

            <!-- Harfler -->
            <div class="genre-letters">
                <?php foreach (range('A', 'Z') as $char): ?>
                    <button class="genre-letter" data-letter="<?= $char ?>"><?= $char ?></button>
                <?php endforeach; ?>
                <?php if (isset($groupedGenres['#'])): ?>
                    <button class="genre-letter" data-letter="#">#</button>
                <?php endif; ?>
            </div>

            <!-- Kategoriler -->
            <div class="genre-list mt-4">
                <?php foreach ($groupedGenres as $letter => $genreList): ?>
                    <div class="genre-section" id="genre-<?= $letter ?>" style="display: none;">
                        <h2 style="color: #e53637aa" class="mt-4"><?= $letter ?></h2>
                        <ul class="genre-grid">
                            <?php foreach ($genreList as $genre): ?>
                                <li>
                                    <a href="/anime/genre/<?= urlencode(strtolower($genre['name'])) ?>" class="text-white">
                                        <?= htmlspecialchars($genre['name']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>


    
    <!-- Hero Section Begin -->
    <section class="hero">
        <div class="container">
            <div class="hero__slider owl-carousel">
                <?php foreach ($hero as $anime): ?>
                    <div class="hero__items set-bg" data-setbg="<?= htmlspecialchars($anime['images']['webp']['large_image_url']) ?>">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="hero__text">
                                    <h2><?= htmlspecialchars($anime['title']) ?></h2>
                                    <p>
                                        <?= htmlspecialchars(mb_strimwidth($anime['synopsis'], 0, 150, '...')) ?>
                                    </p>
                                    <div class="label">
                                        <a href="/genre/<?= urlencode($anime['genres'][0]['name'] ?? 'Anime') ?>" style="color: inherit; text-decoration: none;">
                                            <?= htmlspecialchars($anime['genres'][0]['name'] ?? 'Anime') ?>
                                        </a>
                                    </div>
                                    <a href="/anime/<?= $anime['mal_id'] ?>"><span>Watch Now</span> <i class="fa fa-angle-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <!-- Hero Section End -->

    <!-- Product Section Begin -->
    <section class="product spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="trending__product">
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-8">
                                <div class="section-title">
                                    <h4>Trending Now</h4>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="btn__all">
                                    <a href="#" class="primary-btn">View All <span class="arrow_right"></span></a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <?php foreach ($trending as $anime): ?>
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <div class="product__item">
                                    <div class="product__item__pic set-bg" data-setbg="<?= htmlspecialchars($anime['images']['jpg']['large_image_url']) ?>">
                                        <div class="ep"><?= $anime['episodes'] ?? '?' ?> Episodes</div>
                                        <div class="view"><i class="fa fa-eye"></i><?= number_format($anime['members']) ?></div>
                                        <div class="comment"><i class="fa fa-star"></i> <?= $anime['score'] ?? 'N/A' ?></div>
                                    </div>
                                    <div class="product__item__text">
                                        <ul>
                                        <?php foreach ($anime['genres'] as $g): ?>
                                            <li><?= htmlspecialchars($g['name']) ?></li>
                                        <?php endforeach; ?>
                                        </ul>

                                        <h5>
                                        <a href="/anime/<?= $anime['mal_id'] ?>">
                                            <?= htmlspecialchars($anime['title']) ?>
                                        </a>
                                        </h5>
                                    </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="popular__product">
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-8">
                                <div class="section-title">
                                    <h4>Adventure Shows</h4>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="btn__all">
                                    <a href="#" class="primary-btn">View All <span class="arrow_right"></span></a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <?php foreach ($adventure as $anime): ?>
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <div class="product__item">
                                        <div class="product__item__pic set-bg" data-setbg="<?= htmlspecialchars($anime['images']['jpg']['large_image_url']) ?>">
                                            <div class="ep"><?= $anime['episodes'] ?? '?' ?> Episodes</div>
                                            <div class="comment"><i class="fa fa-star"></i> <?= $anime['score'] ?? 'N/A' ?></div>
                                            <div class="view"><i class="fa fa-eye"></i> <?= $anime['members'] ?? '0' ?></div>
                                        </div>
                                        <div class="product__item__text">
                                            <ul>
                                                <li><?= htmlspecialchars($anime['type'] ?? 'Unknown') ?></li>
                                                <li><?= htmlspecialchars($anime['status'] ?? 'Unknown') ?></li>
                                            </ul>
                                            <h5><a href="<?= htmlspecialchars($anime['url']) ?>" target="_blank"><?= htmlspecialchars($anime['title']) ?></a></h5>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="recent__product">
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-8">
                                <div class="section-title">
                                    <h4>Recently Added Shows</h4>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="btn__all">
                                    <a href="#" class="primary-btn">View All <span class="arrow_right"></span></a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <?php foreach ($recent as $anime): ?>
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <div class="product__item">
                                        <div class="product__item__pic set-bg" data-setbg="<?= htmlspecialchars($anime['images']['jpg']['large_image_url']) ?>">
                                            <div class="ep"><?= $anime['episodes'] ?? '?' ?> Episodes</div>
                                            <div class="comment"><i class="fa fa-star"></i> <?= $anime['score'] ?? 'N/A' ?></div>
                                            <div class="view"><i class="fa fa-eye"></i> <?= number_format($anime['members']) ?></div>
                                        </div>
                                        <div class="product__item__text">
                                            <ul>
                                                <li><?= htmlspecialchars($anime['type'] ?? 'Unknown') ?></li>
                                                <li><?= htmlspecialchars($anime['status'] ?? 'Unknown') ?></li>
                                            </ul>
                                            <h5><a href="<?= htmlspecialchars($anime['url']) ?>" target="_blank"><?= htmlspecialchars($anime['title']) ?></a></h5>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="live__product">
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-8">
                                <div class="section-title">
                                    <h4>On TV Now</h4>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="btn__all">
                                    <a href="#" class="primary-btn">View All <span class="arrow_right"></span></a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <?php foreach ($live as $anime): ?>
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <div class="product__item">
                                        <div class="product__item__pic set-bg" data-setbg="<?= htmlspecialchars($anime['images']['jpg']['large_image_url']) ?>">
                                            <div class="ep"><?= $anime['episodes'] ?? '?' ?> Episodes</div>
                                            <div class="comment"><i class="fa fa-star"></i> <?= $anime['score'] ?? 'N/A' ?></div>
                                            <div class="view"><i class="fa fa-eye"></i> <?= number_format($anime['members']) ?></div>
                                        </div>
                                        <div class="product__item__text">
                                            <ul>
                                                <li><?= htmlspecialchars($anime['type'] ?? 'Unknown') ?></li>
                                                <li><?= htmlspecialchars($anime['status'] ?? 'Unknown') ?></li>
                                            </ul>
                                            <h5><a href="<?= htmlspecialchars($anime['url']) ?>" target="_blank"><?= htmlspecialchars($anime['title']) ?></a></h5>
                                        </div> 
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-8">
                    <div class="product__sidebar">
                        <div class="product__sidebar__view">
                            <div class="product__sidebar__comment">
                                <div class="section-title">
                                    <h5>For You</h5>
                                </div>
                                <div class="product__sidebar__comment__item">
                                    <div class="product__sidebar__comment__item__pic">
                                        <img src="img/sidebar/comment-1.jpg" alt="">
                                    </div>
                                    <div class="product__sidebar__comment__item__text">
                                        <ul>
                                            <li>Active</li>
                                            <li>Movie</li>
                                        </ul>
                                        <h5><a href="#">The Seven Deadly Sins: Wrath of the Gods</a></h5>
                                        <span><i class="fa fa-eye"></i> 19.141 Viewes</span>
                                    </div>
                                </div>
                                <div class="product__sidebar__comment__item">
                                    <div class="product__sidebar__comment__item__pic">
                                        <img src="img/sidebar/comment-2.jpg" alt="">
                                    </div>
                                    <div class="product__sidebar__comment__item__text">
                                        <ul>
                                            <li>Active</li>
                                            <li>Movie</li>
                                        </ul>
                                        <h5><a href="#">Shirogane Tamashii hen Kouhan sen</a></h5>
                                        <span><i class="fa fa-eye"></i> 19.141 Viewes</span>
                                    </div>
                                </div>
                                <div class="product__sidebar__comment__item">
                                    <div class="product__sidebar__comment__item__pic">
                                        <img src="img/sidebar/comment-3.jpg" alt="">
                                    </div>
                                    <div class="product__sidebar__comment__item__text">
                                        <ul>
                                            <li>Active</li>
                                            <li>Movie</li>
                                        </ul>
                                        <h5><a href="#">Kizumonogatari III: Reiket su-hen</a></h5>
                                        <span><i class="fa fa-eye"></i> 19.141 Viewes</span>
                                    </div>
                                </div>
                                <div class="product__sidebar__comment__item">
                                    <div class="product__sidebar__comment__item__pic">
                                        <img src="img/sidebar/comment-4.jpg" alt="">
                                    </div>
                                    <div class="product__sidebar__comment__item__text">
                                        <ul>
                                            <li>Active</li>
                                            <li>Movie</li>
                                        </ul>
                                        <h5><a href="#">Monogatari Series: Second Season</a></h5>
                                        <span><i class="fa fa-eye"></i> 19.141 Viewes</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<!-- Product Section End -->


    <script>
        document.querySelectorAll('.genre-letter').forEach(letterBtn => {
            letterBtn.addEventListener('click', () => {
                const letter = letterBtn.dataset.letter;

                // Tüm blokları gizle
                document.querySelectorAll('.genre-section').forEach(section => {
                    section.style.display = 'none';
                });

                // Sadece tıklanan harfi aç
                const selected = document.getElementById('genre-' + letter);
                if (selected) {
                    selected.style.display = 'block';
                    // selected.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
