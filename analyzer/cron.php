<?php 
namespace Plugins\Analyzer;

// Disable direct access
if (!defined('APP_VERSION')) 
    die("Yo, what's up?"); 

/**
 * All functions related to the cron task
 */

function addCronTask()
{
    // Settings of the plugin
    $settings = namespace\settings();
    $checkInterval = $settings->get("data.checkInterval");

    // Analyzer
    require_once PLUGINS_PATH . "/analyzer/controllers/AnalyzerController.php";
    require_once PLUGINS_PATH . "/analyzer/models/AnalyzerModel.php";

    // Establish some vars
    $date = (new \DateTime())->format('Y-m-d H:i:s');

    $nextpostAccounts = \DB::table(TABLE_PREFIX . "accounts")
        ->select(array("id"))
        ->where("login_required", "=", "0")
        ->get();

    foreach($nextpostAccounts as $nextpostAccount) {
        $Account = \Controller::model("Account", $nextpostAccount->id);

        // Check if the account needs to be updated in the analyzer or not
        $analyzerAccount = \DB::table(TABLE_PREFIX . "analyzer_users")
            ->select(array("id"))
            ->where(\DB::raw("TIMESTAMPDIFF(HOUR, `last_check_date`, '{$date}') > {$checkInterval}"))
            ->where("username", $Account->get("username"))
            ->first();

        if(is_null($analyzerAccount)) continue;

        $Analyzer = new AnalyzerModel(0, "`id` = '{$analyzerAccount->id}'");

        if(!$Analyzer->isAvailable()) continue;

        // Login into the account
        try {
            $Instagram = \InstagramController::login($Account);
        } catch (\Exception $e) {
            // Couldn't login into the account
            $Account->refresh();

            // Mark as checked for now
            \DB::table(TABLE_PREFIX . "analyzer_users")
                ->select(array("id"))
                ->where("id", $Analyzer->get("id"))
                ->update(array("last_check_date", $date));

            continue;
        }

        // Get the new details & Process them
        $AnalyzerController = new AnalyzerController();
        $instagramNewData = $AnalyzerController->instagramProcess($Account);

        // Update the model and data from database
        $Analyzer->updateModelData($instagramNewData);

        // Insert the new data
        $Analyzer->update();

        // Process the data into the logs in database
        $AnalyzerController->logsProcess($Analyzer);

    }

}
\Event::bind("cron.add", __NAMESPACE__."\addCronTask");
