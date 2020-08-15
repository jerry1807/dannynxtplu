<?php if (!defined('APP_VERSION')) die("Yo, what's up?"); ?>

<div class="skeleton skeleton--full" id="analyzer-schedule">
    <div class="clearfix">
        <aside class="skeleton-aside hide-on-medium-and-down">
            <div class="aside-list js-loadmore-content" data-loadmore-id="1"></div>

            <div class="loadmore pt-20 none">
                <a class="fluid button button--light-outline js-loadmore-btn js-autoloadmore-btn" data-loadmore-id="1" href="<?= APPURL."/e/".$idname."?aid=".$Account->get("id")."&ref=analyzer" ?>">
                    <span class="icon sli sli-refresh"></span>
                    <?= __("Load More") ?>
                </a>
            </div>
        </aside>

        <section class="skeleton-content">
            <div class="section-header clearfix">
                <h2 class="section-title"><a href="<?= 'https://instagram.com/'. $Analyzer->get("username") ?>" target="_blank"><?= '@' . htmlchars($Account->get("username")) ?></a></h2>
                <small class="ml-25"><span class="sli sli-reload"></span> <?= sprintf(__("Last check date: %s"), (new DateTime($Analyzer->get("last_check_date")))->setTimezone(new DateTimeZone($AuthUser->get("preferences.timezone")))->format('Y-m-d H:i:s')) ?></small>

            </div>

            <div class="section-header clearfix">
                <?php
                $now = (new DateTime())->setTimezone(new DateTimeZone($AuthUser->get("preferences.timezone")));
                $dateformat = "Y-m-d";
                ?>

                <div class="clearfix"></div>

                <input type="hidden" name="baseUrl" value="<?= APPURL . "/e/analyzer/" . $Account->get("id") ?>" />

                <div class="col s12 m4 l3">
                    <label class="form-label"><?= __("Start Date") ?></label>
                    <div class="pos-r">
                        <input class="input leftpad js-analyzer-datepicker"
                               name="dateStart"
                               data-position="bottom left"
                               data-date-format="<?= str_replace(["Y", "m", "d", "F"], ["yyyy", "mm", "dd", "MM"], $dateformat) ?>"
                               data-max-date="<?= $now->format("c") ?>"
                               data-start-date="<?= $now->format("c") ?>"
                               data-user-datetime-format="<?= $dateformat ?>"
                               type="text"
                               value="<?= $dateStart ? $dateStart : $now->format($dateformat); ?>"
                               data-min="<?= (new DateTime($Analyzer->get("added_date")))->setTimezone(new DateTimeZone($AuthUser->get("preferences.timezone")))->format('Y-m-d') ?>"
                               readonly>
                        <span class="sli sli-calendar field-icon--left pe-none"></span>
                    </div>
                </div>

                <div class="col s12 m4 l3">
                    <label class="form-label"><?= __("End Date") ?></label>
                    <div class="pos-r">
                        <input class="input leftpad js-analyzer-datepicker"
                               name="dateEnd"
                               data-position="bottom left"
                               data-date-format="<?= str_replace(["Y", "m", "d", "F"], ["yyyy", "mm", "dd", "MM"], $dateformat) ?>"
                               data-max-date="<?= $now->format("c") ?>"
                               data-start-date="<?= $now->format("c") ?>"
                               data-user-datetime-format="<?= $dateformat ?>"
                               type="text"
                               value="<?= $dateEnd ? $dateEnd : $now->format($dateformat); ?>"
                               readonly>
                        <span class="sli sli-calendar field-icon--left pe-none"></span>
                    </div>
                </div>

                <div class="col s12 m4 l3">
                    <label class="form-label"><?= __("Select Dates") ?></label>
                    <input id="dateSubmit" class="fluid button" type="submit" value="<?= __("Submit") ?>">
                </div>
            </div>

            <div class="section-content">
                <div class="form-result mb-25"></div>

                <?php require_once(__DIR__.'/analyzer_header.fragment.php'); ?>
                <?php require_once(__DIR__.'/analyzer_content.fragment.php'); ?>
            </div>
        </section>
    </div>
</div>