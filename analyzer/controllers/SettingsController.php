<?php
namespace Plugins\Analyzer;

// Disable direct access
if (!defined('APP_VERSION')) 
    die("Yo, what's up?");

/**
 * Analyzer Controller
 *
 */
class SettingsController extends \Controller
{
    /**
     * idname of the plugin for internal use
     */
    const IDNAME = 'analyzer';


    /**
     * Process
     * @return null
     */
    public function process()
    {
        $AuthUser = $this->getVariable("AuthUser");
        $this->setVariable("idname", self::IDNAME);

        // Auth
        if (!$AuthUser || !$AuthUser->isAdmin()){
            header("Location: ".APPURL."/login");
            exit;
        } else if ($AuthUser->isExpired()) {
            header("Location: ".APPURL."/expired");
            exit;
        }

        // Plugin settings
        $this->setVariable("Settings", namespace\settings());

        // Actions
        if (\Input::post("action") == "save") {
            $this->save();
        }

        $this->view(PLUGINS_PATH."/".self::IDNAME."/views/settings.php", null);
    }


    /**
     * Save plugin settings
     * @return boolean 
     */
    private function save()
    {  
        $Settings = $this->getVariable("Settings");
        if (!$Settings->isAvailable()) {
            // Settings is not available yet
            $Settings->set("name", "plugin-".self::IDNAME."-settings");
        }

        // Interval settings
        $checkInterval = (int) \Input::post("check-interval");
        

        // Save settings
        $Settings->set("data.checkInterval", $checkInterval)
                 ->save();

        $this->resp->result = 1;
        $this->resp->msg = __("Changes saved!");
        $this->jsonecho();

        return $this;
    }
}
