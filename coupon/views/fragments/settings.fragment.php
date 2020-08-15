<?php if (!defined('APP_VERSION')) die("Yo, what's up?"); ?>
<div class='skeleton' id="account">
<div class="container-1200">
<div class="row clearfix">
<div class="col s12 m9">
  <form class="js-ajax-form" 
        action="<?= APPURL . "/e/" . $idname . "/settings" ?>"
        method="POST">
    <input type="hidden" name="action" value="save">
    <!-- <div class="container-1200"> -->
      <!-- <div class="row clearfix"> -->
        <div class="form-result">
        </div>
        <!-- <div class="col s12 m12 l12"> -->
          <section class="section">
            <div class="section-header clearfix hide-on-small-only">
              <h2 class="section-title">
                <?= __("Generate Coupons") ?>
              </h2>
            </div>
            <div class="section-content">
              <div class="mb-10 clearfix">
                <div class="col s12 m4 l4">
                  <label class="form-label" for="packages">
                    <?= __("Packages") ?>
                  </label>
                  <select name="packages"  id="packages" tabindex="1" class="input">
                    <?php //$s = $Settings->get("data.speeds.very_slow") ?>
                    <?php //$s = $Packages ?>
                    <?php //print_r($Packages->getDataAs("Package")); die;  ?>
                    <?php foreach ($Packages->getDataAs("Package") as $package): ?>
                    <option value="<?php echo $package->get('id')?>">
                      <?php echo htmlchars($package->get("title")); ?>
                    </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col s12 m4 l4">
                  <label for="no_of_coupons" class="form-label" >
                    <?= __("No of Coupons") ?>
                  </label>
                  <input type="number" min="1"  value="10" tabindex="1"  max="2000" name="no_of_coupons" id="no_of_coupons" class="input" />
                </div>
                <div class="col s12 s-last m4 m-last l4 l-last mb-20">
                  <label for="no_of_days" class="form-label" >
                    <?= __("Package Expiry Days") ?>
                  </label>
                  <input type="number"  value="10" tabindex="1"  max="20000" name="package_expiry_days" id="package_expiry_days" class="input" maxlength="5" />
                </div>
              </div>
              <ul class="field-tips">
                <li>
                  <?= __("Select packages from dropdown, and enter number of coupons to generate. Then Click save") ?>
                </li>
                <li>
                  <?= __("It will generate coupons with unique code, and will be displayed below.") ?>
                </li>
                <li>
                  <?= __("No of coupons should be between 0 and 2000.") ?>
                </li>
                <li>
                  <?= __("expiry days should be between 1 and 6000 or -1 for lifetime.") ?>
                </li>
              </ul>
            </div>
            <input class="fluid button button--footer" type="submit" tabindex="1"  value="<?= __("Save") ?>">
          </section>
        <!-- </div> -->
      <!-- </div> -->
    <!-- </div> -->
  </form>
  </div>
  <div class="col s12 m3 m-last">
  <form class="js-ajax-for" 
        action="<?= APPURL . "/e/" . $idname . "/export" ?>"
        method="POST">
    <input type="hidden" name="action" value="save">
    <!-- <div class="container-1200 mt-20"> -->
      <!-- <div class="row clearfix"> -->
        <div class="form-result">
        </div>
        <!-- <div class="col s12 m12 l12"> -->
          <section class="section">
            <div class="section-header clearfix hide-on-small-only">
              <h2 class="section-title">
                <?= __("Exports Coupons") ?>
              </h2>
            </div>
            <div class="section-content">
                <div class="mb-10 clearfix">
                  <!-- <label class="form-label" for="packages">
                    <?= __("Packages") ?>
                  </label> -->
                  <select name="packages"  id="packages" tabindex="1" class="input">
                    <?php //$s = $Settings->get("data.speeds.very_slow") ?>
                    <?php //$s = $Packages ?>
                    <?php //print_r($Packages->getDataAs("Package")); die;  ?>
                    <?php foreach ($Packages->getDataAs("Package") as $package): ?>
                    <option value="<?php echo $package->get('id')?>">
                      <?php echo htmlchars($package->get("title")); ?>
                    </option>
                    <?php endforeach; ?>
                  </select>
            </div>
            </div>
                  <input class="fluid button button--footer" type="submit" tabindex="1"  value="<?= __("Export") ?>">
                
          </section>
        <!-- </div> -->
      </div>
    </div>
  </form>
  </div>
  </div>
  </div>
</div>
