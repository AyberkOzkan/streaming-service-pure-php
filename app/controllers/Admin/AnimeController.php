<?php
    require_once __DIR__ . '/../../core/helpers.php';
    require_once __DIR__ . '/../../models/AnimeLocalModel.php';

    class Admin_AnimeController
    {
        public function index(): void {
            requireSuperAdmin();

            $q = trim($_GET['q'] ?? '');
            $page = max(1, (int)($_GET['page'] ?? 1));
            $perPage = 20;

            $m = new AnimeLocalModel();
            $rows = $m->listPaginated($page, $perPage, $q ?: null);
            $total = $m->countFiltered($q ?: null);
            $pages = (int)ceil($total / $perPage);

            $_SESSION['csrf'] ??= bin2hex(random_bytes(16)); $csrf = $_SESSION['csrf'];

            require_once __DIR__ . '/../../views/admin/layouts/header.php';
            require_once __DIR__ . '/../../views/admin/anime/index.php';
            require_once __DIR__ . '/../../views/admin/layouts/footer.php';
        }

        public function new(): void {
            requireSuperAdmin();
            $_SESSION['csrf'] ??= bin2hex(random_bytes(16)); $csrf = $_SESSION['csrf'];
            $item = null;
            require_once __DIR__ . '/../../views/admin/layouts/header.php';
            require_once __DIR__ . '/../../views/admin/anime/form.php';
            require_once __DIR__ . '/../../views/admin/layouts/footer.php';
        }

        public function create(): void {
            requireSuperAdmin();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; return; }
            if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { $_SESSION['flash_error']='CSRF validation failed.'; header('Location:/admin/anime'); return; }

            $title = trim($_POST['title'] ?? '');
            if ($title === '') { $_SESSION['flash_error']='Title is required.'; header('Location:/admin/anime/new'); return; }

            $m = new AnimeLocalModel();
            $id = $m->create([
                'mal_id' => $_POST['mal_id'] !== '' ? (int)$_POST['mal_id'] : null,
                'title'  => $title,
                'synopsis' => trim($_POST['synopsis'] ?? ''),
                'poster_url' => trim($_POST['poster_url'] ?? ''),
                'trailer_url'=> trim($_POST['trailer_url'] ?? ''),
                'release_date' => $_POST['release_date'] !== '' ? $_POST['release_date'] : null,
                'total_episodes' => $_POST['total_episodes'] !== '' ? (int)$_POST['total_episodes'] : null,
            ]);

            $_SESSION['flash_success'] = "Anime #$id created.";
            header('Location:/admin/anime');
        }

        public function edit(): void {
            requireSuperAdmin();
            $id = (int)($_GET['id'] ?? 0);
            if ($id <= 0) { header('Location:/admin/anime'); return; }

            $m = new AnimeLocalModel();
            $item = $m->getById($id);
            if (!$item) { $_SESSION['flash_error']='Anime not found.'; header('Location:/admin/anime'); return; }

            $_SESSION['csrf'] ??= bin2hex(random_bytes(16)); $csrf = $_SESSION['csrf'];
            require_once __DIR__ . '/../../views/admin/layouts/header.php';
            require_once __DIR__ . '/../../views/admin/anime/form.php';
            require_once __DIR__ . '/../../views/admin/layouts/footer.php';
        }

        public function update(): void {
            requireSuperAdmin();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; return; }
            if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { $_SESSION['flash_error']='CSRF validation failed.'; header('Location:/admin/anime'); return; }

            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) { $_SESSION['flash_error']='Invalid id.'; header('Location:/admin/anime'); return; }

            $title = trim($_POST['title'] ?? '');
            if ($title === '') { $_SESSION['flash_error']='Title is required.'; header("Location:/admin/anime/edit?id={$id}"); return; }

            $m = new AnimeLocalModel();
            $ok = $m->update($id, [
                'mal_id' => $_POST['mal_id'] !== '' ? (int)$_POST['mal_id'] : null,
                'title'  => $title,
                'synopsis' => trim($_POST['synopsis'] ?? ''),
                'poster_url' => trim($_POST['poster_url'] ?? ''),
                'trailer_url'=> trim($_POST['trailer_url'] ?? ''),
                'release_date' => $_POST['release_date'] !== '' ? $_POST['release_date'] : null,
                'total_episodes' => $_POST['total_episodes'] !== '' ? (int)$_POST['total_episodes'] : null,
            ]);

            $_SESSION['flash_success'] = $ok ? "Anime #$id updated." : "Failed to update anime #$id.";
            header('Location:/admin/anime');
        }

        public function delete(): void {
            requireSuperAdmin();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; return; }
            if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { $_SESSION['flash_error']='CSRF validation failed.'; header('Location:/admin/anime'); return; }

            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) { header('Location:/admin/anime'); return; }

            $m = new AnimeLocalModel();
            $ok = $m->delete($id);
            $_SESSION['flash_success'] = $ok ? 'Anime deleted.' : 'Failed to delete anime.';
            header('Location:/admin/anime');
        }

        /* ===== Episodes ===== */
        public function episodes(): void {
            requireSuperAdmin();
            $aid = (int)($_GET['anime_id'] ?? 0);
            if ($aid <= 0) { header('Location:/admin/anime'); return; }

            $m = new AnimeLocalModel();
            $item = $m->getById($aid);
            if (!$item) { $_SESSION['flash_error']='Anime not found.'; header('Location:/admin/anime'); return; }

            $episodes = $m->episodesByAnimeId($aid);
            $_SESSION['csrf'] ??= bin2hex(random_bytes(16)); $csrf = $_SESSION['csrf'];

            require_once __DIR__ . '/../../views/admin/layouts/header.php';
            require_once __DIR__ . '/../../views/admin/anime/episodes.php';
            require_once __DIR__ . '/../../views/admin/layouts/footer.php';
        }

        public function addEpisode(): void {
            requireSuperAdmin();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; return; }
            if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { $_SESSION['flash_error']='CSRF validation failed.'; header('Location:/admin/anime'); return; }

            $aid = (int)($_POST['anime_id'] ?? 0);
            $no  = (int)($_POST['ep_no'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $url = trim($_POST['stream_url'] ?? '');
            $dur = $_POST['duration_seconds'] !== '' ? (int)$_POST['duration_seconds'] : null;

            if ($aid<=0 || $no<=0 || $title==='') {
                $_SESSION['flash_error']='Invalid episode payload.';
                header("Location:/admin/anime/episodes?anime_id={$aid}"); return;
            }

            $m = new AnimeLocalModel();
            $ok = $m->addEpisode($aid, $no, $title, $url ?: null, $dur);
            $_SESSION['flash_success'] = $ok ? 'Episode saved.' : 'Failed to save episode.';
            header("Location:/admin/anime/episodes?anime_id={$aid}");
        }

        public function deleteEpisode(): void {
            requireSuperAdmin();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; return; }
            if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { $_SESSION['flash_error']='CSRF validation failed.'; header('Location:/admin/anime'); return; }

            $id = (int)($_POST['id'] ?? 0);
            $aid = (int)($_POST['anime_id'] ?? 0);
            if ($id<=0 || $aid<=0) { $_SESSION['flash_error']='Invalid request.'; header('Location:/admin/anime'); return; }

            $m = new AnimeLocalModel();
            $ok = $m->deleteEpisode($id);
            $_SESSION['flash_success'] = $ok ? 'Episode deleted.' : 'Failed to delete episode.';
            header("Location:/admin/anime/episodes?anime_id={$aid}");
        }

        /* Quick path: ensure by MAL id then go to episodes */
        public function manageByMal(): void {
            requireSuperAdmin();
            $malId = (int)($_GET['mal_id'] ?? 0);
            if ($malId <= 0) { $_SESSION['flash_error']='Invalid MAL id.'; header('Location:/admin/anime'); return; }

            $m = new AnimeLocalModel();
            $animeId = $m->ensureFromMal($malId);
            header("Location:/admin/anime/episodes?anime_id={$animeId}");
        }
    }
