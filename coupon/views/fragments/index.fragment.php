<?php if (!defined('APP_VERSION')) die("Yo, what's up?"); ?>

<div class='skeleton' id="account">
    <form class="js-ajax-form"
          action="<?= APPURL . "/e/" . $idname . "/" ?>"
          method="POST">
        <input type="hidden" name="action" value="save">

        <div class="container-1200">
            <div class="row clearfix">
                <div class="form-result">
                </div>

                <div class="col s12 m12 l12">
                    <section class="section">
                        <div class="section-header clearfix hide-on-small-only">
                            <h2 class="section-title"><?= __("Apply Coupon") ?></h2>
                        </div>

                        <div class="section-content">
                            <div class="mb-10 clearfix">
                                <div class="col s6 m6 l6">
                                    <label class="form-label" for="packages"><?= __("Coupon code") ?></label>
                                    <input type="text"  value="" name="coupon" id="coupon" class="input" />
                                </div>
                            </div>

                            <ul class="field-tips">
                                <li><?= __("Coupon code is case-insensitive.") ?></li>
                                <li><?= __("Once coupon is applied, it will activate the associated package.") ?></li>
                                <li><?= __("If you have higher package available, don't apply coupon that associated with lower package.") ?></li>
                            </ul>
                        </div>
                        <input class="fluid button button--footer" type="submit" tabindex="1"  value="<?= __("Apply") ?>">
                    </section>
                </div>
                <?php /*
                <div class="col s12 m8 l4">
                    <section class="section">
                        <div class="section-header clearfix hide-on-small-only">
                            <h2 class="section-title"><?= __("Other Settings") ?></h2>
                        </div>

                        <div class="section-content">
                            <div class="mb-20">
                                <label>
                                    <input type="checkbox"
                                           class="checkbox"
                                           name="random_delay"
                                           value="1"
                                           <?= $Settings->get("data.random_delay") ? "checked" : "" ?>>
                                    <span>
                                        <span class="icon unchecked">
                                            <span class="mdi mdi-check"></span>
                                        </span>
                                        <?= __('Enable Random Delays') ?>
                                        (<?= __("Recommended") ?>)

                                        <ul class="field-tips">
                                            <li><?= __("If you enable this option, script will add random delays automatically between each requests.") ?></li>
                                            <li><?= __("Delays could be up to 5 minutes.") ?></li>
                                        </ul>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <input class="fluid button button--footer" type="submit" value="<?= __("Save") ?>">
                    </section>
                </div>
                */ ?>
            </div>
        </div>
    </form>
</div>