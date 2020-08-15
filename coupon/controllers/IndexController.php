<?php
namespace Plugins\Coupon;

// Disable direct access
if (!defined('APP_VERSION'))
    die("Yo, what's up?");

/**
 * Index Controller
 */
class IndexController extends \Controller
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
        if (!$AuthUser){
            header("Location: ".APPURL."/login");
            exit;
        } else if ($AuthUser->isExpired()) {
            header("Location: ".APPURL."/expired");
            exit;
        }

        $user_modules = $AuthUser->get("settings.modules");

        if (!is_array($user_modules) || !in_array(self::IDNAME, $user_modules)) {
            // Module is not accessible to this user
            header("Location: ".APPURL."/post");
            exit;
        }


        // Actions
        if (\Input::post("action") == "save") {
            return $this->applyCoupon();
        }

        $this->view(PLUGINS_PATH."/".self::IDNAME."/views/index.php", null);
    }

    public function applyCoupon()
    {

        $AuthUser = $this->getVariable("AuthUser");
        $Coupon = \Input::post("coupon");
        if(empty($Coupon)){
            $this->resp->msg = __("Please enter coupon code.");
            $this->jsonecho();
        }
        $query = \DB::table(TABLE_PREFIX."coupons")
        ->where("code", "=", $Coupon)
        ->limit(1)
        ->select("*");

        if($query->count() == 1 ) {

            $CouponData = $query->first();

            if($CouponData->status == 2) {
                $this->resp->msg = __("This coupon is already used");
                $this->jsonecho();
            }

            if($CouponData->expire_days == "-1") {
                // for lifetime
                $expire_date =  "2050-12-12 23:59:00";
            }   else {
                if($AuthUser->get("package_id") == $CouponData->package_id){
                    // for existing package
                    $expire_date =  date('Y-m-d H:i:s', strtotime($AuthUser->get("expire_date")." +$CouponData->expire_days days"));
                }   else {
                    $expire_date = date('Y-m-d H:i:s', strtotime("+$CouponData->expire_days days"));
                }
            }

            $Package = \Controller::model("Package", $CouponData->package_id);
            if ((int)$CouponData->package_id == 0) {
                $TrialPackage = \Controller::model("GeneralData", "free-trial");
                $settings = json_decode($TrialPackage->get("data"), true);
                unset($settings["size"]);
                $settings = json_encode($settings);
            } else {
                $settings = $Package->get("settings");
            }

            $id = \DB::table(TABLE_PREFIX."users")
            ->where("id", "=", $AuthUser->get("id"))
            ->update(array(
                "package_id" => $CouponData->package_id,
                "expire_date" => $expire_date,
                "package_subscription"=>1,
                "settings" => $settings
            ));

            $id = \DB::table(TABLE_PREFIX."coupon_user")
            ->insert(array(
                "user_id" => $AuthUser->get("id"),
                "coupon_id" => $CouponData->id,
                "created_at" => date('Y-m-d H:i:s'),
            ));

            $id = \DB::table(TABLE_PREFIX."coupons")
            ->where("code", "=", $Coupon)
            ->update(array(
                "status" => 2,
            ));

            $this->resp->result = 1;
            $this->resp->msg = __("Coupon applied!");
            $this->jsonecho();

        }   else {
            $this->resp->msg = __("Invalid coupon!");
            $this->jsonecho();
        }

    }
}
