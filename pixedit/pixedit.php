<?php 
namespace Plugins\Pixedit;
const IDNAME = "pixedit";

if (!defined('APP_VERSION')) 
    die("Yo, what's up?"); 

function uninstall($Plugin)
{
    if ($Plugin->get("idname") != IDNAME) {
        return false;
    }

    $settings = namespace\settings();
    $settings->remove();

}
\Event::bind("plugin.remove", __NAMESPACE__ . '\uninstall');

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
                    <?= __('Pixedit') ?>
                </span>
            </label>
        </div>
    <?php
}
\Event::bind("package.add_module_option", __NAMESPACE__ . '\add_module_option');

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
	
    $GLOBALS[$global_variable_name]->map("GET|POST", "/e/".IDNAME."/req/[:action]?", [
        PLUGINS_PATH . "/". IDNAME ."/controllers/RequestController.php",
        __NAMESPACE__ . "\RequestController"
    ]);
	
}
\Event::bind("router.map", __NAMESPACE__ . '\route_maps');

function navigation($Nav, $AuthUser)
{
    $idname = IDNAME;
    include __DIR__."/views/fragments/navigation.fragment.php";
}
\Event::bind("navigation.add_menu", __NAMESPACE__ . '\navigation');

function settings()
{
    $settings = \Controller::model("GeneralData", "plugin-pixedit-settings");
    return $settings;
}

