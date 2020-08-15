<?php if (!defined('APP_VERSION')) die("Yo, what's up?"); ?>

<div class='skeleton' id="account">
    <form class="js-ajax-form" 
          action="<?= APPURL . "/e/" . $idname . "/settings" ?>"
          method="POST">
        <input type="hidden" name="action" value="save">

        <div class="container-1200">
            <div class="row clearfix">
                <div class="form-result">
                </div>

                <div class="col s12 m8 l4">
                    <section class="section mb-20">
                        <div class="section-header clearfix">
                            <h2 class="section-title"><?= __("General") ?></h2>
                        </div>

                        <div class="section-content">
                            <div class="mb-5">
                                <label class="form-label"><?= __("Refresh Interval") ?></label>

                                <input type="number" name="check-interval" class="input" value="<?= $Settings->get("data.checkInterval") ?>" min="1" />
                            </div>

                            <span class="field-tips mb-30"><?= __("An account should be verified again after X hours") ?></span>
                        </div>

                        <input class="fluid button button--footer" type="submit" value="<?= __("Save") ?>">
                    </section>
                </div>


            </div>
        </div>
    </form>
</div>