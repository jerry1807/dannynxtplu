<?php if (!defined('APP_VERSION')) die("Yo, what's up?"); ?>

<div class="mb-20 clearfix">
    <div class="col s4 m3 l2 ">
        <img src="<?= $Analyzer->get("profile_picture_url") ?>" class="analyzer-instagram-avatar" alt="<?= $Analyzer->get("full_name") ?>" />
    </div>

    <div class="col s8 m9 l6">
        <div class="row">
            <h1>
                <?= $Analyzer->get("full_name") ?>

                <?php if($Analyzer->get("is_private")): ?>
                    <span data-toggle="tooltip tippy" title="<?= __("Private IG") ?>"><i class="fa fa-lock user-private-badge"></i></span>
                <?php endif; ?>

                <?php if($Analyzer->get("is_verified")): ?>
                    <span data-toggle="tooltip tippy" title="<?= __("Verified IG") ?>"><i class="fa fa-check-circle user-verified-badge"></i></span>
                <?php endif; ?>
            </h1>

            <span><?= $Analyzer->get("description") ?></span>
        </div>
    </div>
</div>

<div class="mb-40 clearfix">
    <div class="col l2 mr-20">
        <strong><?= __("Followers"); ?></strong>
        <p class="analyzer-header-number"><?= number_format($Analyzer->get("followers")) ?></p>
    </div>

    <div class="col l2 mr-20">
        <strong><?= __("Following"); ?></strong>
        <p class="analyzer-header-number"><?= number_format($Analyzer->get("following")) ?></p>
    </div>

    <div class="col l2 mr-20">
        <strong><?= __("Uploads") ?></strong>
        <p class="analyzer-header-number"><?= number_format($Analyzer->get("uploads")) ?></p>
    </div>

    <div class="col l2 mr-20">
        <strong><?= __("Engagement Rate") ?></strong>
        <p class="analyzer-header-number">
            <?= number_format($Analyzer->get("average_engagement_rate"), 2) ?>%
        </p>
    </div>
</div>



