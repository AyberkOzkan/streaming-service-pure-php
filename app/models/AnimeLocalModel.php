<?php
    class AnimeLocalModel {
        private PDO $db;
        public function __construct() {
            require_once __DIR__ . '/../config/db.php';
            $this->db = Database::connect();
        }

        public function listPaginated(int $page=1, int $perPage=20, ?string $q=null): array {
            $offset = ($page - 1) * $perPage;
            $where = 'WHERE 1=1';
            $params = [];
            if ($q) { $where .= " AND (LOWER(title) ILIKE :q OR synopsis ILIKE :q)"; $params[':q'] = "%{$q}%"; }

            $sql = "SELECT id, mal_id, title, release_date, total_episodes, created_at, updated_at
                    FROM animes
                    $where
                    ORDER BY id DESC
                    LIMIT :limit OFFSET :offset";
            $st = $this->db->prepare($sql);
            foreach ($params as $k => $v) $st->bindValue($k, $v, PDO::PARAM_STR);
            $st->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $st->bindValue(':offset', $offset, PDO::PARAM_INT);
            $st->execute();
            return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }

        public function countFiltered(?string $q=null): int {
            $where = 'WHERE 1=1';
            $params = [];
            if ($q) { $where .= " AND (LOWER(title) ILIKE :q OR synopsis ILIKE :q)"; $params[':q'] = "%{$q}%"; }
            $st = $this->db->prepare("SELECT COUNT(*) FROM animes $where");
            $st->execute($params);
            return (int)$st->fetchColumn();
        }

        public function getById(int $id): ?array {
            $st = $this->db->prepare("SELECT * FROM animes WHERE id=:id LIMIT 1");
            $st->execute([':id'=>$id]);
            $row = $st->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        }

        public function getByMalId(int $malId): ?array {
            $st = $this->db->prepare("SELECT * FROM animes WHERE mal_id=:mid LIMIT 1");
            $st->execute([':mid'=>$malId]);
            $row = $st->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        }

        public function create(array $d): int {
            $st = $this->db->prepare("
                INSERT INTO animes (mal_id, title, synopsis, poster_url, trailer_url, release_date, total_episodes)
                VALUES (:mal_id, :title, :synopsis, :poster_url, :trailer_url, :release_date, :total_episodes)
                RETURNING id
            ");
            $st->execute([
                ':mal_id' => $d['mal_id'] ?? null,
                ':title'  => $d['title'],
                ':synopsis' => $d['synopsis'] ?? null,
                ':poster_url' => $d['poster_url'] ?? null,
                ':trailer_url'=> $d['trailer_url'] ?? null,
                ':release_date'=> $d['release_date'] ?? null,
                ':total_episodes'=> $d['total_episodes'] ?? null,
            ]);
            return (int)$st->fetchColumn();
        }

        public function update(int $id, array $d): bool {
            $st = $this->db->prepare("
                UPDATE animes SET
                    mal_id = :mal_id,
                    title  = :title,
                    synopsis = :synopsis,
                    poster_url = :poster_url,
                    trailer_url= :trailer_url,
                    release_date = :release_date,
                    total_episodes = :total_episodes,
                    updated_at = NOW()
                WHERE id = :id
            ");
            return $st->execute([
                ':mal_id' => $d['mal_id'] ?? null,
                ':title'  => $d['title'],
                ':synopsis' => $d['synopsis'] ?? null,
                ':poster_url' => $d['poster_url'] ?? null,
                ':trailer_url'=> $d['trailer_url'] ?? null,
                ':release_date'=> $d['release_date'] ?? null,
                ':total_episodes'=> $d['total_episodes'] ?? null,
                ':id' => $id,
            ]);
        }

        public function delete(int $id): bool {
            $st = $this->db->prepare("DELETE FROM animes WHERE id=:id");
            return $st->execute([':id'=>$id]);
        }

        /** Create if missing by MAL id; prefill from Jikan if possible */
        public function ensureFromMal(int $malId): int {
            if ($ex = $this->getByMalId($malId)) return (int)$ex['id'];

            $baseUrl = getenv('JIKAN_API_URL') ?: 'https://api.jikan.moe/v4';
            $json = cachedGet("{$baseUrl}/anime/{$malId}");
            $title = "MAL #{$malId}";
            $synopsis = $poster = $release = null;
            $total = null;

            if ($json) {
                $d = json_decode($json, true)['data'] ?? null;
                if ($d) {
                    $title = $d['title'] ?? $title;
                    $poster = $d['images']['jpg']['large_image_url'] ?? null;
                    $synopsis = $d['synopsis'] ?? null;
                    $total = $d['episodes'] ?? null;
                    $release = !empty($d['aired']['from']) ? substr($d['aired']['from'], 0, 10) : null;
                }
            }
            return $this->create([
                'mal_id' => $malId,
                'title' => $title,
                'synopsis' => $synopsis,
                'poster_url' => $poster,
                'trailer_url' => null,
                'release_date' => $release,
                'total_episodes' => $total,
            ]);
        }

        /* ====== EPISODES ====== */
        public function episodesByAnimeId(int $animeId): array {
            $st = $this->db->prepare("SELECT * FROM anime_episodes WHERE anime_id=:id ORDER BY ep_no ASC");
            $st->execute([':id'=>$animeId]);
            return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }

        public function episodesByMalId(int $malId): array {
            $sql = "SELECT e.*
                    FROM anime_episodes e
                    JOIN animes a ON a.id = e.anime_id
                    WHERE a.mal_id = :mid
                    ORDER BY e.ep_no ASC";
            $st = $this->db->prepare($sql);
            $st->execute([':mid'=>$malId]);
            return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }

        public function addEpisode(int $animeId, int $epNo, string $title, ?string $streamUrl=null, ?int $duration=null): bool {
            $st = $this->db->prepare("
                INSERT INTO anime_episodes (anime_id, ep_no, title, stream_url, duration_seconds)
                VALUES (:aid, :no, :title, :url, :dur)
                ON CONFLICT (anime_id, ep_no) DO UPDATE SET
                title = EXCLUDED.title,
                stream_url = EXCLUDED.stream_url,
                duration_seconds = EXCLUDED.duration_seconds
            ");
            return $st->execute([
                ':aid'=>$animeId, ':no'=>$epNo, ':title'=>$title,
                ':url'=>$streamUrl, ':dur'=>$duration
            ]);
        }

        public function deleteEpisode(int $id): bool {
            $st = $this->db->prepare("DELETE FROM anime_episodes WHERE id=:id");
            return $st->execute([':id'=>$id]);
        }

        public function listLatestMapped(int $limit = 12): array {
            $sql = "
                SELECT id, mal_id, title, poster_url, release_date, total_episodes, created_at, updated_at
                FROM animes
                ORDER BY COALESCE(updated_at, created_at) DESC
                LIMIT :limit
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            // Jikan benzeri yapı + added_ts
            $out = [];
            foreach ($rows as $r) {
                $out[] = [
                    'local_id' => (int)$r['id'],
                    'mal_id'   => $r['mal_id'] !== null ? (int)$r['mal_id'] : null,
                    'title'    => $r['title'],
                    'images'   => ['jpg' => ['image_url' => $r['poster_url']]],
                    'episodes' => $r['total_episodes'],
                    'members'  => 0,
                    'aired'    => [
                        'string' => $r['release_date'] ?? null,
                        'from'   => $r['release_date'] ?? null,
                    ],
                    'added_ts' => strtotime($r['updated_at'] ?? $r['created_at'] ?? 'now'),
                    'source'   => 'local',
                ];
            }
            return $out;
        }

    }
