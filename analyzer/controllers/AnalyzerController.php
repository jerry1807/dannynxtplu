<?php
namespace Plugins\Analyzer;

// Disable direct access
if (!defined('APP_VERSION')) 
    die("Yo, what's up?");

/**
 * Analyzer Controller
 */
class AnalyzerController extends \Controller
{
    /**
     * idname of the plugin for internal use
     */
    const IDNAME = 'analyzer';

    /**
     * Process
     */
    public function process()
    {
        $AuthUser = $this->getVariable("AuthUser");
        $Route = $this->getVariable("Route");
        $this->setVariable("idname", self::IDNAME);

        // Auth
        if (!$AuthUser) {
            header("Location: " . APPURL . "/login");
            exit;
        } else if ($AuthUser->isExpired()) {
            header("Location: " . APPURL . "/expired");
            exit;
        }

        $user_modules = $AuthUser->get("settings.modules");
        if (!is_array($user_modules) || !in_array(self::IDNAME, $user_modules)) {
            // Module is not accessible to this user
            header("Location: " . APPURL . "/post");
            exit;
        }

        // Get account
        $Account = \Controller::model("Account", $Route->params->id);
        if (!$Account->isAvailable() ||
            $Account->get("user_id") != $AuthUser->get("id")) {
            header("Location: " . APPURL . "/e/" . self::IDNAME);
            exit;
        }
        $this->setVariable("Account", $Account);

        // Parse the date from the url
        $dateStart = isset($Route->params->start) ? $Route->params->start : false;
        $dateEnd = isset($Route->params->end) ? $Route->params->end : false;

        if ($dateStart && $dateEnd && (!isValidDate($dateStart, "Y-m-d") || !isValidDate($dateEnd, "Y-m-d"))) {
            header("Location: " . APPURL . "/e/" . self::IDNAME);
            exit;
        }

        // Get plugin settings
        $Settings = namespace\settings();

        // Get Analyzer Data
        require_once PLUGINS_PATH . "/" . self::IDNAME . "/models/AnalyzerModel.php";

        $Analyzer = new AnalyzerModel($Account->get("instagram_id"));

        // First processing and insertion if not available in the database
        if (!$Analyzer->isAvailable()) {

            $instagramNewData = $this->instagramProcess($Account);

            $Analyzer->updateModelData($instagramNewData);

            // Insert the new data
            $Analyzer->insert();

        }

        // Start the processing of the instagram account if needed and update
        if($Analyzer->isAvailable() &&
            (new \DateTime())->setTimezone(new \DateTimeZone($AuthUser->get("preferences.timezone")))->modify("-" . $Settings->get("data.checkInterval") . " hours") >
            (new \DateTime($Analyzer->get("last_check_date")))->setTimezone(new \DateTimeZone($AuthUser->get("preferences.timezone")))
        ) {

            $instagramNewData = $this->instagramProcess($Account);

            $Analyzer->updateModelData($instagramNewData);

            // Insert the new data
            $Analyzer->update();

        }

        // Process the logs for this request
        $this->logsProcess($Analyzer);

        $logsData = $this->getLogs($Analyzer, $dateStart, $dateEnd);
        $this->setVariable("logsData", $logsData);


        $this->setVariable("Analyzer", $Analyzer);
        $this->setVariable("AnalyzerConroller", $this);
        $this->setVariable("Settings", $Settings);
        $this->setVariable("dateStart", $dateStart);
        $this->setVariable("dateEnd", $dateEnd);

        // Link the view
        $this->view(PLUGINS_PATH . "/" . self::IDNAME . "/views/analyzer.php", null);
    }


    public function logsProcess($Analyzer)
    {
        $date = (new \DateTime())->format('Y-m-d H:i:s');

        // Logs system
        $log = \DB::table(TABLE_PREFIX . "analyzer_logs")
            ->select("id")
            ->where("analyzer_user_id", "=", $Analyzer->get("id"))
            ->where(\DB::raw("DATEDIFF('{$date}', `date`) = 0"))
            ->first();

        if($log) {

            // Update the log
            \DB::table(TABLE_PREFIX . "analyzer_logs")
                ->where("id", $log->id)
                ->update(array(
                    "analyzer_user_id"          => $Analyzer->get("id"),
                    "instagram_id"              => $Analyzer->get("instagram_id"),
                    "username"                  => $Analyzer->get("username"),
                    "followers"                 => $Analyzer->get("followers"),
                    "following"                 => $Analyzer->get("following"),
                    "uploads"                   => $Analyzer->get("uploads"),
                    "average_engagement_rate"   => $Analyzer->get("average_engagement_rate"),
                    "date"                      => $date
                ));

        } else {

            // Insert new log
            \DB::table(TABLE_PREFIX . "analyzer_logs")
                ->insert(array(
                    "id"                        => null,
                    "analyzer_user_id"          => $Analyzer->get("id"),
                    "instagram_id"              => $Analyzer->get("instagram_id"),
                    "username"                  => $Analyzer->get("username"),
                    "followers"                 => $Analyzer->get("followers"),
                    "following"                 => $Analyzer->get("following"),
                    "uploads"                   => $Analyzer->get("uploads"),
                    "average_engagement_rate"   => $Analyzer->get("average_engagement_rate"),
                    "date"                      => $date
                ));

        }
    }

    /**
     * @param $username
     * Get data of the instagram account
     */
    public function instagramProcess($Account)
    {

        try {
            $Instagram = \InstagramController::login($Account);
        } catch (\Exception $e) {
            $this->resp->msg = $e->getMessage();
            $this->jsonecho();
        }

        try {
            $resp = $Instagram->people->getSelfInfo();
        } catch (\Exception $e) {
            $this->resp->msg = $e->getMessage();
            $this->jsonecho();
        }

        if (!$resp->isOk()) {
            $this->resp->msg = __("Couldn't get user info.");
            $this->jsonecho();
        }


        $instagramNewData = [
            "instagram_id"      => $Account->get("instagram_id"),
            "username"          => $resp->getUser()->getUsername(),
            "full_name"         => $this->replace_4byte($resp->getUser()->getFullName()),
            "description"       => $resp->getUser()->getBiography(),
            "website"           => $resp->getUser()->getExternalUrl(),
            "following"         => $resp->getUser()->getFollowingCount(),
            "followers"         => $resp->getUser()->getFollowerCount(),
            "uploads"           => $resp->getUser()->getMediaCount(),
            "profile_picture_url" => $resp->getUser()->getProfilePicUrl(),
            "is_private"        => (int)$resp->getUser()->getIsPrivate(),
            "is_verified"       => (int)$resp->getUser()->getIsVerified(),
        ];

        /* Get extra details from last media */
        $likes_array = [];
        $comments_array = [];
        $engagement_rate_array = [];
        $hashtags_array = [];
        $mentions_array = [];
        $top_posts_array = [];
        $details = [];

        try {
            $media = $Instagram->timeline->getSelfUserFeed();
        } catch (\Exception $e) {
            $this->resp->msg = $e->getMessage();
            $this->jsonecho();
        }

        /* Get details from the media of the user */
        foreach ($media->getItems() as $media) {
            if(is_null($media)) continue;

            $likes_array[$media->getCode()] = $media->getLikeCount();
            $comments_array[$media->getCode()] = $media->getCommentCount();
            $engagement_rate_array[$media->getCode()] = number_format(($media->getLikeCount() + $media->getCommentCount()) / $instagramNewData['followers'] * 100, 2);

            /* Hashtags processing from the caption */
            $hashtags = $mentions = [];
            $caption = $media->getCaption();

            if(!is_null($caption)) {
                $hashtags = $this->get_hashtags($caption->getText());

                foreach ($hashtags as $hashtag) {
                    if (!isset($hashtags_array[$hashtag])) {
                        $hashtags_array[$hashtag] = 1;
                    } else {
                        $hashtags_array[$hashtag]++;
                    }
                }

                /* Mentions processing from the caption */
                $mentions = $this->get_mentions($caption->getText());

                foreach ($mentions as $mention) {
                    if (!isset($mentions_array[$mention])) {
                        $mentions_array[$mention] = 1;
                    } else {
                        $mentions_array[$mention]++;
                    }
                }
            }

        }

        /* Calculate needed details */
        $details['total_likes'] = intval($instagramNewData['uploads']) > 0 ? array_sum($likes_array) : 0;
        $details['total_comments'] = intval($instagramNewData['uploads']) > 0 ? array_sum($comments_array) : 0;
        $details['average_comments'] = intval($instagramNewData['uploads']) > 0 ? number_format($details['total_comments'] / count($comments_array), 2) : 0;
        $details['average_likes'] = intval($instagramNewData['uploads']) > 0 ? number_format($details['total_likes'] / count($likes_array), 2) : 0;
        $instagramNewData['average_engagement_rate'] = intval($instagramNewData['uploads']) > 0 ? number_format(array_sum($engagement_rate_array) / count($engagement_rate_array), 2) : 0;

        /* Do proper sorting */
        arsort($engagement_rate_array);
        arsort($hashtags_array);
        arsort($mentions_array);
        $top_posts_array = array_slice($engagement_rate_array, 0, 3);
        $top_hashtags_array = array_slice($hashtags_array, 0, 15);
        $top_mentions_array = array_slice($mentions_array, 0, 15);

        /* Get them all together */
        $details['top_hashtags'] = $top_hashtags_array;
        $details['top_mentions'] = $top_mentions_array;
        $details['top_posts'] = $top_posts_array;

        $instagramNewData['details'] = $details;

        return $instagramNewData;
    }

    /**
     * @param $Analyzer
     * @param $dateStart
     * @param $dateEnd
     * @return object
     * Get the logs for the day by day and charts statistics
     */
    public function getLogs($Analyzer, $dateStart, $dateEnd)
    {

        // Check if we have a specific datew
        if($dateStart && $dateStart) {
            // Offset the date with 1 day in order to select the current date also
            $dateStart = (new \DateTime($dateStart))->setTimezone(new \DateTimeZone($this->getVariable("AuthUser")->get("preferences.timezone")))->modify("-1 day")->format("Y-m-d");
            $dateEnd = (new \DateTime($dateEnd))->setTimezone(new \DateTimeZone($this->getVariable("AuthUser")->get("preferences.timezone")))->modify("+1 day")->format("Y-m-d");

            $logs = \DB::table(TABLE_PREFIX . "analyzer_logs")
                ->select("*")
                ->where(\DB::raw("`date` BETWEEN '{$dateStart}' AND '{$dateEnd}'"))
                ->where("analyzer_user_id", $Analyzer->get("id"))
                ->orderBy("id", "DESC")
                ->get();
        } else {
            $logs = \DB::table(TABLE_PREFIX . "analyzer_logs")
                ->select("*")
                ->where("analyzer_user_id", $Analyzer->get("id"))
                ->orderBy("id", "DESC")
                ->limit(15)
                ->get();
        }

        $logs = array_reverse($logs);

        /* Generate data for the charts and retrieving the average followers /uploads per day */
        $chart_labels_array = [];
        $chart_followers_array = $chart_following_array = $chart_average_engagement_rate_array = [];
        $total_new_followers = $total_new_uploads = [];

        for($i = 0; $i < count($logs); $i++) {
            // Transform to array
            $logs[$i] = (array) $logs[$i];

            $chart_labels_array[] = (new \DateTime($logs[$i]["date"]))->setTimezone(new \DateTimeZone($this->getVariable("AuthUser")->get("preferences.timezone")))->format("Y-m-d");
            $chart_followers_array[] = $logs[$i]["followers"];
            $chart_following_array[] = $logs[$i]["following"];
            $chart_average_engagement_rate_array[] = $logs[$i]["average_engagement_rate"];

            if($i != 0) {
                $total_new_followers[] = $logs[$i]["followers"] - $logs[$i - 1]["followers"];
                $total_new_uploads[] = $logs[$i]["uploads"] - $logs[$i - 1]["uploads"];
            }

        }

        /* Defining the chart data */
        $chart_labels = '["' . implode('", "', $chart_labels_array) . '"]';
        $chart_followers = '[' . implode(', ', $chart_followers_array) . ']';
        $chart_following = '[' . implode(', ', $chart_following_array) . ']';
        $chart_average_engagement_rate = '[' . implode(', ', $chart_average_engagement_rate_array) . ']';

        /* Defining the future projections data */
        $total_days = @(new \DateTime($logs[count($logs)-1]["date"]))->setTimezone(new \DateTimeZone($this->getVariable("AuthUser")->get("preferences.timezone")))->modify("+1 day")->diff((new \DateTime($logs[1]["date"]))->setTimezone(new \DateTimeZone($this->getVariable("AuthUser")->get("preferences.timezone")))->modify("-1 day"))->format("%a");
        if(!$total_days) $total_days = 0;

        $average_followers = $total_days > 0 ? (int) ceil(array_sum($total_new_followers) / $total_days) : 0;
        $average_uploads = $total_days > 0 ? (int) ceil((array_sum($total_new_uploads) / $total_days)) : 0;

        /* Save all the data in a variable */
        $logsData = (object) [
            "total_days"            => $total_days,
            "logs"                  => $logs,
            "chart_labels"          => $chart_labels,
            "chart_followers"       => $chart_followers,
            "chart_following"       => $chart_following,
            "chart_average_engagement_rate" => $chart_average_engagement_rate,
            "average_followers"     => $average_followers,
            "average_uploads"       => $average_uploads
        ];

        return $logsData;
    }

    public function replace_4byte($string)
    {
        return preg_replace('%(?:
          \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
        | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
        | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
    )%xs', '', $string);
    }

    public function get_hashtags($string)
    {

        preg_match_all('/#([^\s#]+)/', $string, $array);


        return $array[1];

    }

    public function get_mentions($string)
    {

        preg_match_all('/@([^\s@]+)/', $string, $array);


        return $array[1];

    }

    public function get_embed_html($shortcode)
    {

        $url = 'https://api.instagram.com/oembed/?url=http://instagr.am/p/' . $shortcode . '/&hidecaption=true&maxwidth=450';

        /* Initiate curl */
        $ch = curl_init();

        /* Disable SSL verification */
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        /* Will return the response */
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        /* Set the Url */
        curl_setopt($ch, CURLOPT_URL, $url);

        /* Execute */
        $data = curl_exec($ch);

        /* Close */
        curl_close($ch);

        $response = json_decode($data);

        return $response ? $response->html : false;

    }
}