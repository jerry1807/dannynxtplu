<?php if (!defined('APP_VERSION')) die("Yo, what's up?");  ?>
<!DOCTYPE html>
<html lang="<?= ACTIVE_LANG ?>">
    <head>
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
        <link rel="stylesheet" type="text/css" href="<?= PLUGINS_URL."/".$idname."/assets/css/core.css?v=".VERSION ?>">

        <title><?= htmlchars($Account->get("username")) ?></title>
    </head>

    <body>
        <?php 
            $Nav = new stdClass;
            $Nav->activeMenu = $idname;
            require_once(APPPATH.'/views/fragments/navigation.fragment.php');
        ?>

        <?php 
            $TopBar = new stdClass;
            $TopBar->title = htmlchars($Account->get("username"));
            $TopBar->btn = false;
            require_once(APPPATH.'/views/fragments/topbar.fragment.php'); 
        ?>

        <?php require_once(__DIR__.'/fragments/analyzer.fragment.php'); ?>
        
        <script type="text/javascript" src="<?= APPURL."/assets/js/plugins.js?v=".VERSION ?>"></script>
        <?php require_once(APPPATH.'/inc/js-locale.inc.php'); ?>
        <script type="text/javascript" src="<?= APPURL."/assets/js/core.js?v=".VERSION ?>"></script>
        <script type="text/javascript" src="<?= PLUGINS_URL."/".$idname."/assets/js/Chart.bundle.min.js?v=".VERSION ?>"></script>
        <script type="text/javascript" src="<?= PLUGINS_URL."/".$idname."/assets/js/core.js?v=".VERSION ?>"></script>
        <script type="text/javascript" charset="utf-8">
            $(function(){
                // Initialize the datepicker
                Analyzer.DatePicker();

                // Initialize the charts
                let followers_chart = new Chart(document.getElementById('followers_chart').getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: <?= $logsData->chart_labels ?>,
                        datasets: [{
                            label: '<?= __("Followers Evolution Chart") ?>',
                            data: <?= $logsData->chart_followers ?>,
                            backgroundColor: '#f71748',
                            borderColor: '#f71748',
                            fill: false
                        }]
                    },
                    options: {
                        tooltips: {
                            mode: 'index',
                            intersect: false
                        },
                        title: {
                            display: false
                        },
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });

                let following_chart = new Chart(document.getElementById('following_chart').getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: <?= $logsData->chart_labels ?>,
                        datasets: [{
                            label: '<?= __("Followings Evolution Chart") ?>',
                            data: <?= $logsData->chart_following ?>,
                            backgroundColor: '#6d38f7',
                            borderColor: '#6d38f7',
                            fill: false
                        }]
                    },
                    options: {
                        tooltips: {
                            mode: 'index',
                            intersect: false
                        },
                        title: {
                            display: false
                        },
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });


                let average_engagement_rate_chart = new Chart(document.getElementById('average_engagement_rate_chart').getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: <?= $logsData->chart_labels ?>,
                        datasets: [{
                            label: '<?= __("Average Engagement Rate Chart") ?>',
                            data: <?= $logsData->chart_average_engagement_rate ?>,
                            backgroundColor: '#f71748',
                            borderColor: '#f71748',
                            fill: false
                        }]
                    },
                    options: {
                        tooltips: {
                            mode: 'index',
                            intersect: false
                        },
                        title: {
                            display: false
                        },
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });

            })
        </script>

        <!-- GOOGLE ANALYTICS -->
        <?php require_once(APPPATH.'/views/fragments/google-analytics.fragment.php'); ?>
    </body>
</html>