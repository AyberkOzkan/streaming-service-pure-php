<?php 
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require_once __DIR__ . '/../../controllers/GenreController.php';
    $genres = GenreController::all();
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Anime Template">
    <meta name="keywords" content="Anime, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= isset($page_title) ? $page_title : 'Anime | Template' ?></title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@300;400;500;600;700;800;900&display=swap"
    rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="<?= asset('css/bootstrap.min.css'); ?>" type="text/css">
    <link rel="stylesheet" href="<?= asset('css/font-awesome.min.css'); ?>" type="text/css">
    <link rel="stylesheet" href="<?= asset('css/elegant-icons.css'); ?>" type="text/css">
    <link rel="stylesheet" href="<?= asset('css/plyr.css'); ?>" type="text/css">
    <link rel="stylesheet" href="<?= asset('css/nice-select.css'); ?>" type="text/css">
    <link rel="stylesheet" href="<?= asset('css/owl.carousel.min.css'); ?>" type="text/css">
    <link rel="stylesheet" href="<?= asset('css/slicknav.min.css'); ?>" type="text/css">
    <link rel="stylesheet" href="<?= asset('css/style.css'); ?>" type="text/css">
</head>

<body>
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>

    <!-- Header Section Begin -->
    <header class="header">
        <div class="container">
            <div class="row">
                <div class="col-lg-2">
                    <div class="header__logo">
                        <a href="<?= HOMEPAGE ?>">
                            <img class="mt-2" src="<?= asset('img/logo.png') ?>" alt="" style="height: 28px; width: auto;">
                        </a>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="header__nav">
                        <nav class="header__menu mobile-menu">
                            <ul>

                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="header__right d-flex align-items-center justify-content-end">
                        <a href="javascript:void(0);" id="search-toggle" class="mr-3" aria-label="Search" role="button">
                            <span class="icon_search"></span>
                        </a>
                        <div id="search-box" style="display:none; position: relative;">
                            <input type="text" id="search-input" 
                                placeholder="Search" 
                                style="padding:6px 10px;border-radius:4px;border:1px solid #444;background:#111;color:#e53637;">
                            <div id="search-results" 
                                style="position:absolute;top:36px;left:0;width:230px;background:#1f1f1f;border-radius:6px;display:none;z-index:1000;">
                            </div>
                        </div>
                        <div class="header__right__auth">
                            <?php if (isset($_SESSION['user_name']) && isset($_SESSION['user_id'])): ?>
                                <a href="/profile" class="text-white mr-2 d-inline-flex align-items-center">
                                    <i class="icon_profile mr-1"></i>
                                    <?= htmlspecialchars($_SESSION['user_name']) ?>
                                </a>
                                <a href="/logout" class="primary-btn small-btn">Logout</a>
                            <?php else: ?>
                                <a href="/login"><span class="icon_profile"></span> Login</a>
                                <a href="/register" class="primary-btn ml-2">Register</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div id="mobile-menu-wrap"></div>
        </div>
    </header>
    <!-- Header End -->

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const searchBox   = document.getElementById("search-box");
        const searchInput = document.getElementById("search-input");
        const resultsBox  = document.getElementById("search-results");

        const openBox  = () => { searchBox.style.display = "block"; resultsBox.style.display = "none"; searchInput.focus(); };
        const closeBox = () => { searchBox.style.display = "none"; resultsBox.style.display = "none"; activeIdx = -1; };

        // Tek bir delegasyonlu click handler: ikonun içi (span/::before) dahil çalışır
        document.addEventListener('click', (e) => {
            if (e.target.closest('#search-toggle')) {
            const visible = searchBox.style.display !== 'none' && searchBox.style.display !== '';
            visible ? closeBox() : openBox();
            e.preventDefault();
            return;
            }
            // Kutu dışına tıklayınca kapat
            if (!e.target.closest('#search-box')) {
            closeBox();
            }
        });

        window.addEventListener("popstate", closeBox);
        searchInput.addEventListener("keydown", (e) => { if (e.key === "Escape") closeBox(); });

        let debounceTimer;
        let controller = null;
        const cache = new Map(); // query -> results
        let activeIdx = -1;

        const highlight = (idx) => {
            [...resultsBox.querySelectorAll("a")].forEach((el, i) => {
            el.style.background = (i === idx) ? "#2a2a2a" : "transparent";
            });
            activeIdx = idx;
        };

        const renderResults = (items) => {
            resultsBox.innerHTML = "";
            if (!items || items.length === 0) {
            resultsBox.innerHTML = "<div style='padding:8px;color:#aaa;'>No results found</div>";
            resultsBox.style.display = "block";
            activeIdx = -1;
            return;
            }
            items.forEach((anime, i) => {
            const row = document.createElement("a");
            row.href = "/anime/" + anime.mal_id;
            row.style.cssText = "display:flex;align-items:center;padding:8px;color:#eee;text-decoration:none;border-bottom:1px solid #333;gap:8px;";
            row.setAttribute("data-idx", i);
            row.innerHTML = `
                <img src="${anime.images?.jpg?.image_url || '/img/placeholder.jpg'}"
                    width="40" height="55" style="border-radius:4px;object-fit:cover;">
                <span style="flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${anime.title}</span>`;
            row.addEventListener("mouseenter", () => highlight(i));
            resultsBox.appendChild(row);
            });
            resultsBox.style.display = "block";
            activeIdx = -1;
        };

        const search = (q) => {
            if (cache.has(q)) { renderResults(cache.get(q)); return; }
            if (controller) controller.abort();
            controller = new AbortController();

            fetch(`https://api.jikan.moe/v4/anime?q=${encodeURIComponent(q)}&limit=5`, { signal: controller.signal })
            .then(r => r.ok ? r.json() : Promise.reject())
            .then(d => { const items = d?.data || []; cache.set(q, items); renderResults(items); })
            .catch(err => { if (err?.name !== "AbortError") {
                resultsBox.innerHTML = "<div style='padding:8px;color:#aaa;'>Error loading results</div>";
                resultsBox.style.display = "block";
            }});
        };

        searchInput.addEventListener("input", () => {
            clearTimeout(debounceTimer);
            const q = searchInput.value.trim();
            if (q.length < 2) { resultsBox.style.display = "none"; return; }
            debounceTimer = setTimeout(() => search(q), 300);
        });

        searchInput.addEventListener("keydown", (e) => {
            const rows = [...resultsBox.querySelectorAll("a")];
            if (!rows.length) return;
            if (e.key === "ArrowDown") { e.preventDefault(); highlight((activeIdx + 1) % rows.length); }
            else if (e.key === "ArrowUp") { e.preventDefault(); highlight((activeIdx - 1 + rows.length) % rows.length); }
            else if (e.key === "Enter" && activeIdx >= 0) { rows[activeIdx].click(); }
        });
    });
    </script>