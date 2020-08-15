<?php
namespace Plugins\Pixedit;

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
    const IDNAME = 'pixedit';

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
            header("Location: ".APPURL."/post");
            exit;
        }

        // Get accounts
        $Accounts = \Controller::model("Accounts");
        $Accounts->where("user_id", "=", $AuthUser->get("id"))
                 ->orderBy("id","DESC")
                 ->fetchData();

        $this->setVariable("Accounts", $Accounts);
		
		$Settings = \Controller::model("GeneralData", "plugin-pixedit-settings");
		
		if($Settings->get("data.endpoint") === null){
			$Settings->set("data.endpoint", "https://pixie.vebto.com/final/")
					 ->save();			
		}	
		
        $this->setVariable("Settings", $Settings);
		
        $this->view(PLUGINS_PATH."/".self::IDNAME."/views/index.php", null);
    }
}