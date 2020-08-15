<?php
    if (!defined('APP_VERSION'))
        die("Yo, what's up?");

    if (!isset($GLOBALS["_PLUGINS_"][$idname]["config"]))
          return null;

    $config = $GLOBALS["_PLUGINS_"][$idname]["config"];
    $user_modules = $AuthUser->get("settings.modules");
    if (empty($user_modules)) {
        $user_modules = [];
    }
?>

<?php if (in_array($idname, $user_modules)): ?>
    <li class="<?= $Nav->activeMenu == $idname ? "active" : "" ?>">
        <a href="<?= APPURL."/e/".$idname ?>">

			<!-- <span class="sli sli-screen-smartphone menu-icon "> -->
			<span class="mdi mdi-barcode-scan menu-icon ">
                <?php
//                    $name = empty($config["plugin_name"]) ? $idname : $config["plugin_name"];
//                    echo textInitials($name, 2)
                ?>
            </span>

            <span class="label"><?= __('Coupon') ?></span>

            <span class="tooltip tippy"
                  data-position="right"
                  data-delay="100"
                  data-arrow="true"
                  data-distance="-1"
                  title="<?= __('Coupon') ?>"></span>
        </a>
    </li>
<?php endif; ?>