<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Panel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="/css/admin.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</head>
<?php
  // aktif menü için basit helper
  $current = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
  $isActive = function(string $path) use ($current) {
    return strpos($current, $path) === 0 ? 'active' : '';
  };
?>
<body>
  <!-- Topbar -->
  <nav class="navbar navbar-dark bg-dark fixed-top admin-topbar">
    <button class="btn btn-outline-light d-lg-none" id="sidebarToggle" aria-label="Toggle sidebar">
      ☰
    </button>
    <a class="navbar-brand ml-2" href="/admin">LOGO</a>
    <ul class="navbar-nav ml-auto flex-row align-items-center">
      <li class="nav-item mr-3"><a class="nav-link" href="/">Go Back to Site</a></li>

      <?php if (!empty($_SESSION['user_name'])): ?>
        <li class="nav-item mr-3">
          <span class="nav-link"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
        </li>
        <li class="nav-item">
          <a class="btn btn-danger-rose" href="/logout">Logout</a>
        </li>
      <?php else: ?>
        <li class="nav-item"><a class="nav-link" href="/login">Login</a></li>
      <?php endif; ?>
    </ul>
  </nav>


  <div class="admin-wrap">
    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
      <div class="admin-sidebar__inner">
        <div class="admin-sidebar__section text-uppercase text-muted small px-3 mb-2">Navigation</div>
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link <?= $isActive('/admin') && $current === '/admin' ? 'active' : '' ?>" href="/admin">
              <span class="oi mr-2"></span> Dashboard
            </a>
          </li>
          <li class="nav-item mt-2">
            <div class="text-uppercase text-muted small px-3">Manage</div>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $isActive('/admin/admins') ?>" href="/admin/admins">
              <span class="oi mr-2"></span> Admins
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $isActive('/admin/users') ?>" href="/admin/users">
              <span class="oi mr-2"></span> Users
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $isActive('/admin/comments') ?>" href="/admin/comments">
              <span class="oi mr-2"></span> Comments
            </a>
          </li>
        </ul>
      </div>
    </aside>

    <!-- Content -->
    <main class="admin-content">
      <div class="container-fluid">
