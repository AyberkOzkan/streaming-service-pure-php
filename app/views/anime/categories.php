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


<section class="product spad">
    <div class="container">
        <div class="section-title">
            <h4><?= htmlspecialchars($categoryTitle) ?></h4>
        </div>
        <div class="row">
            <?php foreach ($animeList as $anime): ?>
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
                            <h5><a href="/anime/<?= $anime['mal_id'] ?>"><?= htmlspecialchars($anime['title']) ?></a></h5>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?= renderPagination($currentPage ?? 1, $lastPage ?? 1, $basePath ?? '') ?>
<?php require_once '../app/views/layouts/footer.php'; ?>