<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php bloginfo() ?></title>

    <head>
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <meta charset="utf-8">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'> -->
        <title><?php bloginfo() ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="<?php bloginfo() ?>" name="keywords">
        <meta content="<?php bloginfo() ?>" name="description">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="fontawesome/css/all.css">
        <?php wp_head() ?>
    </head>

<body <?php body_class() ?>>
    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v15.0&appId=1527273154354707&autoLogAppEvents=1" nonce="O8xBK4qj"></script>

    <nav class="navbar   navbar-expand-lg mb-0  pb-0  sticky-top bg-light">
        <div class="container-fluid">
            <a href="<?php get_template_directory_uri() ?>" class="navbar-brand custom-logo">
                <?php the_custom_logo() ?>
            </a>
            <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" style="outline:none ;border:none;">
                <span class="navbar-toggler-icon openbtn" onclick="openNav()"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarCollapse">
                <?php
                wp_nav_menu(array(
                    'theme_location'  => 'primary',
                    'container_class' => 'ms-auto  p-1 p-lg-0 ',
                    'container_id'    => 'primarymenu',
                    'menu_class'      => 'navbar-nav',
                    'sub_menu_class' => "submenu",
                    'a_class' => 'nav-link p-2 ',
                    'li_class' => 'nav-item ml-1',
                    'active_class' => 'active',
                    'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
                    'walker'          => new bootstrap_5_wp_nav_menu_walker(),

                ));
                ?>

            </div>
        </div>
    </nav>

    <!-- Navbar End -->