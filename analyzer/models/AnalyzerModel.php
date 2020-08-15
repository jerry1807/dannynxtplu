<?php 
namespace Plugins\Analyzer;

// Disable direct access
if (!defined('APP_VERSION')) 
    die("Yo, what's up?");

/**
 * Schedule Model
 *
 * @version 1.0
 * @author Onelab <hello@onelab.co> 
 * 
 */

class AnalyzerModel extends \DataEntry
{	

	private $table;


    public function __construct($instagram_id, $customWhere = false)
    {
        parent::__construct();

        $this->table = TABLE_PREFIX."analyzer_users";
        $this->customWhere = $customWhere;


        // Try and get the already inserted result
        $this->select($instagram_id);

    }

    /**
     * @param int|string $where
     * @return $this|\DataEntry
     */
    public function select($uniqid = 0)
    {
        $query = \DB::table($this->table);

        /* Search in the analyzer users table by the instagram id */
        if($this->customWhere) {
            $query->where(\DB::raw($this->customWhere));
        } else {
            $query->where('instagram_id', $uniqid);
        }

        $query->limit(1)->select("*");

        if ($query->count() > 0) {
            $resp = $query->get();
            $r = $resp[0];

            foreach ($r as $field => $value)
                $this->set($field, $value);

            $this->is_available = true;
        } else {
            $this->data = array();
            $this->is_available = false;
        }

        $this->set("description", urldecode($this->get("description")));
        $this->set("details", json_decode($this->get("details")));

        return $this;
    }

    /**
     * @param array $newData
     * Update current data
     */
    public function updateModelData(array $newData)
    {
        foreach($newData as $k => $v) {
            $this->set($k, $v);
        }

        $this->set("details", ((object) $this->get("details")));

    }


    /**
     * Insert Data as new entry
     */
    public function insert()
    {
    	if ($this->isAvailable())
    		return false;

    	$id = \DB::table($this->table)
	    	->insert(array(
	    		"id" => null,
	    		"instagram_id"          => $this->get("instagram_id"),
	    		"username"              => $this->get("username"),
	    		"full_name"             => $this->get("full_name"),
                "description"           => urlencode($this->get("description")),
                "website"               => $this->get("website"),
                "followers"             => $this->get("followers"),
                "following"             => $this->get("following"),
	    		"uploads"               => $this->get("uploads"),
	    		"profile_picture_url"   => $this->get("profile_picture_url"),
                "is_private"            => $this->get("is_private"),
	    		"is_verified"           => $this->get("is_verified"),
                "average_engagement_rate" => $this->get("average_engagement_rate"),
                "details"               => json_encode($this->get("details")),
                "added_date"            => date("Y-m-d H:i:s"),
                "last_check_date"       => date("Y-m-d H:i:s")
            ));


    	$this->set("id", $id);
    	$this->markAsAvailable();
    	return $this->get("id");
    }


    /**
     * Update selected entry with Data
     */
    public function update()
    {
    	if (!$this->isAvailable())
    		return false;

    	$id = \DB::table($this->table)
    		->where("id", "=", $this->get("id"))
	    	->update(array(
                "username"              => $this->get("username"),
                "full_name"             => $this->get("full_name"),
                "description"           => urlencode($this->get("description")),
                "website"               => $this->get("website"),
                "followers"             => $this->get("followers"),
                "following"             => $this->get("following"),
                "uploads"               => $this->get("uploads"),
                "profile_picture_url"   => $this->get("profile_picture_url"),
                "is_private"            => $this->get("is_private"),
                "is_verified"           => $this->get("is_verified"),
                "average_engagement_rate" => $this->get("average_engagement_rate"),
                "details"               => json_encode($this->get("details")),
                "last_check_date"       => date("Y-m-d H:i:s")
	    	));


    	return $this;
    }


    /**
	 * Remove selected entry from database
	 */
    public function delete()
    {
    	if(!$this->isAvailable())
    		return false;

    	\DB::table($this->table)->where("id", "=", $this->get("id"))->delete();
    	$this->is_available = false;
    	return true;
    }
}
