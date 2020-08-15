<?php
namespace Plugins\Pixedit;

if (!defined('APP_VERSION')) 
    die("Yo, what's up?");

class SettingsController extends \Controller
{

    const IDNAME = 'pixedit';

    public function process()
    {
        $AuthUser = $this->getVariable("AuthUser");
        $this->setVariable("idname", self::IDNAME);

        if (!$AuthUser || !$AuthUser->isAdmin()){
            header("Location: ".APPURL."/login");
            exit;
        } else if ($AuthUser->isExpired()) {
            header("Location: ".APPURL."/expired");
            exit;
        }
		
		$Settings = \Controller::model("GeneralData", "plugin-pixedit-settings");
        $this->setVariable("Settings", $Settings);	
		
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

        $Settings->set("data.endpoint", \Input::post("endpoint"))
                 ->set("data.advanced",\Input::post("advanced"))
                 ->set("data.theme",\Input::post("theme"))
                 ->set("data.headerbar",\Input::post("headerbar"))
                 ->set("data.advanced",\Input::post("advanced"))
                 ->set("data.gfapikey",\Input::post("gfapikey"))
                 ->set("data.save_trial",\Input::post("save_trial"))
                 ->set("data.watermark",\Input::post("watermark"))
                 ->set("data.watermarktext",\Input::post("watermarktext"))
                 ->set("data.dialog",\Input::post("dialog"))
                 ->set("data.googleanalytics",\Input::post("googleanalytics"))
                 ->set("name","plugin-pixedit-settings")
                 ->save();

        $this->resp->result = 1;
        $this->resp->msg = __("Changes saved!");
        $this->jsonecho();

        return $this;
    }
}
