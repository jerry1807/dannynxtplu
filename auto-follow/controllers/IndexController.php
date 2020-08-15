<?php
namespace Plugins\AutoFollow;

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
    const IDNAME = 'auto-follow';


    /**
     * Process
     */
    public function process()
    {
        $AuthUser = $this->getVariable("AuthUser");
        $this->setVariable("idname", self::IDNAME);
        $Route = $this->getVariable("Route");

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

        if(isset($Route->params->action) && $Route->params->action == "byName"){
          $ordertable = "accounts.";
    			$orderBy = "username";
    			$order = "ASC";

    			$this->setVariable("Order", "name");
    			$this->setVariable("OrderL", "e/auto-follow");

    		}else if(isset($Route->params->action) && $Route->params->action == "byStatus"){

          $ordertable = "auto_follow_schedule.";
          $orderBy = "is_active";
    			$order = "ASC";

    			$this->setVariable("Order", "status");
    			$this->setVariable("OrderL", "e/auto-follow");

        }else{
          $ordertable = "accounts.";
    			$orderBy = "id";
    			$order = "DESC";
    			$this->setVariable("Order", "date");
    			$this->setVariable("OrderL", "e/auto-follow");
    		}

        $q4 = "SELECT ".TABLE_PREFIX."accounts.*, ".TABLE_PREFIX."auto_follow_schedule.is_active FROM ".TABLE_PREFIX."accounts LEFT JOIN ".TABLE_PREFIX."auto_follow_schedule ON ".TABLE_PREFIX."accounts.id = ".TABLE_PREFIX."auto_follow_schedule.account_id WHERE ".TABLE_PREFIX."accounts.user_id =" . $AuthUser->get("id") . " ORDER BY ".TABLE_PREFIX.$ordertable . $orderBy . " " .$order;
        $query = \DB::query($q4);
        $accounts =  $query->get();
        $this->setVariable("Accounts", $accounts);

        $this->view(PLUGINS_PATH."/".self::IDNAME."/views/index.php", null);
    }
}
