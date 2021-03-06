<?php if (!defined('APP_VERSION')) die("Yo, what's up?");  ?>
<!DOCTYPE html>
<html lang="<?= ACTIVE_LANG ?>" ng-app="pixeditApp">
    <head>
        <base href="<?= PLUGINS_URL."/pixedit/pixie/"?>" />
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
        <meta name="theme-color" content="#fff">

        <meta name="description" content="<?= site_settings("site_description") ?>">
        <meta name="keywords" content="<?= site_settings("site_keywords") ?>">

        <link rel="icon" href="<?= site_settings("logomark") ? site_settings("logomark") : APPURL."/assets/img/logomark.png" ?>" type="image/x-icon">
        <link rel="shortcut icon" href="<?= site_settings("logomark") ? site_settings("logomark") : APPURL."/assets/img/logomark.png" ?>" type="image/x-icon">
        <link rel="stylesheet" type="text/css" href="<?= APPURL."/assets/css/plugins.css?v=".VERSION ?>">
        <link rel="stylesheet" type="text/css" href="<?= APPURL."/assets/css/core.css?v=".VERSION ?>">
        <link rel="stylesheet" type="text/css" href="<?= PLUGINS_URL."/pixedit/assets/css/style.css"?>">
        <link rel="stylesheet" type="text/css" href="<?= PLUGINS_URL."/pixedit/pixie/styles.min.css?v8"?>">

        <link rel="stylesheet" type="text/css" href="<?= PLUGINS_URL."/pixedit/assets/js/toaster.css"?>">
        <script src="<?= PLUGINS_URL."/pixedit/assets/js/angular.min.js"?>"></script>

        <title><?= __("Image Editor") ?></title>

    </head>

    <body ng-controller="PixeditController">

    <toaster-container toaster-options="{'time-out': 7000, 'position-class': 'toast-bottom-right'}"></toaster-container>
        <?php
            $Nav = new stdClass;
            $Nav->activeMenu = "pixedit";
            require_once(APPPATH.'/views/fragments/navigation.fragment.php');
        ?>

        <?php
            $TopBar = new stdClass;
            $TopBar->title = __("Image Editor");
            $TopBar->btn = true;
            require_once(APPPATH.'/views/fragments/topbar.fragment.php');
        ?>

        <?php
            require_once(__DIR__.'/fragments/index.fragment.php');

        ?>

        <script src="<?= APPURL."/assets/js/plugins.js?v=".VERSION ?>"></script>
        <script src="<?= PLUGINS_URL."/pixedit/assets/js/toaster.js"?>"></script>
        <script src="<?= PLUGINS_URL."/pixedit/assets/js/app.js"?>"></script>

        <?php require_once(APPPATH.'/inc/js-locale.inc.php'); ?>
        <script src="<?= APPURL."/assets/js/core.js?v=".VERSION ?>"></script>
        <?php if (!$Settings->get("data.googleanalytics")): ?>  
        <?php require_once(APPPATH.'/views/fragments/google-analytics.fragment.php'); ?>
         <?php endif; ?>
        
    </body>
</html>
