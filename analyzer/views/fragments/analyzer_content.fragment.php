<?php if (!defined('APP_VERSION')) die("Yo, what's up?"); ?>

<div class="clearfix"></div>

<div class="mb-40">
    <h2><?= __("Other Engagements"); ?></h2>

    <div class="clearfix">
        <div class="col s6 m-0">
            <h4>
                <?= __("Engagement") ?>
                <span class="tooltip tippy" title="<?= __("The engagement rate is the number of active likes / comments on each post"); ?>"><span class="sli sli-question"></span></span>
            </h4>
        </div>

        <div class="col s6 m-0">
            <span class="analyzer-content-number"><?= number_format($Analyzer->get("average_engagement_rate"), 2) ?>%</span>
        </div>
    </div>

    <div class="clearfix">

        <div class="col s6 m-0">
            <h4>
                <?= __("Average Likes") ?>
                <span class="tooltip tippy" title="<?= __("Average likes based on the last 10 posts"); ?>"><span class="sli sli-like"></span></span>
            </h4>
        </div>

        <div class="col s6 m-0">
            <span class="analyzer-content-number"><?= $Analyzer->get("details")->average_likes ?></span>
        </div>
    </div>

    <div class="clearfix">

        <div class="col s6 m-0">
            <h4>
                <?= __("Average Comments") ?>
                <span class="tooltip tippy" title="<?= __("Average comments based on the last 10 posts"); ?>"><span class="sli sli-bubble"></span></span>
            </h4>
        </div>

        <div class="col s6 m-0">
            <span class="analyzer-content-number"><?= $Analyzer->get("details")->average_comments ?></span>
        </div>
    </div>
</div>


<div class="clearfix"></div>

<div class="mb-40">
    <h2><?= __("Account Statistics Charts") ?></h2>

    <div class="analyzer-chart-container">
        <canvas id="followers_chart"></canvas>
    </div>

    <div class="analyzer-chart-container">
        <canvas id="following_chart"></canvas>
    </div>
</div>

<div class="clearfix"></div>

<div class="mb-40">
    <h2><?= __("Account Statistics") ?></h2>
    <p class="text-muted"><?= __("Day by day statistics, ups and downs of the account.") ?></p>

    <table class="analyzer-ass-table">
        <thead>
        <tr>
            <th><?= __("Date") ?></th>
            <th></th>
            <th><?= __("Followers") ?></th>
            <th></th>
            <th><?= __("Following") ?></th>
            <th></th>
            <th><?= __("Uploads") ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        function colorful_number($number) {
            if($number > 0) {
                return '<span style="color: #28a745 !important;">+' . $number . '</span>';
            }
            elseif($number < 0) {
                return '<span style="color: #dc3545 !important;">' . $number . '</span>';

            } else {
                return '-';
            }
        }

        $total_new_followers = $total_new_uploads = 0;
        for($i = 0; $i < count($logsData->logs); $i++):
            $log_yesterday = ($i == 0) ? false : $logsData->logs[$i-1];
            $log = $logsData->logs[$i];

            $date = (new \DateTime($log['date']))->format('Y-m-d');
            $date_name = (new \DateTime($log['date']))->format('D');

            $followers_difference = $log_yesterday ? $log['followers'] - $log_yesterday['followers'] : 0;
            $following_difference = $log_yesterday ? $log['following'] - $log_yesterday['following'] : 0;
            $uploads_difference = $log_yesterday ? $log['uploads'] - $log_yesterday['uploads'] : 0;

            $total_new_followers += $followers_difference;
            $total_new_uploads += $uploads_difference;

            ?>
            <tr>
                <td><?= $date ?></td>
                <td><?= $date_name ?></td>
                <td><?= number_format($log['followers']) ?></td>
                <td><?= colorful_number($followers_difference); ?></td>
                <td><?= number_format($log['following']) ?></td>
                <td><?= colorful_number($following_difference); ?></td>
                <td><?= number_format($log['uploads']) ?></td>
                <td><?= colorful_number($uploads_difference); ?></td>
            </tr>
        <?php endfor; ?>

        <tr class="highlight">
            <td colspan="2"><?= __("Total Summary") ?></td>
            <td colspan="2"><?= colorful_number($total_new_followers); ?></td>
            <td colspan="2"></td>
            <td colspan="2"><?= colorful_number($total_new_uploads); ?></td>
        </tr>

        </tbody>
    </table>

</div>


<div class="clearfix"></div>

<div class="mb-40">
    <h2><?= __("Average Engagement Rate Chart"); ?></h2>
    <p class="text-muted"><?= __("Each value in this chart is equal to the Average Engagement Rate of the account in that specific day.") ?></p>

    <div class="analyzer-chart-container">
        <canvas id="average_engagement_rate_chart"></canvas>
    </div>
</div>

<div class="clearfix"></div>

<div class="mb-40">
    <h2><?= __("Highest Engagement Posts") ?></h2>
    <p class="text-muted"><?= __("Top posts from the last 10 pictures"); ?></p>

    <div class="">
        <?php $i = 1; ?>
        <?php foreach($Analyzer->get("details")->top_posts as $shortcode => $engagement_rate): if($i > 2) continue; else $i++; ?>

            <div class="col s12 m6 l5 ml-0 mr-40">

                <?= $AnalyzerConroller->get_embed_html($shortcode) ?>

            </div>


        <?php endforeach; ?>
    </div>
</div>





<div class="clearfix"></div>

<div class="mb-40">
    <h2><?= __("Future Projections") ?></h2>
    <p class="text-muted"><?= __("Here you can see the approximated future projections based on your previous days averages") ?></p>

    <table class="analyzer-ass-table">
        <thead class="">
        <tr>
            <th><?= __("Time Until") ?></th>
            <th><?= __("Date") ?></th>
            <th><?= __("Followers") ?></th>
            <th><?= __("Uploads") ?></th>
        </tr>
        </thead>

        <tbody>
        <tr class="bg-light">
            <td><?= __("Current Stats") ?></td>
            <td><?= (new \DateTime())->format('Y-m-d') ?></td>
            <td><?= number_format($Analyzer->get("followers")) ?></td>
            <td><?= number_format($Analyzer->get("uploads")) ?></td>
        </tr>

        <?php if($logsData->total_days < 2): ?>

            <tr class="bg-light">
                <td colspan="4"><?= __("We need at least 2 days of data until we can make these projections.") ?></td>
            </tr>

        <?php else: ?>
            <tr>
                <td><?= sprintf(__("%s Days"), 30) ?></td>
                <td><?= (new \DateTime())->modify('+30 day')->format('Y-m-d') ?></td>
                <td><?= number_format($Analyzer->get("followers") + ($logsData->average_followers * 30)) ?></td>
                <td><?= number_format($Analyzer->get("uploads") + ($logsData->average_uploads * 30)) ?></td>
            </tr>

            <tr>
                <td><?= sprintf(__("%s Days"), 60) ?></td>
                <td><?= (new \DateTime())->modify('+60 day')->format('Y-m-d') ?></td>
                <td><?= number_format($Analyzer->get("followers") + ($logsData->average_followers * 60)) ?></td>
                <td><?= number_format($Analyzer->get("uploads") + ($logsData->average_uploads * 60)) ?></td>
            </tr>

            <tr>
                <td><?= sprintf(__("%s Months"), 3) ?></td>
                <td><?= (new \DateTime())->modify('+90 day')->format('Y-m-d') ?></td>
                <td><?= number_format($Analyzer->get("followers") + ($logsData->average_followers * 90)) ?></td>
                <td><?= number_format($Analyzer->get("uploads") + ($logsData->average_uploads * 90)) ?></td>
            </tr>

            <tr>
                <td><?= sprintf(__("%s Months"), 6) ?></td>
                <td><?= (new \DateTime())->modify('+180 day')->format('Y-m-d') ?></td>
                <td><?= number_format($Analyzer->get("followers") + ($logsData->average_followers * 180)) ?></td>
                <td><?= number_format($Analyzer->get("uploads") + ($logsData->average_uploads * 180)) ?></td>
            </tr>

            <tr>
                <td><?= sprintf(__("%s Months"), 9) ?></td>
                <td><?= (new \DateTime())->modify('+270 day')->format('Y-m-d') ?></td>
                <td><?= number_format($Analyzer->get("followers") + ($logsData->average_followers * 270)) ?></td>
                <td><?= number_format($Analyzer->get("uploads") + ($logsData->average_uploads * 270)) ?></td>
            </tr>

            <tr>
                <td><?= sprintf(__("1 Year")) ?></td>
                <td><?= (new \DateTime())->modify('+365 day')->format('Y-m-d') ?></td>
                <td><?= number_format($Analyzer->get("followers") + ($logsData->average_followers * 365)) ?></td>
                <td><?= number_format($Analyzer->get("uploads") + ($logsData->average_uploads * 365)) ?></td>
            </tr>

            <tr>
                <td><?= sprintf(__("1 Year and half")) ?></td>
                <td><?= (new \DateTime())->modify('+547 day')->format('Y-m-d') ?></td>
                <td><?= number_format($Analyzer->get("followers") + ($logsData->average_followers * 547)) ?></td>
                <td><?= number_format($Analyzer->get("uploads") + ($logsData->average_uploads * 547)) ?></td>
            </tr>

            <tr>
                <td><?= sprintf(__("%s Years"), 2) ?></td>
                <td><?= (new \DateTime())->modify('+730 day')->format('Y-m-d') ?></td>
                <td><?= number_format($Analyzer->get("followers") + ($logsData->average_followers * 730)) ?></td>
                <td><?= number_format($Analyzer->get("uploads") + ($logsData->average_uploads * 730)) ?></td>
            </tr>

            <tr class="highlight">
                <td colspan="2"><?= __("Based on an average of") ?></td>
                <td><?= sprintf(__("%s followers /day"), colorful_number(number_format($logsData->average_followers))) ?></td>
                <td><?= sprintf(__("%s uploads /day"), colorful_number(number_format($logsData->average_uploads))) ?></td>
            </tr>

        <?php endif; ?>
        </tbody>
    </table>
</div>


<?php if(count((array) $Analyzer->get("details")->top_hashtags) > 0): ?>
    <div class="clearfix"></div>

    <div class="mb-40">
        <h2><?= __("Top Hashtags") ?></h2>
        <p class="text-muted"><?= __("Top hashtags used in the last 10 posts"); ?></p>


        <div class="">
            <?php foreach((array) $Analyzer->get("details")->top_hashtags as $hashtag => $use): ?>
                <div class="col s4 m4 l4 mb-15 mr-0">
                    <a href="https://www.instagram.com/explore/tags/<?= $hashtag ?>/" class="text-dark mr-5" target="_blank">#<?= $hashtag ?></a>

                    <span class="analyzer-content-number tooltip tippy" title="<?= sprintf(__("Used in %s out of 10 posts"), $use) ?>"><?= $use ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>



<?php if(count((array) $Analyzer->get("details")->top_mentions) > 0): ?>
    <div class="clearfix"></div>

    <div class="mb-40">
        <h2><?= __("Top Mentions") ?></h2>
        <p class="text-muted"><?= __("Top mentions from the last 10 posts"); ?></p>

        <div class="">
            <?php foreach((array) $Analyzer->get("details")->top_mentions as $mention => $use): ?>
                <div class="col s4 m4 l4 mb-15 mr-0">
                    <a href="https://www.instagram.com/<?= $mention ?>" class="text-dark mr-5" target="_blank">@<?= $mention ?></a>

                    <span class="analyzer-content-number tooltip tippy" title="<?= sprintf(__("Used in %s out of 10 posts"), $use) ?>"><?= $use ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<div class="clearfix"></div>

<div class="mb-40">
    <h2><?= __("Engagement Rates on Instagram") ?></h2>
    <p class="text-muted"><?= __("These are overall / average engagement rates found on Instagram. Statistics based on analysis of more than 1 million influencer profiles.") ?></p>

    <?php

    $report_engagement = '<img src="' . $Analyzer->get("profile_picture_url") . '" class="analyzer-instagram-avatar-small" alt="' . $Analyzer->get("full_name") . '" /><br />' . '<strong>' . number_format($Analyzer->get("average_engagement_rate"), 2) . '%</strong>';

    ?>

    <table class="analyzer-er-table">
        <thead>
        <tr>
            <th><?= __("Followers") ?></th>
            <th><?= __("Others Average Engagement") ?></th>
            <th><?= __("Your Profile Engagement") ?></th>
        </tr>
        </thead>

        <tbody>
        <tr <?php if($Analyzer->get("followers") < 1000) echo 'class="active"' ?>>
            <td>< 1,000</td>
            <td>8%</td>
            <td>
                <?php if($Analyzer->get("followers") < 1000): ?>

                    <?= $report_engagement ?>

                <?php endif; ?>
            </td>
        </tr>

        <tr <?php if($Analyzer->get("followers") >= 1000 && $Analyzer->get("followers") < 5000) echo 'class="active"' ?>>
            <td>< 5,000</td>
            <td>5.7%</td>
            <td>
                <?php if($Analyzer->get("followers") >= 1000 && $Analyzer->get("followers") < 5000): ?>

                    <?= $report_engagement ?>

                <?php endif; ?>
            </td>
        </tr>

        <tr <?php if($Analyzer->get("followers") >= 5000 && $Analyzer->get("followers") < 10000) echo 'class="active"' ?>>
            <td>< 10,000</td>
            <td>4%</td>
            <td>
                <?php if($Analyzer->get("followers") >= 5000 && $Analyzer->get("followers") < 10000): ?>

                    <?= $report_engagement ?>

                <?php endif; ?>
            </td>
        </tr>

        <tr <?php if($Analyzer->get("followers") >= 10000 && $Analyzer->get("followers") < 100000) echo 'class="active"' ?>>
            <td>< 100,000</td>
            <td>2.4%</td>
            <td>
                <?php if($Analyzer->get("followers") >= 10000 && $Analyzer->get("followers") < 100000): ?>

                    <?= $report_engagement ?>

                <?php endif; ?>
            </td>
        </tr>

        <tr <?php if($Analyzer->get("followers") >= 100000 && $Analyzer->get("followers") >= 1000000) echo 'class="active"' ?>>
            <td>100,000+</td>
            <td>1.7%</td>
            <td>
                <?php if($Analyzer->get("followers") >= 100000 && $Analyzer->get("followers") >= 1000000): ?>

                    <?= $report_engagement ?>

                <?php endif; ?>
            </td>
        </tr>
        </tbody>
    </table>
</div>

