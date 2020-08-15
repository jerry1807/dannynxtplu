<?php

namespace Plugins\Coupon;

// Disable direct access
if (!defined('APP_VERSION'))
    die("Yo, what's up?");

define('TABLE_NAME', 'coupons');

/**
 * Settings Controller
 */
class SettingsController extends \Controller
{
    /**
     * idname of the plugin for internal use
     */
    const IDNAME = 'coupon';


    /**
     * Process
     * @return null
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

        // Actions
        if (\Input::post("action") == "save") {
            $this->save();
        }

        // Get accounts
        $Packages = \Controller::model("Packages");
        $Packages->setPageSize(20)
            ->orderBy("title", "ASC")
            ->fetchData();

        /**
         * Coupons fetch, all. Via Controller::model
         */
        $Coupons = \Controller::model([PLUGINS_PATH . "/" . self::IDNAME . "/models/CouponsModel.php", __NAMESPACE__ . "\CouponsModel"]);
        $package_id = 0;
        foreach($Packages->getDataAs("Package") as $p ) {
            if($p->get('id') == \Input::get("package") )
            $package_id = \Input::get("package") ; 
        }

        if($package_id){
            $Coupons->setPageSize(20)
            ->setPage(\Input::get("page"))
            ->join(TABLE_PREFIX.'packages', TABLE_PREFIX.TABLE_NAME.'.package_id', '=', TABLE_PREFIX.'packages'.'.id')
            ->orderBy(TABLE_PREFIX.TABLE_NAME.".created_at", "DESC")
            ->orderBy(TABLE_PREFIX.TABLE_NAME.".id", "DESC")
            ->where('package_id', $package_id)
            ->fetchData();
        }   else {
            $Coupons->setPageSize(20)
                ->setPage(\Input::get("page"))
                ->join(TABLE_PREFIX.'packages', TABLE_PREFIX.TABLE_NAME.'.package_id', '=', TABLE_PREFIX.'packages'.'.id')
                ->orderBy(TABLE_PREFIX.TABLE_NAME.".created_at", "DESC")
                ->orderBy(TABLE_PREFIX.TABLE_NAME.".id", "DESC")
                ->fetchData();
        }
        $as = [PLUGINS_PATH . "/" . self::IDNAME . "/models/CouponModel.php", __NAMESPACE__ . "\CouponModel"];
        

        $this->setVariable("Packages", $Packages);
        $this->setVariable("Coupons", $Coupons);
        $this->setVariable("CouponsAs", $as);
        $this->view(PLUGINS_PATH . "/" . self::IDNAME . "/views/settings.php", null);
    }


    /**
     * Save plugin settings
     * @return boolean
     */
    private function save()
    {

        $no_of_coupons = (int)\Input::post("no_of_coupons");
        $package_expiry_days = (int)\Input::post("package_expiry_days");
        $packages = (int)\Input::post("packages");

        if ($no_of_coupons > 2000 || $no_of_coupons < 1) {
            $this->resp->result = 0;
            $this->resp->msg = __("No of Coupons should be between 1 and 2000!");
            $this->jsonecho();
            return $this;
        }


        if ($package_expiry_days != -1 && $package_expiry_days > 6000 || $package_expiry_days != -1 && $package_expiry_days < 1) {
            $this->resp->result = 0;
            $this->resp->msg = __("Package expiry days should be between 1 and 6000 or -1 for lifetime!");
            $this->jsonecho();
            return $this;
        }

        $this->resp->result = 0;
        $this->resp->msg = __("Coupons generation failed!");


        if ($this->generate_coupons($packages, $no_of_coupons, $package_expiry_days)) {
            $this->resp->result = 1;
            $this->resp->msg = __("Coupons generated! Please refresh the page.");
        }

        $this->jsonecho();

        return $this;
    }

    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param  int $length
     * @return string
     */
    private function str_random($length = 16)
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }

    private function generate_coupons($package_id, $no_of_coupons = 1, $package_expiry_days=1)
    {
        try {
            $sql = " INSERT INTO `" . TABLE_PREFIX . "coupons` ";
            $sql .= " (`id`, `package_id`, `code`,`expire_days`, `created_at`, `updated_at`) ";
            $sql .= " VALUES  ";

            for ($i = 0; $i < $no_of_coupons; $i++) {
                $coupon = strtoupper($this->str_random(32));
                $sql .= " (NULL, '$package_id', '$coupon', '$package_expiry_days', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),  ";
            }

            $sql = rtrim(trim($sql), ',');
            $sql = $sql . ';';

            $pdo = \DB::pdo();
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
