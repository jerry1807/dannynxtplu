<?php
namespace Plugins\Analyzer;
const IDNAME = "analyzer";

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

    $sql = "CREATE TABLE ".TABLE_PREFIX."analyzer_users (
        id int(11) NOT NULL AUTO_INCREMENT,
        instagram_id varchar(255),
        username varchar(255),
        full_name varchar(128),
        description TEXT,
        website varchar(128),
        followers int,
        following int,
        uploads int,
        profile_picture_url varchar(256),
        is_private int,
        is_verified int,
        average_engagement_rate varchar(16),
        details TEXT,
        added_date DATETIME,
        last_check_date DATETIME,
        PRIMARY KEY (`id`),
        UNIQUE KEY `".TABLE_PREFIX."analyzer_users_id_uindex` (`id`),
        KEY `instagram_id` (`instagram_id`)
    );";

//    $sql .= "ALTER TABLE ".TABLE_PREFIX."analyzer_users CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;";

    $sql .= "CREATE TABLE ".TABLE_PREFIX."analyzer_logs(
        id int(11) NOT NULL AUTO_INCREMENT,
        analyzer_user_id int,
        instagram_id varchar(255),
        username varchar(255),
        followers int,
        following int,
        uploads int,
        average_engagement_rate varchar(16),
        date datetime,
        PRIMARY KEY (`id`),
        UNIQUE KEY `".TABLE_PREFIX."analyzer_logs_id_uindex` (`id`),
        KEY `analyzer_user_id` (`analyzer_user_id`)
    );";


    $sql .= "INSERT INTO ".TABLE_PREFIX."general_data (`name`, `data`) VALUES ('plugin-analyzer-settings', '{\"checkInterval\":5}');";

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
    $settings = namespace\settings();
    $settings->remove();

    $sql  = "DROP TABLE `".TABLE_PREFIX."analyzer_users`;";
    $sql .= "DROP TABLE `".TABLE_PREFIX."analyzer_logs`;";

    $pdo = \DB::pdo();
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

}
\Event::bind("plugin.remove", __NAMESPACE__ . '\uninstall');


/**
 * Add module as a package options
 * Only users with granted permission
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
                    <?= __('Analyzer') ?>
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

    // Analyzer
    $GLOBALS[$global_variable_name]->map("GET|POST", "/e/".IDNAME."/[i:id]?", [
        PLUGINS_PATH . "/". IDNAME ."/controllers/AnalyzerController.php",
        __NAMESPACE__ . "\AnalyzerController"
    ]);
    $GLOBALS[$global_variable_name]->map("GET|POST", "/e/".IDNAME."/[i:id]/[*:start]/[*:end]?", [
        PLUGINS_PATH . "/". IDNAME ."/controllers/AnalyzerController.php",
        __NAMESPACE__ . "\AnalyzerController"
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
 * Get Plugin Settings
 * @return \GeneralDataModel
 */
function settings()
{
    $settings = \Controller::model("GeneralData", "plugin-".IDNAME."-settings");
    return $settings;
}


/**
 * Include Cron Task functions
 */
require_once __DIR__ . "/cron.php";
