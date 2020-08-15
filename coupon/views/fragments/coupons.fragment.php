<?php if (!defined('APP_VERSION')) die("Yo, what's up?"); ?>

<div class='skeleton' id="coupons" style="margin-top:0px">

    <div class="container-1200">
    
    <div class="row clearfix">
    <section class="section">
    <div class="section-header clearfix hide-on-small-only">
        
            <div class="col s12 m6">
              <h2 class="section-title">
                <?= __("Coupons") ?>
              </h2>
            </div>
            <div class="col s12 m6 m-last">
            <form  
                action="<?= APPURL . "/e/" . $idname . "/settings" ?>"
                method="GET">
            <div class="col s12 m6">
              <select name="package"  id="packages" tabindex="1" class="input">
                    <?php //$s = $Settings->get("data.speeds.very_slow") ?>
                    <?php //$s = $Packages ?>
                    <?php  
                    $package_id = 0;
                    foreach($Packages->getDataAs("Package") as $p ) {
                        if($p->get('id') == \Input::get("package") )
                        $package_id = \Input::get("package") ; 
                    }
                    ?>
                    
                    <option <?php if ($package_id == 0 ) echo 'selected' ; ?>  value="0">
                        ALL
                    </option>
                    <?php foreach ($Packages->getDataAs("Package") as $package): ?>
                    <option <?php if ($package_id == $package->get('id') ) echo 'selected' ; ?>  value="<?php echo $package->get('id')?>">
                      <?php echo htmlchars($package->get("title")); ?>
                    </option>
                    <?php endforeach; ?>
                  </select>
                  </div>
                  <div class="col s12 m6 m-last">
                  <input class="fluid button button--footer" type="submit" tabindex="1"  value="<?= __("Apply") ?>">
                </div>
                </div>
                </for>
            </div>
            <?php if ($Coupons->getTotalCount() > 0): ?>
                <table class="plugins-table">
                    <thead>
                    <tr>
                        <td><b>Package</b></td>
                        <td><b>Code</b></td>
                        <td><b>Package expiry days</b></td>
                        <td><b>Created on</b></td>
                        <td><b>Status</b></td>
                    </tr>
                    </thead>
                    <tbody class="js-loadmore-content" data-loadmore-id="1">
                    <?php foreach ($Coupons->getDataAs($CouponsAs) as $c): ?>
                        <tr class="js-list-item">
                            <td>
                                <?php echo $c->get('title'); ?>
                            </td>
                            <td>
                                <code><?= $c->get('code'); ?></code>
                            </td>
                            <td>
                                <code><?= $c->get('expire_days') == "-1" ? "Lifetime" : $c->get('expire_days') ; ?></code>
                            </td>
                            <td>
                                <?php echo $c->get('created_at'); ?>
                            </td>
                            <td>
                                <code class="red"><?= $c->get('status') == 2 ? "Used" : "Unused"; ?></code>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>

                <?php if ($Coupons->getPage() < $Coupons->getPageCount()): ?>
                    <div class="loadmore">
                        <?php
                        $url = parse_url($_SERVER["REQUEST_URI"]);
                        $path = $url["path"];
                        if (isset($url["query"])) {
                            $qs = parse_str($url["query"], $qsarray);
                            unset($qsarray["page"]);

                            $url = $path . "?" . (count($qsarray) > 0 ? http_build_query($qsarray) . "&" : "") . "page=";
                        } else {
                            $url = $path . "?page=";
                        }
                        ?>
                        <a class="fluid button button--light-outline js-loadmore-btn" data-loadmore-id="1"
                           href="<?= $url . ($Coupons->getPage() + 1) ?>">
                            <span class="icon sli sli-refresh"></span>
                            <?= __("Load More") ?>
                        </a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
            <div class="clearfix">
                <div class="no-data">
                    <p><?= __("No coupons found.") ?></p>
                </div>
                </div>
            <?php endif; ?>
        
        </section>
        </div>
    </div>
</div>
