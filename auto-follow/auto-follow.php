<?php
namespace Plugins\AutoFollow;
const IDNAME = "auto-follow";

// Disable direct access
if (!defined('APP_VERSION'))
    die("Yo, what's up?");


/**
 * Event: plugin.install
 */
function install($Plugin)
{
    if ($Plugin->get("idname") != IDNAME) {
        return false;
    }

    $sql = "CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."auto_follow_schedule` (
                `id` INT NOT NULL AUTO_INCREMENT ,
                `user_id` INT NOT NULL ,
                `account_id` INT NOT NULL ,
                `target` TEXT NOT NULL ,
                `speed` VARCHAR(20) NOT NULL ,
                `daily_pause` BOOLEAN NOT NULL,
                `daily_pause_from` TIME NOT NULL,
                `daily_pause_to` TIME NOT NULL,
                `is_active` BOOLEAN NOT NULL ,
                `schedule_date` DATETIME NOT NULL ,
                `end_date` DATETIME NOT NULL ,
                `last_action_date` DATETIME NOT NULL ,
                `data` TEXT NOT NULL,
                PRIMARY KEY (`id`),
                INDEX (`user_id`),
                INDEX (`account_id`)
            ) ENGINE = InnoDB;";

    $sql .= "CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."auto_follow_log` (
                `id` INT NOT NULL AUTO_INCREMENT ,
                `user_id` INT NOT NULL ,
                `account_id` INT NOT NULL ,
                `status` VARCHAR(20) NOT NULL,
                `followed_user_pk` VARCHAR(50) NOT NULL,
                `data` TEXT NOT NULL ,
                `date` DATETIME NOT NULL ,
                PRIMARY KEY (`id`),
                INDEX (`user_id`),
                INDEX (`account_id`),
                INDEX (`followed_user_pk`)
            ) ENGINE = InnoDB;";

    $sql .= "ALTER TABLE `".TABLE_PREFIX."auto_follow_schedule`
                ADD CONSTRAINT `".uniqid("ibfk_")."` FOREIGN KEY (`user_id`)
                REFERENCES `".TABLE_PREFIX."users`(`id`)
                ON DELETE CASCADE ON UPDATE CASCADE;";

    $sql .= "ALTER TABLE `".TABLE_PREFIX."auto_follow_schedule`
                ADD CONSTRAINT `".uniqid("ibfk_")."` FOREIGN KEY (`account_id`)
                REFERENCES `".TABLE_PREFIX."accounts`(`id`)
                ON DELETE CASCADE ON UPDATE CASCADE;";

    $sql .= "ALTER TABLE `".TABLE_PREFIX."auto_follow_log`
                ADD CONSTRAINT `".uniqid("ibfk_")."` FOREIGN KEY (`user_id`)
                REFERENCES `".TABLE_PREFIX."users`(`id`)
                ON DELETE CASCADE ON UPDATE CASCADE;";

    $sql .= "ALTER TABLE `".TABLE_PREFIX."auto_follow_log`
                ADD CONSTRAINT `".uniqid("ibfk_")."` FOREIGN KEY (`account_id`)
                REFERENCES `".TABLE_PREFIX."accounts`(`id`)
                ON DELETE CASCADE ON UPDATE CASCADE;";

    $pdo = \DB::pdo();
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}
\Event::bind("plugin.install", __NAMESPACE__ . '\install');



/**
 * Event: plugin.remove
 */
function uninstall($Plugin)
{
    if ($Plugin->get("idname") != IDNAME) {
        return false;
    }

    // Remove plugin settings
    $Settings = \Controller::model("GeneralData", "plugin-auto-follow-settings");
    $Settings->remove();

    // Remove plugin tables
    $sql = "DROP TABLE `".TABLE_PREFIX."auto_follow_schedule`;";
    $sql .= "DROP TABLE `".TABLE_PREFIX."auto_follow_log`;";

    $pdo = \DB::pdo();
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}
\Event::bind("plugin.remove", __NAMESPACE__ . '\uninstall');


/**
 * Add module as a package options
 * Only users with correct permission
 * Will be able to use module
 *
 * @param array $package_modules An array of currently active
 *                               modules of the package
 */
function add_module_option($package_modules)
{
    $config = include __DIR__."/config.php";
    ?>
        <div class="mt-15">
            <label>
                <input type="checkbox"
                       class="checkbox"
                       name="modules[]"
                       value="<?= IDNAME ?>"
                       <?= in_array(IDNAME, $package_modules) ? "checked" : "" ?>>
                <span>
                    <span class="icon unchecked">
                        <span class="mdi mdi-check"></span>
                    </span>
                    <?= __('Auto Follow') ?>
                </span>
            </label>
        </div>
    <?php
}
\Event::bind("package.add_module_option", __NAMESPACE__ . '\add_module_option');




/**
 * Map routes
 */
function route_maps($global_variable_name)
{
    // Settings (admin only)
    $GLOBALS[$global_variable_name]->map("GET|POST", "/e/".IDNAME."/settings/?", [
        PLUGINS_PATH . "/". IDNAME ."/controllers/SettingsController.php",
        __NAMESPACE__ . "\SettingsController"
    ]);

    // Index
    $GLOBALS[$global_variable_name]->map("GET|POST", "/e/".IDNAME."/?", [
        PLUGINS_PATH . "/". IDNAME ."/controllers/IndexController.php",
        __NAMESPACE__ . "\IndexController"
    ]);

    // Index
    $GLOBALS[$global_variable_name]->map("GET|POST", "/e/".IDNAME."/sort/[:action]?", [
        PLUGINS_PATH . "/". IDNAME ."/controllers/IndexController.php",
        __NAMESPACE__ . "\IndexController"
    ]);

    // Schedule
    $GLOBALS[$global_variable_name]->map("GET|POST", "/e/".IDNAME."/[i:id]/?", [
        PLUGINS_PATH . "/". IDNAME ."/controllers/ScheduleController.php",
        __NAMESPACE__ . "\ScheduleController"
    ]);

    // Log
    $GLOBALS[$global_variable_name]->map("GET|POST", "/e/".IDNAME."/[i:id]/log/?", [
        PLUGINS_PATH . "/". IDNAME ."/controllers/LogController.php",
        __NAMESPACE__ . "\LogController"
    ]);
}
\Event::bind("router.map", __NAMESPACE__ . '\route_maps');



/**
 * Event: navigation.add_special_menu
 */
function navigation($Nav, $AuthUser)
{
    $idname = IDNAME;
    include __DIR__."/views/fragments/navigation.fragment.php";
}
\Event::bind("navigation.add_special_menu", __NAMESPACE__ . '\navigation');



/**
 * Add cron task to follow new users
 */
function addCronTask()
{
    require_once __DIR__."/models/SchedulesModel.php";
    require_once __DIR__."/models/LogModel.php";


    // Get auto follow schedules
    $Schedules = new SchedulesModel;
    $Schedules->where("is_active", "=", 1)
              ->where("schedule_date", "<=", date("Y-m-d H:i:s"))
              ->where("end_date", ">=", date("Y-m-d H:i:s"))
              ->orderBy("last_action_date", "ASC")
              ->setPageSize(10) // required to prevent server overload
              ->setPage(1)
              ->fetchData();

    if ($Schedules->getTotalCount() < 1) {
        return false;
    }

    $settings = namespace\settings();
    $default_speeds = [
        "very_slow" => 1,
        "slow" => 2,
        "medium" => 3,
        "fast" => 4,
        "very_fast" => 5,
    ];
    $speeds = $settings->get("data.speeds");
    if (empty($speeds)) {
        $speeds = [];
    } else {
        $speeds = json_decode(json_encode($speeds), true);
    }
    $speeds = array_merge($default_speeds, $speeds);

    $as = [__DIR__."/models/ScheduleModel.php", __NAMESPACE__."\ScheduleModel"];
    foreach ($Schedules->getDataAs($as) as $sc) {
        $Log = new LogModel;
        $Account = \Controller::model("Account", $sc->get("account_id"));
        $User = \Controller::model("User", $sc->get("user_id"));

        // Calculate next schedule datetime...
        if (isset($speeds[$sc->get("speed")]) && (int)$speeds[$sc->get("speed")] > 0) {
            $speed = (int)$speeds[$sc->get("speed")];
            $delta = round(3600/$speed);

            if ($settings->get("data.random_delay")) {
                $delay = rand(0, 300);
                $delta += $delay;
            }
        } else {
            $delta = rand(720, 7200);
        }

        $next_schedule = date("Y-m-d H:i:s", time() + $delta);
        if ($sc->get("daily_pause")) {
            $pause_from = date("Y-m-d")." ".$sc->get("daily_pause_from");
            $pause_to = date("Y-m-d")." ".$sc->get("daily_pause_to");
            if ($pause_to <= $pause_from) {
                // next day
                $pause_to = date("Y-m-d", time() + 86400)." ".$sc->get("daily_pause_to");
            }

            if ($next_schedule > $pause_to) {
                // Today's pause interval is over
                $pause_from = date("Y-m-d H:i:s", strtotime($pause_from) + 86400);
                $pause_to = date("Y-m-d H:i:s", strtotime($pause_to) + 86400);
            }

            if ($next_schedule >= $pause_from && $next_schedule <= $pause_to) {
                $next_schedule = $pause_to;
            }
        }
        $sc->set("schedule_date", $next_schedule)
           ->set("last_action_date", date("Y-m-d H:i:s"))
           ->save();


        // Set default values for the log...
        $Log->set("user_id", $User->get("id"))
            ->set("account_id", $Account->get("id"))
            ->set("status", "error");


        // Check account
        if (!$Account->isAvailable() || $Account->get("login_required")) {
            // Account is either removed (unexected, external factors)
            // Or login required for this account
            // Deactivate schedule
            $sc->set("is_active", 0)->save();

            // Log data
            $Log->set("data.error.msg", "Activity has been stopped")
                ->set("data.error.details", "Re-login is required for the account.")
                ->save();
            continue;
        }

        // Check user account
        if (!$User->isAvailable() || !$User->get("is_active") || $User->isExpired()) {
            // User is not valid
            // Deactivate schedule
            $sc->set("is_active", 0)->save();

            // Log data
            $Log->set("data.error.msg", "Activity has been stopped")
                ->set("data.error.details", "User account is either disabled or expred.")
                ->save();
            continue;
        }

        if ($User->get("id") != $Account->get("user_id")) {
            // Unexpected, data modified by external factors
            // Deactivate schedule
            $sc->set("is_active", 0)->save();
            continue;
        }

        // Check targets
        $targets = @json_decode($sc->get("target"));
        if (!$targets) {
            // Unexpected, data modified by external factors
            // Deactivate schedule
            $sc->set("is_active", 0)->save();
            continue;
        }

        // Select random target
        $i = rand(0, count($targets) - 1);
        $target = $targets[$i];

        // Check selected target
        if (empty($target->type) ||
            empty($target->id) ||
            !in_array($target->type, ["hashtag", "location", "people"]))
        {
            // Unexpected, data modified by external factors
            continue;
        }

        try {
            $Instagram = \InstagramController::login($Account);
        } catch (\Exception $e) {
            // Couldn't login into the account
            $Account->refresh();

            // Log data
            if ($Account->get("login_required")) {

                $error_count = $sc->get("data.error_count");

                if($error_count == null){
                  $error_count = 0;
                }

                $error_count++;
                $sc->set("data.error_count", $error_count);
                if($error_count >= 3){
                  $sc->set("is_active", 0)->save();
                  $sc->set("data.error_count", 0);
                }
                $Log->set("data.error.msg", "Activity Relogin/Connection Problems " . $error_count ."/3");
            } else {
                $Log->set("data.error.msg", "Action re-scheduled");
            }

            $Log->set("data.error.details", $e->getMessage())
                ->save();

            continue;
        }

        // Logged in successfully
        // Now script will try to get feed and follow new user
        // And will log result
        $Log->set("data.trigger", $target);


        // Find username to follow
        $follow_pk = null;
        $follow_username = null;
        $follow_full_name = null;
        $follow_profile_pic_url = null;

        $turns = 1;

        $rank_token = \InstagramAPI\Signatures::generateUUID();

        if ($target->type == "hashtag") {
            try {
                $feed = $Instagram->hashtag->getFeed(
                    str_replace("#", "", $target->id),
                    $rank_token);
            } catch (\Exception $e) {
                // Couldn't get instagram feed related to the hashtag

                // Log data
                $Log->set("data.error.msg", "Couldn't get the feed")
                    ->set("data.error.details", $e->getMessage())
                    ->save();
                continue;
            }

            if (count($feed->getItems()) < 1) {
                // Invalid
                continue;
            }

            foreach ($feed->getItems() as $item) {
                if (empty($item->getUser()->getFriendshipStatus()->getFollowing()) &&
                    empty($item->getUser()->getFriendshipStatus()->getOutgoingRequest()) &&
                    $item->getUser()->getPk()!= $Account->get("instagram_id"))
                {

                    $_log = new LogModel([
                        "user_id" => $User->get("id"),
                        "account_id" => $Account->get("id"),
                        "followed_user_pk" => $item->getUser()->getPk(),
                        "status" => "success"
                    ]);

                    if (!$_log->isAvailable()) {

                        $filter1 = namespace\customFilterFast($item->getUser()->getPk(),$item->getUser()->getFullName(),$item->getUser()->getProfilePicUrl(),(bool)$item->getUser()->getIsPrivate(),$sc,$User,$Account);

                        if(!$filter1){
                          continue;
                        }
                        $filter = namespace\customFilter($Instagram, $item->getUser()->getPk(), $sc, $User,$Account);

                        if(!$filter && $turns <= 3){
                          $turns++;
                          continue;
                        }

                        // Found new user
                        $follow_pk = $item->getUser()->getPk();
                        $follow_username = $item->getUser()->getUsername();
                        $follow_full_name = $item->getUser()->getFullName();
                        $follow_profile_pic_url = $item->getUser()->getProfilePicUrl();
                        $follow_is_private = (bool)$item->getUser()->getIsPrivate();
                        break;
                    }
                }
            }
        } else if ($target->type == "location") {
            try {
                $feed = $Instagram->location->getFeed($target->id, $rank_token);
            } catch (\Exception $e) {
                // Couldn't get instagram feed related to the location id

                // Log data
                $Log->set("data.error.msg", "Couldn't get the feed")
                    ->set("data.error.details", $e->getMessage())
                    ->save();
                continue;
            }

            if (count($feed->getItems()) < 1) {
                // Invalid
                continue;
            }

            foreach ($feed->getItems() as $item) {
                if (empty($item->getUser()->getFriendshipStatus()->getFollowing()) &&
                    empty($item->getUser()->getFriendshipStatus()->getOutgoingRequest()) &&
                    $item->getUser()->getPk() != $Account->get("instagram_id"))
                {
                    $_log = new LogModel([
                        "user_id" => $User->get("id"),
                        "account_id" => $Account->get("id"),
                        "followed_user_pk" => $item->getUser()->getPk(),
                        "status" => "success"
                    ]);

                    if (!$_log->isAvailable()) {

                        // Codingmatter Modification
                        // Lets get all user related data for filtering.

                        $filter1 = namespace\customFilterFast($item->getUser()->getPk(),$item->getUser()->getFullName(),$item->getUser()->getProfilePicUrl(),(bool)$item->getUser()->getIsPrivate(),$sc,$User,$Account);

                        if(!$filter1){
                          continue;
                        }

                        //filter
                        $filter = namespace\customFilter($Instagram, $item->getUser()->getPk(), $sc, $User,$Account);

                        if(!$filter && $turns <= 3){
                          $turns++;
                          continue;
                        }

                        // Found new user
                        $follow_pk = $item->getUser()->getPk();
                        $follow_username = $item->getUser()->getUsername();
                        $follow_full_name = $item->getUser()->getFullName();
                        $follow_profile_pic_url = $item->getUser()->getProfilePicUrl();
                        $follow_is_private = (bool)$item->getUser()->getIsPrivate();
                        break;
                    }
                }
            }
        } else if ($target->type == "people") {
            $round = 1;
            $loop = true;
            $next_max_id = null;

            while ($loop) {
                try {
                    $feed = $Instagram->people->getFollowers($target->id, $rank_token, null, $next_max_id);
                } catch (\Exception $e) {
                    // Couldn't get instagram feed related to the user id
                    $loop = false;

                    if ($round == 1) {
                        // Log data
                        $Log->set("data.error.msg", "Couldn't get the feed")
                            ->set("data.error.details", $e->getMessage())
                            ->save();
                    }

                    continue 2;
                }

                if (count($feed->getUsers()) < 1) {
                    // Invalid
                    $loop = false;
                    continue 2;
                }

                foreach ($feed->getUsers() as $user) {
                    if (empty($user->getFriendshipStatus()) &&
                        $user->getPk() != $Account->get("instagram_id"))
                    {
                        $_log = new LogModel([
                            "user_id" => $User->get("id"),
                            "account_id" => $Account->get("id"),
                            "followed_user_pk" => $user->getPk(),
                            "status" => "success"
                        ]);

                        if (!$_log->isAvailable()) {

                            $filter1 = namespace\customFilterFast($user->getPk(),$user->getFullName(),$user->getProfilePicUrl(),(bool)$user->getIsPrivate(),$sc,$User,$Account);

                            if(!$filter1){
                              continue;
                            }

                            $filter = namespace\customFilter($Instagram, $user->getPk(), $sc, $User,$Account);

                            if(!$filter && $turns <= 3){
                              $turns++;
                              continue;
                            }

                            // Found new user
                            $follow_pk = $user->getPk();
                            $follow_username = $user->getUsername();
                            $follow_full_name = $user->getFullName();
                            $follow_profile_pic_url = $user->getProfilePicUrl();
							              $follow_is_private = (bool)$user->getIsPrivate();
                            break 2;
                        }
                    }
                }

                $round++;
                $next_max_id = empty($feed->getNextMaxId()) ? null : $feed->getNextMaxId();
                if ($round >= 5 || !empty($follow_pk) || $next_max_id) {
                    $loop = false;
                }
            }
        }

        if (empty($follow_pk)) {
            $Log->set("data.error.msg", "Couldn't find new user to follow")
                ->save();
            continue;
        }


        // New user found to follow
        try {

            // MODIFICATION by Codingmatters.
            // https://www.facebook.com/CodingMatters-945340885622490/
            // We want a Powerlike feature.
            // pull the timeline of the getFollower
            // and get last 3 posts to like.

            $power_count = $sc->get("data.powerlike_count");
            $power_like = $sc->get("data.powerlike");
            $power_random = $sc->get("data.powerlike_random");
            $likedmedia = [];

            if($power_count > 3){
              $power_count = 3; //max value
            };

            if($power_like && !$follow_is_private){
              try {
                  $feed = $Instagram->timeline->getUserFeed($follow_pk);

                  $items = $feed->getItems();

                  if($power_random){
                    $power_count = mt_rand(1, intval($power_count));
                  }

                  $temp_count = $power_count;

                  foreach ($items as $item) {

                    $media = new \stdClass();

                       if (!empty($item->getId()) && !$item->getHasLiked())  {

                               if($temp_count== 0){
                                 break;
                               }else{
                                 $temp_count = $temp_count - 1;
                               }

                               $media->media_id = $item->getId();
                               $media->media_code = $item->getCode();
                               $media->media_type = $item->getMediaType();
                               $media->media_thumb = namespace\_get_media_thumb_igitem($item);
                               $media->user_pk = $item->getUser()->getPk();
                               $media->user_username = $item->getUser()->getUsername();
                               $media->user_full_name = $item->getUser()->getFullName();

                               try {
                                   $resp = $Instagram->media->like($item->getId());
                               } catch (\Exception $e) {
                                   continue;
                               }

                               if (!$resp->isOk()) {
                                   continue;
                               }else{
                                   array_push($likedmedia, $media);
                               }
                       }
                  }

              } catch (\Exception $e) {
                  // Couldn't get instagram feed related to the hashtag
              }

            }else{
              $power_count = 0;
            }

            $resp = $Instagram->people->follow($follow_pk);

        } catch (\Exception $e) {
            $Log->set("data.error.msg", "Couldn't follow the user")
                ->set("data.error.details", $e->getMessage())
                ->save();
            continue;
        }


        if (!$resp->isOk()) {
            $Log->set("data.error.msg", "Couldn't follow the user")
                ->set("data.error.details", "Something went wrong")
                ->save();
            continue;
        }


        // Followed new user successfully
        $Log->set("status", "success")
            ->set("data.followed", [
                "pk" => $follow_pk,
                "username" => $follow_username,
                "full_name" => $follow_full_name,
                "profile_pic_url" => $follow_profile_pic_url
            ])
            ->set("data.powerlike", [
                "count" => count($likedmedia),
                "posts" => $likedmedia
            ])
            ->set("followed_user_pk", $follow_pk)
            ->save();
    }
}
\Event::bind("cron.add", __NAMESPACE__."\addCronTask");


/**
 * Get Plugin Settings
 * @return \GeneralDataModel
 */
function settings()
{
    $settings = \Controller::model("GeneralData", "plugin-auto-follow-settings");
    return $settings;
}

function _get_media_thumb_igitem($item)
{
    $media_thumb = null;

    $media_type = empty($item->getMediaType()) ? null : $item->getMediaType();

    if ($media_type == 1 || $media_type == 2) {
        // Photo (1) OR Video (2)
        $candidates = $item->getImageVersions2()->getCandidates();
        if (!empty($candidates[0]->getUrl())) {
            $media_thumb = $candidates[0]->getUrl();
        }
    } else if ($media_type == 8) {
        // ALbum
        $carousel = $item->getCarouselMedia();
        $candidates = $carousel[0]->getImageVersions2()->getCandidates();
        if (!empty($candidates[0]->getUrl())) {
            $media_thumb = $candidates[0]->getUrl();
        }
    }



    return $media_thumb;
}

function customFilterFast($user_pk,$fullname,$picture,$private,$sc,$User,$Account){


  $filter_dont_follow_twice = (bool)$sc->get("data.filter_unfollowed");
  $filter_gender = $sc->get("data.filter_gender");
  $filter_profil_private = (bool)$sc->get("data.filter_privat");
  $filter_profil_picture = (bool)$sc->get("data.filter_picture");

  if($filter_dont_follow_twice){
    $q4 = "SELECT * FROM ".TABLE_PREFIX."auto_unfollow_log WHERE user_id = " . intval($User->get("id")) . " AND account_id = " . intval($Account->get("id")) . " AND unfollowed_user_pk = " . intval($user_pk) . " AND status = 'success'";
    $query = \DB::query($q4);
    $logs =  $query->get();

    if (count($logs) > 0) {
      //echo "<br> ## Filter - Follow twice Triggered <br>";
      return false;
    }
  }

  if($filter_gender == "male" || $filter_gender == "female"){
    //$gender_check = namespace\Gender($userinfo->getUser()>getFullName(), $Account->get("proxy"));
    $gender_check = namespace\Gender2($filter_gender, $fullname);
    if(!$gender_check){
      //echo "<br> ## Filter - Gender not match <br>";
      return false;
    }
      //echo "<br> ++ Filter - Gender match <br>";
  }

  if($filter_profil_private){
    if($private){
      //echo "<br> ## Filter - Private Profil Triggered <br>";
      return false;
    }
  }

  if($filter_profil_picture){
    if($picture == ""){
      //echo "<br> ## Filter - Profil Picture Tiggered <br>";
      return false;
    }
  }

  return true;

}


function customFilter($Instagram,$user_pk,$sc,$User,$Account){

  $validation = false;

  $filter_profil_business = (bool)$sc->get("data.filter_business");

  $filter_media_min = (int)$sc->get("data.filter_media_min");
  $filter_follower_min = (int)$sc->get("data.filter_followed_min");
  $filter_follower_max = (int)$sc->get("data.filter_followed_max");
  $filter_following_min = (int)$sc->get("data.filter_following_min");
  $filter_following_max = (int)$sc->get("data.filter_following_max");
  $filter_blacklist = explode(",",$sc->get("data.filter_blacklist"));

  $userinfo = $Instagram->people->getInfoById($user_pk);

  if($userinfo->getUser()->getIsBusiness()){

    if($userinfo->getUser()->getPublicEmail() != ""){
        $info_mail = $userinfo->getUser()->getPublicEmail();
    }else{$info_mail = "NO MAIL";}

    if($userinfo->getUser()->getContactPhoneNumber()!= ""){
        $info_phone = $userinfo->getUser()->getContactPhoneNumber();
    }else{$info_phone = "NO NUMBER";}

    if($userinfo->getUser()->getCityName() != ""){
        $info_cname = $userinfo->getUser()->getCityName();
    }else{$info_cname = "NO CITY";}

    if($userinfo->getUser()->getAddressStreet() != ""){
        $info_street = trim(preg_replace('/\s\s+/', ' ', $userinfo->getUser()->getAddressStreet()));
    }else{$info_street = "NO STREET";}

    if($userinfo->getUser()->getExternalUrl() != ""){
        $info_eurl = $userinfo->getUser()->getExternalUrl();
    }else{$info_eurl = "NO URL";}

    if($userinfo->getUser()->getCategory() != ""){
        $info_category = $userinfo->getUser()->getCategory();
    }else{$info_category = "NO Category";}

    if($userinfo->getUser()->getProfilePicUrl() != ""){
        $info_ppic = $userinfo->getUser()->getProfilePicUrl();
    }else{$info_ppic = "NO Picture";}

    if($userinfo->getUser()->getBiography() != ""){
        $info_bio = str_replace(";" , " ", $userinfo->getUser()->getBiography());
    		$info_bio = str_replace("\n", " ", $info_bio);
    		$info_bio = str_replace("\r", " ", $info_bio);
    }else{$info_bio = "NO Biotext";}

  }

  if(count($filter_blacklist) > 0){
    //echo "<br> ## Filter - Blacklist check <br>";
    $tempString = $userinfo->getUser()->getUsername() . " ";
    $tempString .= $userinfo->getUser()->getFullName() . " ";
    $tempString .= $userinfo->getUser()->getBiography() . " ";

   foreach ($filter_blacklist as $key => $value){
       //echo "<br> ## Filter - Blacklist keyword : ".$value."<br>";
     if (strpos($value, $tempString) !== false) {
       //echo "<br> ## Filter - Blacklist keyword found : ".$value."<br>";
       return $validation;
     }

   }

  }

  if($filter_profil_business){
    if($userinfo->getUser()->getIsBusiness() == 1){
      //echo "<br> ## Filter - Business Tiggered <br>";
      return $validation;
    }
  }

  // Media

  if($filter_media_min > 0){
    if($userinfo->getUser()->getMediaCount() <= $filter_media_min){
      //echo "<br> ## Filter - Media count Tiggered <br>";
      return $validation;
    }
  }

  // Followers

  if($filter_follower_max > 0){
    if($userinfo->getUser()->getFollowerCount() >= $filter_follower_max){
      //echo "<br> ## Filter - Followers Max <br>";
      return $validation;
    }
  }

  if($filter_follower_min > 0){
    if($userinfo->getUser()->getFollowerCount() <= $filter_follower_min){
      //echo "<br> ## Filter - Followers Min <br>";
      return $validation;
    }
  }

  //Followings

  if($filter_following_max > 0){
    if($userinfo->getUser()->getFollowingCount() >= $filter_following_max){
      //echo "<br> ## Filter - Following Max <br>";
      return $validation;
    }
  }

  if($filter_following_min > 0){
    if($userinfo->getUser()->getFollowingCount() <= $filter_following_min){
      //echo "<br> ## Filter - Following Min <br>";
      return $validation;
    }
  }

  return true;
}

function Gender2($gender,$fullname){

  if($fullname == ""){
    return false;
  }

  $firstname = strtolower(explode(" ", $fullname)[0]);

  $firstname = preg_replace('~[^a-zA-Z]+~', '', $firstname);

  if(strlen($firstname) <= 2){
    return false;
  }


  if($gender == "female"){
    $names = file_get_contents(PLUGINS_PATH."/".IDNAME."/assets/female.json");

    $array_names = json_decode($names, true);

    if (in_array($firstname, array_map('strtolower', $array_names['female']))) {
        return true;
    }

  }else{
    $names = file_get_contents(PLUGINS_PATH."/".IDNAME."/assets/male.json");

    $array_names = json_decode($names, true);

    if (in_array($firstname, array_map('strtolower', $array_names['male']))) {
        return true;
    }

  }

  // lets save names we could not resolve and add them later
  $file = PLUGINS_PATH."/".IDNAME."/assets/".$gender.'_fails.txt';
  $myfile = file_put_contents($file, $firstname.PHP_EOL , FILE_APPEND | LOCK_EX);

  return false;
}

function Gender($fullname,$proxy){
    $app_url = "https://api.genderize.io/?name=";

    //$loginpassw = 'username:password';
    //$proxy_ip = '192.168.1.1';
    //$proxy_port = '12345';
    //$url = 'http://www.domain.com';
    $proxy = "https://188.39.20.136:8080";

    if($fullname != ""){
            $loginpassw = false;

            if($proxy != ""){
              $foo = explode(":",$proxy);

              if(count($foo) == 3){
                //ohne password
                $proxy_type = $foo[0];
                $proxy_ip =   $foo[0] .":". $foo[1];
                $proxy_port = $foo[2];
                $loginpassw = false;
                //echo "<br>".$proxy_ip."<br>";
              }

            }
            $url = $app_url . urldecode(explode(" ", $fullname)[0]);
              //echo "<br>".$url."<br>";
            try{
              $curl = curl_init();
              curl_setopt($curl, CURLOPT_URL, $url);
              curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($curl, CURLOPT_HEADER, false);

              if($proxy != ""){
              //curl_setopt($curl, CURLOPT_PROXYPORT, $proxy_port);
              //curl_setopt($curl, CURLOPT_PROXYTYPE, strtoupper($proxy_type));
              curl_setopt($curl, CURLOPT_PROXY, $proxy);
                if($loginpassw != false){
                  curl_setopt($curl, CURLOPT_PROXYUSERPWD, $loginpassw);
                }
              }
              //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
              //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
              $data = curl_exec($curl);

              if(curl_errno($curl))
                //echo 'Curl error: '.curl_error($curl);

              curl_close($curl);
                //echo "<br> Gender Lookup Proxy Error <br>";

              $data = json_decode($data);
              if(!empty($data) && isset($data->gender)){
                return $data->gender;
              }else{
                return false;
              }
            }catch(\Exception $e){
              //echo "<br> Gender Lookup Proxy Error <br>";
                return false;
            }
    }else{
      return false;
    }
}
