<?php 
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
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
                        <a href="./">
                            <img class="mt-2" src="<?= asset('img/logo.png') ?>" alt="" style="height: 28px; width: auto;">
                        </a>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="header__nav">
                        <nav class="header__menu mobile-menu">
                            <ul>
                                <li><a href="./">Homepage</a></li>
                                <li><a href="./categories">Categories <span class="arrow_carrot-down"></span></a>
                                    <ul class="dropdown">
                                        <li><a href="./categories">Magic</a></li>
                                        <li><a href="./categories">Adventure</a></li>
                                        <li><a href="./categories">Action</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="header__right d-flex align-items-center justify-content-end">
                        <a href="#" class="search-switch mr-3"><span class="icon_search"></span></a>
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