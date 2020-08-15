<?php if (!defined('APP_VERSION')) die("Yo, what's up?"); ?>

<div class="skeleton skeleton--full">
    <div class="clearfix">
        <aside class="skeleton-aside">
            <?php if (count($Accounts) > 0): ?>
                <?php $active_item_id = Input::get("aid"); ?>
                <div class="aside-list js-loadmore-content" data-loadmore-id="1">
                    <?php foreach ($Accounts as $a): ?>
                        <div class="aside-list-item js-list-item <?= $active_item_id == $a->id ? "active" : "" ?>">
                            <div class="clearfix">
                                <?php $title = htmlchars($a->username); ?>
                                <span class="circle">
                                    <span><?= textInitials($title, 2); ?></span>
                                </span>

                                <div class="inner">
                                    <div class="title"><?= $title ?></div>
                                    <div class="sub" style="<?php if($a->is_active){ echo "color:green;";}else{echo "color:red;";}?>">
                                        <?php if($a->is_active){ echo __("Active");}else{echo __("Inactive");}?>
                                        <?php if ($a->login_required): ?>
                                            <span class="color-danger ml-5">
                                                <span class="mdi mdi-information"></span>
                                                <?= __("Re-login required!") ?>
                                            </span>
                                        <?php endif ?>
                                    </div>
                                </div>

                                <?php
                                    $url = APPURL."/e/".$idname."/".$a->id;
                                    switch (\Input::get("ref")) {
                                        case "log":
                                            $url .= "/log";
                                            break;

                                        default:
                                            break;
                                    }
                                ?>
                                <a class="full-link js-ajaxload-content" href="<?= $url ?>"></a>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>

            <?php else: ?>
                <div class="no-data">
                    <?php if ($AuthUser->get("settings.max_accounts") == -1 || $AuthUser->get("settings.max_accounts") > 0): ?>
                        <p><?= __("You haven't add any Instagram account yet. Click the button below to add your first account.") ?></p>
                        <a class="small button" href="<?= APPURL."/accounts/new" ?>">
                            <span class="sli sli-user-follow"></span>
                            <?= __("New Account") ?>
                        </a>
                    <?php else: ?>
                        <p><?= __("You don't have a permission to add any Instagram account.") ?></p>
                    <?php endif; ?>
                </div>
            <?php endif ?>
        </aside>

        <section class="skeleton-content hide-on-medium-and-down">
            <div class="no-data">
                <span class="no-data-icon sli sli-social-instagram"></span>
                <p><?= __("Please select an account from left side list.") ?></p>
            </div>
        </section>
    </div>
</div>
