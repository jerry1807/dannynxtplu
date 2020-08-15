<?php

namespace Plugins\Coupon;

// Disable direct access
if (!defined('APP_VERSION'))
    die("Yo, what's up?");

define('TABLE_NAME', 'coupons');

/**
 * Index Controller
 */
class ExportController extends \Controller
{
    /**
     * idname of the plugin for internal use
     */
    const IDNAME = 'coupon';


    /**
     * Process
     */
    public function process()
    {
        $AuthUser = $this->getVariable("AuthUser");
        $this->setVariable("idname", self::IDNAME);

        // Auth
        if (!$AuthUser || !$AuthUser->isAdmin()) {
            header("Location: " . APPURL . "/login");
            exit;
        } else if ($AuthUser->isExpired()) {
            header("Location: " . APPURL . "/expired");
            exit;
        }

        // Plugin settings
        $this->setVariable("Settings", namespace\settings());

        /**
         * Coupons fetch, all. Via Controller::model
         */
        // Get Activity Log
        $Coupons = \Controller::model([PLUGINS_PATH . "/" . self::IDNAME . "/models/CouponsModel.php", __NAMESPACE__ . "\CouponsModel"]);

        $package_id = (int)\Input::post("packages");


        $Coupons
//            ->setPageSize(20)
//            ->setPage(\Input::get("page"))
//            ->join(TABLE_PREFIX . 'coupon_user', TABLE_PREFIX . TABLE_NAME . '.id', '!=', TABLE_PREFIX . 'coupon_user' . '.coupon_id')
//            ->orderBy(TABLE_PREFIX . TABLE_NAME . ".created_at", "DESC")
            ->where('status', '1')
            ->where('package_id', $package_id)
            ->orderBy(TABLE_PREFIX . TABLE_NAME . ".id", "ASC")
            ->fetchData();
        $as = [PLUGINS_PATH . "/" . self::IDNAME . "/models/CouponModel.php", __NAMESPACE__ . "\CouponModel"];

        $couponString = '';
        foreach ($Coupons->getDataAs($as) as $c):
            $couponString .= $c->get('code') . PHP_EOL;
        endforeach;

        // Set the limit to 50 MB.
        $fiveMBs = 50 * 1024 * 1024;
        $fp = fopen("php://temp/maxmemory:$fiveMBs", 'r+');
        fputs($fp, $couponString);

        // Read what we have written.
        rewind($fp);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . ('coupons.txt') . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
//        header('Content-Length: ' . filesize($filepath));
        flush(); // Flush system output buffer
        echo stream_get_contents($fp);
        exit;

    }
}