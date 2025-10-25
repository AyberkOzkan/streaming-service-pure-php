<?php 
    require_once '../app/views/layouts/header.php';

    function renderPagination($currentPage, $lastPage, $basePath) {
        echo '<div class="product__pagination">';

        if ($currentPage > 1) {
            echo '<a href="' . $basePath . '?page=' . ($currentPage - 1) . '">&laquo; Prev</a>';
        }

        if ($currentPage > 2) {
            echo '<a href="' . $basePath . '?page=1">1</a>';
            if ($currentPage > 3) {
                echo '<span>...</span>';
            }
        }

        for ($i = max(1, $currentPage - 1); $i <= min($lastPage, $currentPage + 1); $i++) {
            if ($i == $currentPage) {
                echo '<span class="current-page">' . $i . '</span>';
            } else {
                echo '<a href="' . $basePath . '?page=' . $i . '">' . $i . '</a>';
            }
        }

        if ($currentPage < $lastPage - 1) {
            if ($currentPage < $lastPage - 2) {
                echo '<span>...</span>';
            }
            echo '<a href="' . $basePath . '?page=' . $lastPage . '">' . $lastPage . '</a>';
        }

        if ($currentPage < $lastPage) {
            echo '<a href="' . $basePath . '?page=' . ($currentPage + 1) . '">Next &raquo;</a>';
        }

        echo '</div>';
    }
?>

    <section class="normal-breadcrumb set-bg" data-setbg="<?= asset('img/normal-breadcrumb.jpg'); ?>">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="normal__breadcrumb__text">
                        <h2>My Profile</h2>
                        <p>Manage your account, shows and comments.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="product spad">
        <div class="container">
            <style>
                @media (min-width: 992px) {
                    .profile-row {
                        display: flex;
                        /* gap: 24px; */
                        align-items: stretch; 
                    }
                    .profile-card {
                        background:#1f1f1f;
                        border-radius:8px;
                        padding:20px;
                        display:flex;
                        flex-direction:column;
                        height:100%;
                    }
                    .profile-card.equal {
                        height: 380px;
                    }
                    .profile-card .scroll-area {
                        overflow: auto;
                        margin-top: 8px;
                    }
                }
                @media (max-width: 991.98px) {
                    .profile-card { background:#1f1f1f; border-radius:8px; padding:20px; }
                    .profile-card.equal { height: auto; }
                    .profile-card .scroll-area { overflow: visible; }
                }
            </style>
            <div class="section-title">
                <h4><?= htmlspecialchars($categoryTitle) ?></h4>
            </div>

            <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger" style="color:#fff;background:#e53637;padding:10px;border-radius:6px;margin-bottom:20px;">
                    <?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($_SESSION['flash_success'])): ?>
                <div class="alert alert-success" style="color:#0f0;background:#1b1b1b;padding:10px;border-radius:6px;margin-bottom:20px;">
                    <?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
                </div>
            <?php endif; ?>

            <div class="row profile-row" style="margin-bottom:30px;">
                <div class="col-lg-12">
                    <div class="profile-card">
                        <h5 style="color:#fff;margin-bottom:15px;">Profile Info</h5>
                        <form method="POST" action="/profile/update">
                            <div class="form-group" style="margin-bottom:10px;">
                                <label style="color:#e53637;">Name</label>
                                <input type="text" name="name" required
                                    value="<?= htmlspecialchars($user['name'] ?? $_SESSION['user_name'] ?? '') ?>"
                                    style="width:100%;padding:10px;border-radius:6px;border:1px solid #333;background:#111;color:#fff;">
                            </div>
                            <div class="form-group" style="margin-bottom:10px;">
                                <label style="color:#e53637;">Email</label>
                                <input type="email" name="email" disabled
                                    value="<?= htmlspecialchars($user['email'] ?? $_SESSION['user_email'] ?? '') ?>"
                                    style="width:100%;padding:10px;border-radius:6px;border:1px solid #333;background:#111;color:#fff;">
                            </div>
                            <button type="submit" class="site-btn" style="margin-top: 16px;">Save</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row profile-row" style="margin-bottom:30px;">
                <div class="col-lg-6">
                    <div class="profile-card equal">
                    <h5 style="color:#fff;margin-bottom:15px;">Change Password</h5>
                    <form method="POST" action="/profile/password">
                        <div class="form-group" style="margin-bottom:10px;">
                        <label style="color:#e53637;">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required
                                style="width:100%;padding:10px;border-radius:6px;border:1px solid #333;background:#111;color:#fff;">
                        </div>
                        <div class="form-group" style="margin-bottom:10px;">
                        <label style="color:#e53637;">New Password</label>
                        <input type="password" name="new_password" class="form-control" required
                                style="width:100%;padding:10px;border-radius:6px;border:1px solid #333;background:#111;color:#fff;">
                        </div>
                        <div class="form-group" style="margin-bottom:10px;">
                        <label style="color:#e53637;">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" required
                                style="width:100%;padding:10px;border-radius:6px;border:1px solid #333;background:#111;color:#fff;">
                        </div>
                        <button type="submit" class="site-btn" style="margin-top: 16px;">Change</button>
                    </form>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="profile-card equal">
                    <h5 style="color:#fff;margin-bottom:15px;">Recent Comments</h5>

                    <?php if (empty($recentComments)): ?>
                        <p class="text-white-50">You haven't written a comment yet.</p>
                    <?php else: ?>
                        <ul class="scroll-area" style="list-style:none;padding:0;margin:0;">
                        <?php foreach ($recentComments as $c):
                            $aid   = (int)$c['anime_id'];
                            $title = $titles[$aid] ?? ("Anime #".$aid);
                            $when  = date('d.m.Y, H:i', strtotime($c['created_at']));
                            $short = mb_strimwidth($c['body'], 0, 120, '…', 'UTF-8');
                        ?>
                        <li style="padding:12px 0;border-bottom:1px solid #2a2a2a;">
                            <div style="display:flex;justify-content:space-between;gap:12px;">
                            <div style="min-width:0;">
                                <a href="/anime/<?= $aid ?>"
                                style="color:#e6e6e6;font-weight:600;display:inline-block;max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                <?= htmlspecialchars($title) ?>
                                </a>
                                <div style="color:#bdbdbd;margin-top:6px;">
                                “<?= htmlspecialchars($short) ?>”
                                <a href="/anime/<?= $aid ?>#comments" style="color:#e53637;">→</a>
                                </div>
                            </div>
                            <div style="color:#e53637;white-space:nowrap;"><?= htmlspecialchars($when) ?></div>
                            </div>
                        </li>
                        <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="section-title">
                <h4>Followed Shows</h4>
            </div>

            <div class="row">
                <?php if (empty($animeList)): ?>
                    <div class="col-12">
                        <p class="text-white-50">You don't have any anime you follow yet.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($animeList as $a): ?>
                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="product__item">
                            <div class="product__item__pic set-bg" data-setbg="<?= htmlspecialchars($a['img'] ?? '/img/placeholder.jpg') ?>">
                                <div class="ep"><?= htmlspecialchars($a['episodes'] ?? '?') ?> Episodes</div>

                                <?php if (!empty($a['is_local'])): ?>
                                <div class="comment"><span class="badge badge-blue">Local</span></div>
                                <?php else: ?>
                                <div class="comment"><i class="fa fa-star"></i> <?= htmlspecialchars($a['score'] ?? 'N/A') ?></div>
                                <div class="view"><i class="fa fa-eye"></i> <?= number_format($a['members'] ?? 0) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="product__item__text">
                                <?php if (empty($a['is_local'])): ?>
                                <ul>
                                    <li><?= htmlspecialchars($a['type'] ?? 'Unknown') ?></li>
                                    <li><?= htmlspecialchars($a['status'] ?? 'Unknown') ?></li>
                                </ul>
                                <?php endif; ?>

                                <h5><a href="<?= htmlspecialchars($a['href'] ?? '#') ?>">
                                <?= htmlspecialchars($a['title'] ?? 'Untitled') ?>
                                </a></h5>
                            </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?= renderPagination($currentPage ?? 1, $lastPage ?? 1, $basePath ?? '/profile') ?>

        </div>
    </section>
<?php require_once '../app/views/layouts/footer.php'; ?>
