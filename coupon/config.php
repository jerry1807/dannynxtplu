<?php if (!defined('APP_VERSION')) die("Yo, what's up?"); ?>
<?php 
    return [
        "idname" => "coupon",
        "plugin_name" => "NextCoupon",
        "plugin_uri" => "https://novashock.net",
        "author" => "Novashock Inc.",
        "author_uri" => "https://novashock.net",
        "version" => "1.0",
        "desc" => "Generate coupons for available packages and allow user to get that coupon.",
        "icon_style" => "background-color: #00DBDE; background: linear-gradient(136.03deg, #00DBDE 0%, #FC00FF 100%); color: #fff;",
        "settings_page_uri" => APPURL."/e/coupon/settings"
    ];
