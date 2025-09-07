<?php

    function asset($path) {
        return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
    }

    function cachedGet(string $url, int $ttl = 3600): string|false {
        $ttl = getenv('API_CACHE_TTL') ?: $ttl;
        $key = '/tmp/api_' . md5($url);
        if (file_exists($key) && time() - filemtime($key) < $ttl) {
            return file_get_contents($key);
        }
        $data = @file_get_contents($url);
        if ($data) file_put_contents($key, $data);
        return $data;
    }

    /** Always start session before touching $_SESSION */
    function ensureSession(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /** Normalize role strings (e.g., "Super Admin", "super-admin" -> "super_admin") */
    function normalize_role(?string $role): string {
        $role = strtolower(trim((string)$role));
        $role = str_replace(['-', ' '], '_', $role);
        return $role;
    }

    /** Require any admin (keeps your existing behavior) */
    function requireAdmin(): void {
        ensureSession();
        if (empty($_SESSION['user_id'])) {
            header('Location: /login'); exit;
        }
        require_once __DIR__ . '/../models/AdminModel.php';
        $am = new AdminModel();
        if (!$am->isAdmin((int)$_SESSION['user_id'])) {
            http_response_code(403);
            echo "Forbidden (admin only)";
            exit;
        }
    }

    /** Require super admin role */
    function requireSuperAdmin(): void {
        requireAdmin(); // ensures admin
        require_once __DIR__ . '/../models/AdminModel.php';
        $am = new AdminModel();
        $role = normalize_role($am->getRoleByUserId((int)$_SESSION['user_id']) ?? '');
        if (!in_array($role, ['superadmin', 'super_admin'], true)) {
            $_SESSION['flash_error'] = 'Unauthorized.';
            header('Location: /admin'); exit;
        }
    }

    /** Optional helper to check in views */
    function isSuperAdmin(): bool {
        ensureSession();
        if (empty($_SESSION['user_id'])) return false;
        require_once __DIR__ . '/../models/AdminModel.php';
        $am = new AdminModel();
        $role = normalize_role($am->getRoleByUserId((int)$_SESSION['user_id']) ?? '');
        return in_array($role, ['superadmin', 'super_admin'], true);
    }
