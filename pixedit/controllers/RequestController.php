<?php
namespace Plugins\Pixedit;
// Disable direct access
if (!defined('APP_VERSION'))
    die("Yo, what's up?");

/**
 * Index Controller
 */
class RequestController extends \Controller
{

    public $settings = null;
    /**
     * Process
     */
    public function process()
    {

        $AuthUser = $this->getVariable("AuthUser");
        $Route = $this->getVariable("Route");

        if (!$AuthUser){
          header("Location: ".APPURL."/post");
          exit;
        } else if ($AuthUser->isExpired()) {
          header("Location: ".APPURL."/post");
          exit;
        }

        $user_modules = $AuthUser->get("settings.modules");

        if (!is_array($user_modules) || !in_array("pixedit", $user_modules)) {
          header("Location: ".APPURL."/post");
          exit;
        }

        if($Route->params->action == "init"){

          $this->getAccounts();
		      $this->getSettings();
          $this->resp->result = 1;
          $this->jsonecho();

        }else if($Route->params->action == "savepicture"){

          $this->savePicture();

        }else{

          $this->resp->msg = "Error";
          $this->resp->result = 0;
          $this->jsonecho();

        }

    }

  public function savePicture(){
    $AuthUser = $this->getVariable("AuthUser");
    $params = json_decode(file_get_contents('php://input'));
    $data = $params->data;

    if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
      $data = substr($data, strpos($data, ',') + 1);
      $type = strtolower($type[1]); // jpg, png, gif
  
      if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
          throw new \Exception('invalid image type');
      }
  
      $data = base64_decode($data);
  
      if ($data === false) {
          throw new \Exception('base64_decode failed');
      }
    } else {
        throw new \Exception('did not match data URI with image data');
    }

    $fileurl = APPURL."/assets/uploads/temp/" . $params->name . ".{$type}";

    $tmpfilepath = TEMP_PATH. "/". $params->name . ".{$type}";

    file_put_contents($tmpfilepath, $data); 
    
    $connector_options = [
      "host" => DB_HOST,
      "database" => DB_NAME,
      "username" => DB_USER,
      "password" => DB_PASS,
      "charset" => DB_ENCODING,
      "table_name" => TABLE_PREFIX.TABLE_FILES,
      "opions" => array(
          \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
      ),

      "user_id" => $AuthUser->get("id")
  ];
  
  $Connector = new \OneFileManager\Connector;
  $Connector->setOptions($connector_options)->init();

  /**
   * File manager configurations
   */
  $path_to_users_directory = ROOTPATH."/assets/uploads/"
                           . $AuthUser->get("id")
                           . "/";

  if (!file_exists($path_to_users_directory)) {
      mkdir($path_to_users_directory);
  } 

  $user_dir_url = APPURL."/assets/uploads/"
                . $AuthUser->get("id")
                . "/";

  $options = [
      "path" => $path_to_users_directory,
      "url" => $user_dir_url,

      "allow" => array("jpeg", "jpg", "png", "mp4"),
      "queue_size" => 10
  ];

  if ($AuthUser->get("settings.storage.file") >= 0) {
      $options["max_file_size"] = (double)$AuthUser->get("settings.storage.file") * 1024*1024;
  }

  if ($AuthUser->get("settings.storage.total") >= 0) {
      $options["max_storage_size"] = (double)$AuthUser->get("settings.storage.total") * 1024*1024;
  }

  $p = new \stdClass();
  $p->cmd = "upload";
  $p->callback = "";
  $p->type = "url";
  $p->file = $fileurl;

  $FileManager = new \OneFileManager\FileManager;
  $FileManager->setOptions($options)
              ->setConnector($Connector)
              ->run($p);

  unlink($tmpfilepath);

  }
    
	public function getSettings(){
		$AuthUser = $this->getVariable("AuthUser");
		$Settings = namespace\settings();
			
		$this->resp->searchfield = $Settings->get("data.searchfield");
		
		$this->resp->language = $AuthUser->get("preferences.language");
		
		$this->resp->package_id = $AuthUser->get("package_id"); 
		
		if($Settings->get("data.searchfield_trials")){
			if($this->resp->package_id == 3){
				$this->resp->searchfield = false;
			}
		}
		
	}
	
    public function getAccounts(){

      $q3 = 'SELECT username FROM '.TABLE_PREFIX.'accounts WHERE user_id = '.$this->getVariable("AuthUser")->get("id");
      $query = \DB::query($q3);
	  
	  $this->resp->accounts = $query->get();	  
	  
    }
	
}
